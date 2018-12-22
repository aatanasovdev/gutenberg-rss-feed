<?php

/**
 * Handle fetching a feed.
 */
trait FetchFeed {
	
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
    protected function feed_setup() {
        self::$output = self::get_output();         

        $feed = fetch_feed(self::$feed_url);
        self::$feed_items = $feed->get_items(0, 5);
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
}