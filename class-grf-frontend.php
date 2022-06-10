<?php
/**
 * Responsible for rendering the feed output on the front-end.
 *
 * @since 0.1
 */
class GRF_Frontend {
	
	/**
	 * Feed object.
	 *
	 * @since 0.1
	 * @var object
	 */	
	static private $feed;

	/**
	 * The data of the attributes used on the back-end.
	 *
	 * @since 0.1
	 * @var array
	 */	
	static private $data;

	/**
	 * Default number of posts.
	 *
	 * @since 0.1
	 * @var integer
	 */	
	static private $number_of_posts = 10;

	/**
	 * Render the RSS feed on the front-end.
	 *
	 * @since 0.1
	 * @param array $data
	 * @return mixed
	 */	
	static function render($data) {		
		if(is_admin()) {
			return;
		}

		self::$data = $data;

		if(!empty(self::$data['url'])) {
			self::$feed = GRF_Helper::fetch_feed(self::$data['url']);	
		}

		$error = self::validate();

		if(!empty($error)) {
			return $error;
		}

		return self::output();
	}

	/**
	 * Validate the feed and prepare error messages if any.
	 *
	 * @return string
	 */	
	static function validate() {
		if(empty(self::$data['url'])) {
			return __('Please set the URL of the RSS feed through the WordPress dashboard.');
		}		

		if(is_wp_error(self::$feed) || isset(self::$feed->errors)) {
			return __('Please make sure the provided URL is a valid feed.');
		}

		if(isset(self::$feed->get_item_quantity) && !self::$feed->get_item_quantity()) {
			return __('No feed items found.');
		}

		return false;
	}	

	/**
	 * Prepare the html markup of the feed.
	 *
	 * @return string
	 */	
	static function output() {	
		if(!empty(self::$data['numberOfPosts'])) {
			self::$number_of_posts = intval(self::$data['numberOfPosts']);
		}

		$feed_items = self::$feed->get_items(0, self::$number_of_posts);

		$date_format = get_option('date_format') . ' | ' . get_option('time_format') ;

		do_action('grf_before_items');

		ob_start();
		?>
		<div class="grf_items">

			<?php foreach($feed_items as $item) : ?>
				<?php do_action('grf_before_item'); ?>

	            <div class="grf_item">

	                <h3 class="grf_item_title">
	                	<a class="grf_item_link" target="_blank" href="<?php echo esc_url( $item->get_permalink() ); ?>">
	                    	<?php echo esc_html( $item->get_title() ); ?>
	                	</a>
	                </h3>

	                <?php if(isset(self::$data['showDate']) && self::$data['showDate'] ) : ?>
						<p class="grf_item_date"><?php echo $item->get_date($date_format); ?></p>
	            	<?php endif; ?>
					
					<?php if(isset(self::$data['showDescription']) && self::$data['showDescription'] && method_exists($item, 'get_description') && $item->get_description()) : ?>

						<p class="grf_item_description">
							<?php echo $item->get_description(); ?>
						</p>

					<?php endif; ?>


					<?php if(isset(self::$data['showContent']) && self::$data['showContent'] && method_exists($item, 'get_content') && $item->get_content()) : ?>

						<p class="grf_item_description">
							<?php echo $item->get_content(); ?>
						</p>

					<?php endif; ?>

					<?php do_action('grf_after_item'); ?>

	            </div>

			<?php endforeach; ?>

		</div>
		<?php
		$output = ob_get_clean();

		do_action('grf_after_items');

		return apply_filters('grf_frontend_output', $output);		
	}	
}