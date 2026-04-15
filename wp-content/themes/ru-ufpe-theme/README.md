# Portal RU Theme

Tema autoral open source para portais de restaurantes universitários de institutos de ensino, distribuído sob a licença MIT.
Ele foi configurado para funcionar como landing page institucional, sem remover o fluxo editorial de posts.
Todo o front público do tema deve usar `Tailwind + Vite` como padrão.

## Objetivo

Fornecer uma base minima e funcional para o front do WordPress sem depender de temas padrao.

## Arquivos principais

- `style.css`: apenas cabecalho necessario para o WordPress reconhecer o tema
- `functions.php`: setup basico do tema e carregamento do CSS compilado
- `inc/cardapio-semanal.php`: CPT, helpers, UI do admin e queries do cardapio semanal
- `front-page.php`: landing page padrao da home
- `home.php`: listagem principal de avisos
- `page.php`: fallback generico para paginas
- `page-unit-overview.php`: visao geral publica da unidade em `/{slug}/`
- `page-unit-cardapio.php`: pagina de cardapio da unidade em `/{slug}/cardapio/`
- `page-unit-avisos.php`: pagina de avisos da unidade em `/{slug}/avisos/`
- `single-unidade.php`: pagina individual de cada unidade
- `archive-unidade.php`: listagem publica de unidades
- `single.php`: pagina individual de aviso
- `index.php`: fallback do tema
- `header.php`: abertura do documento HTML e `wp_head()`
- `footer.php`: fechamento do documento e `wp_footer()`
- `assets/src/css/app.css`: fonte do Tailwind
- `assets/src/js/app.js`: entrada do Vite
- `assets/dist/app.css`: CSS compilado versionado
- `assets/dist/app.js`: JS compilado versionado
- `assets/admin/cardapio-semanal.css`: estilos do editor de cardapio no admin
- `assets/admin/cardapio-semanal.js`: scripts do editor de cardapio no admin
- `assets/images/brand/`: logos e marca do Portal RU
- `assets/images/illustrations/`: ilustracoes decorativas do front
- `assets/images/patterns/`: padroes de background do front
- `assets/images/favicon.png`: favicon padrao do site
- `vite.config.js`: configuracao do pipeline Vite
- `tailwind.config.cjs`: configuracao complementar do Tailwind

## Caracteristicas atuais

- Pipeline de front com `Tailwind + Vite`
- Pacote de icones `Lucide` inicializado no front via JS
- Fonte `Geist` empacotada localmente no build
- Templates publicos com componentes customizados do tema
- Home em formato de landing page
- Posts nativos renomeados para `Avisos`
- CPT `Unidades` com dados estruturados
- CPT `Cardapios semanais` com `menu_json` canonico por unidade e semana
- Paginas publicas sincronizadas por unidade em `/{slug}/`, `/{slug}/cardapio/` e `/{slug}/avisos/`
- Links externos opcionais por unidade para Google Maps ou OpenStreetMap, Instagram, Telefone e empresa administradora
- Configuracao de refeicoes padrao por unidade em `ru_enabled_meals`
- Editor estruturado no admin para serializar o cardapio semanal em `menu_json`
- Avisos gerais ou vinculados a uma ou mais unidades
- Pagina individual de aviso via `single.php` com layout customizado do tema
- Paginas sincronizadas marcadas como auto-geradas no admin
- Favicon padrao via `assets/images/favicon.png` com fallback automatico quando nenhum site icon esta definido no WordPress
- Menu `primary` registrado com fallback hardcoded para Inicio, Unidades e Avisos
- Campos estruturados da unidade incluem `ru_cidade`, `ru_status` e horarios base por refeicao em `ru_meal_time_desjejum`, `ru_meal_time_almoco` e `ru_meal_time_jantar`
- Campo `ru_google_maps_embed_url` para embed do mapa na pagina da unidade, com sanitizacao automatica de iframe colado do Google Maps ou OpenStreetMap e seletor de provedor em `ru_map_provider`
- Suporte a `title-tag`
- Suporte a `post-thumbnails`
- Suporte basico a HTML5

## Como evoluir

1. Criar templates dedicados como `page.php` e arquivos de taxonomia, se necessario.
2. Separar componentes recorrentes em `template-parts`.
3. Adicionar menus, areas de widget e configuracoes de personalizacao.
5. Refinar a identidade visual do Portal RU com layout e conteudo reais.

## Desenvolvimento

Com o projeto rodando na raiz:

```bash
docker compose up -d
php -S localhost:8080 router.php
```

Instale as dependencias e rode o build do front:

```bash
cd wp-content/themes/ru-ufpe-theme
npm install
npm run build
```

Durante o desenvolvimento do front:

```bash
npm run dev
```

Ative o tema, se necessario:

```bash
wp theme activate ru-ufpe-theme
```

## Convencao

- Novos templates publicos devem usar Tailwind e componentes customizados do tema.
- Novos icones do front devem usar `Lucide`.
- O CSS fonte deve ficar em `assets/src`.
- Os arquivos compilados `assets/dist/app.css` e `assets/dist/app.js` devem permanecer versionados no repositorio.
- `editor.css` foi removido do fluxo e nao deve ser recriado sem necessidade real.
- A navegacao principal deve apontar para as paginas sincronizadas por unidade, nao para `/unidades/` ou `/avisos/`.
- O conteudo das paginas sincronizadas deve ser editado via CPT `Unidades` e `Cardapios semanais`, nao pela edicao direta das paginas geradas.
- `/{slug}/cardapio/` deve consumir o `cardapio_semanal` vigente ou, na falta dele, o mais recente publicado.
- `allergens` e `dietary` usam icones default por grupo no tema, sem mapeamento por termo individual.
- `assets/images/` organiza brand, ilustracoes, padroes e favicon em subpastas; manter a estrutura existente e nao criar subpastas novas sem necessidade real.
- Os assets de admin em `assets/admin/` sao carregados apenas nas telas de edicao relevantes e nao fazem parte do pipeline Vite.
