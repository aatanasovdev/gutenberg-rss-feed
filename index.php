<?php
/*
 * Plugin Name: Gutenberg -- RSS feed
 * Version: 0.1
 * Description: A Gutenberg block that displays posts from an RSS feed.
 * Author: Aleksandar Atanasov
 * License: GPL2
*/

if (!defined('ABSPATH')) {
	exit;
}

class GutenbergRssFeed {

	/**
	 * @var string
	 * @since 0.1
	 */
	static $plugin_prefix = 'custom_block_rss_feed';

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

		if(empty($data['url'])) {
			self::show_message( __('Please set the URL of the RSS feed through the WordPress dashboard.') );
		}

		$feed = fetch_feed($data['url']);

		if(is_wp_error($feed) || isset($feed->errors)) {
			return self::show_message( __('Please make sure the provided URL is a valid feed.') );
		}

		if(isset($feed->get_item_quantity) && !$feed->get_item_quantity()) {
			return self::show_message( __('No feed items found.') );
		}

		$feed_items = $feed->get_items(0, $number_of_posts);

		$date_format = get_option('date_format') . ' | ' . get_option('time_format') ;

		do_action(self::add_plugin_prefix_to_string('before_items', true));

		ob_start();
		?>
		<div class="<?php self::add_plugin_prefix_to_string('items'); ?>">

			<?php foreach($feed_items as $item) : ?>

				<?php do_action(self::add_plugin_prefix_to_string('before_item', true)); ?>

	            <div class="<?php self::add_plugin_prefix_to_string('item'); ?>">

	                <h3>
	                	<a class="<?php self::add_plugin_prefix_to_string('item_link'); ?>" target="_blank" href="<?php echo esc_url( $item->get_permalink() ); ?>" title="<?php printf( __('Posted %s'), $item->get_date($date_format) ); ?>">
	                    	<?php echo esc_html( $item->get_title() ); ?>
	                	</a>
	                </h3>

	                <?php if(isset($data['showDate']) && $data['showDate'] ) : ?>
						<p class="custom_block_rss_feed_item_date"><?php echo $item->get_date($date_format); ?></p>
	            	<?php endif; ?>
					
					<?php if(isset($data['showDescription']) && $data['showDescription'] && !empty($item->get_description()) ) : ?>

						<p class="<?php self::add_plugin_prefix_to_string('item_description'); ?>">
							<?php echo $item->get_description() ?>
						</p>

					<?php endif; ?>

					<?php do_action(self::add_plugin_prefix_to_string('after_item', true)); ?>

	            </div>

			<?php endforeach; ?>

		</div>
		<?php
		$output = ob_get_clean();

		do_action(self::add_plugin_prefix_to_string('after_items', true));

		return apply_filters(self::add_plugin_prefix_to_string('frontend_output', true), $output);
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
	 * Validate a given URL if it is a correct RSS feed.
	 *
	 * @param array $data
	 * @return boolean
	 * @since 0.1
	 */	
	static function validate_feed_url_endpoint($data) {
		$validated['success'] = false;

		if(!empty($data['url'])) {
			$feed = fetch_feed($data['url']);

			if(!is_wp_error($feed) && !isset($feed->errors)) {
				$validated['success'] = true;
			}
		}

		return wp_send_json($validated);
	}

	/**
	 * Display or return a string with the plugin's prefix
	 *
	 * @param string $string
	 * @param boolean $return
	 * @return string
	 */	
	static function add_plugin_prefix_to_string($string, $return = false) 
	{	
		if(empty($string)) {
			return '';
		}

		$string = self::$plugin_prefix . '_' . $string;

		if($return) {
			return $string;
		}

		echo $string;
	}	

}

add_action('init', array('GutenbergRssFeed', 'init'));