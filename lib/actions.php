<?php
/**
 * Project: MinervaKB Lite.
 * Copyright: 2015-2017 @KonstruktStudio
 */

class MinervaKB_ContentHooks {

	private $info;

	/**
	 * Constructor
	 * @param $deps
	 */
	public function __construct($deps) {

		$this->setup_dependencies( $deps );

		// single template actions
		add_action('minerva_single_title_after', array($this, 'single_breadcrumbs'), 100);

		// single entry actions
		add_action('minerva_single_entry_header_meta', array($this, 'single_reading_estimate'), 50);
		add_action('minerva_single_entry_footer_meta', array($this, 'single_tags'), 100);
		add_action('minerva_single_entry_footer_meta', array($this, 'single_extra_pageviews'), 300);
		add_action('minerva_single_entry_footer_meta', array($this, 'minerva_kb_pro_link'), 900);

		// page
		add_action('minerva_page_loop_after', array($this, 'minerva_kb_pro_link'), 900);

		// topic template actions
		add_action('minerva_category_title_after', array($this, 'category_breadcrumbs'), 100);
		add_action('minerva_category_title_after', array($this, 'category_children'), 150);
		add_action('minerva_category_loop_after', array($this, 'category_pagination'), 100);
		add_action('minerva_category_loop_after', array($this, 'minerva_kb_pro_link'), 900);

		// tag template actions
		add_action('minerva_tag_loop_after', array($this, 'tag_pagination'), 100);
		add_action('minerva_tag_loop_after', array($this, 'minerva_kb_pro_link'), 900);

		// search template actions
		add_action('minerva_search_title_after', array($this, 'search_results_breadcrumbs'), 100);
		add_action('minerva_search_loop_after', array($this, 'search_results_pagination'), 100);
		add_action('minerva_search_loop_after', array($this, 'minerva_kb_pro_link'), 900);

		// no results
		add_action('minerva_no_content_inside', array($this, 'no_results_search'), 100);
	}

	/**
	 * Sets up dependencies
	 * @param $deps
	 */
	private function setup_dependencies($deps) {
		if (isset($deps['info'])) {
			$this->info = $deps['info'];
		}
	}

	/**
	 * Article breadcrumbs
	 */
	public function single_breadcrumbs() {
		if (MKB_Options::option('show_breadcrumbs_single')) {
			$terms = wp_get_post_terms( get_the_ID(), MKB_Options::option( 'article_cpt_category' ));
			$term = null;

			if ($terms && !empty($terms) && isset($terms[0])) {
				$term = $terms[0];
			}

			MKB_TemplateHelper::breadcrumbs( $term, MKB_Options::option( 'article_cpt_category' ), 'single' );
		}
	}

	/**
	 * Article reading estimate
	 */
	public function single_reading_estimate() {
		$words_per_minute = 275;
		$content = get_post_field( 'post_content', get_the_ID() );
		$word_count = str_word_count( strip_tags( $content ) );
		$est_reading_time_raw = round( $word_count / $words_per_minute );

		if ( $est_reading_time_raw < 1 ) {
			$est_reading_time = MKB_Options::option( 'estimated_time_less_than_min' );
		} else {
			$est_reading_time = $est_reading_time_raw . ' ' . MKB_Options::option( 'estimated_time_min' );
		}

		if ( MKB_Options::option( 'show_reading_estimate' ) ): ?>
			<div
				class="mkb-article-header__estimate">
				<i class="mkb-estimated-icon fa <?php esc_attr_e(MKB_Options::option( 'estimated_time_icon' )); ?>"></i>
				<?php esc_html_e( MKB_Options::option( 'estimated_time_text' ) ); ?> <?php esc_html_e( $est_reading_time ); ?>
			</div>
		<?php endif;
	}

	/**
	 * Article tags
	 */
	public function single_tags() {
		if ( MKB_Options::option( 'show_article_tags' ) ):
			?><div class="mkb-article-extra__tags"><?php
			if (MKB_Options::option( 'show_article_tags_icon' )):
				?><i class="fa <?php echo esc_attr(MKB_Options::option( 'article_tags_icon' )); ?>"></i><?php
			endif;
				$tags = wp_get_object_terms(get_the_ID(), MKB_Options::option( 'article_cpt_tag' ));

				if (sizeof($tags)):
					foreach($tags as $tag):
						?><span class="mkb-tag-nolink"><?php esc_html_e($tag->name); ?></span><?php
					endforeach;
				endif;
			?></div><?php
		endif;
	}

	/**
	 * Article pageviews display
	 */
	public function single_extra_pageviews() {
		$id = get_the_ID();
		$views = get_post_meta( $id, '_mkb_views', true );
		?>
		<div class="mkb-article-extra__stats">
			<?php if ( MKB_Options::option( 'show_pageviews' ) ): ?>
				<div class="mkb-article-extra__stats-pageviews">
					<?php echo esc_html(MKB_Options::option( 'pageviews_label' )); ?> <?php echo esc_html( $views ? $views : 0 ); ?>
				</div>
			<?php endif; ?>
		</div>
	<?php
	}

	/**
	 * Topic breadcrumbs
	 */
	public function category_breadcrumbs() {
		$term = get_term_by( 'id', get_queried_object_id(), MKB_Options::option( 'article_cpt_category' ) );

		if (MKB_Options::option('show_breadcrumbs_category')) {
			MKB_TemplateHelper::breadcrumbs( $term, MKB_Options::option( 'article_cpt_category' ) );
		}
	}

	/**
	 * Topic children
	 */
	public function category_children() {
		$term = get_term_by( 'id', get_queried_object_id(), MKB_Options::option( 'article_cpt_category' ) );

		$children = $terms = get_terms( array(
			'taxonomy'   => MKB_Options::option( 'article_cpt_category' ),
			'hide_empty' => true,
			'parent'     => $term->term_id
		) );

		$children_columns = MKB_TemplateHelper::get_topic_children_columns();
		$view_mode = MKB_Options::option('topic_children_view');
		$row_open = false;

		if ( ! empty( $children ) ):
			?>
			<div class="mkb-topic__children mkb-columns mkb-columns-<?php echo esc_attr($children_columns); ?>">
				<?php

				$i = 0;

				foreach ( $children as $topic ):

					if ($i % $children_columns === 0):
						echo '<div class="mkb-row">';
						$row_open = true;
					endif;

					if ($view_mode === 'list'):
						MKB_TemplateHelper::render_as_list($topic);
					else:
						MKB_TemplateHelper::render_as_box($topic);
					endif;

					if ( ($i + 1) % $children_columns === 0 ):
						echo '</div >';
						$row_open = false;
					endif;

					++$i;
				endforeach;

				if ( $row_open ):
					echo '</div >';
					$row_open = false;
				endif;
				?>
			</div>
		<?php
		endif;
	}

	/**
	 * Search breadcrumbs
	 */
	public function search_results_breadcrumbs() {
		if (MKB_Options::option('show_breadcrumbs_search')) {
			MKB_TemplateHelper::search_breadcrumbs( $_REQUEST['s'] );
		}
	}

	/**
	 * Pagination for category page
	 */
	public function category_pagination () {
		MKB_TemplateHelper::pagination();
	}

	/**
	 * Pagination for tag page
	 */
	public function tag_pagination () {
		MKB_TemplateHelper::pagination();
	}

	/**
	 * Pagination for search results page
	 */
	public function search_results_pagination () {
		MKB_TemplateHelper::pagination();
	}

	/**
	 * Pagination for search results page
	 */
	public function no_results_search () {
		MKB_TemplateHelper::render_search(array(
			"search_title" => MKB_Options::option( 'topic_search_title' ),
			"search_border_color" => MKB_Options::option( 'topic_search_border_color' ),
			"search_placeholder" => MKB_Options::option( 'topic_search_placeholder' ),
			"show_search_tip" => MKB_Options::option( 'topic_show_search_tip' ),
			"disable_autofocus" => MKB_Options::option( 'topic_disable_autofocus' ),
			"search_tip" => MKB_Options::option( 'topic_search_tip' ),
			"show_topic_in_results" => MKB_Options::option( 'topic_show_topic_in_results' )
		));
	}

	/**
	 * Please, do not remove this link :)
	 * If you like this plugin, consider supporting us by buying full version
	 */
	public function minerva_kb_pro_link() {
		if (!MKB_Options::option('show_minerva_link_switch')) {
			return;
		}
		?><div class="mkb-plugin-link"><?php
			_e( 'Powered by ', 'minerva-kb' ); ?><a href="https://www.minerva-kb.com/" target="_blank"><?php
				_e( 'Minerva knowledge base', 'minerva-kb' ); ?></a></div><?php
	}
}

