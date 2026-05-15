<?php

function ru_ufpe_theme_google_oauth_is_configured() {
	return defined( 'RU_UFPE_GOOGLE_CLIENT_ID' )
		&& defined( 'RU_UFPE_GOOGLE_CLIENT_SECRET' )
		&& '' !== trim( (string) RU_UFPE_GOOGLE_CLIENT_ID )
		&& '' !== trim( (string) RU_UFPE_GOOGLE_CLIENT_SECRET );
}

function ru_ufpe_theme_google_oauth_client_id() {
	return ru_ufpe_theme_google_oauth_is_configured() ? trim( (string) RU_UFPE_GOOGLE_CLIENT_ID ) : '';
}

function ru_ufpe_theme_google_oauth_client_secret() {
	return ru_ufpe_theme_google_oauth_is_configured() ? trim( (string) RU_UFPE_GOOGLE_CLIENT_SECRET ) : '';
}

function ru_ufpe_theme_google_oauth_redirect_uri() {
	return admin_url( 'admin-post.php?action=ru_ufpe_google_callback' );
}

function ru_ufpe_theme_current_url() {
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';
	$request_uri = is_string( $request_uri ) ? $request_uri : '/';

	return home_url( $request_uri );
}

function ru_ufpe_theme_sanitize_auth_redirect( $url ) {
	$url = esc_url_raw( (string) $url );

	return wp_validate_redirect( $url, home_url( '/' ) );
}

function ru_ufpe_theme_get_ufpe_id_icon_uri() {
	return ru_ufpe_theme_asset_uri( 'assets/images/brand/ufpe-id.png' );
}

function ru_ufpe_theme_get_ufpe_id_login_url( $redirect_to = '' ) {
	$redirect_to = $redirect_to ? $redirect_to : ru_ufpe_theme_current_url();

	return add_query_arg(
		array(
			'action'      => 'ru_ufpe_google_start',
			'redirect_to' => ru_ufpe_theme_sanitize_auth_redirect( $redirect_to ),
			'_wpnonce'   => wp_create_nonce( 'ru_ufpe_google_start' ),
		),
		admin_url( 'admin-post.php' )
	);
}

function ru_ufpe_theme_is_ufpe_email( $email ) {
	$email = strtolower( sanitize_email( (string) $email ) );

	return '' !== $email && str_ends_with( $email, '@ufpe.br' );
}

function ru_ufpe_theme_is_ufpe_id_user( $user = null ) {
	$user = $user instanceof WP_User ? $user : wp_get_current_user();

	if ( ! $user || ! $user->exists() ) {
		return false;
	}

	$google_sub = (string) get_user_meta( $user->ID, 'ru_ufpe_google_sub', true );

	return '' !== $google_sub && ru_ufpe_theme_is_ufpe_email( $user->user_email );
}

function ru_ufpe_theme_get_auth_error_message( $code ) {
	$messages = array(
		'config'           => 'UFPE ID ainda não está configurado para este ambiente.',
		'denied'           => 'A autenticação com UFPE ID foi cancelada.',
		'state'            => 'Não foi possível validar a sessão de autenticação. Tente entrar novamente.',
		'token'            => 'Não foi possível concluir a autenticação com o Google.',
		'id_token'         => 'Não foi possível validar a identidade retornada pelo Google.',
		'domain'           => 'Use uma conta institucional com e-mail @ufpe.br.',
		'account_conflict' => 'Esta conta UFPE ID já está vinculada a outro usuário.',
	);

	return isset( $messages[ $code ] ) ? $messages[ $code ] : 'Não foi possível entrar com UFPE ID.';
}

function ru_ufpe_theme_get_auth_notice_markup() {
	if ( empty( $_GET['ufpe_id_error'] ) ) {
		return '';
	}

	$code = sanitize_key( wp_unslash( $_GET['ufpe_id_error'] ) );

	return '<div class="notice notice-warning ru-auth-notice"><i data-lucide="shield-alert"></i><span>' . esc_html( ru_ufpe_theme_get_auth_error_message( $code ) ) . '</span></div>';
}

function ru_ufpe_theme_redirect_with_auth_error( $redirect_to, $code ) {
	$redirect_to = ru_ufpe_theme_sanitize_auth_redirect( $redirect_to );
	$redirect_to = remove_query_arg( array( 'ufpe_id', 'ufpe_id_error' ), $redirect_to );

	wp_safe_redirect( add_query_arg( 'ufpe_id_error', sanitize_key( $code ), $redirect_to ) );
	exit;
}

function ru_ufpe_theme_handle_google_oauth_start() {
	if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'ru_ufpe_google_start' ) ) {
		ru_ufpe_theme_redirect_with_auth_error( home_url( '/' ), 'state' );
	}

	$redirect_to = isset( $_GET['redirect_to'] ) ? rawurldecode( (string) wp_unslash( $_GET['redirect_to'] ) ) : home_url( '/' );
	$redirect_to = ru_ufpe_theme_sanitize_auth_redirect( $redirect_to );

	if ( ! ru_ufpe_theme_google_oauth_is_configured() ) {
		ru_ufpe_theme_redirect_with_auth_error( $redirect_to, 'config' );
	}

	$state = wp_generate_password( 40, false, false );
	set_transient(
		'ru_ufpe_google_state_' . $state,
		array(
			'redirect_to' => $redirect_to,
			'created_at'  => time(),
		),
		10 * MINUTE_IN_SECONDS
	);

	$auth_url = add_query_arg(
		array(
			'client_id'     => ru_ufpe_theme_google_oauth_client_id(),
			'redirect_uri'  => ru_ufpe_theme_google_oauth_redirect_uri(),
			'response_type' => 'code',
			'scope'         => 'openid email profile',
			'state'         => $state,
			'hd'            => 'ufpe.br',
			'prompt'        => 'select_account',
		),
		'https://accounts.google.com/o/oauth2/v2/auth'
	);

	wp_redirect( esc_url_raw( $auth_url ) );
	exit;
}
add_action( 'admin_post_ru_ufpe_google_start', 'ru_ufpe_theme_handle_google_oauth_start' );
add_action( 'admin_post_nopriv_ru_ufpe_google_start', 'ru_ufpe_theme_handle_google_oauth_start' );

function ru_ufpe_theme_handle_google_oauth_callback() {
	$state = isset( $_GET['state'] ) ? sanitize_text_field( wp_unslash( $_GET['state'] ) ) : '';
	$data  = $state ? get_transient( 'ru_ufpe_google_state_' . $state ) : false;

	if ( $state ) {
		delete_transient( 'ru_ufpe_google_state_' . $state );
	}

	$redirect_to = is_array( $data ) && ! empty( $data['redirect_to'] ) ? $data['redirect_to'] : home_url( '/' );
	$redirect_to = ru_ufpe_theme_sanitize_auth_redirect( $redirect_to );

	if ( ! $state || ! is_array( $data ) ) {
		ru_ufpe_theme_redirect_with_auth_error( $redirect_to, 'state' );
	}

	if ( ! empty( $_GET['error'] ) ) {
		ru_ufpe_theme_redirect_with_auth_error( $redirect_to, 'denied' );
	}

	if ( ! ru_ufpe_theme_google_oauth_is_configured() ) {
		ru_ufpe_theme_redirect_with_auth_error( $redirect_to, 'config' );
	}

	$code = isset( $_GET['code'] ) ? sanitize_text_field( wp_unslash( $_GET['code'] ) ) : '';

	if ( '' === $code ) {
		ru_ufpe_theme_redirect_with_auth_error( $redirect_to, 'token' );
	}

	$token_response = ru_ufpe_theme_exchange_google_oauth_code( $code );

	if ( is_wp_error( $token_response ) || empty( $token_response['id_token'] ) ) {
		ru_ufpe_theme_redirect_with_auth_error( $redirect_to, 'token' );
	}

	$payload = ru_ufpe_theme_validate_google_id_token( $token_response['id_token'] );

	if ( is_wp_error( $payload ) ) {
		ru_ufpe_theme_redirect_with_auth_error( $redirect_to, $payload->get_error_code() );
	}

	$user_id = ru_ufpe_theme_get_or_create_ufpe_id_user( $payload );

	if ( is_wp_error( $user_id ) ) {
		ru_ufpe_theme_redirect_with_auth_error( $redirect_to, $user_id->get_error_code() );
	}

	$user = get_user_by( 'id', $user_id );

	wp_set_current_user( $user_id );
	wp_set_auth_cookie( $user_id, true, is_ssl() );
	update_user_meta( $user_id, 'ru_ufpe_google_last_login', current_time( 'mysql', true ) );
	do_action( 'wp_login', $user->user_login, $user );

	$redirect_to = remove_query_arg( array( 'ufpe_id', 'ufpe_id_error' ), $redirect_to );
	wp_safe_redirect( add_query_arg( 'ufpe_id', 'ok', $redirect_to ) );
	exit;
}
add_action( 'admin_post_ru_ufpe_google_callback', 'ru_ufpe_theme_handle_google_oauth_callback' );
add_action( 'admin_post_nopriv_ru_ufpe_google_callback', 'ru_ufpe_theme_handle_google_oauth_callback' );

function ru_ufpe_theme_exchange_google_oauth_code( $code ) {
	$response = wp_remote_post(
		'https://oauth2.googleapis.com/token',
		array(
			'timeout' => 15,
			'body'    => array(
				'code'          => $code,
				'client_id'     => ru_ufpe_theme_google_oauth_client_id(),
				'client_secret' => ru_ufpe_theme_google_oauth_client_secret(),
				'redirect_uri'  => ru_ufpe_theme_google_oauth_redirect_uri(),
				'grant_type'    => 'authorization_code',
			),
		)
	);

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return new WP_Error( 'token', 'Token exchange failed.' );
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	return is_array( $body ) ? $body : new WP_Error( 'token', 'Invalid token response.' );
}

function ru_ufpe_theme_base64url_decode( $value ) {
	$value = strtr( (string) $value, '-_', '+/' );
	$pad   = strlen( $value ) % 4;

	if ( $pad ) {
		$value .= str_repeat( '=', 4 - $pad );
	}

	return base64_decode( $value, true );
}

function ru_ufpe_theme_decode_jwt_part( $value ) {
	$decoded = ru_ufpe_theme_base64url_decode( $value );

	if ( false === $decoded ) {
		return null;
	}

	$json = json_decode( $decoded, true );

	return is_array( $json ) ? $json : null;
}

function ru_ufpe_theme_validate_google_id_token( $id_token ) {
	$parts = explode( '.', (string) $id_token );

	if ( 3 !== count( $parts ) ) {
		return new WP_Error( 'id_token', 'Malformed ID token.' );
	}

	$header  = ru_ufpe_theme_decode_jwt_part( $parts[0] );
	$payload = ru_ufpe_theme_decode_jwt_part( $parts[1] );

	if ( ! $header || ! $payload || empty( $header['kid'] ) || 'RS256' !== ( $header['alg'] ?? '' ) ) {
		return new WP_Error( 'id_token', 'Invalid ID token header.' );
	}

	$signature = ru_ufpe_theme_base64url_decode( $parts[2] );

	if ( false === $signature || ! ru_ufpe_theme_verify_google_jwt_signature( $parts[0] . '.' . $parts[1], $signature, $header['kid'] ) ) {
		return new WP_Error( 'id_token', 'Invalid ID token signature.' );
	}

	$issuer = isset( $payload['iss'] ) ? (string) $payload['iss'] : '';
	$aud    = isset( $payload['aud'] ) ? (string) $payload['aud'] : '';
	$exp    = isset( $payload['exp'] ) ? (int) $payload['exp'] : 0;
	$email  = isset( $payload['email'] ) ? strtolower( sanitize_email( $payload['email'] ) ) : '';
	$hd     = isset( $payload['hd'] ) ? strtolower( (string) $payload['hd'] ) : '';
	$email_verified = isset( $payload['email_verified'] ) ? $payload['email_verified'] : false;

	if ( ! in_array( $issuer, array( 'accounts.google.com', 'https://accounts.google.com' ), true ) ) {
		return new WP_Error( 'id_token', 'Invalid issuer.' );
	}

	if ( ru_ufpe_theme_google_oauth_client_id() !== $aud || $exp < time() ) {
		return new WP_Error( 'id_token', 'Invalid audience or expired token.' );
	}

	if ( ! in_array( $email_verified, array( true, 'true', 1, '1' ), true ) || 'ufpe.br' !== $hd || ! ru_ufpe_theme_is_ufpe_email( $email ) ) {
		return new WP_Error( 'domain', 'Invalid UFPE email.' );
	}

	if ( empty( $payload['sub'] ) ) {
		return new WP_Error( 'id_token', 'Missing Google subject.' );
	}

	$payload['email'] = $email;

	return $payload;
}

function ru_ufpe_theme_verify_google_jwt_signature( $signed_data, $signature, $kid ) {
	if ( ! function_exists( 'openssl_verify' ) ) {
		return false;
	}

	$key = ru_ufpe_theme_get_google_jwk_by_kid( $kid );

	if ( ! $key ) {
		delete_transient( 'ru_ufpe_google_jwks' );
		$key = ru_ufpe_theme_get_google_jwk_by_kid( $kid );
	}

	if ( ! $key ) {
		return false;
	}

	$pem = ru_ufpe_theme_jwk_to_pem( $key );

	if ( ! $pem ) {
		return false;
	}

	return 1 === openssl_verify( $signed_data, $signature, $pem, OPENSSL_ALGO_SHA256 );
}

function ru_ufpe_theme_get_google_jwk_by_kid( $kid ) {
	$jwks = ru_ufpe_theme_get_google_jwks();

	if ( ! is_array( $jwks ) || empty( $jwks['keys'] ) || ! is_array( $jwks['keys'] ) ) {
		return null;
	}

	foreach ( $jwks['keys'] as $key ) {
		if ( isset( $key['kid'] ) && hash_equals( (string) $key['kid'], (string) $kid ) ) {
			return $key;
		}
	}

	return null;
}

function ru_ufpe_theme_get_google_jwks() {
	$cached = get_transient( 'ru_ufpe_google_jwks' );

	if ( is_array( $cached ) ) {
		return $cached;
	}

	$response = wp_remote_get( 'https://www.googleapis.com/oauth2/v3/certs', array( 'timeout' => 10 ) );

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return null;
	}

	$jwks = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( ! is_array( $jwks ) || empty( $jwks['keys'] ) ) {
		return null;
	}

	$cache_control = wp_remote_retrieve_header( $response, 'cache-control' );
	$ttl           = HOUR_IN_SECONDS;

	if ( is_string( $cache_control ) && preg_match( '/max-age=(\d+)/', $cache_control, $matches ) ) {
		$ttl = max( 5 * MINUTE_IN_SECONDS, min( DAY_IN_SECONDS, (int) $matches[1] ) );
	}

	set_transient( 'ru_ufpe_google_jwks', $jwks, $ttl );

	return $jwks;
}

function ru_ufpe_theme_asn1_length( $length ) {
	if ( $length < 128 ) {
		return chr( $length );
	}

	$bytes = '';

	while ( $length > 0 ) {
		$bytes  = chr( $length & 0xff ) . $bytes;
		$length = $length >> 8;
	}

	return chr( 0x80 | strlen( $bytes ) ) . $bytes;
}

function ru_ufpe_theme_asn1_sequence( $data ) {
	return "\x30" . ru_ufpe_theme_asn1_length( strlen( $data ) ) . $data;
}

function ru_ufpe_theme_asn1_integer( $data ) {
	$data = ltrim( $data, "\x00" );

	if ( '' === $data ) {
		$data = "\x00";
	}

	if ( ord( $data[0] ) > 0x7f ) {
		$data = "\x00" . $data;
	}

	return "\x02" . ru_ufpe_theme_asn1_length( strlen( $data ) ) . $data;
}

function ru_ufpe_theme_asn1_bit_string( $data ) {
	$data = "\x00" . $data;

	return "\x03" . ru_ufpe_theme_asn1_length( strlen( $data ) ) . $data;
}

function ru_ufpe_theme_jwk_to_pem( $jwk ) {
	if ( empty( $jwk['n'] ) || empty( $jwk['e'] ) ) {
		return '';
	}

	$modulus  = ru_ufpe_theme_base64url_decode( $jwk['n'] );
	$exponent = ru_ufpe_theme_base64url_decode( $jwk['e'] );

	if ( false === $modulus || false === $exponent ) {
		return '';
	}

	$rsa_public_key = ru_ufpe_theme_asn1_sequence(
		ru_ufpe_theme_asn1_integer( $modulus )
		. ru_ufpe_theme_asn1_integer( $exponent )
	);

	$algorithm_identifier = "\x30\x0d\x06\x09\x2a\x86\x48\x86\xf7\x0d\x01\x01\x01\x05\x00";
	$public_key_info      = ru_ufpe_theme_asn1_sequence(
		$algorithm_identifier
		. ru_ufpe_theme_asn1_bit_string( $rsa_public_key )
	);

	return "-----BEGIN PUBLIC KEY-----\n"
		. chunk_split( base64_encode( $public_key_info ), 64, "\n" )
		. "-----END PUBLIC KEY-----\n";
}

function ru_ufpe_theme_get_or_create_ufpe_id_user( $payload ) {
	$sub   = sanitize_text_field( (string) $payload['sub'] );
	$email = sanitize_email( (string) $payload['email'] );
	$name  = isset( $payload['name'] ) ? sanitize_text_field( (string) $payload['name'] ) : '';

	if ( '' === $sub || ! ru_ufpe_theme_is_ufpe_email( $email ) ) {
		return new WP_Error( 'domain', 'Invalid UFPE account.' );
	}

	$users_by_sub = get_users(
		array(
			'meta_key'   => 'ru_ufpe_google_sub',
			'meta_value' => $sub,
			'number'     => 2,
			'fields'     => 'ID',
		)
	);
	$user_by_email = get_user_by( 'email', $email );

	if ( count( $users_by_sub ) > 1 ) {
		return new WP_Error( 'account_conflict', 'Google subject linked to multiple users.' );
	}

	if ( ! empty( $users_by_sub ) ) {
		$user_id = (int) $users_by_sub[0];

		if ( $user_by_email && (int) $user_by_email->ID !== $user_id ) {
			return new WP_Error( 'account_conflict', 'Google subject and e-mail belong to different users.' );
		}

		ru_ufpe_theme_update_ufpe_id_user_meta( $user_id, $payload );

		return $user_id;
	}

	if ( $user_by_email ) {
		$existing_sub = (string) get_user_meta( $user_by_email->ID, 'ru_ufpe_google_sub', true );

		if ( '' !== $existing_sub && ! hash_equals( $existing_sub, $sub ) ) {
			return new WP_Error( 'account_conflict', 'E-mail already linked to another Google subject.' );
		}

		ru_ufpe_theme_update_ufpe_id_user_meta( $user_by_email->ID, $payload );

		return (int) $user_by_email->ID;
	}

	$user_login = ru_ufpe_theme_generate_user_login_from_email( $email );
	$user_id    = wp_insert_user(
		array(
			'user_login'   => $user_login,
			'user_pass'    => wp_generate_password( 32, true, true ),
			'user_email'   => $email,
			'display_name' => $name ? $name : $email,
			'nickname'     => $name ? $name : $email,
			'role'         => 'subscriber',
		)
	);

	if ( is_wp_error( $user_id ) ) {
		return new WP_Error( 'account_conflict', 'Could not create UFPE ID user.' );
	}

	ru_ufpe_theme_update_ufpe_id_user_meta( $user_id, $payload );

	return (int) $user_id;
}

function ru_ufpe_theme_generate_user_login_from_email( $email ) {
	$email_parts = explode( '@', $email );
	$base        = sanitize_user( $email_parts[0], true );
	$base        = $base ? $base : 'ufpe';
	$login       = $base;
	$index       = 2;

	while ( username_exists( $login ) ) {
		$login = $base . $index;
		$index++;
	}

	return $login;
}

function ru_ufpe_theme_update_ufpe_id_user_meta( $user_id, $payload ) {
	update_user_meta( $user_id, 'ru_ufpe_google_sub', sanitize_text_field( (string) $payload['sub'] ) );
	update_user_meta( $user_id, 'ru_ufpe_google_hd', 'ufpe.br' );

	if ( ! empty( $payload['picture'] ) ) {
		update_user_meta( $user_id, 'ru_ufpe_google_picture', esc_url_raw( $payload['picture'] ) );
	}
}

function ru_ufpe_theme_render_ufpe_id_button( $redirect_to = '', $class = '' ) {
	if ( ! ru_ufpe_theme_google_oauth_is_configured() ) {
		return '<span class="ru-ufpe-id-unavailable">UFPE ID indisponível</span>';
	}

	$class = trim( 'ru-ufpe-id-button ' . $class );

	return '<a class="' . esc_attr( $class ) . '" href="' . esc_url( ru_ufpe_theme_get_ufpe_id_login_url( $redirect_to ) ) . '">'
		. '<img src="' . esc_url( ru_ufpe_theme_get_ufpe_id_icon_uri() ) . '" alt="" width="24" height="24">'
		. '<span>Entrar com UFPE ID</span>'
		. '</a>';
}

function ru_ufpe_theme_render_ufpe_id_account() {
	$user = wp_get_current_user();

	if ( ru_ufpe_theme_is_ufpe_id_user( $user ) ) {
		$return_url = remove_query_arg( array( 'ufpe_id', 'ufpe_id_error' ), ru_ufpe_theme_current_url() );

		return '<div class="ru-ufpe-id-account">'
			. '<img src="' . esc_url( ru_ufpe_theme_get_ufpe_id_icon_uri() ) . '" alt="" width="24" height="24">'
			. '<span><strong>UFPE ID</strong><small>' . esc_html( $user->user_email ) . '</small></span>'
			. '<a href="' . esc_url( wp_logout_url( $return_url ) ) . '">Sair</a>'
			. '</div>';
	}

	return ru_ufpe_theme_render_ufpe_id_button( ru_ufpe_theme_current_url(), 'ru-ufpe-id-button-compact' );
}
