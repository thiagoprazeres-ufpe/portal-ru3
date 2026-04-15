<?php
get_header();
if (!have_posts()) {
	get_footer();
	exit;
}
the_post();
$unit_meta   = ru_ufpe_theme_get_unit_meta(get_the_ID());
$page_links  = ru_ufpe_theme_get_unit_page_links(get_the_ID());
$external_links = ru_ufpe_theme_get_unit_external_links($unit_meta);
?>

<main class="min-h-screen">
	<div class="mx-auto max-w-ru px-4 py-10 md:py-14 space-y-8">

		<!-- Page header -->
		<header class="space-y-4">
			<div class="flex flex-wrap gap-2">
				<span class="tag tag-outline-secondary tag-lg">Unidade</span>
				<?php if ($unit_meta['cidade']): ?><span class="tag tag-outline tag-lg"><?php echo esc_html($unit_meta['cidade']); ?></span><?php endif; ?>
				<span class="tag tag-success tag-lg"><?php echo esc_html($unit_meta['status'] ?: 'Funcionamento regular'); ?></span>
			</div>
			<h1 class="text-4xl font-semibold leading-tight tracking-tight text-base-content md:text-5xl"><?php the_title(); ?></h1>
			<p class="max-w-2xl text-lg leading-8 text-base-muted">
				<?php echo esc_html(get_the_excerpt() ?: wp_trim_words(wp_strip_all_tags(get_the_content()), 26)); ?>
			</p>
		</header>

		<!-- 2-col grid -->
		<div class="grid gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(260px,1fr)]">
			<!-- Content -->
			<article class="card">
				<div class="card-body gap-5">
					<?php if (has_post_thumbnail()): ?>
						<figure class="overflow-hidden rounded-md">
							<?php the_post_thumbnail('large', ['class' => 'max-h-80 w-full object-cover']); ?>
						</figure>
					<?php endif; ?>
					<div class="prose-ru"><?php the_content(); ?></div>
					<div class="flex flex-wrap gap-2 pt-2">
						<a class="btn btn-primary" href="<?php echo esc_url($page_links['overview']); ?>">Página da unidade</a>
						<a class="btn btn-outline" href="<?php echo esc_url($page_links['cardapio']); ?>">Ver cardápio</a>
						<a class="btn btn-outline" href="<?php echo esc_url($page_links['avisos']); ?>">Ver avisos</a>
					</div>
				</div>
			</article>

			<!-- Sidebar -->
			<aside class="space-y-4">
				<div class="card">
					<div class="card-body gap-3">
						<h2 class="text-xs font-semibold uppercase tracking-widest text-base-muted">Dados da unidade</h2>
						<ul class="space-y-3 text-sm leading-7">
							<?php if ($unit_meta['cidade']): ?><li><strong>Cidade:</strong> <?php echo esc_html($unit_meta['cidade']); ?></li><?php endif; ?>
							<?php if ($unit_meta['endereco']): ?><li><strong>Endereço:</strong> <?php echo esc_html($unit_meta['endereco']); ?></li><?php endif; ?>
							<?php if ($unit_meta['horario_funcionamento']): ?><li><strong>Horário:</strong> <?php echo esc_html($unit_meta['horario_funcionamento']); ?></li><?php endif; ?>
							<?php if ($unit_meta['contato']): ?><li><strong>Contato:</strong> <?php echo esc_html($unit_meta['contato']); ?></li><?php endif; ?>
						</ul>
					</div>
				</div>

				<?php if (!empty($external_links)): ?>
					<div class="card">
						<div class="card-body gap-2">
							<h2 class="text-xs font-semibold uppercase tracking-widest text-base-muted">Links e contato</h2>
							<?php foreach ($external_links as $link):
								$is_tel = str_starts_with($link['url'], 'tel:'); ?>
								<a class="btn btn-outline justify-start gap-2" href="<?php echo esc_url($link['url']); ?>" <?php echo $is_tel ? '' : 'target="_blank" rel="noopener noreferrer"'; ?>>
									<?php echo wp_kses_post(ru_ufpe_theme_get_icon_markup($link['icon'])); ?>
									<span class="truncate"><?php echo esc_html($link['label']); ?></span>
									<?php if (!$is_tel): ?><i data-lucide="external-link" class="ml-auto text-base-muted shrink-0"></i><?php endif; ?>
								</a>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
			</aside>
		</div>

	</div>
</main>

<?php get_footer(); ?>
