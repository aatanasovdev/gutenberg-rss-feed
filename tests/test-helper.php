<?php
use PHPUnit\Framework\TestCase;

/**
 * Test Helper methods.
 */
class HelperTestRender extends TestCase {
	
	use FetchFeed;

	/**
	 * Test if getting the title method exists
     *
	 * @return void	 
	 */
	public function test_fetch_feed() {
		$this->assertNotEmpty(GRF_Helper::fetch_feed(self::$feed_url));		
	}
}