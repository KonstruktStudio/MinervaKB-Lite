<?php
/**
 * Project: Minerva KB Lite
 * Copyright: 2015-2017 @KonstruktStudio
 */

class MinervaKB_Shortcodes {

	/**
	 * Constructor
	 */
	public function __construct () {
		add_shortcode( 'mkb-search', array($this, 'search_shortcode') );
		add_shortcode( 'mkb-topics',  array($this, 'topics_shortcode') );
		add_shortcode( 'mkb-topic',  array($this, 'topic_shortcode') );
	}

	/**
	 * Search
	 * @param $atts
	 * @return string
	 */
	public function search_shortcode( $atts ) {
		ob_start();
		?><div class="mkb-shortcode-container"><?php
		MinervaKB::put_search(wp_parse_args($atts, array(
			"title" => '',
			"no_focus" => true,
			"show_topic" => true
		)));
		?></div><?php
		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Topic list
	 * @param $atts
	 * @return string
	 */
	public function topics_shortcode( $atts ) {
		ob_start();
		MinervaKB::put_topics($atts);
		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Topic
	 * @param $atts
	 * @return string
	 */
	public function topic_shortcode( $atts ) {

		if (!isset($atts["id"])) {
			return '';
		}

		$args = shortcode_atts( array(
			'view'    => 'box',
			'columns' => '3col',
			'limit' => 5
		), $atts );

		$term = get_term_by( 'id', $atts["id"], MKB_Options::option( 'article_cpt_category' ) );

		$children = $terms = get_terms( array(
			'taxonomy'   => MKB_Options::option( 'article_cpt_category' ),
			'hide_empty' => true,
			'parent'     => $term->term_id
		) );

		$children_columns = MKB_TemplateHelper::get_columns( $args['columns'] );
		$view_mode        = $args['view'];
		$row_open         = false;

		ob_start();
		?>
		<div class="mkb-shortcode-container">
			<?php

			if ( ! empty( $children ) ):
				?>
				<div class="mkb-topic__children mkb-columns mkb-columns-<?php echo esc_attr( $children_columns ); ?>">
					<?php

					$i = 0;

					foreach ( $children as $topic ):

						if ( $i % $children_columns === 0 ):
							echo '<div class="mkb-row">';
							$row_open = true;
						endif;

						if ( $view_mode === 'list' ):
							MKB_TemplateHelper::render_as_list( $topic );
						else:
							MKB_TemplateHelper::render_as_box( $topic );
						endif;

						if ( ( $i + 1 ) % $children_columns === 0 ):
							echo '</div >';
							$row_open = false;
						endif;

						++ $i;
					endforeach;

					if ( $row_open ):
						echo '</div >';
						$row_open = false;
					endif;
					?>
				</div>
			<?php
			endif;

			$query_args = array(
				'post_type' => MKB_Options::option( 'article_cpt' ),
				'ignore_sticky_posts' => 1,
				'posts_per_page' => $args["limit"],
				'tax_query' => array(
					array(
						'taxonomy' => MKB_Options::option( 'article_cpt_category' ),
						'field'    => 'slug',
						'terms'    => $term->slug,
					),
				)
			);

			$topic_loop = new WP_Query( $query_args );

			if ($topic_loop->have_posts()):
				while ( $topic_loop->have_posts() ) : $topic_loop->the_post();
					include( MINERVA_KB_PLUGIN_DIR . 'lib/templates/content.php' );
				endwhile;
			endif;

			wp_reset_postdata();
			?>
		</div>
		<?php

		$html = ob_get_clean();

		return $html;
	}
}
