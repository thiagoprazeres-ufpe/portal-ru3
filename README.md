# Portal RU — Open Source para Restaurantes Universitários

Projeto WordPress open source para portais de Restaurantes Universitários de Institutos de Ensino, com banco MySQL em Docker e tema autoral customizado.
Este repositório usa a licença MIT e foi pensado para ser liberado como projeto público reutilizável.
O tema foi configurado para abrir a home como landing page institucional e manter o tipo nativo de posts renomeado para `Avisos`.
O front do tema usa `Tailwind + Vite`, com CSS compilado versionado no repositório.
O tema também usa `Lucide` para ícones e `Geist` como fonte padrão do front público.

## Requisitos

- Docker e Docker Compose
- PHP 8+
- WP-CLI

## Estrutura

- `docker-compose.yml`: sobe o banco MySQL local
- `wp-config.php`: configuracao do WordPress para o ambiente local
- `wp-content/themes/ru-ufpe-theme`: tema autoral do projeto
- `wp-content/themes/ru-ufpe-theme/assets/src`: fontes do front em Tailwind
- `wp-content/themes/ru-ufpe-theme/assets/dist`: artefatos compilados do front
- `wp-content/themes/ru-ufpe-theme/assets/admin`: assets do editor de cardapio no admin (fora do pipeline Vite)
- `wp-content/themes/ru-ufpe-theme/assets/images`: imagens do tema (brand, ilustracoes, padroes, favicon)
- `router.php`: roteador local para o servidor embutido do PHP suportar permalinks amigaveis

## Como rodar localmente

1. Suba o banco:

```bash
docker compose up -d
```

2. Inicie o servidor PHP na raiz do projeto com o roteador local:

```bash
php -S localhost:8080 router.php
```

3. Em outro terminal, instale as dependencias do tema e gere o front:

```bash
cd wp-content/themes/ru-ufpe-theme
npm install
npm run build
```

4. Acesse:

```text
http://localhost:8080
```

## Banco de dados local

- Host: `127.0.0.1:3307`
- Database: `ru_ufpe`
- User: `ru_user`
- Password: `ru_pass`

## Admin local do WordPress

- Usuario: `admin`
- Senha: `Admin@123456`
- Email: `admin@ruufpe.local`

## Comandos uteis

Verificar se o banco esta no ar:

```bash
docker compose ps
```

Ativar o tema autoral:

```bash
wp theme activate ru-ufpe-theme
```

Listar temas instalados:

```bash
wp theme list
```

Rodar o watcher do Tailwind/Vite no tema:

```bash
cd wp-content/themes/ru-ufpe-theme
npm run dev
```

## Deploy manual para homolog

Este projeto possui um script local para sincronizar somente o tema com a VM de homolog, mantendo o fluxo de desenvolvimento local em `http://localhost:8080`.

Arquivo:

- `scripts/deploy-homolog-theme.sh`

### Como usar

1. Simulacao (padrao, sem alterar o servidor):

```bash
scripts/deploy-homolog-theme.sh
```

2. Deploy real:

```bash
scripts/deploy-homolog-theme.sh --apply
```

### O que o script faz

- Faz `rsync` do tema local para uma pasta de stage no servidor remoto
- Faz `sudo rsync` da stage para o docroot de homolog
- Aplica permissoes padrao em arquivos e diretorios do tema
- Executa `restorecon` para alinhar contexto SELinux
- Limpa cache com WP-CLI
- Roda health checks HTTP da home e dos assets criticos

### Variaveis opcionais

O script aceita override por variavel de ambiente:

- `REMOTE_USER` (padrao: `06215350443`)
- `REMOTE_HOST` (padrao: `150.161.0.230`)
- `HOST_HEADER` (padrao: `ru-homolog.ufpe.br`)
- `LOCAL_THEME_DIR` (padrao: `wp-content/themes/ru-ufpe-theme`)
- `REMOTE_STAGE_DIR` (padrao: `~/deploy/portal-ru3/wp-content/themes/ru-ufpe-theme`)
- `REMOTE_THEME_DIR` (padrao: `/var/www/ru/wp-classic/wp-content/themes/ru-ufpe-theme`)
- `WP_PATH` (padrao: `/var/www/ru/wp-classic`)

Exemplo:

```bash
REMOTE_USER=usuario REMOTE_HOST=150.161.0.230 scripts/deploy-homolog-theme.sh --apply
```

### Troubleshooting rapido

- Sintoma: imagem retorna `403 Forbidden` apos deploy
- Causa mais comum: contexto SELinux/permissao incorreta no arquivo sincronizado
- Acao: reexecutar o script com `--apply` (ele ja roda `chmod` + `restorecon` no tema)

- Sintoma: tela branca apos deploy
- Causa comum: tema ativo incorreto
- Acao:

```bash
wp --path=/var/www/ru/wp-classic theme activate ru-ufpe-theme
wp --path=/var/www/ru/wp-classic cache flush
```

## Comportamento do tema

- A home usa `front-page.php` como landing page padrao
- Os posts nativos continuam existindo, mas aparecem no admin e no tema como `Avisos`
- A listagem de avisos usa `home.php`, mas nao faz parte da navegacao principal
- A pagina individual de cada aviso usa `single.php` com layout customizado do tema
- Existe um CPT `Unidades` com campos estruturados e paginas publicas sincronizadas
- Existe um CPT `Cardapios semanais` vinculado a uma unica unidade por semana
- Cada aviso pode ser geral ou vinculado a uma ou mais unidades
- Cada unidade publicada gera automaticamente `/{slug-da-unidade}/`, `/{slug-da-unidade}/cardapio/` e `/{slug-da-unidade}/avisos/`
- O conteudo de `/{slug}/cardapio/` vem do CPT `cardapio_semanal`
- A estrutura semanal canonica e persistida no meta `menu_json`
- Cada unidade define refeicoes padrao em `ru_enabled_meals`, com override por semana
- Cada unidade possui campos de cidade (`ru_cidade`), status (`ru_status`), horarios base por refeicao (`ru_meal_time_desjejum`, `ru_meal_time_almoco`, `ru_meal_time_jantar`) e mapa gerenciavel no admin com provedor (`ru_map_provider`) e embed (`ru_google_maps_embed_url`)
- `/{slug}/avisos/` mostra apenas avisos explicitamente vinculados a essa unidade
- O CSS do front e carregado de `assets/dist/app.css`
- O JS do front e carregado de `assets/dist/app.js`
- O `Lucide` e inicializado no front para icones de interface e badges
- A fonte `Geist` e empacotada localmente no build do tema
- Os templates publicos usam componentes customizados do tema
- Cada unidade pode expor links externos opcionais para Google Maps ou OpenStreetMap, Instagram, Telefone e empresa administradora
- O favicon padrao vem de `assets/images/favicon.png` quando nenhum site icon esta configurado no WordPress
- O menu `primary` e registrado com fallback hardcoded para Inicio, Unidades e Avisos quando nenhum menu esta atribuido

## Observacoes

- O projeto esta configurado para desenvolvimento local.
- O banco depende do container `ru_ufpe_db`.
- O front depende de um servidor PHP ativo; abrir os arquivos diretamente no navegador nao funciona.
- Para permalinks amigaveis como `/recife/`, use `php -S localhost:8080 router.php`.
- O CSS compilado do tema e versionado; ao editar templates ou `assets/src`, rode `npm run build`.
- O JS compilado do tema tambem e versionado; ao editar `assets/src/js`, rode `npm run build`.
- `editor.css` nao faz parte do fluxo atual do tema.
- Os assets de admin (`assets/admin/`) nao fazem parte do pipeline Vite e sao versionados separadamente.
- Imagens de marca, ilustracoes e padroes ficam em `assets/images/` e sao referenciadas diretamente pelos templates via `ru_ufpe_theme_asset_uri()`.
