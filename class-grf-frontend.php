<?php
/**
 * Responsible for rendering the feed output on the front-end.
 *
 * @since 0.1
 */
class GRF_Frotnend {
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

		$number_of_posts = 10;

		if(!empty($data['numberOfPosts'])) {
			$number_of_posts = intval($data['numberOfPosts']);
		}

		if(empty($data['url'])) {
			return __('Please set the URL of the RSS feed through the WordPress dashboard.');
		}

		$feed = fetch_feed($data['url']);

		if(is_wp_error($feed) || isset($feed->errors)) {
			return __('Please make sure the provided URL is a valid feed.');
		}

		if(isset($feed->get_item_quantity) && !$feed->get_item_quantity()) {
			return __('No feed items found.');
		}

		$feed_items = $feed->get_items(0, $number_of_posts);

		$date_format = get_option('date_format') . ' | ' . get_option('time_format') ;

		do_action('grf_before_items');

		ob_start();
		?>
		<div class="grf_items">

			<?php foreach($feed_items as $item) : ?>

				<?php do_action('grf_before_item'); ?>

	            <div class="grf_item">

	                <h3 class="grf_item_title">
	                	<a class="grf_item_link" target="_blank" href="<?php echo esc_url( $item->get_permalink() ); ?>" title="<?php printf( __('Posted %s'), $item->get_date($date_format) ); ?>">
	                    	<?php echo esc_html( $item->get_title() ); ?>
	                	</a>
	                </h3>

	                <?php if(isset($data['showDate']) && $data['showDate'] ) : ?>
						<p class="grf_item_date"><?php echo $item->get_date($date_format); ?></p>
	            	<?php endif; ?>
					
					<?php if(isset($data['showDescription']) && $data['showDescription'] && !empty($item->get_description()) ) : ?>

						<p class="grf_item_description">
							<?php echo $item->get_description() ?>
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