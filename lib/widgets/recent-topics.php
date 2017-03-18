<?php
/**
 * Project: MinervaKB Lite.
 * Copyright: 2015-2017 @KonstruktStudio
 */

class MKB_Recent_Topics_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'mkb_recent_topics_widget',
			'description' => __('Displays recent Knowledge Base topics', 'minerva-kb' ),
		);
		parent::__construct( 'kb_recent_topics_widget', __('KB Topics', 'minerva-kb' ), $widget_ops );
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

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		$topics = get_terms( MKB_Options::option( 'article_cpt_category' ), array(
			'hide_empty' => true,
			'number' => $limit
		) );

		if ( sizeof( $topics ) ):
			?>
			<div class="mkb-widget-topics__list">
				<ul>
					<?php
					foreach ( $topics as $topic ):
						$topic_link = get_term_link( $topic );
						?>
						<li class="mkb-widget-topics__list-item">
							<a href="<?php echo esc_attr( $topic_link ); ?>">
								<i class="mkb-widget-topics__list-icon fa <?php echo esc_attr( MKB_TemplateHelper::get_topic_icon( $topic ) ); ?>"></i>
								<?php echo esc_html( $topic->name ); ?>
							</a>
						</li>
					<?php endforeach; // end of terms loop
					?>
				</ul>
			</div>
		<?php
		endif; // end of topics loop

		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Recent topics', 'minerva-kb' );
		$limit = ! empty( $instance['limit'] ) ? $instance['limit'] : 5;

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

		return $instance;
	}
}