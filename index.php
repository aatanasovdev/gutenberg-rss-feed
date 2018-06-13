<?php
/*
 * Plugin Name: Gutenberg -- RSS feed
 * Version: 0.1
 * Description: A Gutenberg block that displays posts from an RSS feed.
 * Author: Aleksandar Atanasov
 * License: GPL2
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GutenbergRssFeed {

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
		self::register_custom_endpoint();
	}

	/**
	 * Register the block and load the back-end JavaScript code.
	 *
	 * @since 0.1
	 */	
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

	/**
	 * Render the RSS feed on the front-end.
	 *
	 * @since 0.1
	 * @param array $data
	 * @return mixed
	 */	
	static function gutenberg_rss_feed_render($data) {
		if(is_admin()) {
			return;
		}

		$number_of_posts = 10;

		if(!empty($data['numberOfPosts'])) {
			$number_of_posts = intval($data['numberOfPosts']);
		}

		if(!$data['url']) {
			self::show_message( __('Please set the URL of the RSS feed through the WordPress dashboard.', 'gutenberg-rss-feed') );
		}

		$feed = fetch_feed($data['url']);

		if(is_wp_error($feed) || isset($feed->errors)) {
			return self::show_message( __('Please make sure the provided URL is a valid feed.', 'gutenberg-rss-feed') );
		}

		if(isset($feed->get_item_quantity) && !$feed->get_item_quantity()) {
			return self::show_message( __('No feed items found.', 'gutenberg-rss-feed') );
		}

		$feed_items = $feed->get_items(0, $number_of_posts);

		ob_start();
		?>
		<ul class="custom_block_rss_feed_items">
			<?php foreach($feed_items as $item) : ?>
	            <li class="custom_block_rss_feed_item">
	                <a class="custom_block_rss_feed_item_link" target="_blank" href="<?php echo esc_url( $item->get_permalink() ); ?>" title="<?php printf( __( 'Posted %s', 'gutenberg-rss-feed' ), $item->get_date('j F Y | g:i a') ); ?>">
	                    <?php echo esc_html( $item->get_title() ); ?>
	                </a>
	            </li>
			<?php endforeach; ?>
		</ul>
		<?php
		$output = ob_get_clean();

		return apply_filters('custom_block_rss_feed_frontend_output', $output);
	}

	/**
	 * Display a message on the front-end.
	 *
	 * @since 0.1
	 * @param string $text
	 * @return string
	 */	
	static function show_message($text) {
		return $text;
	}

	static function check_gutenberg() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if( is_plugin_active( 'gutenberg/gutenberg.php' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Register a custom WP REST API endpoint for validating an added FEED URL to the back-end.
	 *
	 * @since 0.1
	 */	
	static function register_custom_endpoint() {
		add_action( 'rest_api_init', function () {

			$route_settings = array(
				'path' => 'gutenbergrssfeed/v2',
				'name' => '/validateFeedUrl/'
			);

			wp_localize_script( 'wp-api', 'wpApiSettings', array(
			    'root' => esc_url_raw( rest_url() ),
			    'validateFeedUrl' => esc_url_raw( rest_url() . $route_settings['path'] . $route_settings['name'] ),
			    'nonce' => wp_create_nonce( 'wp_rest' )
			) );

			register_rest_route( $route_settings['path'], $route_settings['name'], array(
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

	/**
	 * Validate a given URL if it si a correct RSS feed.
	 *
	 * @param array $data
	 * @return boolean
	 * @since 0.1
	 */	
	static function validate_feed_url_endpoint($data) {
		$validated['success'] = false;

		if(!empty($data['url'])) {
			$feed = fetch_feed($data['url']);

			if(!is_wp_error($feed) || !isset($feed->errors)) {
				$validated['success'] = true;
			}
		}

		return wp_send_json($validated);
	}

}

add_action('init', array('GutenbergRssFeed', 'init'));