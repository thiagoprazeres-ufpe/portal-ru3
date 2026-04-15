<?php
$brand_ru_ufpe         = ru_ufpe_theme_asset_uri('assets/images/brand/ru-ufpe.svg');
$bandejao_illustration = ru_ufpe_theme_asset_uri('assets/images/illustrations/bandejao.png');
$cook_avatar           = ru_ufpe_theme_asset_uri('assets/images/avatars/cozinheira.png');
$brand_strip           = ru_ufpe_theme_asset_uri('assets/images/patterns/ru-brand-strip.svg');
$site_tagline          = get_bloginfo('description') ?: 'Alimentação de qualidade para a comunidade acadêmica.';
get_header();
?>

<main>
	<!-- Hero ── landing ───────────────────────────────── -->
	<section class="relative overflow-hidden bg-base-100 text-base-content">
		<div class="mx-auto flex max-w-ru flex-col-reverse items-center gap-8 px-4 py-12 md:flex-row md:py-20 lg:py-24">
			<div class="flex-1 space-y-6 text-center md:text-left">
				   <img class="mx-auto h-20 w-auto md:mx-0 md:h-24 animate-fade-in-up"
					   src="<?php echo esc_url($brand_ru_ufpe); ?>"
					   alt="<?php bloginfo('name'); ?>">
				   <p class="max-w-xl text-lg leading-8 text-base-content/80 mx-auto md:mx-0">
					   <?php echo esc_html($site_tagline); ?>
				   </p>

				   <?php
				   // Próxima refeição highlight (hero)
				   // Find the first published unit
				   $next_meal_unit = null;
				   $unidades_query = new WP_Query([
					   'post_type'      => 'unidade',
					   'post_status'    => 'publish',
					   'posts_per_page' => 1,
					   'orderby'        => ['menu_order' => 'ASC', 'title' => 'ASC'],
				   ]);
				   if ($unidades_query->have_posts()) {
					   $unidades_query->the_post();
					   $next_meal_unit = get_the_ID();
					   wp_reset_postdata();
				   }
				   if ($next_meal_unit) {
					   $next_meal = ru_ufpe_theme_get_next_unit_meal_summary($next_meal_unit);
					   if ($next_meal) {
						   $badge_classes = 'inline-flex items-center gap-2 rounded-full bg-primary/90 px-4 py-2 text-base font-semibold text-primary-content shadow-lg ring-2 ring-primary/30 animate-fade-in-up';
						   $icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" /></svg>';
						   echo '<div class="my-4 flex justify-center md:justify-start">';
						   echo '<span class="' . $badge_classes . '">';
						   echo $icon;
						   echo 'Próxima refeição: ' . esc_html($next_meal['meal_label']) . ' (' . esc_html($next_meal['display_label']) . ' às ' . esc_html($next_meal['time']) . ')';
						   echo '</span>';
						   echo '</div>';
					   }
				   }
				   ?>
				   <div class="flex flex-wrap justify-center gap-3 md:justify-start animate-fade-in-up" style="animation-delay:0.15s;animation-fill-mode:both;">
					   <a class="btn btn-neutral btn-lg transition-transform duration-300 hover:scale-105" href="#unidades">Explorar unidades</a>
					   <a class="btn btn-outline btn-lg transition-transform duration-300 hover:scale-105" href="#avisos-recentes">Ver avisos</a>
				   </div>
			</div>
			   <div class="w-full max-w-xs md:max-w-sm lg:max-w-md shrink-0 animate-fade-in-up" style="animation-delay:0.25s;animation-fill-mode:both;">
				   <img class="w-full h-auto" src="<?php echo esc_url($bandejao_illustration); ?>"
					   alt="Bandejão UFPE">
			   </div>
		</div>
		<img class="h-4 w-full object-cover object-center"
			src="<?php echo esc_url($brand_strip); ?>" alt="">
	</section>

	<!-- Units grid ───────────────────────────────────── -->
	<section id="unidades" class="mx-auto max-w-ru space-y-6 px-4 py-12">
		<header class="space-y-1">
			<h2 class="text-3xl font-semibold tracking-tight text-base-content md:text-4xl">Unidades</h2>
			<p class="text-base-muted">Conheça os restaurantes universitários da UFPE.</p>
		</header>

		<?php
		$unidades = new WP_Query([
			'post_type'      => 'unidade',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => ['menu_order' => 'ASC', 'title' => 'ASC'],
		]);
		?>

		<?php if ($unidades->have_posts()): ?>
			<div class="grid gap-5 lg:grid-cols-2">
				<?php while ($unidades->have_posts()):
					$unidades->the_post();
					$unit_meta  = ru_ufpe_theme_get_unit_meta(get_the_ID());
					$unit_links = ru_ufpe_theme_get_unit_page_links(get_the_ID());
				?>
				   <article <?php post_class('card flex flex-col transition-transform duration-300 hover:scale-[1.025] hover:shadow-xl hover:z-10'); ?>>
					<?php if (has_post_thumbnail()): ?>
						<a href="<?php echo esc_url($unit_links['overview']); ?>" class="block overflow-hidden no-underline">
							<?php the_post_thumbnail('large', ['class' => 'h-52 w-full object-cover transition-transform duration-300 hover:scale-[1.02]']); ?>
						</a>
					<?php endif; ?>

					<div class="card-body flex-1">
						<div class="flex flex-wrap gap-2">
							<span class="tag tag-success">
								<?php echo esc_html($unit_meta['status'] ?: 'Unidade ativa'); ?>
							</span>
							<?php if ($unit_meta['cidade']): ?>
								<span class="tag tag-outline"><?php echo esc_html($unit_meta['cidade']); ?></span>
							<?php endif; ?>
						</div>
						<h3 class="card-title text-2xl">
							<a class="hover:underline no-underline"
								href="<?php echo esc_url($unit_links['overview']); ?>">
								<?php the_title(); ?>
							</a>
						</h3>
						<?php if ($unit_meta['horario_funcionamento']): ?>
							<p class="text-sm font-medium text-base-content">
								<i data-lucide="clock-3" class="mr-1"></i>
								<?php echo esc_html($unit_meta['horario_funcionamento']); ?>
							</p>
						<?php endif; ?>
						<p class="text-sm leading-7 text-base-muted line-clamp-3">
							<?php echo esc_html(get_the_excerpt() ?: wp_trim_words(get_the_content(), 22)); ?>
						</p>
						<div class="card-actions pt-2">
							<a class="btn btn-primary btn-sm"
								href="<?php echo esc_url($unit_links['overview']); ?>">Visão geral</a>
							<a class="btn btn-outline btn-sm"
								href="<?php echo esc_url($unit_links['cardapio']); ?>">Cardápio</a>
							<a class="btn btn-outline btn-sm"
								href="<?php echo esc_url($unit_links['avisos']); ?>">Avisos</a>
						</div>
					</div>
				</article>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		<?php else: ?>
			<div class="notice notice-info">
				<i data-lucide="info"></i>
				<span>Nenhuma unidade cadastrada ainda.</span>
			</div>
		<?php endif; ?>
	</section>

	<!-- Recent notices ───────────────────────────────── -->
	<section id="avisos-recentes" class="bg-base-200 text-base-content">
		<div class="mx-auto max-w-ru space-y-6 px-4 py-12">
			<div class="flex items-center justify-between gap-4">
				<div class="space-y-1">
					<span class="tag tag-outline-accent">Avisos</span>
					<h2 class="text-3xl font-semibold tracking-tight text-base-content">Últimas atualizações</h2>
				</div>
				<img class="h-16 w-16 shrink-0 rounded-full bg-base-200 object-cover p-1"
					src="<?php echo esc_url($cook_avatar); ?>"
					alt="Equipe do <?php bloginfo('name'); ?>">
			</div>

			<?php $avisos = ru_ufpe_theme_get_general_or_related_notices_query(0, 4); ?>
			<?php if ($avisos->have_posts()): ?>
				<div class="grid gap-4 sm:grid-cols-2">
					<?php while ($avisos->have_posts()):
						$avisos->the_post();
						$related_units = ru_ufpe_theme_get_related_units(get_the_ID());
					?>
					<article class="card">
						<div class="card-body gap-3">
							<div class="flex flex-wrap gap-2">
								<span class="tag tag-outline text-base-muted text-xs">
									<?php echo esc_html(get_the_date('d/m/Y')); ?>
								</span>
								<?php if (!empty($related_units)): ?>
									<?php foreach ($related_units as $ru):
										$rl = ru_ufpe_theme_get_unit_page_links($ru->ID); ?>
										<a class="tag tag-outline-secondary hover:no-underline"
											href="<?php echo esc_url($rl['overview']); ?>">
											<?php echo esc_html(get_the_title($ru)); ?>
										</a>
									<?php endforeach; ?>
								<?php else: ?>
									<span class="tag tag-neutral">Aviso geral</span>
								<?php endif; ?>
							</div>
							<h3 class="card-title text-lg">
								<a class="hover:underline no-underline" href="<?php the_permalink(); ?>">
									<?php the_title(); ?>
								</a>
							</h3>
							<p class="text-sm leading-6 text-base-muted line-clamp-2">
								<?php echo esc_html(wp_trim_words(get_the_excerpt(), 18)); ?>
							</p>
						</div>
					</article>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
			<?php else: ?>
				<div class="notice notice-info">
					<i data-lucide="info"></i>
					<span>Nenhum aviso publicado até o momento.</span>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php get_footer(); ?>
