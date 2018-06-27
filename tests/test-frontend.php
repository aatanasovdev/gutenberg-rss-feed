<?php
use PHPUnit\Framework\TestCase;

/**
 * Test Frontend output.
 */
class FrontendTestRender extends TestCase {
	/**
	 * Example feed URL. Expected to be a valid feed with at least 3 posts and all of them have titles/descriptions.
	 *
	 * @var string
	 */	
	static protected $feed_url = 'https://aatanasov.net/feed/';

	/**
	 * Number of posts.
	 *
	 * @var integer
	 */	
	static protected $number_of_posts = 1;

	/**
	 * Whether to show the descriptions of the posts.
	 *
	 * @var boolean
	 */	
	static protected $show_description = true;	

	/**
	 * Whether to show the dates of the posts.
	 *
	 * @var boolean
	 */	
	static protected $show_date = true;

	/**
	 * The feed front-end output.
	 *
	 * @var object
	 */	
	static protected $output;

	/**
	 * Example feed items.
	 *
	 * @var object
	 */	
	static protected $feed_items;	

	/**
	 * Set up the initial data.
     *
	 * @return void
	 */		
    protected function setUp() {
        self::$output = self::get_output();         

        $feed = fetch_feed(self::$feed_url);
        self::$feed_items = $feed->get_items(0, 5);
    }

	/**
	 * Test if the output contains HTML code.
     *
	 * @return void	 
	 */
	public function test_output() {
		$html_tag = 'div';

		$this->assertContains( $html_tag, self::$output );
		
		$this->assertContains( $html_tag, GRF_Frontend::output() );
	}

	/**
	 * Test how many posts to be shown.
     *
	 * @return void	 
	 */
	public function test_number_of_posts() {
		$this->assertNotContains(self::$feed_items[1]->get_title(), self::$output);

		self::$number_of_posts = 2;

		$output = self::get_output();

		$this->assertContains(self::$feed_items[1]->get_title(), $output);
		$this->assertNotContains(self::$feed_items[2]->get_title(), $output);
	}	

	/**
	 * Test showing post description.
     *
	 * @return void	 
	 */
	public function test_showing_description() {
		$description = self::$feed_items[0]->get_description();

		$this->assertContains( $description, self::$output );

		self::$show_description = false;

		$output = self::get_output();

		$this->assertNotContains( $description, $output );

	}

	/**
	 * Test showing post date.
     *
	 * @return void	 
	 */
	public function test_showing_date() {
		$date = self::$feed_items[0]->get_date(self::get_date_format());

		$this->assertContains( $date, self::$output );

		self::$show_date = false;

		$output = self::get_output();

		$this->assertNotContains( $date, $output );

	}		

	/**
	 * Test showing an error when the feed URL is empty.
     *
	 * @return void	 
	 */
	public function test_empty_feed_url() {
		$main_feed_url = self::$feed_url;
		self::$feed_url = '';

		$output = self::get_output();

		$this->assertContains( 'Please set the URL of the RSS feed through the WordPress dashboard.', $output );

		self::$feed_url = $main_feed_url;
	}	

	/**
	 * Test showing an error when the feed URL is invalid.
     *
	 * @return void	 
	 */
	public function test_invalid_feed_url() {
		self::$feed_url = 'http://example.com/feed/';

		// Expected PHP notice from SimplePie because of the incorrect feed URL.
		$output = self::get_output();

		$this->assertContains( 'Please make sure the provided URL is a valid feed.', $output );
	}		

	/**
	 * Get example data.
	 *
	 * @return array
	 */	
	static function get_data() {	
		$data = array(
			'url' => self::$feed_url,
			'showDate' => self::$show_date,
			'showDescription' => self::$show_description,
			'numberOfPosts' => self::$number_of_posts,
		);

		return $data;
	}

	/**
	 * Get output feed.
	 *
	 * @return mixed
	 */	
	static function get_output() {	
		return GRF_Frontend::render(self::get_data());
	}	

	/**
	 * Get the date format used on the feed output.
	 *
	 * @return string
	 */	
	static function get_date_format() {	
		return get_option('date_format') . ' | ' . get_option('time_format');
	}	
}
