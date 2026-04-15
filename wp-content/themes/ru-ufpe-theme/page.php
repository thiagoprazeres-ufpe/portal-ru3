<?php get_header(); ?>

<main class="min-h-screen">
	<div class="mx-auto max-w-3xl px-4 py-10 md:py-14">
		<div class="card">
			<div class="card-body gap-6">
				<?php while (have_posts()): the_post(); ?>
				<article <?php post_class('space-y-6'); ?>>
					<header class="space-y-3 pb-5">
						<span class="tag tag-outline-secondary">Página</span>
						<h1 class="text-4xl font-semibold leading-tight tracking-tight text-base-content"><?php the_title(); ?></h1>
					</header>
					<div class="prose-ru">
						<?php the_content(); ?>
					</div>
				</article>
				<?php endwhile; ?>
			</div>
		</div>
	</div>
</main>

<?php get_footer(); ?>
