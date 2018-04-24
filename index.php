<?php
/*
Plugin Name: Gutenberg -- external RSS feed
Version: 1.0
License: GPL2
*/

function gutenberg_external_rss_feed() {
	wp_register_script(
		'gutenberg-external-rss-feed',
		plugins_url( 'dist/bundle.js', __FILE__ ),
		array( 'wp-blocks', 'wp-element' )
	);

	register_block_type( 'gutenberg-widget-block/external-rss-feed', array(
		'editor_script' => 'gutenberg-external-rss-feed',
		'render_callback' => 'gutenberg_external_rss_feed_render',
	) );
}

add_action('init', 'gutenberg_external_rss_feed');


function gutenberg_external_rss_feed_render() {
	echo '<p>Test</p>';
}
