<?php

function ru_ufpe_theme_current_user_can_comment_with_ufpe_id() {
	return is_user_logged_in() && ru_ufpe_theme_is_ufpe_id_user();
}

function ru_ufpe_theme_is_restricted_comment_post_type( $post_type ) {
	return in_array( $post_type, array( 'post', 'cardapio_semanal' ), true );
}

function ru_ufpe_theme_restrict_comment_submission_to_ufpe_id( $commentdata ) {
	$post_id = isset( $commentdata['comment_post_ID'] ) ? absint( $commentdata['comment_post_ID'] ) : 0;
	$post    = $post_id ? get_post( $post_id ) : null;

	if ( ! $post || ! ru_ufpe_theme_is_restricted_comment_post_type( $post->post_type ) ) {
		return $commentdata;
	}

	if ( ! ru_ufpe_theme_current_user_can_comment_with_ufpe_id() ) {
		wp_die(
			esc_html__( 'Entre com UFPE ID para comentar.', 'ru-ufpe-theme' ),
			esc_html__( 'Comentário restrito', 'ru-ufpe-theme' ),
			array( 'response' => 403 )
		);
	}

	$user = wp_get_current_user();

	$commentdata['user_id']              = $user->ID;
	$commentdata['comment_author']       = $user->display_name ? $user->display_name : $user->user_email;
	$commentdata['comment_author_email'] = $user->user_email;
	$commentdata['comment_author_url']   = '';

	return $commentdata;
}
add_filter( 'preprocess_comment', 'ru_ufpe_theme_restrict_comment_submission_to_ufpe_id' );

function ru_ufpe_theme_open_weekly_menu_comments( $open, $post_id ) {
	$post = get_post( $post_id );

	if ( $post && 'cardapio_semanal' === $post->post_type && 'publish' === $post->post_status ) {
		return true;
	}

	return $open;
}
add_filter( 'comments_open', 'ru_ufpe_theme_open_weekly_menu_comments', 10, 2 );

function ru_ufpe_theme_render_comment_item( $comment, $args, $depth ) {
	$tag = 'div' === $args['style'] ? 'div' : 'li';
	?>
	<<?php echo esc_attr( $tag ); ?> id="comment-<?php echo esc_attr( $comment->comment_ID ); ?>" <?php comment_class( 'ru-comment-item', $comment ); ?>>
		<div class="ru-comment-avatar">
			<?php echo get_avatar( $comment, 40, '', '', array( 'class' => 'rounded-full' ) ); ?>
		</div>
		<div class="ru-comment-content">
			<div class="ru-comment-meta">
				<strong><?php echo esc_html( get_comment_author( $comment ) ); ?></strong>
				<span><?php echo esc_html( get_comment_date( 'd/m/Y H:i', $comment ) ); ?></span>
			</div>
			<?php if ( '0' === $comment->comment_approved ) : ?>
				<div class="notice notice-info ru-comment-awaiting"><i data-lucide="clock-3"></i><span>Comentário aguardando moderação.</span></div>
			<?php endif; ?>
			<div class="prose-ru ru-comment-text">
				<?php comment_text( $comment ); ?>
			</div>
		</div>
	</<?php echo esc_attr( $tag ); ?>>
	<?php
}

function ru_ufpe_theme_get_comments_for_public_thread( $post_id ) {
	return get_comments(
		array(
			'post_id' => absint( $post_id ),
			'status'  => 'approve',
			'orderby' => 'comment_date_gmt',
			'order'   => 'ASC',
		)
	);
}

function ru_ufpe_theme_render_comments_thread( $post_id, $args = array() ) {
	$post_id = absint( $post_id );
	$post    = $post_id ? get_post( $post_id ) : null;

	if ( ! $post ) {
		return;
	}

	$defaults = array(
		'title'       => 'Comentários',
		'description' => 'Entre com UFPE ID para participar da conversa.',
	);
	$args     = wp_parse_args( $args, $defaults );
	$comments = ru_ufpe_theme_get_comments_for_public_thread( $post_id );
	$count    = count( $comments );
	?>

	<section class="ru-comments card" id="comments">
		<div class="card-body">
			<div class="ru-comments-header">
				<div>
					<span class="tag tag-outline-accent">UFPE ID</span>
					<h2 class="card-title"><?php echo esc_html( $args['title'] ); ?></h2>
					<p class="text-sm text-base-muted"><?php echo esc_html( $args['description'] ); ?></p>
				</div>
				<span class="tag tag-outline"><?php echo esc_html( sprintf( _n( '%s comentário', '%s comentários', $count, 'ru-ufpe-theme' ), number_format_i18n( $count ) ) ); ?></span>
			</div>

			<?php if ( $comments ) : ?>
				<div class="ru-comment-list">
					<?php
					wp_list_comments(
						array(
							'style'       => 'div',
							'callback'    => 'ru_ufpe_theme_render_comment_item',
							'avatar_size' => 40,
							'max_depth'   => 1,
						),
						$comments
					);
					?>
				</div>
			<?php else : ?>
				<div class="notice notice-info"><i data-lucide="message-circle"></i><span>Nenhum comentário publicado ainda.</span></div>
			<?php endif; ?>

			<?php if ( comments_open( $post_id ) ) : ?>
				<?php if ( ru_ufpe_theme_current_user_can_comment_with_ufpe_id() ) : ?>
					<?php ru_ufpe_theme_render_comment_form( $post_id ); ?>
				<?php else : ?>
					<div class="ru-comment-login">
						<div>
							<h3 class="text-lg font-semibold text-base-content">Comente com UFPE ID</h3>
							<p class="text-sm text-base-muted">Somente contas institucionais @ufpe.br podem comentar.</p>
						</div>
						<?php echo ru_ufpe_theme_render_ufpe_id_button( ru_ufpe_theme_current_url() . '#comments' ); ?>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<div class="notice notice-warning"><i data-lucide="lock"></i><span>Comentários encerrados para este conteúdo.</span></div>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

function ru_ufpe_theme_render_comment_form( $post_id ) {
	$user = wp_get_current_user();

	comment_form(
		array(
			'title_reply'          => 'Adicionar comentário',
			'title_reply_before'   => '<h3 id="reply-title" class="comment-reply-title text-lg font-semibold text-base-content">',
			'title_reply_after'    => '</h3>',
			'logged_in_as'         => '<p class="ru-comment-logged-in">Comentando como <strong>' . esc_html( $user->display_name ? $user->display_name : $user->user_email ) . '</strong> via UFPE ID.</p>',
			'comment_notes_before' => '',
			'comment_notes_after'  => '',
			'class_form'           => 'ru-comment-form',
			'class_submit'         => 'btn btn-primary',
			'label_submit'         => 'Enviar comentário',
			'comment_field'        => '<p class="comment-form-comment"><label class="sr-only" for="comment">Comentário</label><textarea id="comment" name="comment" class="input ru-comment-textarea" rows="5" required placeholder="Escreva seu comentário..."></textarea></p>',
			'submit_button'        => '<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
		),
		$post_id
	);
}
