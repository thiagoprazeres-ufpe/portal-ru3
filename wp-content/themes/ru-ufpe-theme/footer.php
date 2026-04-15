<?php
$footer_signature  = ru_ufpe_theme_asset_uri('assets/images/brand/institutional/institutional-signature-horizontal.svg');
$footer_brand_strip = ru_ufpe_theme_asset_uri('assets/images/patterns/ru-brand-strip.svg');
?>

<footer class="mt-16 bg-base-100">
	<div class="mx-auto flex max-w-ru flex-col items-center gap-4 px-4 py-10 text-center">
		<img class="h-16 w-auto" src="<?php echo esc_url($footer_signature); ?>"
			alt="<?php bloginfo('name'); ?>">
		<p class="text-sm text-base-muted">
			Restaurante Universitário da UFPE
		</p>
	</div>
	<img class="h-4 w-full object-cover object-center"
		src="<?php echo esc_url($footer_brand_strip); ?>" alt="">
</footer>

<?php wp_footer(); ?>
</body>
</html>
