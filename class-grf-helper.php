<?php
/**
 * Helper functions that are used by the plugin.
 *
 * @since 0.1
 */
class GRF_Helper {
	
	/**
	 * The main method used for fetching a feed.
	 *
	 * @param string $url
	 * @return object
	 */
	static function fetch_feed( $url ) {
		return fetch_feed( $url );
	}
}
