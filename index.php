<?php
/*
 * Plugin Name: RSS Feed Block
 * Version: 0.4
 * Description: A block that pulls posts from an RSS feed.
 * Author: Aleksandar Atanasov
 * License: GPL2
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include_once( 'load.php' );

add_action( 'init', array( 'GRS_Gutenberg_Rss_Feed', 'init' ) );