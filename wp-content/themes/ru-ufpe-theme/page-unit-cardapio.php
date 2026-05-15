<?php
$context = ru_ufpe_theme_get_unit_page_context();
get_header();
?>

<main class="min-h-screen">
	<div class="mx-auto max-w-ru px-4 py-10 md:py-14 space-y-8">

		<?php if (!$context): ?>
			<div class="notice notice-error"><i data-lucide="circle-x"></i><span>Esta página de cardápio não está vinculada corretamente.</span></div>
		<?php else:
			$unit_post        = $context['unit_post'];
			$unit_meta        = $context['unit_meta'];
			$page_links       = $context['page_links'];
			$weekly_menu_data = ru_ufpe_theme_get_weekly_menu_data_for_unit($unit_post->ID);
			$weekly_menu_days = $weekly_menu_data ? $weekly_menu_data['menu']['days'] : [];
			$week_range       = $weekly_menu_data ? ru_ufpe_theme_format_weekly_menu_range($weekly_menu_data['week_start_date'], $weekly_menu_data['week_end_date']) : '';
			$day_labels       = ru_ufpe_theme_get_weekly_menu_day_labels();
			$meal_labels      = ru_ufpe_theme_get_weekly_menu_meal_labels();
			$section_labels   = ru_ufpe_theme_get_weekly_menu_section_labels();
			$allergen_labels  = ru_ufpe_theme_get_weekly_menu_allergen_options();
			$dietary_labels   = ru_ufpe_theme_get_weekly_menu_dietary_options();
			$day_css_map      = ['segunda' => 'seg', 'terca' => 'ter', 'quarta' => 'qua', 'quinta' => 'qui', 'sexta' => 'sex', 'sabado' => 'sab', 'domingo' => 'dom'];
		?>

		<!-- Page header -->
		<header class="space-y-3">
			<nav class="flex items-center gap-1.5 text-sm text-base-muted" aria-label="Breadcrumb">
				<?php $archive_url = get_post_type_archive_link('unidade'); if ($archive_url): ?>
					<a class="no-underline hover:underline" href="<?php echo esc_url($archive_url); ?>">Unidades</a>
					<span aria-hidden="true">&rsaquo;</span>
				<?php endif; ?>
				<a class="no-underline hover:underline" href="<?php echo esc_url($page_links['overview']); ?>"><?php echo esc_html(get_the_title($unit_post)); ?></a>
				<span aria-hidden="true">&rsaquo;</span>
				<span class="font-semibold text-base-content">Cardápio</span>
			</nav>
			<div>
				<h1 class="text-4xl font-semibold leading-tight tracking-tight text-base-content md:text-5xl">
					Cardápio &mdash; <?php echo esc_html(get_the_title($unit_post)); ?>
				</h1>
				<?php if ($week_range): ?>
					<p class="mt-1 text-sm text-base-muted"><?php echo esc_html($week_range); ?></p>
				<?php endif; ?>
			</div>
			<div class="flex flex-wrap gap-2">
				<a class="btn btn-outline" href="<?php echo esc_url($page_links['overview']); ?>">Visão geral</a>
				<a class="btn btn-primary" href="<?php echo esc_url($page_links['cardapio']); ?>">Cardápio</a>
				<a class="btn btn-outline" href="<?php echo esc_url($page_links['avisos']); ?>">Avisos</a>
			</div>
		</header>

		<!-- Weekly menu -->
		<?php if (empty($weekly_menu_days)): ?>
			<div class="notice notice-info"><i data-lucide="info"></i><span>Nenhum cardápio disponível para esta semana.</span></div>
		<?php else: ?>
			<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-7">
				<?php foreach ($day_labels as $day_key => $day_label):
					$day_data   = isset($weekly_menu_days[$day_key]) ? $weekly_menu_days[$day_key] : [];
					$day_css    = isset($day_css_map[$day_key]) ? $day_css_map[$day_key] : '';
					$has_items  = false;
					foreach ($meal_labels as $mk => $_):
						foreach ($section_labels as $sk => $_):
							if (!empty($day_data[$mk][$sk])) { $has_items = true; break 2; }
						endforeach;
					endforeach;
				?>
					<div class="card<?php echo $day_css ? ' day-' . esc_attr($day_css) : ''; ?>">
						<div class="card-body gap-4">
							<h2 class="card-title text-base"><?php echo esc_html($day_label); ?></h2>
							<?php if (!$has_items): ?>
								<p class="text-xs text-base-muted">Sem itens publicados.</p>
							<?php else: ?>
								<?php foreach ($meal_labels as $meal_key => $meal_label):
									$meal_has_items = false;
									foreach ($section_labels as $sk => $_):
										if (!empty($day_data[$meal_key][$sk])) { $meal_has_items = true; break; }
									endforeach;
									if (!$meal_has_items) continue;
								?>
									<div class="space-y-2">
										<h3 class="text-xs font-semibold uppercase tracking-widest text-base-muted pb-1"><?php echo esc_html($meal_label); ?></h3>
										<?php foreach ($section_labels as $section_key => $section_label):
											$items = isset($day_data[$meal_key][$section_key]) ? $day_data[$meal_key][$section_key] : [];
											if (empty($items)) continue;
										?>
										<dl class="space-y-1">
											<dt class="text-xs font-medium text-base-muted"><?php echo esc_html($section_label); ?></dt>
											<?php foreach ($items as $item): ?>
											<dd class="py-0.5">
												<span class="text-sm font-medium"><?php echo esc_html($item['name']); ?></span>
										<?php if (!empty($item['allergens']) || !empty($item['dietary']) || !empty($item['traces']) || $section_key === 'vegetariano'): ?>
										<div class="mt-0.5 flex flex-wrap gap-1">
											<?php if ($section_key === 'vegetariano'): ?><span class="tag tag-success tag-sm gap-0.5"><i data-lucide="leaf"></i>Vegetariano</span><?php endif; ?>
													<?php foreach ($item['allergens'] as $al): ?><span class="tag tag-warning tag-sm gap-0.5"><i data-lucide="triangle-alert"></i><?php echo esc_html($allergen_labels[$al] ?? $al); ?></span><?php endforeach; ?>
													<?php if (!empty($item['traces'])): foreach ($item['traces'] as $tr): ?><span class="tag tag-outline-warning tag-sm gap-0.5"><i data-lucide="info"></i>Traços: <?php echo esc_html($allergen_labels[$tr] ?? $tr); ?></span><?php endforeach; endif; ?>
													<?php foreach ($item['dietary'] as $di): ?><span class="tag tag-success tag-sm gap-0.5"><i data-lucide="leaf"></i><?php echo esc_html($dietary_labels[$di] ?? $di); ?></span><?php endforeach; ?>
												</div>
												<?php endif; ?>
											</dd>
											<?php endforeach; ?>
										</dl>
										<?php endforeach; ?>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php
		if ( ! empty( $weekly_menu_data['post'] ) ) {
			ru_ufpe_theme_render_comments_thread(
				$weekly_menu_data['post']->ID,
				array(
					'title'       => 'Comentários sobre o cardápio semanal',
					'description' => 'Compartilhe dúvidas e observações sobre o cardápio desta semana.',
				)
			);
		}
		?>

		<?php endif; ?>
	</div>
</main>

<?php get_footer(); ?>
