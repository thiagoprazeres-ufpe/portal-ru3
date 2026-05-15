<?php
get_header();
?>

<main class="min-h-screen">
	<div class="mx-auto max-w-3xl px-4 py-10 md:py-14 space-y-6">

		<?php while (have_posts()): the_post();
			$related_units = ru_ufpe_theme_get_related_units(get_the_ID());
		?>
		<!-- Post header ──────────────────────────────── -->
		<header class="space-y-4">
			<div class="flex flex-wrap gap-2">
				<span class="tag tag-outline-accent tag-lg">Aviso</span>
				<span class="tag tag-outline text-base-muted">
					<?php echo esc_html(get_the_date('d/m/Y')); ?>
				</span>
			</div>
			<h1 class="text-4xl font-semibold leading-tight tracking-tight text-base-content md:text-5xl">
				<?php the_title(); ?>
			</h1>
			<?php if (!empty($related_units)): ?>
				<div class="flex flex-wrap gap-2">
					<?php foreach ($related_units as $related_unit):
						$rl = ru_ufpe_theme_get_unit_page_links($related_unit->ID); ?>
						<a class="tag tag-outline-secondary hover:no-underline"
							href="<?php echo esc_url($rl['overview']); ?>">
							<?php echo esc_html(get_the_title($related_unit)); ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php else: ?>
				<div><span class="tag tag-neutral">Aviso geral</span></div>
			<?php endif; ?>
		</header>

		<!-- Post body ────────────────────────────────── -->
		<article <?php post_class('card'); ?>>
			<div class="card-body gap-0">
				<div class="prose-ru">
					<?php the_content(); ?>
				</div>
			</div>
		</article>

		<?php
		ru_ufpe_theme_render_comments_thread(
			get_the_ID(),
			array(
				'title'       => 'Comentários sobre o aviso',
				'description' => 'Entre com UFPE ID para comentar este aviso.',
			)
		);
		?>

		<?php endwhile; ?>
	</div>
</main>

<?php get_footer(); ?>
