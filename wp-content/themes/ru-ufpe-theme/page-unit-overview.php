<?php
$context           = ru_ufpe_theme_get_unit_page_context();
$fruits_illustration = ru_ufpe_theme_asset_uri('assets/images/illustrations/frutas.svg');
get_header();
?>

<main class="min-h-screen">
	<div class="mx-auto max-w-ru px-4 py-10 md:py-14 space-y-8">

		<?php if (!$context): ?>
			<div class="notice notice-error"><i data-lucide="circle-x"></i><span>Esta página de unidade não está vinculada corretamente.</span></div>
		<?php else:
			$unit_post       = $context['unit_post'];
			$unit_meta       = $context['unit_meta'];
			$map_provider    = ru_ufpe_theme_get_unit_map_provider($unit_meta);
			$map_label       = ru_ufpe_theme_get_map_provider_label($map_provider);
			$page_links      = $context['page_links'];
			$external_links  = ru_ufpe_theme_get_unit_external_links($unit_meta);
			$maps_embed_url  = !empty($unit_meta['google_maps_embed_url']) && ru_ufpe_theme_is_valid_google_maps_embed_url($unit_meta['google_maps_embed_url']) ? $unit_meta['google_maps_embed_url'] : '';
			$next_meal       = $unit_meta['next_meal'];
			$featured_notice = ru_ufpe_theme_get_unit_notices_query($unit_post->ID, 3);
			$allergen_labels = ru_ufpe_theme_get_weekly_menu_allergen_options();
			$dietary_labels  = ru_ufpe_theme_get_weekly_menu_dietary_options();
		?>

		<!-- Page header -->
		<header class="space-y-4">
			<div class="flex flex-wrap gap-2">
				<span class="tag tag-outline-secondary tag-lg">Unidade</span>
				<?php if ($unit_meta['cidade']): ?>
					<span class="tag tag-outline tag-lg"><?php echo esc_html($unit_meta['cidade']); ?></span>
				<?php endif; ?>
				<span class="tag tag-success tag-lg"><?php echo esc_html($unit_meta['status'] ?: 'Funcionamento regular'); ?></span>
				<?php if ($next_meal): ?>
					<span class="tag tag-primary tag-lg gap-1">
						<i data-lucide="clock-3"></i>
						<?php echo esc_html('Próxima: ' . $next_meal['meal_label']); ?>
					</span>
				<?php endif; ?>
			</div>
			<h1 class="text-4xl font-semibold leading-tight tracking-tight text-base-content md:text-5xl">
				<?php echo esc_html(get_the_title($unit_post)); ?>
			</h1>
			<p class="max-w-2xl text-lg leading-8 text-base-muted">
				<?php echo esc_html($unit_post->post_excerpt ?: wp_trim_words(wp_strip_all_tags($unit_post->post_content), 26)); ?>
			</p>
			<div class="flex flex-wrap gap-2">
				<a class="btn btn-primary" href="<?php echo esc_url($page_links['overview']); ?>">Visão geral</a>
				<a class="btn btn-outline" href="<?php echo esc_url($page_links['cardapio']); ?>">Cardápio</a>
				<a class="btn btn-outline" href="<?php echo esc_url($page_links['avisos']); ?>">Avisos</a>
			</div>
		</header>

		<!-- Main grid -->
		<div class="grid gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(260px,1fr)]">
			<article class="card">
				<div class="card-body gap-5">
					<?php if (has_post_thumbnail($unit_post)): ?>
						<figure class="overflow-hidden rounded-md">
							<?php echo get_the_post_thumbnail($unit_post, 'large', ['class' => 'max-h-80 w-full object-cover']); ?>
						</figure>
					<?php endif; ?>
					<div class="prose-ru"><?php echo apply_filters('the_content', $unit_post->post_content); ?></div>
				</div>
			</article>

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

				<div class="card">
					<div class="card-body gap-2">
						<h2 class="text-xs font-semibold uppercase tracking-widest text-base-muted">Acessos rápidos</h2>
						<a class="btn btn-primary btn-wide" href="<?php echo esc_url($page_links['cardapio']); ?>"><i data-lucide="utensils"></i> Cardápio semanal</a>
						<a class="btn btn-outline btn-wide" href="<?php echo esc_url($page_links['avisos']); ?>"><i data-lucide="bell"></i> Avisos da unidade</a>
					</div>
				</div>

				<?php if ($next_meal): ?>
					<div class="card">
						<div class="card-body gap-3">
							<div class="flex items-center justify-between gap-2">
								<span class="tag tag-outline-primary text-xs gap-1"><i data-lucide="clock-3"></i> Próxima refeição</span>
								<a class="btn btn-ghost btn-sm" href="<?php echo esc_url($page_links['cardapio']); ?>">Ver cardápio</a>
							</div>
							<h2 class="card-title text-xl"><?php echo esc_html($next_meal['meal_label']); ?></h2>
							<p class="text-sm text-base-muted"><?php echo esc_html($next_meal['display_label'] . ', ' . $next_meal['time']); ?></p>
							<?php if ($next_meal['has_menu']): ?>
								<div class="space-y-3 pt-1">
									<?php foreach ($next_meal['sections'] as $section): ?>
										<div class="space-y-2">
											<h3 class="text-xs font-semibold uppercase tracking-widest text-base-muted"><?php echo esc_html($section['label']); ?></h3>
											<?php foreach ($section['items'] as $item): ?>
												<div class="rounded-md bg-base-200 px-4 py-3">
													<div class="flex flex-wrap gap-1.5 items-center">
														<span class="font-medium text-sm"><?php echo esc_html($item['name']); ?></span>
												<?php if ($section['key'] === 'vegetariano'): ?><span class="tag tag-success tag-sm gap-0.5"><i data-lucide="leaf"></i>Vegetariano</span><?php endif; ?>
												<?php foreach ($item['allergens'] as $al): ?><span class="tag tag-warning tag-sm gap-0.5"><i data-lucide="triangle-alert"></i><?php echo esc_html($allergen_labels[$al] ?? $al); ?></span><?php endforeach; ?>
												<?php if (!empty($item['traces'])): foreach ($item['traces'] as $tr): ?><span class="tag tag-outline-warning tag-sm gap-0.5"><i data-lucide="info"></i>Traços: <?php echo esc_html($allergen_labels[$tr] ?? $tr); ?></span><?php endforeach; endif; ?>
												<?php foreach ($item['dietary'] as $di): ?><span class="tag tag-success tag-sm gap-0.5"><i data-lucide="leaf"></i><?php echo esc_html($dietary_labels[$di] ?? $di); ?></span><?php endforeach; ?>
													</div>
												</div>
											<?php endforeach; ?>
										</div>
									<?php endforeach; ?>
								</div>
							<?php else: ?>
								<div class="notice notice-info text-xs"><i data-lucide="info"></i><span>Nenhum item publicado para esta refeição.</span></div>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>

				<?php if (!empty($external_links)): ?>
					<div class="card">
						<div class="card-body gap-2">
							<h2 class="text-xs font-semibold uppercase tracking-widest text-base-muted">Links</h2>
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

				<?php if ($maps_embed_url): ?>
					<div class="card">
						<div class="card-body gap-3">
							<h2 class="card-title text-base gap-1"><i data-lucide="map-pin"></i> Como chegar</h2>
							<div class="overflow-hidden rounded-md">
								<div class="aspect-[4/3] w-full">
									<iframe class="h-full w-full" src="<?php echo esc_url($maps_embed_url); ?>" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade" title="<?php echo esc_attr('Mapa ' . get_the_title($unit_post)); ?>"></iframe>
								</div>
							</div>
							<?php if (!empty($unit_meta['google_maps_url'])): ?>
								<a class="btn btn-outline justify-start gap-2" href="<?php echo esc_url($unit_meta['google_maps_url']); ?>" target="_blank" rel="noopener noreferrer">
									<i data-lucide="map"></i> Abrir no <?php echo esc_html($map_label); ?>
									<i data-lucide="external-link" class="ml-auto text-base-muted"></i>
								</a>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>
			</aside>
		</div>

		<!-- Latest notices -->
		<section class="space-y-5">
			<div class="flex flex-wrap items-center justify-between gap-3">
				<div class="space-y-1">
					<span class="tag tag-outline-accent">Avisos</span>
					<h2 class="text-2xl font-semibold tracking-tight">Últimas atualizações de <?php echo esc_html(get_the_title($unit_post)); ?></h2>
				</div>
				<a class="btn btn-outline" href="<?php echo esc_url($page_links['avisos']); ?>">Ver todos</a>
			</div>
			<?php if ($featured_notice->have_posts()): ?>
				<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
					<?php while ($featured_notice->have_posts()): $featured_notice->the_post(); ?>
						<article <?php post_class('card'); ?>>
							<div class="card-body gap-2">
								<span class="tag tag-outline text-base-muted text-xs w-fit"><?php echo esc_html(get_the_date('d/m/Y')); ?></span>
								<h3 class="card-title text-base"><a class="hover:underline no-underline" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								<p class="text-sm leading-6 text-base-muted line-clamp-3"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 18)); ?></p>
							</div>
						</article>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
			<?php else: ?>
				<div class="notice notice-info"><i data-lucide="info"></i><span>Nenhum aviso vinculado a esta unidade foi publicado.</span></div>
			<?php endif; ?>
		</section>

		<?php endif; ?>
	</div>
</main>

<?php get_footer(); ?>
