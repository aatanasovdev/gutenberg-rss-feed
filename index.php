<?php
/*
Plugin Name: Gutenberg -- RSS feed
Version: 1.0
License: GPL2
*/

class GutenbergRssFeed {
	
	static function init() {
		self::check_gutenberg();
		self::registerBlock();
	}

	static function registerBlock() {
		wp_register_script(
			'gutenberg-block-rss-feed',
			plugins_url( 'dist/bundle.js', __FILE__ ),
			array( 'wp-blocks', 'wp-element' )
		);

		register_block_type( 'gutenberg-widget-block/rss-feed', array(
			'editor_script' => 'gutenberg-block-rss-feed',
			'render_callback' => array(__CLASS__, 'gutenberg_rss_feed_render'),
		) );		
	}

	static function gutenberg_rss_feed_render() {
		return '<p>List the feed</p>';
	}

	static function check_gutenberg() {
		// Require Gutenberg to be installed
	}
}

add_action('init', array('GutenbergRssFeed', 'init'));