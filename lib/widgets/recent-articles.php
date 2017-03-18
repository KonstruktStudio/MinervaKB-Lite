<?php
/**
 * Project: MinervaKB Lite.
 * Copyright: 2015-2017 @KonstruktStudio
 */

class MKB_Recent_Articles_Widget extends WP_Widget {
	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'mkb_recent_articles_widget',
			'description' => __('Displays recent Knowledge Base articles', 'minerva-kb' ),
		);
		parent::__construct( 'kb_recent_articles_widget', __('KB Articles', 'minerva-kb' ), $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		$limit = ! empty( $instance['limit'] ) ? $instance['limit'] : 5;
		$order = ! empty( $instance['order'] ) ? $instance['order'] : 'recent';

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		if ($order === 'views') {
			$query_args = array(
				'posts_per_page'   => $limit,
				'offset'           => 0,
				'category'         => '',
				'category_name'    => '',
				'orderby'          => 'meta_value_num',
				'order'            => 'DESC',
				'include'          => '',
				'exclude'          => '',
				'meta_key'         => '_mkb_views',
				'meta_value'       => '',
				'post_type'        => MKB_Options::option( 'article_cpt' ),
				'post_mime_type'   => '',
				'post_parent'      => '',
				'author'	   => '',
				'author_name'	   => '',
				'post_status'      => 'publish',
				'suppress_filters' => true
			);
		} else {
			$query_args = array(
				'post_type'           => MKB_Options::option( 'article_cpt' ),
				'ignore_sticky_posts' => 1,
				'posts_per_page' => $limit,
				'post_status'      => 'publish',
				'suppress_filters' => true
			);
		}

		$topic_loop = new WP_Query( $query_args );
		?>
		<div class="mkb-widget-articles__list">
			<ul>
				<?php

				if ( $topic_loop->have_posts() ):
					while ( $topic_loop->have_posts() ) : $topic_loop->the_post();
						?>
						<li class="mkb-widget-articles__list-item">
							<a href="<?php the_permalink(); ?>">
								<i class="mkb-widget-articles__list-icon fa <?php echo esc_attr( MKB_Options::option( 'article_icon' ) ); ?>"></i>
								<?php the_title(); ?>
							</a>
						</li>
					<?php
					endwhile;
				endif;
				?>
			</ul>
		</div>
		<?php

		wp_reset_postdata();

		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Recent articles', 'minerva-kb' );
		$limit = ! empty( $instance['limit'] ) ? $instance['limit'] : 5;
		$order = ! empty( $instance['order'] ) ? $instance['order'] : 'recent';

		$order_options = array(
			array(
				'key' => 'recent',
				'label' => __('Recent', 'minerva-kb' )
			),
			array(
				'key' => 'views',
				'label' => __('Most viewed', 'minerva-kb' )
			)
		);

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'minerva-kb' ); ?></label>
			<input class="widefat"
			       id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
			       name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
			       type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php esc_attr_e( 'Limit:', 'minerva-kb' ); ?></label>
			<input class="widefat"
			       id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"
			       name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>"
			       type="text" value="<?php echo esc_attr( $limit ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>"><?php esc_attr_e( 'Order:', 'minerva-kb' ); ?></label>

			<select class="widefat"
			        id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>"
			        name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>">
				<?php
				foreach ( $order_options as $option ):
					?>
					<option
						value="<?php echo esc_attr( $option["key"] ); ?>"<?php if ($option["key"] === $order) { echo 'selected="selected"'; }?>>
						<?php echo esc_html( $option["label"] ); ?>
					</option>
				<?php
				endforeach;
				?>
			</select>
		</p>
	<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['limit'] = ( ! empty( $new_instance['limit'] ) ) ? strip_tags( $new_instance['limit'] ) : 5;
		$instance['order'] = ( ! empty( $new_instance['order'] ) ) ? strip_tags( $new_instance['order'] ) : 'recent';

		return $instance;
	}
}