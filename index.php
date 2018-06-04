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

	static function gutenberg_rss_feed_render($data) {
		$invalid_feed = false;
		$message = '';

		if(!$data['url']) {
			$invalid_feed = true;
		}

		$feed = fetch_feed($data['url']);

		if(is_wp_error($feed) || isset($feed->errors)) {
			$invalid_feed = true;	
		}

		if($invalid_feed) {
			$message = __('Please configure your RSS feed through the WordPress dashboard.', 'gutenberg-rss-feed');
		}

		if(!$feed->get_item_quantity()) {
			$message = __('No feed items found.', 'gutenberg-rss-feed');
		}

		if($message) {
			return wpautop($message);
		}

		$feed_items = $feed->get_items(0, 10);

		ob_start();
		?>
		<ul>
			<?php foreach($feed_items as $item) ?>
            <li>
                <a target="_blank" href="<?php echo esc_url( $item->get_permalink() ); ?>" title="<?php printf( __( 'Posted %s', 'gutenberg-rss-feed' ), $item->get_date('j F Y | g:i a') ); ?>">
                    <?php echo esc_html( $item->get_title() ); ?>
                </a>
            </li>
		</ul>
		<?php
		return ob_get_clean();

	}

	static function check_gutenberg() {
		// Require Gutenberg to be installed
	}

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

	static function validate_feed_url_endpoint($data) {
		include_once(ABSPATH . WPINC . '/feed.php');

		$validated['success'] = false;

		$feed = fetch_feed($data['url']);
		if(!is_wp_error($feed) || !isset($feed->errors)) {
			$validated['success'] = true;
		}

		return wp_send_json($validated);
	}

}

add_action('init', array('GutenbergRssFeed', 'init'));