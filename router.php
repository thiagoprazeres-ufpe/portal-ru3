<?php

$path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
$file = $path ? __DIR__ . $path : '';

if ( $path && '/' !== $path && $file && file_exists( $file ) && ! is_dir( $file ) ) {
	return false;
}

require __DIR__ . '/index.php';
