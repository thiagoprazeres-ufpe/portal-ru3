<?php
get_header();
$units_query = new WP_Query([
	'post_type'      => 'unidade',
	'posts_per_page' => -1,
	'orderby'        => 'title',
	'order'          => 'ASC',
]);
?>

<main class="min-h-screen">
	<div class="mx-auto max-w-ru px-4 py-10 md:py-14 space-y-8">

		<!-- Page header -->
		<header class="space-y-3">
			<span class="tag tag-outline-secondary tag-lg">Restaurantes Universitários</span>
			<h1 class="text-4xl font-semibold leading-tight tracking-tight text-base-content md:text-5xl">
				Todas as unidades
			</h1>
<p class="max-w-2xl text-lg leading-8 text-base-muted">
				Encontre o restaurante universitário da UFPE mais próximo de você.
			</p>
		</header>

		<?php if ($units_query->have_posts()): ?>
			<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
				<?php while ($units_query->have_posts()): $units_query->the_post();
					$unit_meta   = ru_ufpe_theme_get_unit_meta(get_the_ID());
					$page_links  = ru_ufpe_theme_get_unit_page_links(get_the_ID());
				?>
					<article <?php post_class('card'); ?>>
						<?php if (has_post_thumbnail()): ?>
							<figure class="overflow-hidden rounded-t-md max-h-52">
								<?php the_post_thumbnail('medium', ['class' => 'max-h-52 w-full object-cover']); ?>
							</figure>
						<?php endif; ?>
						<div class="card-body gap-2">
							<div class="flex flex-wrap gap-1.5">
								<?php if ($unit_meta['cidade']): ?><span class="tag tag-outline"><?php echo esc_html($unit_meta['cidade']); ?></span><?php endif; ?>
								<span class="tag tag-success"><?php echo esc_html($unit_meta['status'] ?: 'Funcionamento regular'); ?></span>
							</div>
							<h2 class="card-title text-xl"><a class="hover:underline no-underline" href="<?php echo esc_url($page_links['overview']); ?>"><?php the_title(); ?></a></h2>
							<p class="text-sm leading-6 text-base-muted line-clamp-3"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 18)); ?></p>
							<div class="card-actions mt-1 flex gap-2">
								<a class="btn btn-primary" href="<?php echo esc_url($page_links['overview']); ?>">Saiba mais</a>
								<a class="btn btn-outline" href="<?php echo esc_url($page_links['cardapio']); ?>">Cardápio</a>
							</div>
						</div>
					</article>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		<?php else: ?>
			<div class="notice notice-info"><i data-lucide="info"></i><span>Nenhuma unidade cadastrada.</span></div>
		<?php endif; ?>

	</div>
</main>

<?php get_footer(); ?>
