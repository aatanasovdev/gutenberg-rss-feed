<?php
/*
 * Plugin Name: Gutenberg -- RSS feed
 * Version: 0.3
 * Description: A Gutenberg block that displays posts from an RSS feed.
 * Author: Aleksandar Atanasov
 * License: GPL2
*/

if (!defined('ABSPATH')) {
	exit;
}

include_once('load.php');

add_action('init', array('GRS_Gutenberg_Rss_Feed', 'init'));