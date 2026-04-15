<?php

function ru_ufpe_theme_get_weekly_menu_day_labels() {
	return array(
		'segunda' => 'Segunda-feira',
		'terca'   => 'Terça-feira',
		'quarta'  => 'Quarta-feira',
		'quinta'  => 'Quinta-feira',
		'sexta'   => 'Sexta-feira',
		'sabado'  => 'Sábado',
		'domingo' => 'Domingo',
	);
}

function ru_ufpe_theme_get_weekly_menu_meal_labels() {
	return array(
		'desjejum' => 'Desjejum',
		'almoco'   => 'Almoço',
		'jantar'   => 'Jantar',
	);
}

function ru_ufpe_theme_get_weekly_menu_section_labels() {
	return array(
		'entrada'         => 'Entrada',
		'prato_principal' => 'Prato principal',
		'vegetariano'     => 'Opção vegetariana',
		'guarnicao'       => 'Guarnição',
		'acompanhamento'  => 'Acompanhamento',
		'sobremesa'       => 'Sobremesa',
		'bebida'          => 'Bebida',
		'complemento'     => 'Complemento',
	);
}

function ru_ufpe_theme_get_weekly_menu_allergen_options() {
	return array(
		'gluten'        => 'Glúten',
		'trigo'         => 'Trigo',
		'lactose'       => 'Lactose',
		'leite'         => 'Leite',
		'ovo'           => 'Ovo',
		'soja'          => 'Soja',
		'amendoim'      => 'Amendoim',
		'castanhas'     => 'Castanhas e nozes',
		'peixe'         => 'Peixe',
		'frutos_do_mar' => 'Crustáceos e frutos do mar',
	);
}

function ru_ufpe_theme_get_weekly_menu_dietary_options() {
	return array(
		'vegano' => 'Vegano',
	);
}

function ru_ufpe_theme_get_weekly_menu_section_suggestions() {
	return array(
		'entrada'         => array(
			'Salada de alface',
			'Salada de tomate',
			'Salada de cenoura',
			'Salada de beterraba',
			'Salada de repolho',
			'Salada mista',
			'Salada de pepino',
			'Salada de chuchu',
			'Salada de couve',
			'Vinagrete',
			'Sopa de legumes',
			'Creme de abóbora',
		),
		'prato_principal' => array(
			'Frango assado',
			'Frango grelhado',
			'Isca de frango',
			'Frango ao molho',
			'Strogonoff de frango',
			'Bife acebolado',
			'Bife grelhado',
			'Carne moída',
			'Carne assada',
			'Carne de sol',
			'Guisado de carne',
			'Costela',
			'Filé de peixe',
			'Peixe frito',
		),
		'vegetariano'     => array(
			'PVT ao molho',
			'Soja refogada',
			'Omelete',
			'Ovo cozido',
			'Quibe de soja',
			'Hambúrguer de lentilha',
			'Escondidinho de soja',
			'Feijoada vegetariana',
			'Lasanha de legumes',
			'Bolinho de grão-de-bico',
			'Estrogonofe de soja',
			'Tofu grelhado',
		),
		'guarnicao'       => array(
			'Batata frita',
			'Purê de batata',
			'Batata doce',
			'Macaxeira cozida',
			'Macarrão',
			'Farofa',
			'Pirão',
			'Inhame',
			'Cuscuz',
		),
		'acompanhamento'  => array(
			'Arroz branco',
			'Arroz integral',
			'Feijão carioca',
			'Feijão preto',
			'Feijão macassar',
		),
		'sobremesa'       => array(
			'Banana',
			'Laranja',
			'Maçã',
			'Melancia',
			'Mamão',
			'Goiaba',
			'Manga',
			'Gelatina',
			'Mousse',
			'Pudim',
			'Doce de leite',
			'Bolo',
		),
		'bebida'          => array(
			'Suco de acerola',
			'Suco de caju',
			'Suco de manga',
			'Suco de goiaba',
			'Suco de maracujá',
			'Limonada',
			'Refresco',
		),
		'complemento'     => array(
			'Molho de pimenta',
			'Azeite',
			'Vinagre',
			'Limão',
			'Mostarda',
			'Catchup',
		),
	);
}

function ru_ufpe_theme_get_weekly_menu_flag_aliases() {
	return array(
		'frutos do mar'      => 'frutos_do_mar',
		'crustaceos'         => 'frutos_do_mar',
		'castanhas e nozes'  => 'castanhas',
		'nozes'              => 'castanhas',
		'gluten e lactose'   => 'gluten_e_lactose',
	);
}

function ru_ufpe_theme_normalize_menu_annotation_text( $text ) {
	$text = sanitize_text_field( (string) $text );
	$text = remove_accents( $text );
	$text = strtolower( $text );
	$text = preg_replace( '/\s+/', ' ', $text );

	return trim( (string) $text );
}

function ru_ufpe_theme_parse_menu_flag_list( $raw_value, $allowed_options = null ) {
	if ( is_array( $raw_value ) ) {
		$items = $raw_value;
	} else {
		$items = preg_split( '/,/', (string) $raw_value );
	}

	$normalized = array();
	$allowed    = is_array( $allowed_options ) ? array_keys( $allowed_options ) : null;

	foreach ( $items as $item ) {
		$item = sanitize_key( (string) $item );

		if ( '' === $item ) {
			continue;
		}

		$aliases = ru_ufpe_theme_get_weekly_menu_flag_aliases();

		if ( isset( $aliases[ str_replace( '_', ' ', $item ) ] ) ) {
			$item = $aliases[ str_replace( '_', ' ', $item ) ];
		}

		if ( is_array( $allowed ) && ! in_array( $item, $allowed, true ) ) {
			continue;
		}

		if ( ! in_array( $item, $normalized, true ) ) {
			$normalized[] = $item;
		}
	}

	return $normalized;
}

function ru_ufpe_theme_extract_inline_menu_item_flags( $name ) {
	$name            = sanitize_text_field( (string) $name );
	$allergens       = array();
	$dietary         = array();
	$traces          = array();
	$recognized_note = false;

	$clean_name = preg_replace_callback(
		'/\(([^)]*)\)/u',
		static function( $matches ) use ( &$allergens, &$dietary, &$traces, &$recognized_note ) {
			$note           = isset( $matches[1] ) ? $matches[1] : '';
			$normalized     = ru_ufpe_theme_normalize_menu_annotation_text( $note );
			$found_in_note  = array(
				'allergens' => array(),
				'dietary'   => array(),
				'traces'    => array(),
			);

			$is_traces = (bool) preg_match( '/^(tracos|pode conter|pode conter tracos)/', $normalized );

			if ( false !== strpos( $normalized, 'vegano' ) ) {
				$found_in_note['dietary'][] = 'vegano';
			}

			if ( false !== strpos( $normalized, 'frutos do mar' ) || false !== strpos( $normalized, 'crustaceos' ) ) {
				$found_in_note[ $is_traces ? 'traces' : 'allergens' ][] = 'frutos_do_mar';
			}

			if ( false !== strpos( $normalized, 'castanhas' ) || false !== strpos( $normalized, 'nozes' ) ) {
				$found_in_note[ $is_traces ? 'traces' : 'allergens' ][] = 'castanhas';
			}

			foreach ( array( 'gluten', 'trigo', 'lactose', 'leite', 'ovo', 'soja', 'amendoim', 'peixe' ) as $allergen_key ) {
				if ( false === strpos( $normalized, $allergen_key ) ) {
					continue;
				}

				$found_in_note[ $is_traces ? 'traces' : 'allergens' ][] = $allergen_key;
			}

			$found_in_note['allergens'] = array_values( array_unique( $found_in_note['allergens'] ) );
			$found_in_note['dietary']   = array_values( array_unique( $found_in_note['dietary'] ) );
			$found_in_note['traces']    = array_values( array_unique( $found_in_note['traces'] ) );

			if ( ! empty( $found_in_note['allergens'] ) || ! empty( $found_in_note['dietary'] ) || ! empty( $found_in_note['traces'] ) ) {
				$recognized_note = true;
				$allergens       = array_values( array_unique( array_merge( $allergens, $found_in_note['allergens'] ) ) );
				$dietary         = array_values( array_unique( array_merge( $dietary, $found_in_note['dietary'] ) ) );
				$traces          = array_values( array_unique( array_merge( $traces, $found_in_note['traces'] ) ) );

				return '';
			}

			return $matches[0];
		},
		$name
	);

	$clean_name = trim( preg_replace( '/\s+/', ' ', (string) $clean_name ) );

	return array(
		'name'            => $clean_name ? $clean_name : $name,
		'allergens'       => $allergens,
		'dietary'         => $dietary,
		'traces'          => $traces,
		'recognized_note' => $recognized_note,
	);
}

function ru_ufpe_theme_get_default_enabled_meals() {
	return array_keys( ru_ufpe_theme_get_weekly_menu_meal_labels() );
}

function ru_ufpe_theme_get_default_meal_times() {
	return array(
		'desjejum' => '06:30',
		'almoco'   => '11:00',
		'jantar'   => '17:00',
	);
}

function ru_ufpe_theme_normalize_meal_time( $value ) {
	$value = sanitize_text_field( (string) $value );

	if ( preg_match( '/^(2[0-3]|[01]?[0-9]):([0-5][0-9])$/', $value, $matches ) ) {
		return sprintf( '%02d:%02d', (int) $matches[1], (int) $matches[2] );
	}

	return '';
}

function ru_ufpe_theme_get_unit_meal_times( $unit_id ) {
	$defaults = ru_ufpe_theme_get_default_meal_times();
	$times    = array();

	foreach ( $defaults as $meal_key => $default_time ) {
		$saved_time = ru_ufpe_theme_normalize_meal_time( get_post_meta( $unit_id, 'ru_meal_time_' . $meal_key, true ) );
		$times[ $meal_key ] = $saved_time ? $saved_time : $default_time;
	}

	return $times;
}

function ru_ufpe_theme_get_next_unit_meal( $unit_id ) {
	$enabled_meals = ru_ufpe_theme_get_unit_enabled_meals( $unit_id );

	if ( empty( $enabled_meals ) ) {
		return null;
	}

	$meal_labels = ru_ufpe_theme_get_weekly_menu_meal_labels();
	$meal_times  = ru_ufpe_theme_get_unit_meal_times( $unit_id );
	$timezone    = wp_timezone();
	$now         = new DateTimeImmutable( 'now', $timezone );
	$service_days = array( 1, 2, 3, 4, 5, 6, 7 );

	for ( $day_offset = 0; $day_offset < 8; $day_offset++ ) {
		$candidate_day = $now->setTime( 0, 0 )->modify( '+' . $day_offset . ' day' );
		$weekday       = (int) $candidate_day->format( 'N' );

		if ( ! in_array( $weekday, $service_days, true ) ) {
			continue;
		}

		foreach ( $meal_labels as $meal_key => $meal_label ) {
			if ( ! in_array( $meal_key, $enabled_meals, true ) ) {
				continue;
			}

			$meal_time = isset( $meal_times[ $meal_key ] ) ? $meal_times[ $meal_key ] : '';

			if ( ! $meal_time ) {
				continue;
			}

			list( $hours, $minutes ) = array_map( 'intval', explode( ':', $meal_time ) );
			$meal_datetime = $candidate_day->setTime( $hours, $minutes );

			if ( 0 === $day_offset && $meal_datetime <= $now ) {
				continue;
			}

			return array(
				'meal_key'      => $meal_key,
				'meal_label'    => $meal_label,
				'time'          => $meal_time,
				'datetime'      => $meal_datetime,
				'is_today'      => 0 === $day_offset,
				'day_label'     => wp_date( 'l', $meal_datetime->getTimestamp(), $timezone ),
				'display_label' => 0 === $day_offset ? 'Hoje' : wp_date( 'l', $meal_datetime->getTimestamp(), $timezone ),
			);
		}
	}

	return null;
}

function ru_ufpe_theme_get_weekly_menu_day_key_from_date( $date ) {
	$date = $date instanceof DateTimeInterface ? $date : null;

	if ( ! $date ) {
		return '';
	}

	$map = array(
		1 => 'segunda',
		2 => 'terca',
		3 => 'quarta',
		4 => 'quinta',
		5 => 'sexta',
		6 => 'sabado',
		7 => 'domingo',
	);

	$weekday = (int) $date->format( 'N' );

	return isset( $map[ $weekday ] ) ? $map[ $weekday ] : '';
}

function ru_ufpe_theme_get_weekly_menu_meal_sections( $weekly_menu, $date, $meal_key ) {
	if ( ! is_array( $weekly_menu ) || empty( $meal_key ) ) {
		return array();
	}

	$day_key        = ru_ufpe_theme_get_weekly_menu_day_key_from_date( $date );
	$section_labels = ru_ufpe_theme_get_weekly_menu_section_labels();
	$day_menu       = $day_key && isset( $weekly_menu['menu']['days'][ $day_key ] ) ? $weekly_menu['menu']['days'][ $day_key ] : array();
	$meal_menu      = isset( $day_menu[ $meal_key ] ) && is_array( $day_menu[ $meal_key ] ) ? $day_menu[ $meal_key ] : array();
	$sections       = array();

	foreach ( $section_labels as $section_key => $section_label ) {
		$items = isset( $meal_menu[ $section_key ] ) && is_array( $meal_menu[ $section_key ] ) ? $meal_menu[ $section_key ] : array();

		if ( empty( $items ) ) {
			continue;
		}

		$sections[] = array(
			'key'   => $section_key,
			'label' => $section_label,
			'items' => $items,
		);
	}

	return $sections;
}

function ru_ufpe_theme_get_next_unit_meal_summary( $unit_id ) {
	$next_meal = ru_ufpe_theme_get_next_unit_meal( $unit_id );

	if ( ! $next_meal || empty( $next_meal['datetime'] ) || ! $next_meal['datetime'] instanceof DateTimeInterface ) {
		return null;
	}

	$reference_date = $next_meal['datetime']->format( 'Y-m-d' );
	$weekly_menu    = ru_ufpe_theme_get_weekly_menu_data_for_unit( $unit_id, $reference_date );
	$sections       = $weekly_menu ? ru_ufpe_theme_get_weekly_menu_meal_sections( $weekly_menu, $next_meal['datetime'], $next_meal['meal_key'] ) : array();

	return array(
		'meal_key'      => $next_meal['meal_key'],
		'meal_label'    => $next_meal['meal_label'],
		'time'          => $next_meal['time'],
		'datetime'      => $next_meal['datetime'],
		'date'          => $reference_date,
		'is_today'      => $next_meal['is_today'],
		'day_label'     => $next_meal['day_label'],
		'display_label' => $next_meal['display_label'],
		'has_menu'      => ! empty( $sections ),
		'sections'      => $sections,
		'weekly_menu'   => $weekly_menu,
	);
}

function ru_ufpe_theme_normalize_enabled_meals( $meals ) {
	$normalized = array();
	$allowed    = ru_ufpe_theme_get_weekly_menu_meal_labels();
	$meals      = is_array( $meals ) ? $meals : array();

	foreach ( array_keys( $allowed ) as $meal_key ) {
		if ( in_array( $meal_key, $meals, true ) ) {
			$normalized[] = $meal_key;
		}
	}

	return $normalized;
}

function ru_ufpe_theme_sanitize_enabled_meals_meta( $value ) {
	$value = is_array( $value ) ? array_map( 'sanitize_key', $value ) : array();

	return ru_ufpe_theme_normalize_enabled_meals( $value );
}

function ru_ufpe_theme_get_unit_default_enabled_meals( $unit_post = null ) {
	$defaults = ru_ufpe_theme_get_default_enabled_meals();

	if ( ! $unit_post instanceof WP_Post ) {
		return $defaults;
	}

	$slug  = sanitize_title( $unit_post->post_name ? $unit_post->post_name : $unit_post->post_title );
	$title = sanitize_title( $unit_post->post_title );

	if ( false !== strpos( $slug, 'caruaru' ) || false !== strpos( $title, 'caruaru' ) ) {
		return array( 'almoco', 'jantar' );
	}

	return $defaults;
}

function ru_ufpe_theme_get_unit_enabled_meals( $unit_id ) {
	$unit_post = get_post( $unit_id );
	$saved     = get_post_meta( $unit_id, 'ru_enabled_meals', true );
	$meals     = ru_ufpe_theme_normalize_enabled_meals( is_array( $saved ) ? $saved : array() );

	if ( ! empty( $meals ) ) {
		return $meals;
	}

	return ru_ufpe_theme_get_unit_default_enabled_meals( $unit_post );
}

function ru_ufpe_theme_get_empty_weekly_menu_section_payload() {
	$payload = array();

	foreach ( ru_ufpe_theme_get_weekly_menu_section_labels() as $section_key => $section_label ) {
		$payload[ $section_key ] = array();
	}

	return $payload;
}

function ru_ufpe_theme_get_empty_weekly_menu_days( $enabled_meals ) {
	$days = array();

	foreach ( ru_ufpe_theme_get_weekly_menu_day_labels() as $day_key => $day_label ) {
		$days[ $day_key ] = array();

		foreach ( $enabled_meals as $meal_key ) {
			$days[ $day_key ][ $meal_key ] = ru_ufpe_theme_get_empty_weekly_menu_section_payload();
		}
	}

	return $days;
}

function ru_ufpe_theme_normalize_weekly_menu_item( $item ) {
	$item = is_array( $item ) ? $item : array();
	$name = isset( $item['name'] ) ? sanitize_text_field( (string) $item['name'] ) : '';

	if ( '' === $name ) {
		return null;
	}

	$inline_flags = ru_ufpe_theme_extract_inline_menu_item_flags( $name );
	$allergens    = ru_ufpe_theme_parse_menu_flag_list( isset( $item['allergens'] ) ? $item['allergens'] : array(), ru_ufpe_theme_get_weekly_menu_allergen_options() );
	$dietary      = ru_ufpe_theme_parse_menu_flag_list( isset( $item['dietary'] ) ? $item['dietary'] : array(), ru_ufpe_theme_get_weekly_menu_dietary_options() );
	$traces       = ru_ufpe_theme_parse_menu_flag_list( isset( $item['traces'] ) ? $item['traces'] : array(), ru_ufpe_theme_get_weekly_menu_allergen_options() );
	$allergens    = array_values( array_unique( array_merge( $allergens, $inline_flags['allergens'] ) ) );
	$dietary      = array_values( array_unique( array_merge( $dietary, $inline_flags['dietary'] ) ) );
	$traces       = array_values( array_unique( array_merge( $traces, $inline_flags['traces'] ) ) );

	return array(
		'name'      => $inline_flags['name'],
		'allergens' => $allergens,
		'dietary'   => $dietary,
		'traces'    => $traces,
	);
}

function ru_ufpe_theme_normalize_weekly_menu_json( $menu, $enabled_meals = null ) {
	$enabled_meals = null === $enabled_meals ? ru_ufpe_theme_get_default_enabled_meals() : ru_ufpe_theme_normalize_enabled_meals( $enabled_meals );
	$normalized    = array(
		'enabled_meals' => $enabled_meals,
		'days'          => ru_ufpe_theme_get_empty_weekly_menu_days( $enabled_meals ),
	);
	$menu          = is_array( $menu ) ? $menu : array();
	$menu_days     = isset( $menu['days'] ) && is_array( $menu['days'] ) ? $menu['days'] : $menu;

	foreach ( ru_ufpe_theme_get_weekly_menu_day_labels() as $day_key => $day_label ) {
		$raw_day = isset( $menu_days[ $day_key ] ) && is_array( $menu_days[ $day_key ] ) ? $menu_days[ $day_key ] : array();

		foreach ( $enabled_meals as $meal_key ) {
			$raw_meal = isset( $raw_day[ $meal_key ] ) && is_array( $raw_day[ $meal_key ] ) ? $raw_day[ $meal_key ] : array();

			foreach ( ru_ufpe_theme_get_weekly_menu_section_labels() as $section_key => $section_label ) {
				$raw_items = isset( $raw_meal[ $section_key ] ) && is_array( $raw_meal[ $section_key ] ) ? $raw_meal[ $section_key ] : array();
				$items     = array();

				foreach ( $raw_items as $raw_item ) {
					$normalized_item = ru_ufpe_theme_normalize_weekly_menu_item( $raw_item );

					if ( null !== $normalized_item ) {
						$items[] = $normalized_item;
					}
				}

				$normalized['days'][ $day_key ][ $meal_key ][ $section_key ] = $items;
			}
		}
	}

	return $normalized;
}

function ru_ufpe_theme_decode_weekly_menu_json( $menu_json, $enabled_meals = null ) {
	$decoded = json_decode( (string) $menu_json, true );

	if ( ! is_array( $decoded ) ) {
		$decoded = array();
	}

	if ( isset( $decoded['enabled_meals'] ) && is_array( $decoded['enabled_meals'] ) ) {
		$enabled_meals = $decoded['enabled_meals'];
	}

	return ru_ufpe_theme_normalize_weekly_menu_json( $decoded, $enabled_meals );
}

function ru_ufpe_theme_register_cardapio_semanal_post_type() {
	$labels = array(
		'name'               => 'Cardápios semanais',
		'singular_name'      => 'Cardápio semanal',
		'menu_name'          => 'Cardápios semanais',
		'name_admin_bar'     => 'Cardápio semanal',
		'add_new'            => 'Adicionar',
		'add_new_item'       => 'Adicionar cardápio semanal',
		'new_item'           => 'Novo cardápio semanal',
		'edit_item'          => 'Editar cardápio semanal',
		'view_item'          => 'Ver cardápio semanal',
		'all_items'          => 'Todos os cardápios semanais',
		'search_items'       => 'Buscar cardápios semanais',
		'not_found'          => 'Nenhum cardápio semanal encontrado',
		'not_found_in_trash' => 'Nenhum cardápio semanal encontrado na lixeira',
	);

	register_post_type(
		'cardapio_semanal',
		array(
			'labels'              => $labels,
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => 'edit.php?post_type=unidade',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => false,
			'exclude_from_search' => true,
			'menu_icon'           => 'dashicons-calendar-alt',
			'supports'            => array( 'title' ),
		)
	);
}
add_action( 'init', 'ru_ufpe_theme_register_cardapio_semanal_post_type' );

function ru_ufpe_theme_register_cardapio_semanal_meta() {
	register_post_meta(
		'cardapio_semanal',
		'ru_unit_id',
		array(
			'show_in_rest'      => false,
			'single'            => true,
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'auth_callback'     => '__return_true',
		)
	);

	register_post_meta(
		'cardapio_semanal',
		'ru_week_start_date',
		array(
			'show_in_rest'      => false,
			'single'            => true,
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => '__return_true',
		)
	);

	register_post_meta(
		'cardapio_semanal',
		'ru_week_end_date',
		array(
			'show_in_rest'      => false,
			'single'            => true,
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => '__return_true',
		)
	);

	register_post_meta(
		'cardapio_semanal',
		'menu_json',
		array(
			'show_in_rest'      => false,
			'single'            => true,
			'type'              => 'string',
			'sanitize_callback' => 'wp_kses_no_null',
			'auth_callback'     => '__return_true',
		)
	);
}
add_action( 'init', 'ru_ufpe_theme_register_cardapio_semanal_meta' );

function ru_ufpe_theme_get_weekly_menu_admin_units() {
	return get_posts(
		array(
			'post_type'      => 'unidade',
			'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
		)
	);
}

function ru_ufpe_theme_get_weekly_menu_admin_context( $post ) {
	$units         = ru_ufpe_theme_get_weekly_menu_admin_units();
	$selected_unit = absint( get_post_meta( $post->ID, 'ru_unit_id', true ) );
	$week_start    = get_post_meta( $post->ID, 'ru_week_start_date', true );
	$stored_json   = get_post_meta( $post->ID, 'menu_json', true );

	if ( ! $selected_unit && ! empty( $units ) ) {
		$selected_unit = (int) $units[0]->ID;
	}

	$enabled_meals = array();

	if ( $stored_json ) {
		$decoded       = ru_ufpe_theme_decode_weekly_menu_json( $stored_json );
		$enabled_meals = $decoded['enabled_meals'];
		$menu          = $decoded;
	} else {
		$enabled_meals = $selected_unit ? ru_ufpe_theme_get_unit_enabled_meals( $selected_unit ) : ru_ufpe_theme_get_default_enabled_meals();
		$menu          = ru_ufpe_theme_normalize_weekly_menu_json( array(), $enabled_meals );
	}

	return array(
		'units'              => $units,
		'selected_unit'      => $selected_unit,
		'week_start'         => $week_start,
		'enabled_meals'      => $enabled_meals,
		'menu'               => $menu,
		'prefill_from_unit'  => empty( $stored_json ),
	);
}

function ru_ufpe_theme_render_weekly_menu_items( $day_key, $meal_key, $section_key, $items ) {
	$items = is_array( $items ) ? $items : array();

	echo '<div class="ru-weekly-menu-items" data-items-container>';

	foreach ( $items as $index => $item ) {
		ru_ufpe_theme_render_weekly_menu_item_row( $day_key, $meal_key, $section_key, $index, $item );
	}

	echo '</div>';
	echo '<button type="button" class="button button-secondary ru-add-weekly-menu-item">Adicionar item</button>';
	echo '<template class="ru-weekly-menu-item-template">';
	ru_ufpe_theme_render_weekly_menu_item_row(
		$day_key,
		$meal_key,
		$section_key,
		'__INDEX__',
		array(
			'name'      => '',
			'allergens' => array(),
			'dietary'   => array(),
			'traces'    => array(),
		)
	);
	echo '</template>';
}

function ru_ufpe_theme_render_weekly_menu_item_row( $day_key, $meal_key, $section_key, $index, $item ) {
	$name      = isset( $item['name'] ) ? $item['name'] : '';
	$allergens = isset( $item['allergens'] ) && is_array( $item['allergens'] ) ? $item['allergens'] : array();
	$dietary   = isset( $item['dietary'] ) && is_array( $item['dietary'] ) ? $item['dietary'] : array();
	$traces    = isset( $item['traces'] ) && is_array( $item['traces'] ) ? $item['traces'] : array();
	$allergen_options = ru_ufpe_theme_get_weekly_menu_allergen_options();
	$dietary_options  = ru_ufpe_theme_get_weekly_menu_dietary_options();

	echo '<div class="ru-weekly-menu-item-row" data-item-row>';
	echo '<label><span>Item</span><input type="text" name="ru_menu[' . esc_attr( $day_key ) . '][' . esc_attr( $meal_key ) . '][' . esc_attr( $section_key ) . '][' . esc_attr( $index ) . '][name]" value="' . esc_attr( $name ) . '" list="ru-datalist-' . esc_attr( $section_key ) . '" /></label>';
	echo '<fieldset class="ru-weekly-menu-flag-group"><legend>Alérgenos (contém)</legend><div class="ru-weekly-menu-flag-options">';
	foreach ( $allergen_options as $option_value => $option_label ) {
		echo '<label><input type="checkbox" name="ru_menu[' . esc_attr( $day_key ) . '][' . esc_attr( $meal_key ) . '][' . esc_attr( $section_key ) . '][' . esc_attr( $index ) . '][allergens][]" value="' . esc_attr( $option_value ) . '" ' . checked( in_array( $option_value, $allergens, true ), true, false ) . '> ' . esc_html( $option_label ) . '</label>';
	}
	echo '</div></fieldset>';
	echo '<fieldset class="ru-weekly-menu-flag-group"><legend>Pode conter traços de</legend><div class="ru-weekly-menu-flag-options">';
	foreach ( $allergen_options as $option_value => $option_label ) {
		echo '<label><input type="checkbox" name="ru_menu[' . esc_attr( $day_key ) . '][' . esc_attr( $meal_key ) . '][' . esc_attr( $section_key ) . '][' . esc_attr( $index ) . '][traces][]" value="' . esc_attr( $option_value ) . '" ' . checked( in_array( $option_value, $traces, true ), true, false ) . '> ' . esc_html( $option_label ) . '</label>';
	}
	echo '</div></fieldset>';
	$dietary_value = ! empty( $dietary ) ? $dietary[0] : '';
	echo '<label class="ru-weekly-menu-flag-group"><span>Dietética</span><select name="ru_menu[' . esc_attr( $day_key ) . '][' . esc_attr( $meal_key ) . '][' . esc_attr( $section_key ) . '][' . esc_attr( $index ) . '][dietary]">';
	echo '<option value="">Nenhum</option>';
	foreach ( $dietary_options as $option_value => $option_label ) {
		echo '<option value="' . esc_attr( $option_value ) . '" ' . selected( $dietary_value, $option_value, false ) . '>' . esc_html( $option_label ) . '</option>';
	}
	echo '</select></label>';
	echo '<button type="button" class="button-link-delete ru-remove-weekly-menu-item">Remover</button>';
	echo '</div>';
}

function ru_ufpe_theme_render_cardapio_semanal_meta_box( $post ) {
	wp_nonce_field( 'ru_save_cardapio_semanal', 'ru_cardapio_semanal_nonce' );

	$context       = ru_ufpe_theme_get_weekly_menu_admin_context( $post );
	$meal_labels   = ru_ufpe_theme_get_weekly_menu_meal_labels();
	$section_labels = ru_ufpe_theme_get_weekly_menu_section_labels();
	$day_labels    = ru_ufpe_theme_get_weekly_menu_day_labels();

	echo '<div class="ru-weekly-menu-admin" data-prefill-from-unit="' . esc_attr( $context['prefill_from_unit'] ? '1' : '0' ) . '">';
	echo '<div class="ru-weekly-menu-toolbar">';
	echo '<div class="ru-weekly-menu-toolbar-grid">';
	echo '<label><span><strong>Unidade</strong></span><select name="ru_unit_id" id="ru_unit_id">';
	echo '<option value="0">Selecione uma unidade</option>';

	foreach ( $context['units'] as $unit ) {
		$unit_meals = ru_ufpe_theme_get_unit_enabled_meals( $unit->ID );
		echo '<option value="' . esc_attr( $unit->ID ) . '" data-enabled-meals="' . esc_attr( wp_json_encode( $unit_meals ) ) . '" ' . selected( $context['selected_unit'], $unit->ID, false ) . '>' . esc_html( get_the_title( $unit ) ) . '</option>';
	}

	echo '</select></label>';
	echo '<label><span><strong>Segunda-feira da semana</strong></span><input type="date" name="ru_week_start_date" value="' . esc_attr( $context['week_start'] ) . '" /></label>';
	echo '</div>';
	echo '<fieldset class="ru-weekly-menu-meal-selector"><legend><strong>Refeições habilitadas nesta semana</strong></legend><div class="ru-weekly-menu-checks">';

	foreach ( $meal_labels as $meal_key => $meal_label ) {
		echo '<label><input type="checkbox" name="ru_enabled_meals[]" value="' . esc_attr( $meal_key ) . '" ' . checked( in_array( $meal_key, $context['enabled_meals'], true ), true, false ) . ' data-meal-toggle="' . esc_attr( $meal_key ) . '"> ' . esc_html( $meal_label ) . '</label>';
	}

	echo '</div></fieldset>';
	echo '<p class="description">Alérgenos e traços podem ser informados nas anotações entre parênteses no nome do item.</p>';
	echo '</div>';

	$section_suggestions = ru_ufpe_theme_get_weekly_menu_section_suggestions();
	foreach ( $section_labels as $sk => $sl ) {
		$suggestions = isset( $section_suggestions[ $sk ] ) ? $section_suggestions[ $sk ] : array();
		echo '<datalist id="ru-datalist-' . esc_attr( $sk ) . '">';
		foreach ( $suggestions as $suggestion ) {
			echo '<option value="' . esc_attr( $suggestion ) . '">';
		}
		echo '</datalist>';
	}

	foreach ( $day_labels as $day_key => $day_label ) {
		echo '<section class="ru-weekly-menu-day-card">';
		echo '<h2>' . esc_html( $day_label ) . '</h2>';

		foreach ( $meal_labels as $meal_key => $meal_label ) {
			$is_enabled = in_array( $meal_key, $context['enabled_meals'], true );
			echo '<div class="ru-weekly-menu-meal-card' . ( $is_enabled ? '' : ' is-hidden' ) . '" data-meal-panel="' . esc_attr( $meal_key ) . '">';
			echo '<div class="ru-weekly-menu-meal-header"><h3>' . esc_html( $meal_label ) . '</h3></div>';

			foreach ( $section_labels as $section_key => $section_label ) {
				$items = isset( $context['menu']['days'][ $day_key ][ $meal_key ][ $section_key ] ) ? $context['menu']['days'][ $day_key ][ $meal_key ][ $section_key ] : array();
				echo '<div class="ru-weekly-menu-section">';
				echo '<h4>' . esc_html( $section_label ) . '</h4>';
				ru_ufpe_theme_render_weekly_menu_items( $day_key, $meal_key, $section_key, $items );
				echo '</div>';
			}

			echo '</div>';
		}

		echo '</section>';
	}

	echo '</div>';
}

function ru_ufpe_theme_add_weekly_menu_meta_boxes() {
	add_meta_box(
		'ru-cardapio-semanal',
		'Estrutura do cardápio semanal',
		'ru_ufpe_theme_render_cardapio_semanal_meta_box',
		'cardapio_semanal',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'ru_ufpe_theme_add_weekly_menu_meta_boxes' );

function ru_ufpe_theme_enqueue_weekly_menu_admin_assets( $hook_suffix ) {
	if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	if ( ! $screen || 'cardapio_semanal' !== $screen->post_type ) {
		return;
	}

	$css_path = get_template_directory() . '/assets/admin/cardapio-semanal.css';
	$js_path  = get_template_directory() . '/assets/admin/cardapio-semanal.js';

	wp_enqueue_style(
		'ru-cardapio-semanal-admin',
		get_template_directory_uri() . '/assets/admin/cardapio-semanal.css',
		array(),
		file_exists( $css_path ) ? (string) filemtime( $css_path ) : null
	);
	wp_enqueue_script(
		'ru-cardapio-semanal-admin',
		get_template_directory_uri() . '/assets/admin/cardapio-semanal.js',
		array(),
		file_exists( $js_path ) ? (string) filemtime( $js_path ) : null,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'ru_ufpe_theme_enqueue_weekly_menu_admin_assets' );

function ru_ufpe_theme_get_weekly_menu_notice_key() {
	return 'ru_weekly_menu_notice_' . get_current_user_id();
}

function ru_ufpe_theme_set_weekly_menu_notice( $message, $type = 'error' ) {
	set_transient(
		ru_ufpe_theme_get_weekly_menu_notice_key(),
		array(
			'message' => $message,
			'type'    => $type,
		),
		MINUTE_IN_SECONDS
	);
}

function ru_ufpe_theme_show_weekly_menu_notice() {
	$notice = get_transient( ru_ufpe_theme_get_weekly_menu_notice_key() );

	if ( ! is_array( $notice ) || empty( $notice['message'] ) ) {
		return;
	}

	delete_transient( ru_ufpe_theme_get_weekly_menu_notice_key() );
	echo '<div class="notice notice-' . esc_attr( isset( $notice['type'] ) ? $notice['type'] : 'info' ) . '"><p>' . esc_html( $notice['message'] ) . '</p></div>';
}
add_action( 'admin_notices', 'ru_ufpe_theme_show_weekly_menu_notice' );

function ru_ufpe_theme_is_valid_monday_date( $date_string ) {
	$date = DateTimeImmutable::createFromFormat( 'Y-m-d', (string) $date_string, wp_timezone() );

	if ( ! $date ) {
		return false;
	}

	return '1' === $date->format( 'N' );
}

function ru_ufpe_theme_get_week_end_date( $week_start_date ) {
	$date = DateTimeImmutable::createFromFormat( 'Y-m-d', (string) $week_start_date, wp_timezone() );

	if ( ! $date ) {
		return '';
	}

	return $date->modify( '+6 days' )->format( 'Y-m-d' );
}

function ru_ufpe_theme_get_weekly_menu_title( $unit_id, $week_start_date ) {
	$unit_title = $unit_id ? get_the_title( $unit_id ) : 'Unidade';
	$date       = DateTimeImmutable::createFromFormat( 'Y-m-d', (string) $week_start_date, wp_timezone() );
	$label      = $date ? $date->format( 'd/m/Y' ) : $week_start_date;

	return 'Cardápio semanal — ' . $unit_title . ' — ' . $label;
}

function ru_ufpe_theme_find_duplicate_weekly_menu( $post_id, $unit_id, $week_start_date ) {
	$posts = get_posts(
		array(
			'post_type'      => 'cardapio_semanal',
			'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
			'posts_per_page' => 1,
			'post__not_in'   => array( $post_id ),
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'key'   => 'ru_unit_id',
					'value' => $unit_id,
				),
				array(
					'key'   => 'ru_week_start_date',
					'value' => $week_start_date,
				),
			),
		)
	);

	return ! empty( $posts ) ? (int) $posts[0] : 0;
}

function ru_ufpe_theme_get_weekly_menu_requested_structure() {
	$enabled_meals = isset( $_POST['ru_enabled_meals'] ) ? ru_ufpe_theme_normalize_enabled_meals( array_map( 'sanitize_key', (array) wp_unslash( $_POST['ru_enabled_meals'] ) ) ) : array();
	$raw_menu      = isset( $_POST['ru_menu'] ) ? (array) wp_unslash( $_POST['ru_menu'] ) : array();

	return array(
		'enabled_meals' => $enabled_meals,
		'menu'          => ru_ufpe_theme_normalize_weekly_menu_json( $raw_menu, $enabled_meals ),
	);
}

function ru_ufpe_theme_force_weekly_menu_draft( $post_id ) {
	if ( 'draft' === get_post_status( $post_id ) ) {
		return;
	}

	remove_action( 'save_post_cardapio_semanal', 'ru_ufpe_theme_save_cardapio_semanal', 10 );
	wp_update_post(
		array(
			'ID'          => $post_id,
			'post_status' => 'draft',
		)
	);
	add_action( 'save_post_cardapio_semanal', 'ru_ufpe_theme_save_cardapio_semanal', 10, 3 );
}

function ru_ufpe_theme_save_cardapio_semanal( $post_id, $post, $update ) {
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}

	if ( ! isset( $_POST['ru_cardapio_semanal_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ru_cardapio_semanal_nonce'] ) ), 'ru_save_cardapio_semanal' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$unit_id         = isset( $_POST['ru_unit_id'] ) ? absint( wp_unslash( $_POST['ru_unit_id'] ) ) : 0;
	$week_start_date = isset( $_POST['ru_week_start_date'] ) ? sanitize_text_field( wp_unslash( $_POST['ru_week_start_date'] ) ) : '';
	$payload         = ru_ufpe_theme_get_weekly_menu_requested_structure();
	$enabled_meals   = $payload['enabled_meals'];
	$menu_array      = $payload['menu'];

	if ( $unit_id < 1 || ! get_post( $unit_id ) ) {
		ru_ufpe_theme_force_weekly_menu_draft( $post_id );
		ru_ufpe_theme_set_weekly_menu_notice( 'Selecione uma unidade válida para o cardápio semanal.' );
		return;
	}

	if ( ! ru_ufpe_theme_is_valid_monday_date( $week_start_date ) ) {
		ru_ufpe_theme_force_weekly_menu_draft( $post_id );
		ru_ufpe_theme_set_weekly_menu_notice( 'A data da semana deve ser uma segunda-feira válida.' );
		return;
	}

	if ( empty( $enabled_meals ) ) {
		ru_ufpe_theme_force_weekly_menu_draft( $post_id );
		ru_ufpe_theme_set_weekly_menu_notice( 'Selecione ao menos uma refeição habilitada para a semana.' );
		return;
	}

	if ( ru_ufpe_theme_find_duplicate_weekly_menu( $post_id, $unit_id, $week_start_date ) ) {
		ru_ufpe_theme_force_weekly_menu_draft( $post_id );
		ru_ufpe_theme_set_weekly_menu_notice( 'Já existe um cardápio semanal para esta unidade na semana informada.' );
		return;
	}

	$week_end_date = ru_ufpe_theme_get_week_end_date( $week_start_date );
	$menu_array    = ru_ufpe_theme_normalize_weekly_menu_json( $menu_array, $enabled_meals );
	$menu_json     = wp_json_encode( $menu_array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

	update_post_meta( $post_id, 'ru_unit_id', $unit_id );
	update_post_meta( $post_id, 'ru_week_start_date', $week_start_date );
	update_post_meta( $post_id, 'ru_week_end_date', $week_end_date );
	update_post_meta( $post_id, 'menu_json', $menu_json );

	$title = ru_ufpe_theme_get_weekly_menu_title( $unit_id, $week_start_date );

	if ( $title !== $post->post_title ) {
		remove_action( 'save_post_cardapio_semanal', 'ru_ufpe_theme_save_cardapio_semanal', 10 );
		wp_update_post(
			array(
				'ID'         => $post_id,
				'post_title' => $title,
			)
		);
		add_action( 'save_post_cardapio_semanal', 'ru_ufpe_theme_save_cardapio_semanal', 10, 3 );
	}
}
add_action( 'save_post_cardapio_semanal', 'ru_ufpe_theme_save_cardapio_semanal', 10, 3 );

function ru_ufpe_theme_get_weekly_menu_post_for_unit( $unit_id, $reference_date = '' ) {
	$unit_id        = absint( $unit_id );
	$reference_date = $reference_date ? $reference_date : current_time( 'Y-m-d' );

	if ( $unit_id < 1 ) {
		return null;
	}

	$current = get_posts(
		array(
			'post_type'      => 'cardapio_semanal',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'meta_key'       => 'ru_week_start_date',
			'orderby'        => 'meta_value',
			'order'          => 'DESC',
			'meta_type'      => 'DATE',
			'meta_query'     => array(
				array(
					'key'   => 'ru_unit_id',
					'value' => $unit_id,
				),
				array(
					'key'     => 'ru_week_start_date',
					'value'   => $reference_date,
					'compare' => '<=',
					'type'    => 'DATE',
				),
				array(
					'key'     => 'ru_week_end_date',
					'value'   => $reference_date,
					'compare' => '>=',
					'type'    => 'DATE',
				),
			),
		)
	);

	if ( ! empty( $current ) ) {
		return $current[0];
	}

	$latest = get_posts(
		array(
			'post_type'      => 'cardapio_semanal',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'meta_key'       => 'ru_week_start_date',
			'orderby'        => 'meta_value',
			'order'          => 'DESC',
			'meta_type'      => 'DATE',
			'meta_query'     => array(
				array(
					'key'   => 'ru_unit_id',
					'value' => $unit_id,
				),
			),
		)
	);

	return ! empty( $latest ) ? $latest[0] : null;
}

function ru_ufpe_theme_get_weekly_menu_data_for_unit( $unit_id, $reference_date = '' ) {
	$post = ru_ufpe_theme_get_weekly_menu_post_for_unit( $unit_id, $reference_date );

	if ( ! $post ) {
		return null;
	}

	$menu_json = get_post_meta( $post->ID, 'menu_json', true );
	$data      = ru_ufpe_theme_decode_weekly_menu_json( $menu_json );

	return array(
		'post'            => $post,
		'menu'            => $data,
		'week_start_date' => get_post_meta( $post->ID, 'ru_week_start_date', true ),
		'week_end_date'   => get_post_meta( $post->ID, 'ru_week_end_date', true ),
	);
}

function ru_ufpe_theme_format_weekly_menu_range( $week_start_date, $week_end_date ) {
	$start = DateTimeImmutable::createFromFormat( 'Y-m-d', (string) $week_start_date, wp_timezone() );
	$end   = DateTimeImmutable::createFromFormat( 'Y-m-d', (string) $week_end_date, wp_timezone() );

	if ( ! $start || ! $end ) {
		return '';
	}

	return $start->format( 'd/m/Y' ) . ' a ' . $end->format( 'd/m/Y' );
}
