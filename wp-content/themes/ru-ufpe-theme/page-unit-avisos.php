<?php
$context = ru_ufpe_theme_get_unit_page_context();
get_header();
?>

<main class="min-h-screen">
	<div class="mx-auto max-w-ru px-4 py-10 md:py-14 space-y-8">

		<?php if (!$context): ?>
			<div class="notice notice-error"><i data-lucide="circle-x"></i><span>Esta página de avisos não está vinculada corretamente.</span></div>
		<?php else:
			$unit_post   = $context['unit_post'];
			$unit_meta   = $context['unit_meta'];
			$page_links  = $context['page_links'];
			$posts_query = ru_ufpe_theme_get_unit_notices_query($unit_post->ID, 12);
		?>

		<!-- Page header -->
		<header class="space-y-4">
			<div class="flex flex-wrap gap-2">
				<span class="tag tag-outline-secondary tag-lg">Unidade</span>
				<?php if ($unit_meta['cidade']): ?>
					<span class="tag tag-outline tag-lg"><?php echo esc_html($unit_meta['cidade']); ?></span>
				<?php endif; ?>
			</div>
			<h1 class="text-4xl font-semibold leading-tight tracking-tight text-base-content md:text-5xl">
				Avisos &mdash; <?php echo esc_html(get_the_title($unit_post)); ?>
			</h1>
			<div class="flex flex-wrap gap-2">
				<a class="btn btn-outline" href="<?php echo esc_url($page_links['overview']); ?>">Visão geral</a>
				<a class="btn btn-outline" href="<?php echo esc_url($page_links['cardapio']); ?>">Cardápio</a>
				<a class="btn btn-primary" href="<?php echo esc_url($page_links['avisos']); ?>">Avisos</a>
			</div>
		</header>

		<?php if ($posts_query->have_posts()): ?>
			<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
				<?php while ($posts_query->have_posts()): $posts_query->the_post(); ?>
					<article <?php post_class('card'); ?>>
						<div class="card-body gap-2">
							<div class="flex flex-wrap gap-1.5 items-center">
								<span class="tag tag-outline-accent">Aviso</span>
								<span class="tag tag-outline text-base-muted text-xs"><?php echo esc_html(get_the_date('d/m/Y')); ?></span>
							</div>
							<h2 class="card-title text-base">
								<a class="hover:underline no-underline" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>
							<p class="text-sm leading-6 text-base-muted line-clamp-3"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 20)); ?></p>
							<div class="card-actions mt-1">
								<a class="btn btn-ghost btn-sm" href="<?php the_permalink(); ?>">Ler mais <i data-lucide="arrow-right"></i></a>
							</div>
						</div>
					</article>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>

			<div class="flex justify-center">
				<?php
				$big = 999999999;
				echo paginate_links([
					'base'    => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
					'format'  => '?paged=%#%',
					'current' => max(1, get_query_var('paged')),
					'total'   => $posts_query->max_num_pages,
					'prev_text' => '<i data-lucide="chevron-left"></i>',
					'next_text' => '<i data-lucide="chevron-right"></i>',
				]);
				?>
			</div>
		<?php else: ?>
			<div class="notice notice-info"><i data-lucide="info"></i><span>Nenhum aviso publicado ainda para esta unidade.</span></div>
		<?php endif; ?>

		<?php endif; ?>
	</div>
</main>

<?php get_footer(); ?>
