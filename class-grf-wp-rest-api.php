<?php
/**
 * Register custom WP Rest API endpoints and handle their endpoints.
 *
 * @since 0.1
 */
class GRF_WP_Rest_API {

	/**
	 * Register a custom WP REST API endpoint for validating an added FEED URL to the back-end.
	 *
	 * @since 0.1
	 */
	static function validate_feed_url() {
		add_action( 'rest_api_init' , function () {

			$route_settings = array(
				'path' => 'gutenbergrssfeed/v2',
				'name' => '/validateFeedUrl/'
			);

			register_rest_route( $route_settings['path'], $route_settings['name'], array(
				'methods' => 'GET',
				'args' => array(
					'url'
				),
				'callback' => array( __CLASS__, 'validate_feed_url_endpoint' ),
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
			$feed = GRF_Helper::fetch_feed($data['url']);

			if(!is_wp_error($feed) && !isset($feed->errors)) {
				$validated['success'] = true;
			}
		}

		return wp_send_json($validated);
	}
}
