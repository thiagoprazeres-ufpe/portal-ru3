<?php
$mosaic = ru_ufpe_theme_asset_uri('assets/images/patterns/mosaic.svg');
get_header();
?>

<main class="min-h-screen">
	<div class="mx-auto max-w-ru px-4 py-10 md:py-14 space-y-8">

		<!-- Page header ──────────────────────────────── -->
		<header class="relative overflow-hidden rounded-md bg-accent text-accent-content p-8 md:p-12">
			<img class="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-[0.06]"
				src="<?php echo esc_url($mosaic); ?>" alt="">
			<div class="relative space-y-4">
				<span class="tag tag-outline-accent tag-lg">Comunicados e atualizações</span>
				<h1 class="text-4xl font-semibold tracking-tight text-accent-content md:text-5xl">Avisos</h1>
				<p class="max-w-2xl text-lg leading-8 text-accent-content/80">
					Avisos gerais e comunicados vinculados às unidades do Restaurante Universitário da UFPE.
				</p>
			</div>
		</header>

		<!-- Posts list ───────────────────────────────── -->
		<?php if (have_posts()): ?>
			<div class="space-y-4">
				<?php while (have_posts()):
					the_post();
					$related_units = ru_ufpe_theme_get_related_units(get_the_ID());
				?>
				<article <?php post_class('card'); ?>>
					<div class="card-body gap-3">
						<div class="flex flex-wrap gap-2 items-center">
							<span class="tag tag-outline text-base-muted">
								<?php echo esc_html(get_the_date('d/m/Y')); ?>
							</span>
							<?php if (!empty($related_units)): ?>
								<?php foreach ($related_units as $unit):
									$links = ru_ufpe_theme_get_unit_page_links($unit->ID); ?>
									<a class="tag tag-outline-secondary hover:no-underline"
										href="<?php echo esc_url($links['overview']); ?>">
										<?php echo esc_html(get_the_title($unit)); ?>
									</a>
								<?php endforeach; ?>
							<?php else: ?>
								<span class="tag tag-neutral">Aviso geral</span>
							<?php endif; ?>
						</div>
						<h2 class="card-title text-xl">
							<a class="hover:underline no-underline" href="<?php the_permalink(); ?>">
								<?php the_title(); ?>
							</a>
						</h2>
						<div class="text-sm leading-7 text-base-muted [&_p]:m-0 line-clamp-3">
							<?php the_excerpt(); ?>
						</div>
					</div>
				</article>
				<?php endwhile; ?>
			</div>

			<!-- Pagination -->
			<nav class="flex justify-center gap-2" aria-label="Paginação">
				<?php the_posts_pagination([
					'mid_size'  => 2,
					'prev_text' => '<i data-lucide="chevron-left"></i>',
					'next_text' => '<i data-lucide="chevron-right"></i>',
				]); ?>
			</nav>

		<?php else: ?>
			<div class="notice notice-info">
				<i data-lucide="info"></i>
				<span>Nenhum aviso publicado até o momento.</span>
			</div>
		<?php endif; ?>

	</div>
</main>

<?php get_footer(); ?>
