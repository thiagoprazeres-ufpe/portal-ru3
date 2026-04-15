<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php
$header_logo      = ru_ufpe_theme_asset_uri('assets/images/brand/brand-dark.svg');
$header_signature = ru_ufpe_theme_asset_uri('assets/images/brand/institutional/institutional-signature-horizontal.svg');
?>

<!-- Overlay -->
<div id="nav-overlay" class="nav-overlay" aria-hidden="true"></div>

<!-- Drawer nav -->
<nav id="nav-drawer" class="nav-drawer" aria-label="Navegação principal">
	<div class="flex items-center justify-between px-4 py-3">
		<img class="h-5 w-auto opacity-60" src="<?php echo esc_url($header_signature); ?>"
			alt="<?php bloginfo('name'); ?>">
		<button id="menu-close" type="button" class="btn btn-ghost btn-square" aria-label="Fechar menu">
			<i data-lucide="x"></i>
		</button>
	</div>
	<?php
	wp_nav_menu([
		'menu'        => 'Menu 1',
		'container'   => false,
		'menu_class'  => 'nav-menu',
		'fallback_cb' => 'ru_ufpe_theme_primary_menu_fallback',
	]);
	?>
</nav>

<!-- Top navigation bar -->
<header class="sticky top-0 z-30 bg-base-100">
	<div class="mx-auto flex max-w-ru items-center justify-between gap-4 px-4 py-3">

		<!-- Left: hamburger + logo -->
		<div class="flex items-center gap-2">
			<button id="menu-btn" type="button" class="btn btn-ghost btn-square" aria-label="Abrir menu">
				<i data-lucide="menu"></i>
			</button>
			<a href="<?php echo esc_url(home_url('/')); ?>" class="no-underline flex items-center shrink-0">
				<img class="h-8 w-auto" src="<?php echo esc_url($header_logo); ?>"
					alt="<?php bloginfo('name'); ?>">
			</a>
		</div>

		<!-- Right: search -->
		<div class="relative shrink-0">
			<button id="search-btn" type="button"
				class="btn btn-ghost btn-square"
				aria-label="Abrir busca"
				aria-expanded="false"
				aria-controls="search-panel">
				<i data-lucide="search"></i>
			</button>
			<div id="search-panel"
				class="hidden absolute right-0 top-full mt-2 w-80 rounded-md bg-base-100 p-3 z-50"
				role="search">
				<form method="get" action="<?php echo esc_url(home_url('/')); ?>" class="flex gap-2">
					<input
						type="search"
						name="s"
						class="input"
						placeholder="Buscar no portal…"
						value="<?php echo esc_attr(get_search_query()); ?>"
						autocomplete="off">
					<button type="submit" class="btn btn-primary btn-sm shrink-0">Buscar</button>
				</form>
			</div>
		</div>

	</div>
</header>
