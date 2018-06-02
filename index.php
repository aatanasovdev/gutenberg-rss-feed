<?php
/*
Plugin Name: Gutenberg -- RSS feed
Version: 1.0
License: GPL2
*/

class GutenbergRssFeed {
	
	static function init() {
		self::check_gutenberg();
		self::register_block();
		self::register_custom_endpoint();
	}

	static function register_block() {
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

	static function register_custom_endpoint() {
		add_action( 'rest_api_init', function () {
			wp_localize_script( 'wp-api', 'wpApiSettings', array(
			    'root' => esc_url_raw( rest_url() ),
			    'nonce' => wp_create_nonce( 'wp_rest' )
			) );

			register_rest_route( 'gutenbergrssfeed/v2', '/validateFeedUrl/', array(
				'methods' => 'GET',
				'args' => array(
					'url'
				),				
				'callback' => array('GutenbergRssFeed', 'validate_feed_url_endpoint'),
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				}
			) );
		} );		
	}

	static function validate_feed_url_endpoint() {
		return;
	}
}

add_action('init', array('GutenbergRssFeed', 'init'));