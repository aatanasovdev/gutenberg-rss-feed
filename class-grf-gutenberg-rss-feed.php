<?php
/**
 * The main point for registring the custom Gutenberg RSS feed block.
 *
 * @since 0.1
 */
class GRS_Gutenberg_Rss_Feed {
	
	/**
	 * Enable the custom Gutenberg block.
	 *
	 * @since 0.1
	 * @return mixed
	 */	
	static function init() {

		if(!self::check_gutenberg()) {			
			return;
		}

		self::register_block();
		GRF_WP_Rest_API::validate_feed_url();
	}

	/**
	 * Register the block and load the back-end JavaScript code.
	 *
	 * @since 0.1
	 * @return void
	 */	
	static function register_block() {
		wp_register_script(
			'gutenberg-block-rss-feed',
			plugins_url( 'dist/bundle.js', __FILE__ ),
			array( 'wp-blocks', 'wp-element' )
		);

		register_block_type( 'gutenberg-widget-block/rss-feed', array(
			'editor_script' => 'gutenberg-block-rss-feed',
			'render_callback' => array('GRF_Frontend', 'render'),
		) );		
	}

	/**
	 * Check if the Gutenberg plugin is activated.
	 *
	 * @since 0.1
	 * @return boolean
	 */		
	static function check_gutenberg() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if( is_plugin_active( 'gutenberg/gutenberg.php' ) ) {
			return true;
		}

		return false;
	}



}