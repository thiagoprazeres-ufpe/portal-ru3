
require_once get_template_directory() . '/inc/cardapio-semanal.php';

function ru_ufpe_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	register_nav_menus(
		array(
			'primary' => 'Menu principal',
		)
	);
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);
}
add_action( 'after_setup_theme', 'ru_ufpe_theme_setup' );

function ru_ufpe_theme_set_default_tagline() {
	$current_tagline = (string) get_option( 'blogdescription', '' );

	if ( '' !== trim( $current_tagline ) ) {
		return;
	}

	update_option( 'blogdescription', 'Alimentação de qualidade para a comunidade acadêmica' );
}
add_action( 'after_switch_theme', 'ru_ufpe_theme_set_default_tagline' );

function ru_ufpe_theme_assets() {
	$compiled_css_path = get_template_directory() . '/assets/dist/app.css';
	$compiled_css_uri  = get_template_directory_uri() . '/assets/dist/app.css';
	$compiled_js_path  = get_template_directory() . '/assets/dist/app.js';
	$compiled_js_uri   = get_template_directory_uri() . '/assets/dist/app.js';
	$style_path        = file_exists( $compiled_css_path ) ? $compiled_css_path : get_stylesheet_directory() . '/style.css';
	$style_uri         = file_exists( $compiled_css_path ) ? $compiled_css_uri : get_stylesheet_uri();

	wp_enqueue_style(
		'ru-ufpe-theme-style',
		$style_uri,
		array(),
		file_exists( $style_path ) ? (string) filemtime( $style_path ) : wp_get_theme()->get( 'Version' )
	);

	if ( file_exists( $compiled_js_path ) ) {
		wp_enqueue_script(
			'ru-ufpe-theme-app',
			$compiled_js_uri,
			array(),
			(string) filemtime( $compiled_js_path ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'ru_ufpe_theme_assets' );

function ru_ufpe_theme_asset_uri( $relative_path ) {
	return trailingslashit( get_template_directory_uri() ) . ltrim( $relative_path, '/' );
}

function ru_ufpe_theme_primary_menu_fallback( $args = array() ) {
	$items = array(
		array(
			'label' => 'Início',
			'url'   => home_url( '/' ),
		),
		array(
			'label' => 'Unidades',
			'url'   => home_url( '/#unidades' ),
		),
		array(
			'label' => 'Avisos',
			'url'   => home_url( '/#avisos-recentes' ),
		),
	);

	$items_markup = '';

	foreach ( $items as $item ) {
		$items_markup .= '<li><a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['label'] ) . '</a></li>';
	}

	$items_wrap = isset( $args['items_wrap'] ) ? $args['items_wrap'] : '<ul>%3$s</ul>';
	echo sprintf(
		$items_wrap,
		isset( $args['menu_id'] ) ? esc_attr( $args['menu_id'] ) : '',
		isset( $args['menu_class'] ) ? esc_attr( $args['menu_class'] ) : '',
		$items_markup
	);
}

function ru_ufpe_theme_default_favicon() {
	if ( has_site_icon() ) {
		return;
	}

	$favicon_uri = ru_ufpe_theme_asset_uri( 'assets/images/favicon.png' );
	echo '<link rel="icon" href="' . esc_url( $favicon_uri ) . '" sizes="32x32">' . "\n";
	echo '<link rel="apple-touch-icon" href="' . esc_url( $favicon_uri ) . '">' . "\n";
}
add_action( 'wp_head', 'ru_ufpe_theme_default_favicon', 5 );

function ru_ufpe_theme_rename_posts_to_avisos() {
	global $menu;
	global $submenu;

	$menu[5][0] = 'Avisos';
	$submenu['edit.php'][5][0]  = 'Todos os avisos';
	$submenu['edit.php'][10][0] = 'Adicionar aviso';
	$submenu['edit.php'][16][0] = 'Tags dos avisos';
}
add_action( 'admin_menu', 'ru_ufpe_theme_rename_posts_to_avisos' );

function ru_ufpe_theme_rename_post_object_labels() {
	$post_type = get_post_type_object( 'post' );

	if ( ! $post_type || ! isset( $post_type->labels ) ) {
		return;
	}

	$labels = $post_type->labels;
	$labels->name                     = 'Avisos';
	$labels->singular_name            = 'Aviso';
	$labels->add_new                  = 'Adicionar';
	$labels->add_new_item             = 'Adicionar aviso';
	$labels->edit_item                = 'Editar aviso';
	$labels->new_item                 = 'Novo aviso';
	$labels->view_item                = 'Ver aviso';
	$labels->view_items               = 'Ver avisos';
	$labels->search_items             = 'Buscar avisos';
	$labels->not_found                = 'Nenhum aviso encontrado';
	$labels->not_found_in_trash       = 'Nenhum aviso encontrado na lixeira';
	$labels->all_items                = 'Todos os avisos';
	$labels->archives                 = 'Arquivo de avisos';
	$labels->attributes               = 'Atributos do aviso';
	$labels->insert_into_item         = 'Inserir no aviso';
	$labels->uploaded_to_this_item    = 'Enviado para este aviso';
	$labels->featured_image           = 'Imagem destacada';
	$labels->set_featured_image       = 'Definir imagem destacada';
	$labels->remove_featured_image    = 'Remover imagem destacada';
	$labels->use_featured_image       = 'Usar como imagem destacada';
	$labels->filter_items_list        = 'Filtrar lista de avisos';
	$labels->items_list_navigation    = 'Navegação da lista de avisos';
	$labels->items_list               = 'Lista de avisos';
	$labels->item_published           = 'Aviso publicado.';
	$labels->item_updated             = 'Aviso atualizado.';
	$labels->menu_name                = 'Avisos';
	$labels->name_admin_bar           = 'Aviso';
}
add_action( 'init', 'ru_ufpe_theme_rename_post_object_labels' );

function ru_ufpe_theme_register_unidade_post_type() {
	$labels = array(
		'name'                  => 'Unidades',
		'singular_name'         => 'Unidade',
		'menu_name'             => 'Unidades',
		'name_admin_bar'        => 'Unidade',
		'add_new'               => 'Adicionar',
		'add_new_item'          => 'Adicionar unidade',
		'new_item'              => 'Nova unidade',
		'edit_item'             => 'Editar unidade',
		'view_item'             => 'Ver unidade',
		'view_items'            => 'Ver unidades',
		'all_items'             => 'Todas as unidades',
		'search_items'          => 'Buscar unidades',
		'not_found'             => 'Nenhuma unidade encontrada',
		'not_found_in_trash'    => 'Nenhuma unidade encontrada na lixeira',
		'archives'              => 'Arquivo de unidades',
		'attributes'            => 'Atributos da unidade',
		'insert_into_item'      => 'Inserir na unidade',
		'uploaded_to_this_item' => 'Enviado para esta unidade',
	);

	register_post_type(
		'unidade',
		array(
			'labels'       => $labels,
			'public'       => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-store',
			'has_archive'  => true,
			'rewrite'      => array( 'slug' => 'unidades' ),
			'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
		)
	);
}
add_action( 'init', 'ru_ufpe_theme_register_unidade_post_type' );

function ru_ufpe_theme_register_post_meta() {
		register_post_meta(
			'unidade',
			'ru_service_days',
			array(
				'show_in_rest'      => false,
				'single'            => true,
				'type'              => 'string',
				'sanitize_callback' => function($value) {
					if (is_array($value)) {
						$allowed = array('domingo','segunda','terca','quarta','quinta','sexta','sabado');
						$value = array_values(array_intersect($allowed, $value));
						return maybe_serialize($value);
					}
					if (is_string($value)) return $value;
					return '';
				},
				'auth_callback'     => '__return_true',
			)
		);
	$unit_meta = array(
		'ru_cidade'                => array(
			'type'     => 'string',
			'sanitize' => 'sanitize_text_field',
		),
		'ru_endereco'              => array(
			'type'     => 'string',
			'sanitize' => 'sanitize_textarea_field',
		),
		'ru_horario_funcionamento' => array(
			'type'     => 'string',
			'sanitize' => 'sanitize_text_field',
		),
		'ru_contato'               => array(
			'type'     => 'string',
			'sanitize' => 'sanitize_text_field',
		),
		'ru_enabled_meals'         => array(
			'type'         => 'array',
			'sanitize'     => 'ru_ufpe_theme_sanitize_enabled_meals_meta',
			'show_in_rest' => false,
		),
		'ru_status'                => array(
			'type'     => 'string',
			'sanitize' => 'sanitize_text_field',
		),
		'ru_meal_time_desjejum'    => array(
			'type'     => 'string',
			'sanitize' => 'ru_ufpe_theme_normalize_meal_time',
		),
		'ru_meal_time_almoco'      => array(
			'type'     => 'string',
			'sanitize' => 'ru_ufpe_theme_normalize_meal_time',
		),
		'ru_meal_time_jantar'      => array(
			'type'     => 'string',
			'sanitize' => 'ru_ufpe_theme_normalize_meal_time',
		),
		'ru_map_provider'          => array(
			'type'     => 'string',
			'sanitize' => 'ru_ufpe_theme_sanitize_map_provider',
		),
		'ru_google_maps_url'       => array(
			'type'     => 'string',
			'sanitize' => 'esc_url_raw',
		),
		'ru_google_maps_embed_url' => array(
			'type'     => 'string',
			'sanitize' => 'ru_ufpe_theme_sanitize_google_maps_embed_url',
		),
		'ru_instagram_url'         => array(
			'type'     => 'string',
			'sanitize' => 'esc_url_raw',
		),
		'ru_phone'                 => array(
			'type'     => 'string',
			'sanitize' => 'sanitize_text_field',
		),
		'ru_admin_company_site_url' => array(
			'type'     => 'string',
			'sanitize' => 'esc_url_raw',
		),
	);

	foreach ( $unit_meta as $meta_key => $meta_config ) {
		register_post_meta(
			'unidade',
			$meta_key,
			array(
				'show_in_rest'       => isset( $meta_config['show_in_rest'] ) ? $meta_config['show_in_rest'] : true,
				'single'             => true,
				'type'               => $meta_config['type'],
				'sanitize_callback'  => $meta_config['sanitize'],
				'auth_callback'      => '__return_true',
			)
		);
	}

	register_post_meta(
		'post',
		'ru_related_units',
		array(
			'show_in_rest'      => false,
			'single'            => true,
			'type'              => 'array',
			'sanitize_callback' => 'ru_ufpe_theme_sanitize_related_units_meta',
			'auth_callback'     => '__return_true',
		)
	);

	register_post_meta(
		'page',
		'ru_linked_unit_id',
		array(
			'show_in_rest'      => false,
			'single'            => true,
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'auth_callback'     => '__return_true',
		)
	);

	register_post_meta(
		'page',
		'ru_unit_page_type',
		array(
			'show_in_rest'      => false,
			'single'            => true,
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_key',
			'auth_callback'     => '__return_true',
		)
	);

	register_post_meta(
		'page',
		'ru_autogenerated_page',
		array(
			'show_in_rest'      => false,
			'single'            => true,
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'auth_callback'     => '__return_true',
		)
	);
}
add_action( 'init', 'ru_ufpe_theme_register_post_meta' );

function ru_ufpe_theme_sanitize_related_units_meta( $value ) {
	if ( ! is_array( $value ) ) {
		return array();
	}

	return array_values( array_filter( array_map( 'absint', $value ) ) );
}

function ru_ufpe_theme_add_meta_boxes() {
	add_meta_box(
		'ru-unidade-detalhes',
		'Dados da unidade',
		'ru_ufpe_theme_render_unidade_meta_box',
		'unidade',
		'normal',
		'high'
	);

	add_meta_box(
		'ru-aviso-unidades',
		'Unidades relacionadas',
		'ru_ufpe_theme_render_aviso_units_meta_box',
		'post',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'ru_ufpe_theme_add_meta_boxes' );

function ru_ufpe_theme_render_unidade_meta_box( $post ) {
		// Dias da semana disponíveis
		$weekdays = array(
			'domingo' => 'Domingo',
			'segunda' => 'Segunda-feira',
			'terca' => 'Terça-feira',
			'quarta' => 'Quarta-feira',
			'quinta' => 'Quinta-feira',
			'sexta' => 'Sexta-feira',
			'sabado' => 'Sábado',
		);
		// Buscar dias salvos (meta: ru_service_days)
			$service_days = get_post_meta( $post->ID, 'ru_service_days', true );
		if ( ! is_array( $service_days ) ) {
			$service_days = maybe_unserialize($service_days );
			if ( !is_array($service_days) ) {
				$service_days = array('segunda','terca','quarta','quinta','sexta');
			}
		}
		echo '<hr>';
		echo '<p><strong>Dias de funcionamento</strong></p>';
		echo '<div style="display:flex;gap:1.5em;flex-wrap:wrap;">';
		foreach ( $weekdays as $day_key => $day_label ) {
			echo '<label style="display:inline-flex;align-items:center;gap:0.3em;">';
			echo '<input type="checkbox" name="ru_service_days[]" value="' . esc_attr( $day_key ) . '"' . checked( in_array( $day_key, $service_days, true ), true, false ) . '> ' . esc_html( $day_label );
			echo '</label>';
		}
		echo '</div>';
	wp_nonce_field( 'ru_save_unidade_meta', 'ru_unidade_meta_nonce' );

	$fields = array(
		'ru_cidade'                => 'Cidade',
		'ru_endereco'              => 'Endereço',
		'ru_horario_funcionamento' => 'Horário de funcionamento',
		'ru_contato'               => 'Contato',
		'ru_status'                => 'Status',
	);

	$status_options = array(
		'ativa'        => 'Ativa',
		'manutencao'    => 'Em manutenção',
		'fechada'       => 'Fechada',
	);
	$link_fields = array(
		'ru_google_maps_url'        => 'Link do mapa',
		'ru_google_maps_embed_url'  => 'Embed do mapa (src)',
		'ru_instagram_url'          => 'Instagram',
		'ru_phone'                  => 'Telefone',
		'ru_admin_company_site_url' => 'Site da empresa administradora',
	);

	echo '<div class="ru-meta-fields">';

	foreach ( $fields as $meta_key => $label ) {
		$value = get_post_meta( $post->ID, $meta_key, true );
		echo '<p>';
		echo '<label for="' . esc_attr( $meta_key ) . '"><strong>' . esc_html( $label ) . '</strong></label><br>';

		if ( 'ru_endereco' === $meta_key ) {
			echo '<textarea id="' . esc_attr( $meta_key ) . '" name="' . esc_attr( $meta_key ) . '" rows="3" style="width:100%;">' . esc_textarea( $value ) . '</textarea>';
		} elseif ( 'ru_status' === $meta_key ) {
			echo '<select id="' . esc_attr( $meta_key ) . '" name="' . esc_attr( $meta_key ) . '" style="width:100%;">';
			echo '<option value="">Selecione o status...</option>';
			foreach ( $status_options as $opt_value => $opt_label ) {
				echo '<option value="' . esc_attr( $opt_value ) . '"' . selected( $value, $opt_value, false ) . '>' . esc_html( $opt_label ) . '</option>';
			}
			echo '</select>';
		} else {
			echo '<input id="' . esc_attr( $meta_key ) . '" name="' . esc_attr( $meta_key ) . '" type="text" value="' . esc_attr( $value ) . '" style="width:100%;">';
		}

		echo '</p>';
	}

	$enabled_meals = ru_ufpe_theme_get_unit_enabled_meals( $post->ID );

	echo '<hr>';
	echo '<p><strong>Refeições padrão da unidade</strong></p>';

	foreach ( ru_ufpe_theme_get_weekly_menu_meal_labels() as $meal_key => $meal_label ) {
		echo '<p>';
		echo '<label>';
		echo '<input type="checkbox" name="ru_enabled_meals[]" value="' . esc_attr( $meal_key ) . '" ' . checked( in_array( $meal_key, $enabled_meals, true ), true, false ) . '> ';
		echo esc_html( $meal_label );
		echo '</label>';
		echo '</p>';
	}

	$meal_times = ru_ufpe_theme_get_unit_meal_times( $post->ID );

	echo '<p><strong>Horário base por refeição</strong></p>';

	foreach ( ru_ufpe_theme_get_weekly_menu_meal_labels() as $meal_key => $meal_label ) {
		echo '<p>';
		echo '<label for="ru_meal_time_' . esc_attr( $meal_key ) . '"><strong>' . esc_html( $meal_label ) . '</strong></label><br>';
		echo '<input id="ru_meal_time_' . esc_attr( $meal_key ) . '" name="ru_meal_time_' . esc_attr( $meal_key ) . '" type="time" value="' . esc_attr( $meal_times[ $meal_key ] ) . '" style="width:100%;">';
		echo '</p>';
	}

	echo '<hr>';
	echo '<p><strong>Links externos</strong></p>';

	$current_map_provider = ru_ufpe_theme_sanitize_map_provider( get_post_meta( $post->ID, 'ru_map_provider', true ) );
	if ( '' === $current_map_provider ) {
		$current_map_provider = ru_ufpe_theme_detect_map_provider( get_post_meta( $post->ID, 'ru_google_maps_url', true ) );
	}
	if ( '' === $current_map_provider ) {
		$current_map_provider = ru_ufpe_theme_detect_map_provider( get_post_meta( $post->ID, 'ru_google_maps_embed_url', true ) );
	}
	if ( '' === $current_map_provider ) {
		$current_map_provider = 'google';
	}

	echo '<p>';
	echo '<label for="ru_map_provider"><strong>Provedor do mapa</strong></label><br>';
	echo '<select id="ru_map_provider" name="ru_map_provider" style="width:100%;">';
	foreach ( ru_ufpe_theme_get_map_provider_options() as $provider_key => $provider_label ) {
		echo '<option value="' . esc_attr( $provider_key ) . '" ' . selected( $current_map_provider, $provider_key, false ) . '>' . esc_html( $provider_label ) . '</option>';
	}
	echo '</select>';
	echo '</p>';

	foreach ( $link_fields as $meta_key => $label ) {
		$value = get_post_meta( $post->ID, $meta_key, true );
		echo '<p>';
		echo '<label for="' . esc_attr( $meta_key ) . '"><strong>' . esc_html( $label ) . '</strong></label><br>';
		if ( 'ru_phone' === $meta_key || 'ru_google_maps_embed_url' === $meta_key ) {
			$input_type = 'text';
		} else {
			$input_type = 'url';
		}

		if ( 'ru_phone' === $meta_key ) {
			$placeholder = '(81) 99999-9999';
		} elseif ( 'ru_google_maps_embed_url' === $meta_key ) {
			$placeholder = ( 'openstreetmap' === $current_map_provider )
				? 'https://www.openstreetmap.org/export/embed.html?...'
				: 'https://www.google.com/maps/embed?...';
		} elseif ( 'ru_google_maps_url' === $meta_key ) {
			$placeholder = ( 'openstreetmap' === $current_map_provider )
				? 'https://www.openstreetmap.org/...'
				: 'https://maps.google.com/...';
		} else {
			$placeholder = 'https://';
		}
		echo '<input id="' . esc_attr( $meta_key ) . '" name="' . esc_attr( $meta_key ) . '" type="' . esc_attr( $input_type ) . '" value="' . esc_attr( $value ) . '" placeholder="' . esc_attr( $placeholder ) . '" style="width:100%;">';
		if ( 'ru_google_maps_embed_url' === $meta_key ) {
			echo '<br><small>Cole apenas a URL do src do iframe. Suporta Google Maps e OpenStreetMap; se colar o iframe inteiro, o tema extrai o src.</small>';
		}
		echo '</p>';
	}

	echo '</div>';
}

function ru_ufpe_theme_render_aviso_units_meta_box( $post ) {
	wp_nonce_field( 'ru_save_aviso_units', 'ru_aviso_units_nonce' );

	$related_units = get_post_meta( $post->ID, 'ru_related_units', true );
	$related_units = is_array( $related_units ) ? array_map( 'absint', $related_units ) : array();
	$units         = get_posts(
		array(
			'post_type'      => 'unidade',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
		)
	);

	echo '<p>Sem seleção, o aviso será considerado geral.</p>';

	if ( empty( $units ) ) {
		echo '<p>Nenhuma unidade cadastrada ainda.</p>';
		return;
	}

	foreach ( $units as $unit ) {
		$is_checked = in_array( $unit->ID, $related_units, true );
		echo '<p>';
		echo '<label>';
		echo '<input type="checkbox" name="ru_related_units[]" value="' . esc_attr( $unit->ID ) . '" ' . checked( $is_checked, true, false ) . '> ';
		echo esc_html( get_the_title( $unit ) );
		echo '</label>';
		echo '</p>';
	}
}

function ru_ufpe_theme_get_unit_public_page_definitions( $unit_post ) {
	$unit_title = get_the_title( $unit_post );

	return array(
		'overview' => array(
			'post_title'  => $unit_title,
			'post_name'   => $unit_post->post_name,
			'post_parent' => 0,
		),
		'cardapio' => array(
			'post_title'  => 'Cardápio — ' . $unit_title,
			'post_name'   => 'cardapio',
			'post_parent' => null,
		),
		'avisos' => array(
			'post_title'  => 'Avisos — ' . $unit_title,
			'post_name'   => 'avisos',
			'post_parent' => null,
		),
	);
}

function ru_ufpe_theme_get_unit_public_page_status( $unit_post ) {
	if ( 'publish' === $unit_post->post_status ) {
		return 'publish';
	}

	if ( 'trash' === $unit_post->post_status ) {
		return 'trash';
	}

	return 'draft';
}

function ru_ufpe_theme_get_linked_unit_pages( $unit_id ) {
	$unit_id = absint( $unit_id );

	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'draft', 'private', 'future', 'pending', 'trash' ),
			'posts_per_page' => -1,
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
			'meta_query'     => array(
				array(
					'key'   => 'ru_linked_unit_id',
					'value' => $unit_id,
				),
			),
		)
	);

	$grouped_pages = array();

	foreach ( $pages as $page ) {
		$page_type = get_post_meta( $page->ID, 'ru_unit_page_type', true );

		if ( $page_type ) {
			$grouped_pages[ $page_type ] = $page;
		}
	}

	return $grouped_pages;
}

function ru_ufpe_theme_get_unit_page_context( $page_id = 0 ) {
	$page_id = $page_id ? absint( $page_id ) : get_queried_object_id();

	if ( ! $page_id ) {
		return null;
	}

	$unit_id   = absint( get_post_meta( $page_id, 'ru_linked_unit_id', true ) );
	$page_type = get_post_meta( $page_id, 'ru_unit_page_type', true );
	$unit_post = $unit_id ? get_post( $unit_id ) : null;

	if ( ! $unit_post || 'unidade' !== $unit_post->post_type || ! $page_type ) {
		return null;
	}

	return array(
		'page_id'    => $page_id,
		'page_type'  => $page_type,
		'unit_id'    => $unit_id,
		'unit_post'  => $unit_post,
		'unit_meta'  => ru_ufpe_theme_get_unit_meta( $unit_id ),
		'page_links' => ru_ufpe_theme_get_unit_page_links( $unit_id ),
	);
}

function ru_ufpe_theme_get_unit_page_links( $unit_id ) {
	$pages = ru_ufpe_theme_get_linked_unit_pages( $unit_id );

	return array(
		'overview' => isset( $pages['overview'] ) ? get_permalink( $pages['overview'] ) : get_permalink( $unit_id ),
		'cardapio' => isset( $pages['cardapio'] ) ? get_permalink( $pages['cardapio'] ) : get_permalink( $unit_id ),
		'avisos'   => isset( $pages['avisos'] ) ? get_permalink( $pages['avisos'] ) : get_permalink( $unit_id ),
	);
}

function ru_ufpe_theme_upsert_linked_unit_page( $unit_post, $page_type, $page_definition, $page_status ) {
	$linked_pages = ru_ufpe_theme_get_linked_unit_pages( $unit_post->ID );
	$page_id      = isset( $linked_pages[ $page_type ] ) ? $linked_pages[ $page_type ]->ID : 0;

	if ( $page_id && 'trash' === get_post_status( $page_id ) && 'trash' !== $page_status ) {
		wp_untrash_post( $page_id );
	}

	$page_args = array(
		'post_type'   => 'page',
		'post_status' => 'trash' === $page_status ? 'draft' : $page_status,
		'post_title'  => $page_definition['post_title'],
		'post_name'   => $page_definition['post_name'],
		'post_parent' => isset( $page_definition['post_parent'] ) ? (int) $page_definition['post_parent'] : 0,
		'meta_input'  => array(
			'ru_linked_unit_id'    => (int) $unit_post->ID,
			'ru_unit_page_type'    => $page_type,
			'ru_autogenerated_page' => 1,
		),
	);

	if ( $page_id ) {
		$page_args['ID'] = $page_id;
		wp_update_post( $page_args );
	} else {
		$page_id = wp_insert_post( $page_args );
	}

	if ( 'trash' === $page_status && $page_id && 'trash' !== get_post_status( $page_id ) ) {
		wp_trash_post( $page_id );
	}

	return $page_id;
}

function ru_ufpe_theme_sync_unit_pages( $unit_id ) {
	$unit_post = get_post( $unit_id );

	if ( ! $unit_post || 'unidade' !== $unit_post->post_type || 'auto-draft' === $unit_post->post_status || ! $unit_post->post_name ) {
		return;
	}

	$page_status      = ru_ufpe_theme_get_unit_public_page_status( $unit_post );
	$page_definitions = ru_ufpe_theme_get_unit_public_page_definitions( $unit_post );
	$overview_page_id = ru_ufpe_theme_upsert_linked_unit_page( $unit_post, 'overview', $page_definitions['overview'], $page_status );

	$page_definitions['cardapio']['post_parent'] = $overview_page_id;
	$page_definitions['avisos']['post_parent']   = $overview_page_id;

	ru_ufpe_theme_upsert_linked_unit_page( $unit_post, 'cardapio', $page_definitions['cardapio'], $page_status );
	ru_ufpe_theme_upsert_linked_unit_page( $unit_post, 'avisos', $page_definitions['avisos'], $page_status );
}

function ru_ufpe_theme_sync_all_unit_pages() {
	$units = get_posts(
		array(
			'post_type'      => 'unidade',
			'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
			'posts_per_page' => -1,
		)
	);

	foreach ( $units as $unit ) {
		ru_ufpe_theme_sync_unit_pages( $unit->ID );
	}
}

function ru_ufpe_theme_handle_trashed_unit( $post_id ) {
	$post = get_post( $post_id );

	if ( ! $post || 'unidade' !== $post->post_type ) {
		return;
	}

	$linked_pages = ru_ufpe_theme_get_linked_unit_pages( $post_id );

	foreach ( $linked_pages as $linked_page ) {
		if ( 'trash' !== get_post_status( $linked_page ) ) {
			wp_trash_post( $linked_page->ID );
		}
	}
}
add_action( 'trashed_post', 'ru_ufpe_theme_handle_trashed_unit' );

function ru_ufpe_theme_handle_untrashed_unit( $post_id ) {
	$post = get_post( $post_id );

	if ( ! $post || 'unidade' !== $post->post_type ) {
		return;
	}

	ru_ufpe_theme_sync_unit_pages( $post_id );
}
add_action( 'untrashed_post', 'ru_ufpe_theme_handle_untrashed_unit' );

function ru_ufpe_theme_save_meta_boxes( $post_id, $post ) {
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}

	if ( 'unidade' === $post->post_type ) {
		if ( ! isset( $_POST['ru_unidade_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ru_unidade_meta_nonce'] ) ), 'ru_save_unidade_meta' ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$meta_fields = array(
			'ru_cidade',
			'ru_endereco',
			'ru_horario_funcionamento',
			'ru_contato',
			'ru_status',
			'ru_meal_time_desjejum',
			'ru_meal_time_almoco',
			'ru_meal_time_jantar',
			'ru_phone',
			'ru_map_provider',
			'ru_google_maps_url',
			'ru_google_maps_embed_url',
			'ru_instagram_url',
			'ru_admin_company_site_url',
		);

		foreach ( $meta_fields as $meta_key ) {
			$value = isset( $_POST[ $meta_key ] ) ? wp_unslash( $_POST[ $meta_key ] ) : '';

			if ( 'ru_endereco' === $meta_key ) {
				$value = sanitize_textarea_field( $value );
			} elseif ( in_array( $meta_key, array( 'ru_meal_time_desjejum', 'ru_meal_time_almoco', 'ru_meal_time_jantar' ), true ) ) {
				$value = ru_ufpe_theme_normalize_meal_time( $value );
			} elseif ( 'ru_map_provider' === $meta_key ) {
				$value = ru_ufpe_theme_sanitize_map_provider( $value );
			} elseif ( 'ru_google_maps_embed_url' === $meta_key ) {
				$value = ru_ufpe_theme_sanitize_google_maps_embed_url( $value );
			} elseif ( in_array( $meta_key, array( 'ru_google_maps_url', 'ru_instagram_url', 'ru_admin_company_site_url' ), true ) ) {
				$value = esc_url_raw( $value );
			} else {
				$value = sanitize_text_field( $value );
			}

			if ( '' === $value ) {
				delete_post_meta( $post_id, $meta_key );
			} else {
				update_post_meta( $post_id, $meta_key, $value );
			}
		}

		$enabled_meals = isset( $_POST['ru_enabled_meals'] ) ? ru_ufpe_theme_sanitize_enabled_meals_meta( (array) wp_unslash( $_POST['ru_enabled_meals'] ) ) : array();

		if ( empty( $enabled_meals ) ) {
			delete_post_meta( $post_id, 'ru_enabled_meals' );
		} else {
			update_post_meta( $post_id, 'ru_enabled_meals', $enabled_meals );
		}

		ru_ufpe_theme_sync_unit_pages( $post_id );
	}

	if ( 'post' === $post->post_type ) {
		if ( ! isset( $_POST['ru_aviso_units_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ru_aviso_units_nonce'] ) ), 'ru_save_aviso_units' ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$unit_ids = isset( $_POST['ru_related_units'] ) ? array_values( array_filter( array_map( 'absint', (array) wp_unslash( $_POST['ru_related_units'] ) ) ) ) : array();

		if ( empty( $unit_ids ) ) {
			delete_post_meta( $post_id, 'ru_related_units' );
		} else {
			update_post_meta( $post_id, 'ru_related_units', $unit_ids );
		}
	}
}
add_action( 'save_post', 'ru_ufpe_theme_save_meta_boxes', 10, 2 );

function ru_ufpe_theme_get_unit_meta( $post_id ) {
	return array(
		'cidade'                => get_post_meta( $post_id, 'ru_cidade', true ),
		'endereco'              => get_post_meta( $post_id, 'ru_endereco', true ),
		'horario_funcionamento' => get_post_meta( $post_id, 'ru_horario_funcionamento', true ),
		'contato'               => get_post_meta( $post_id, 'ru_contato', true ),
		'map_provider'          => get_post_meta( $post_id, 'ru_map_provider', true ),
		'google_maps_url'       => get_post_meta( $post_id, 'ru_google_maps_url', true ),
		'google_maps_embed_url' => get_post_meta( $post_id, 'ru_google_maps_embed_url', true ),
		'instagram_url'         => get_post_meta( $post_id, 'ru_instagram_url', true ),
		'phone'                 => get_post_meta( $post_id, 'ru_phone', true ),
		'admin_company_site_url' => get_post_meta( $post_id, 'ru_admin_company_site_url', true ),
		'enabled_meals'         => ru_ufpe_theme_get_unit_enabled_meals( $post_id ),
		'meal_times'            => ru_ufpe_theme_get_unit_meal_times( $post_id ),
		'next_meal'             => ru_ufpe_theme_get_next_unit_meal_summary( $post_id ),
		'status'                => get_post_meta( $post_id, 'ru_status', true ),
	);
}

function ru_ufpe_theme_get_icon_name( $icon_key ) {
	$icon_map = array(
		'allergens'             => 'triangle-alert',
		'dietary'               => 'leaf',
		'google_maps'           => 'map-pinned',
		'instagram'             => 'instagram',
		'phone'                 => 'phone',
		'search'                => 'search',
		'clock'                 => 'clock-3',
		'admin_company_website' => 'building-2',
		'external_link'         => 'arrow-up-right',
	);

	return isset( $icon_map[ $icon_key ] ) ? $icon_map[ $icon_key ] : 'circle';
}

function ru_ufpe_theme_get_icon_markup( $icon_key, $classes = '' ) {
	$icon_name = ru_ufpe_theme_get_icon_name( $icon_key );
	$class_attr = trim( 'lucide-icon ' . $classes );

	return '<i data-lucide="' . esc_attr( $icon_name ) . '" class="' . esc_attr( $class_attr ) . '" aria-hidden="true"></i>';
}

function ru_ufpe_theme_get_phone_link( $phone ) {
	$phone = sanitize_text_field( (string) $phone );

	if ( '' === $phone ) {
		return '';
	}

	$digits = preg_replace( '/[^0-9+]/', '', $phone );

	return $digits ? 'tel:' . $digits : '';
}

function ru_ufpe_theme_get_map_provider_options() {
	return array(
		'google'        => 'Google Maps',
		'openstreetmap' => 'OpenStreetMap',
	);
}

function ru_ufpe_theme_sanitize_map_provider( $value ) {
	$value = sanitize_key( (string) $value );

	return array_key_exists( $value, ru_ufpe_theme_get_map_provider_options() ) ? $value : '';
}

function ru_ufpe_theme_detect_map_provider( $url ) {
	$url = trim( (string) $url );

	if ( '' === $url ) {
		return '';
	}

	$parts = wp_parse_url( $url );
	$host  = isset( $parts['host'] ) ? strtolower( $parts['host'] ) : '';

	if ( '' === $host ) {
		return '';
	}

	if ( false !== strpos( $host, 'google.' ) || false !== strpos( $host, 'maps.app.goo.gl' ) ) {
		return 'google';
	}

	if ( false !== strpos( $host, 'openstreetmap.org' ) || false !== strpos( $host, 'umap.openstreetmap.fr' ) ) {
		return 'openstreetmap';
	}

	return '';
}

function ru_ufpe_theme_get_unit_map_provider( $unit_meta ) {
	$provider = isset( $unit_meta['map_provider'] ) ? ru_ufpe_theme_sanitize_map_provider( $unit_meta['map_provider'] ) : '';

	if ( '' !== $provider ) {
		return $provider;
	}

	$provider = ru_ufpe_theme_detect_map_provider( isset( $unit_meta['google_maps_url'] ) ? $unit_meta['google_maps_url'] : '' );

	if ( '' !== $provider ) {
		return $provider;
	}

	$provider = ru_ufpe_theme_detect_map_provider( isset( $unit_meta['google_maps_embed_url'] ) ? $unit_meta['google_maps_embed_url'] : '' );

	return '' !== $provider ? $provider : 'google';
}

function ru_ufpe_theme_get_map_provider_label( $provider ) {
	$options = ru_ufpe_theme_get_map_provider_options();

	return isset( $options[ $provider ] ) ? $options[ $provider ] : 'Mapa';
}

function ru_ufpe_theme_is_valid_google_maps_embed_url( $url ) {
	$url = trim( (string) $url );

	if ( '' === $url ) {
		return false;
	}

	$parts = wp_parse_url( $url );

	if ( empty( $parts['scheme'] ) || empty( $parts['host'] ) || empty( $parts['path'] ) ) {
		return false;
	}

	if ( 'https' !== strtolower( $parts['scheme'] ) ) {
		return false;
	}

	$host = strtolower( $parts['host'] );
	$path = $parts['path'];

	if ( in_array( $host, array( 'www.openstreetmap.org', 'openstreetmap.org' ), true ) ) {
		return 0 === strpos( $path, '/export/embed.html' );
	}

	if ( 'umap.openstreetmap.fr' === $host ) {
		return 0 === strpos( $path, '/map/' ) || 0 === strpos( $path, '/en/map/' ) || 0 === strpos( $path, '/pt-br/map/' );
	}

	if ( 'www.google.com' === $host ) {
		// Backward compatibility for already saved embeds.
		return 0 === strpos( $path, '/maps/embed' );
	}

	return false;
}

function ru_ufpe_theme_sanitize_google_maps_embed_url( $value ) {
	$value = trim( (string) $value );

	if ( '' === $value ) {
		return '';
	}

	if ( false !== stripos( $value, '<iframe' ) ) {
		if ( preg_match( '/src=(["\'])(.*?)\1/i', $value, $matches ) ) {
			$value = $matches[2];
		} else {
			return '';
		}
	}

	$value = esc_url_raw( $value );

	return ru_ufpe_theme_is_valid_google_maps_embed_url( $value ) ? $value : '';
}

function ru_ufpe_theme_get_unit_external_links( $unit_meta ) {
	$links = array();

	if ( ! empty( $unit_meta['google_maps_url'] ) ) {
		$provider = ru_ufpe_theme_get_unit_map_provider( $unit_meta );
		$label    = ru_ufpe_theme_get_map_provider_label( $provider );

		$links[] = array(
			'label' => $label,
			'url'   => $unit_meta['google_maps_url'],
			'icon'  => 'google_maps',
		);
	}

	if ( ! empty( $unit_meta['instagram_url'] ) ) {
		$links[] = array(
			'label' => 'Instagram',
			'url'   => $unit_meta['instagram_url'],
			'icon'  => 'instagram',
		);
	}

	if ( ! empty( $unit_meta['phone'] ) ) {
		$phone_url = ru_ufpe_theme_get_phone_link( $unit_meta['phone'] );

		if ( $phone_url ) {
			$links[] = array(
				'label' => $unit_meta['phone'],
				'url'   => $phone_url,
				'icon'  => 'phone',
			);
		}
	}

	if ( ! empty( $unit_meta['admin_company_site_url'] ) ) {
		$links[] = array(
			'label' => 'Empresa administradora',
			'url'   => $unit_meta['admin_company_site_url'],
			'icon'  => 'admin_company_website',
		);
	}

	return $links;
}

function ru_ufpe_theme_get_related_unit_ids( $post_id ) {
	$unit_ids = get_post_meta( $post_id, 'ru_related_units', true );

	return is_array( $unit_ids ) ? array_values( array_filter( array_map( 'absint', $unit_ids ) ) ) : array();
}

function ru_ufpe_theme_get_related_units( $post_id ) {
	$unit_ids = ru_ufpe_theme_get_related_unit_ids( $post_id );

	if ( empty( $unit_ids ) ) {
		return array();
	}

	return get_posts(
		array(
			'post_type'      => 'unidade',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'post__in'       => $unit_ids,
			'orderby'        => 'post__in',
		)
	);
}

function ru_ufpe_theme_get_unit_notice_meta_query( $unit_id, $include_general = false ) {
	$unit_id = absint( $unit_id );

	if ( $unit_id < 1 ) {
		return array();
	}

	$meta_query = array(
		'relation' => 'OR',
	);

	if ( $include_general ) {
		$meta_query[] = array(
			'key'     => 'ru_related_units',
			'compare' => 'NOT EXISTS',
		);
	}

	$meta_query[] = array(
		'key'     => 'ru_related_units',
		'value'   => 'i:' . $unit_id . ';',
		'compare' => 'LIKE',
	);
	$meta_query[] = array(
		'key'     => 'ru_related_units',
		'value'   => ':"' . $unit_id . '";',
		'compare' => 'LIKE',
	);

	return $meta_query;
}

function ru_ufpe_theme_get_general_or_related_notices_query( $unit_id = 0, $posts_per_page = 3 ) {
	$args = array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => $posts_per_page,
	);

	if ( $unit_id > 0 ) {
		$args['meta_query'] = ru_ufpe_theme_get_unit_notice_meta_query( $unit_id, true );
	}

	return new WP_Query( $args );
}

function ru_ufpe_theme_get_unit_notices_query( $unit_id, $posts_per_page = -1 ) {
	return new WP_Query(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => $posts_per_page,
			'meta_query'     => ru_ufpe_theme_get_unit_notice_meta_query( $unit_id, false ),
		)
	);
}

function ru_ufpe_theme_get_unit_page_template( $page_type ) {
	$template_map = array(
		'overview' => 'page-unit-overview.php',
		'cardapio' => 'page-unit-cardapio.php',
		'avisos'   => 'page-unit-avisos.php',
	);

	if ( ! isset( $template_map[ $page_type ] ) ) {
		return '';
	}

	return locate_template( $template_map[ $page_type ] );
}

function ru_ufpe_theme_template_include( $template ) {
	if ( ! is_page() ) {
		return $template;
	}

	$page_type = get_post_meta( get_queried_object_id(), 'ru_unit_page_type', true );

	if ( ! $page_type ) {
		return $template;
	}

	$unit_template = ru_ufpe_theme_get_unit_page_template( $page_type );

	return $unit_template ? $unit_template : $template;
}
add_filter( 'template_include', 'ru_ufpe_theme_template_include' );

function ru_ufpe_theme_show_autogenerated_page_notice() {
	if ( ! is_admin() ) {
		return;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	$post_id = isset( $_GET['post'] ) ? absint( wp_unslash( $_GET['post'] ) ) : 0;

	if ( ! $screen || 'page' !== $screen->id || ! $post_id || ! get_post_meta( $post_id, 'ru_autogenerated_page', true ) ) {
		return;
	}

	$unit_id = absint( get_post_meta( $post_id, 'ru_linked_unit_id', true ) );
	$edit_url = $unit_id ? get_edit_post_link( $unit_id ) : '';

	echo '<div class="notice notice-warning"><p>Esta pagina e gerada automaticamente a partir de uma unidade.';

	if ( $edit_url ) {
		echo ' Edite o conteudo em <a href="' . esc_url( $edit_url ) . '">Unidades</a>.';
	}

	echo '</p></div>';
}
add_action( 'admin_notices', 'ru_ufpe_theme_show_autogenerated_page_notice' );
