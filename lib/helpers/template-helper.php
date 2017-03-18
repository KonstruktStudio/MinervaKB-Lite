<?php
/**
 * Project: Minerva KB Lite
 * Copyright: 2015-2016 @KonstruktStudio
 */

class MKB_TemplateHelper {
	public function __construct() {
	}

	/**
	 * Determines the parent term for current term
	 * @param $term
	 * @param $taxonomy
	 *
	 * @return array|false|WP_Term
	 */
	private static function get_root_term($term, $taxonomy) {
		if ($term->parent != '0') { // child
			$ancestors = get_ancestors( $term->term_id, $taxonomy, 'taxonomy' );

			if (!empty($ancestors)) {
				return get_term_by( 'id', $ancestors[sizeof($ancestors) - 1], $taxonomy );
			}
		}

		return $term;
	}

	/**
	 * Gets KB home page for given term
	 * @param $term
	 * @param $taxonomy
	 *
	 * @return false|string
	 */
	public static function get_home_page_link($term, $taxonomy) {
		$root_term = self::get_root_term($term, $taxonomy);

		return get_the_permalink(self::get_topic_home_page($root_term));
	}

	/**
	 * Determines if there's a custom KB home page set for term
	 * @param $term
	 *
	 * @return null
	 */
	public static function get_topic_home_page($term) {
		$home_page_id = MKB_Options::option( 'kb_page' );

		return $home_page_id;
	}

	/**
	 * Renders breadcrumbs
	 * @param $term
	 * @param $taxonomy
	 * @param bool $is_single
	 */
	public static function breadcrumbs( $term, $taxonomy, $type = false ) {

		$icon = MKB_Options::option('breadcrumbs_separator_icon');

		$home_label = MKB_Options::option('breadcrumbs_home_label') ?
			MKB_Options::option('breadcrumbs_home_label') :
			__( 'KB Home', 'minerva-kb' );

		$breadcrumbs = array(
			array(
				'name' => $home_label,
				'link' => self::get_home_page_link($term, $taxonomy),
				'icon' => $icon
			)
		);

		$ancestors = null;

		if ($term) {
			$ancestors = get_ancestors( $term->term_id, $taxonomy, 'taxonomy' );
		}

		if ( ! empty( $ancestors ) ) {
			$breadcrumbs = array_merge( $breadcrumbs,
				array_reverse(
					array_map( function ( $id ) use ( $taxonomy, $icon ) {
						$parent = get_term_by( 'id', $id, $taxonomy );

						return array(
							'name' => $parent->name,
							'link' => get_term_link( $parent ),
							'icon' => $icon
						);
					}, $ancestors )
				)
			);
		}

		if ($type === 'single'):
			array_push($breadcrumbs, array(
				'name' => $term->name,
				'link' => get_term_link( $term ),
				'icon' => $icon
			));

			array_push($breadcrumbs, array(
				'name' => get_the_title()
			));
		else:
			array_push($breadcrumbs, array(
				'name' => $term->name,
			));
		endif;

		?>
		<div class="mkb-breadcrumbs">
			<span
				class="mkb-breadcrumbs__label">
				<?php echo esc_html( MKB_Options::option( 'breadcrumbs_label' ) ); ?>
			</span>
			<ul class="mkb-breadcrumbs__list">
				<?php
				foreach ( $breadcrumbs as $crumb ):
					?>
					<li>
						<?php if (array_key_exists( "link", $crumb ) && ! empty( $crumb["link"] )): ?>
						<a href="<?php echo esc_attr( $crumb["link"] ); ?>">
							<?php endif; ?>
							<?php echo esc_html( $crumb["name"] ); ?>
							<?php if (array_key_exists( "link", $crumb ) && ! empty( $crumb["link"] )): ?>
						</a>
					<?php endif; ?>
						<?php if (array_key_exists( "icon", $crumb )): ?>
					<i class="mkb-breadcrumbs-icon fa <?php echo esc_attr($crumb["icon"]); ?>"></i>
					<?php endif; ?>
					</li>
				<?php
				endforeach;
				?>
			</ul>
		</div>
	<?php
	}

	/**
	 * Search page breadcrumbs
	 * @param $needle
	 */
	public static function search_breadcrumbs( $needle ) {

		$icon = MKB_Options::option('breadcrumbs_separator_icon');

		$home_label = MKB_Options::option('breadcrumbs_home_label') ?
			MKB_Options::option('breadcrumbs_home_label') :
			__( 'KB Home', 'minerva-kb' );

		$breadcrumbs = array(
			array(
				'name' => $home_label,
				'link' => get_the_permalink(MKB_Options::option( 'kb_page' )),
				'icon' => $icon
			),
			array(
				'name' => sprintf(MKB_Options::option( 'search_results_breadcrumbs_label' ), $needle)
			)
		);

		?>
		<div class="mkb-breadcrumbs">
			<span
				class="mkb-breadcrumbs__label">
				<?php echo esc_html( MKB_Options::option( 'breadcrumbs_label' ) ); ?>
			</span>
			<ul class="mkb-breadcrumbs__list">
				<?php
				foreach ( $breadcrumbs as $crumb ):
					?>
					<li>
						<?php if (isset($crumb["link"]) && ! empty($crumb["link"]) ): ?>
							<a href="<?php esc_attr_e( $crumb["link"] ); ?>">
						<?php endif; ?>
						<?php esc_html_e( $crumb["name"] ); ?>
						<?php if ( isset($crumb["link"]) && ! empty($crumb["link"]) ): ?>
							</a>
						<?php endif; ?>
						<?php if (isset( $crumb["icon"])): ?>
							<i class="mkb-breadcrumbs-icon fa <?php esc_attr_e($crumb["icon"]); ?>"></i>
						<?php endif; ?>
					</li>
				<?php
				endforeach;
				?>
			</ul>
		</div>
	<?php
	}

	/**
	 * Content class for use in templates
	 * @param string $type
	 * @return string
	 */
	public static function root_class($type = 'page') {
		$classes = array('mkb-root', 'mkb-clearfix', 'mkb-container');

		if (MKB_Options::option( $type . '_sidebar' ) !== 'none') {
			array_push($classes, 'mkb-sidebar-' . MKB_Options::option( $type . '_sidebar' ));
		}

		return join(' ', $classes);
	}

	/**
	 * Content class for use in templates
	 * @param string $type
	 * @return string
	 */
	public static function content_class($type = 'page') {
		$classes = array('mkb-content-main');

		array_push($classes, 'mkb-content-main--' . $type);

		if (MKB_Options::option( $type . '_sidebar' ) !== 'none') {
			array_push($classes, 'mkb-content-main--has-sidebar');
		}

		return join(' ', $classes);
	}

	/**
	 * Left sidebar
	 * @param string $type
	 */
	public static function maybe_render_left_sidebar($type = 'page') {
		if (MKB_Options::option( $type . '_sidebar' ) === 'left') {
			self::render_sidebar($type);
		}
	}

	/**
	 * Right sidebar
	 * @param string $type
	 */
	public static function maybe_render_right_sidebar($type = 'page') {
		if (MKB_Options::option( $type . '_sidebar' ) === 'right') {
			self::render_sidebar($type);
		}
	}

	/**
	 * Sidebar render
	 * @param $sidebar_id
	 */
	public static function render_sidebar($sidebar_id) {
		?><aside class="mkb-sidebar" role="complementary">
			<?php dynamic_sidebar( 'sidebar-kb-' . $sidebar_id ); ?>
		</aside><?php
	}

	/**
	 * Gets icon for topic
	 * @param $term
	 * @param array $args
	 * @return null
	 */
	public static function get_topic_icon($term, $args = array()) {
		$icon = empty($args) ? MKB_Options::option('topic_icon') : $args['topic_icon'];

		return $icon;
	}

	/**
	 * Gets topic color
	 * @param $term
	 * @param array $args
	 *
	 * @return null
	 */
	public static function get_topic_color($term, $args = array()) {
		$color = empty($args) ? MKB_Options::option('topic_color') : $args['topic_color'];

		return $color;
	}

	/**
	 * Renders topics depending on settings
	 * @param array $settings
	 */
	public static function render_topics($settings = array()) {

		// parse global options
		$args = wp_parse_args(
			$settings,
			array(
				"home_topics" => MKB_Options::option( 'home_topics' ),
				"home_view" => MKB_Options::option( 'home_view' ),
				"home_layout" => MKB_Options::option( 'home_layout' ),
				"box_view_item_bg" => MKB_Options::option( 'box_view_item_bg' ),
				"box_view_item_hover_bg" => MKB_Options::option( 'box_view_item_hover_bg' ),
				"topics_title" => ""
			)
		);

		$topics = array();

		if ($args['home_topics']) {
			$ids = explode(',', $args['home_topics']);

			foreach ($ids as $id) {
				$topic = get_term_by('id', (int)$id, MKB_Options::option( 'article_cpt_category' ));
				array_push($topics, $topic);
			}
		} else {
			$topics = get_terms( MKB_Options::option( 'article_cpt_category' ), array(
				'hide_empty' => true,
			) );
		}

		$columns = self::get_home_columns($args['home_layout']);
		$view_mode = $args['home_view'];
		$row_open = false;

		if ($args['topics_title']) :
			?>
			<div class="mkb-container mkb-section-title">
				<?php echo esc_html($settings['topics_title']); ?>
			</div>
		<?php
		endif;
		?>
		<div class="mkb-home-topics mkb-container mkb-columns mkb-columns-<?php echo esc_attr($columns); ?>">
			<?php

			if ( sizeof( $topics ) ):
				$i = 0;

				foreach ( $topics as $topic ):

					if ($i % $columns === 0):
						echo '<div class="mkb-row">';
						$row_open = true;
					endif;

					if ($view_mode === 'list'):
						self::render_as_list($topic, $args);
					else:
						self::render_as_box($topic, $args);
					endif;

					if ( ($i + 1) % $columns === 0 ):
						echo '</div >';
						$row_open = false;
					endif;

					++$i;
				endforeach; // end of terms loop

				if ( $row_open ):
					echo '</div >';
					$row_open = false;
				endif;

			endif; // end of topics loop
			?>
		</div>
		<?php
	}

	/**
	 * Render topic as articles list
	 * @param $term
	 */
	public static function render_as_list($term, $settings = array()) {

		if (!$term) {
			return;
		}

		$topic_link = self::get_term_link( $term );

		$args = wp_parse_args(
			$settings,
			array(
				"show_articles_count" => MKB_Options::option( 'show_articles_count' ),
				"show_all_switch" => MKB_Options::option( 'show_all_switch' ),
				"show_all_label" => MKB_Options::option( 'show_all_label' ),
				"home_topics_articles_limit" => MKB_Options::option( 'home_topics_articles_limit' ),
				"articles_count_bg" => MKB_Options::option( 'articles_count_bg' ),
				"articles_count_color" => MKB_Options::option( 'articles_count_color' ),
				"show_topic_icons" => MKB_Options::option( 'show_topic_icons' ),
				"show_article_icons" => MKB_Options::option( 'show_article_icons' ),
				"article_icon" => MKB_Options::option( 'article_icon' ),
				"topic_color" => MKB_Options::option( 'topic_color' ),
				"topic_icon" => MKB_Options::option( 'topic_icon' ),
			)
		);

		$loop = self::get_term_items_loop($term, $args);

		$count_style = 'background: ' . $args['articles_count_bg'] . '; color: ' . $args['articles_count_color'] . ';';

		$topic_color = self::get_topic_color( $term, $args );

		?>
		<section class="kb-topic">
			<div class="kb-topic__inner">
				<h3 class="kb-topic__title" <?php
				if ( $topic_color ) { echo 'style="color: ' . esc_attr( $topic_color ) . ';"'; }
				?>>
					<?php if($topic_link && $topic_link!= '#'): ?>
						<a class="kb-topic__title-link" href="<?php echo esc_attr( $topic_link ); ?>" <?php
							if ( $topic_color ) { echo 'style="color: ' . esc_attr( $topic_color ) . ';"'; }
						?>>
					<?php endif; ?>
						<?php if ( isset($args['show_topic_icons']) && $args['show_topic_icons'] ): ?>
							<span class="kb-topic__title-icon">
								<i class="kb-topic__list-icon fa <?php echo esc_attr( self::get_topic_icon( $term, $args ) ); ?>"></i>
							</span>
						<?php endif; ?>

						<?php echo esc_html( self::get_term_name($term) ); ?>

						<?php if ( isset($args['show_articles_count']) && $args['show_articles_count'] ): ?>
							<span class="kb-topic__count" style="<?php echo esc_attr($count_style); ?>">
								<?php
								$post_count = self::get_term_post_count($term, $loop);
									echo esc_html($post_count); ?> <?php echo esc_html($post_count === 1 ?
										MKB_Options::option( 'article_text' ) :
										MKB_Options::option( 'articles_text' )
									);
								?>
							</span>
						<?php endif; ?>
				<?php if($topic_link && $topic_link!= '#'): ?>
					</a>
				<?php endif; ?>
				</h3>

				<div class="kb-topic__articles <?php if (isset($args['show_article_icons']) && $args['show_article_icons']):
					echo ' kb-topic__articles--with-icons';
				endif; ?>">
					<ul>
						<?php

						if ( $loop->have_posts() ) :
							while ( $loop->have_posts() ) : $loop->the_post(); ?>
								<li>
									<a href="<?php echo esc_attr( get_the_permalink() ); ?>">
										<?php if(isset($args['show_article_icons']) && $args['show_article_icons']): ?>
										<i class="kb-topic__list-article-icon fa <?php echo esc_attr( $args['article_icon'] ); ?>"></i>
										<?php endif; ?>
										<span class="kb-topic__list-article-title"><?php echo esc_html(get_the_title()); ?></span>
									</a>
								</li>
							<?php endwhile;
						endif;
						wp_reset_postdata();
						?>
					</ul>
					<?php if ( $args['show_all_switch'] ): ?>
						<a class="kb-topic__show-all"
						   href="<?php echo esc_attr( $topic_link ); ?>">
							<?php echo esc_html($args['show_all_label']); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</section>
	<?php
	}

	/**
	 * Render topic as articles boxes
	 * @param $term
	 */
	public static function render_as_box($term, $settings = array()) {

		if (!$term) {
			return;
		}

		$topic_link = self::get_term_link( $term );

		$args = wp_parse_args(
			$settings,
			array(
				"show_articles_count" => MKB_Options::option( 'show_articles_count' ),
				"show_all_switch" => MKB_Options::option( 'show_all_switch' ),
				"show_all_label" => MKB_Options::option( 'show_all_label' ),
				"home_topics_articles_limit" => MKB_Options::option( 'home_topics_articles_limit' ),
				"articles_count_bg" => MKB_Options::option( 'articles_count_bg' ),
				"articles_count_color" => MKB_Options::option( 'articles_count_color' ),
				"show_topic_icons" => MKB_Options::option( 'show_topic_icons' ),
				"show_article_icons" => MKB_Options::option( 'show_article_icons' ),
				"article_icon" => MKB_Options::option( 'article_icon' ),
				"topic_color" => MKB_Options::option( 'topic_color' ),
				"force_default_topic_color" => MKB_Options::option( 'force_default_topic_color' ),
				"topic_icon" => MKB_Options::option( 'topic_icon' ),
				"use_topic_image" => MKB_Options::option( 'use_topic_image' ),
				"image_size" => MKB_Options::option( 'image_size' ),
				"topic_icon_padding_top" => MKB_Options::option( 'topic_icon_padding_top' ),
				"topic_icon_padding_bottom" => MKB_Options::option( 'topic_icon_padding_bottom' ),
			)
		);

		$loop = self::get_term_items_loop($term, $args);

		$topic_color = self::get_topic_color( $term, $args );

		?>
		<section class="kb-topic kb-topic--box-view">
			<a href="<?php echo esc_attr( $topic_link ); ?>">
				<div class="kb-topic__inner">
					<header class="kb-topic__box-header" <?php
					if ( $topic_color ) { echo 'style="color: ' . esc_attr( $topic_color ) . ';"'; }
					?>>
						<?php if (isset($args['show_topic_icons']) && $args['show_topic_icons']): ?>
						<div class="kb-topic__icon-holder">
							<i class="kb-topic__box-icon fa <?php echo esc_attr( self::get_topic_icon( $term, $args ) ); ?>"></i>
						</div>
						<?php endif; ?>
						<h3 class="kb-topic__title" <?php
						if ( $topic_color ) { echo 'style="color: ' . esc_attr( $topic_color ) . ';"'; }
						?>>
							<?php echo esc_html( self::get_term_name($term) ); ?>
						</h3>
					</header>

					<div class="kb-topic__articles">
						<?php if ( self::get_term_description($term) ): ?>
							<div class="kb-topic__description">
								<?php echo esc_html( self::get_term_description($term) ); ?>
							</div>
						<?php endif; ?>
						<?php if ( isset($args['show_articles_count']) && $args['show_articles_count'] ): ?>
							<div class="kb-topic__box-count">
								<?php $post_count = self::get_term_post_count($term, $loop);
									echo esc_html($post_count); ?> <?php
									echo esc_html($post_count === 1 ?
										MKB_Options::option( 'article_text' ) :
										MKB_Options::option( 'articles_text' )
									);
								?>
							</div>
						<?php endif; ?>
						<?php if ( $args['show_all_switch'] ): ?>
						<div class="kb-topic__show-all">
							<?php echo esc_html($args['show_all_label']); ?>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</a>
		</section>
	<?php
		wp_reset_postdata();
	}

	protected static function get_term_post_count($term, $loop) {
		return $loop->found_posts;
	}

	protected static function get_term_name($term) {
		return $term->name;
	}

	protected static function get_term_description($term) {
		return $term->description;
	}

	protected static function get_term_items_loop($term, $options) {
		$query_args = array(
			'post_type' => MKB_Options::option( 'article_cpt' ),
			'posts_per_page' => isset($options['home_topics_articles_limit']) ?
				$options['home_topics_articles_limit'] :
				5,
			'ignore_sticky_posts' => 1
		);

		if (isset($term->slug)) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => MKB_Options::option( 'article_cpt_category' ),
					'field'    => 'slug',
					'terms'    => $term->slug,
					'include_children' => false
				),
			);
		}

		$loop = new WP_Query( $query_args );

		return $loop;
	}

	protected static function get_term_link($term) {
		return get_term_link($term);
	}

	public static function render_search($settings = array()) {
		$args = wp_parse_args(
			$settings,
			array(
				"search_title" => MKB_Options::option( 'search_title' ),
				"search_placeholder" => MKB_Options::option( 'search_placeholder' ),
				"show_search_icon" => MKB_Options::option( 'show_search_icon' ),
				"search_icon" => MKB_Options::option( 'search_icon' ),
				"search_clear_icon" => MKB_Options::option( 'search_clear_icon' ),
				"search_clear_icon_tooltip" => MKB_Options::option( 'search_clear_icon_tooltip' ),
				"show_search_tip" => MKB_Options::option( 'show_search_tip' ),
				"disable_autofocus" => MKB_Options::option( 'disable_autofocus' ),
				"search_tip" => MKB_Options::option( 'search_tip' ),
				"show_topic_in_results" => MKB_Options::option( 'show_topic_in_results' ),
				"search_result_topic_label" => MKB_Options::option( 'search_result_topic_label' )
			)
		);

		$input_wrap_extra_class = 'mkb-search-theme__minerva';

		?><header class="kb-header">
			<div class="kb-search">
				<?php if (isset($args["search_title"]) && $args["search_title"]): ?>
				<div class="kb-search__title">
					<?php echo esc_html($args["search_title"]); ?>
				</div>
				<?php endif; ?>
				<form class="kb-search__form" action="<?php echo site_url('/'); ?>" method="get" novalidate>
					<div class="kb-search__input-wrap <?php echo esc_attr($input_wrap_extra_class); ?>">
						<input type="hidden" name="source" value="kb" />
						<input class="kb-search__input"
						       name="s"
						       placeholder="<?php echo esc_attr( $args['search_placeholder'] ); ?>"
						       type="text"
						       data-show-results-topic="<?php echo esc_attr($args['show_topic_in_results']); ?>"
						       data-topic-label="<?php echo esc_attr($args['search_result_topic_label']); ?>"
						       data-autofocus="<?php echo esc_attr($args['disable_autofocus'] ? '0' : '1'); ?>"
							/>
						<span class="kb-search__results-summary"></span>
						<?php if ( $args['show_search_icon'] ): ?>
							<span class="kb-search__icon-holder">
								<i class="kb-search__icon fa <?php echo esc_attr( $args['search_icon'] ); ?>"></i>
							</span>
						<?php endif; ?>
						<a href="#" class="kb-search__clear" title="<?php echo esc_attr($args['search_clear_icon_tooltip']); ?>">
							<i class="kb-search__clear-icon fa <?php echo esc_attr( $args['search_clear_icon'] ); ?>"></i>
						</a>

						<div class="kb-search__results<?php if ($args['show_topic_in_results'] == 1) {
							echo esc_attr(' kb-search__results--with-topics');
						}?>"></div>
					</div>
					<?php if($args['show_search_tip']): ?>
					<div class="kb-search__tip">
						<?php esc_html_e( $args['search_tip'] ); ?>
					</div>
					<?php endif; ?>
				</form>
			</div>
		</header><?php
	}

	/**
	 * Renders home page content
	 */
	public static function home_content() {
		global $minerva_kb;

		if ( $minerva_kb->info->is_settings_home() ) {
			if ( MKB_Options::option( 'show_page_content' ) === 'before' ) {
				the_content();
			}

			include( MINERVA_KB_PLUGIN_DIR . '/lib/templates/kb-home.php' );

			if ( MKB_Options::option( 'show_page_content' ) === 'after' ) {
				the_content();
			}
		}
	}

	/**
	 * Renders article content
	 */
	public static function single_content() {

		self::single_header_meta();

		?><div class="mkb-article-text"><?php

		the_content();

		?></div><?php

		self::single_footer_meta();
	}

	/**
	 * Single header meta
	 */
	public static function single_header_meta() {
		?>
		<div class="mkb-article-header">
			<?php

			do_action('minerva_single_entry_header_meta');

			?>
		</div>
	<?php
	}

	/**
	 * Single footer meta
	 */
	public static function single_footer_meta() {
		?>
		<div class="mkb-article-extra">
			<div class="mkb-article-extra__hidden">
				<span class="mkb-article-extra__tracking-data"
				      data-article-id="<?php echo esc_attr( get_the_ID() ); ?>"
				      data-article-title="<?php echo esc_attr( get_the_title() ); ?>"></span>
			</div>
			<?php

			do_action('minerva_single_entry_footer_meta');

			?>
		</div>
	<?php
	}

	/**
	 * Default WP pagination
	 */
	public static function theme_pagination() {
		the_posts_pagination( array(
			'prev_text' => __( 'Previous', 'minerva-kb' ),
			'next_text' => __( 'Next', 'minerva-kb' ),
		) );
	}

	/**
	 * Pagination for search results page
	 */
	public static function pagination () {
		self::theme_pagination();
	}

	/**
	 * Home page columns layout
	 * @param $home_layout
	 * @return int
	 */
	public static function get_home_columns($home_layout) {
		$columns = 2;

		switch ($home_layout) {
			case '2col':
				$columns = 2;
				break;

			case '3col':
				$columns = 3;
				break;

			case '4col':
				$columns = 4;
				break;

			default:
				break;

		}

		return $columns;
	}

	public static function get_topic_children_columns() {
		$columns = 2;

		$home_layout = MKB_Options::option('topic_children_layout');

		switch ($home_layout) {
			case '2col':
				$columns = 2;
				break;

			case '3col':
				$columns = 3;
				break;

			case '4col':
				$columns = 4;
				break;

			default:
				break;

		}

		return $columns;
	}


	public static function get_columns($value) {
		$columns = 2;

		$layout = $value;

		switch ($layout) {
			case '2col':
				$columns = 2;
				break;

			case '3col':
				$columns = 3;
				break;

			case '4col':
				$columns = 4;
				break;

			default:
				break;

		}

		return $columns;
	}

	protected function hextorgb($hex, $alpha = false) {
		$hex = str_replace( '#', '', $hex );

		if ( strlen( $hex ) == 6 ) {
			$rgb['r'] = hexdec( substr( $hex, 0, 2 ) );
			$rgb['g'] = hexdec( substr( $hex, 2, 2 ) );
			$rgb['b'] = hexdec( substr( $hex, 4, 2 ) );
		} else if ( strlen( $hex ) == 3 ) {
			$rgb['r'] = hexdec( str_repeat( substr( $hex, 0, 1 ), 2 ) );
			$rgb['g'] = hexdec( str_repeat( substr( $hex, 1, 1 ), 2 ) );
			$rgb['b'] = hexdec( str_repeat( substr( $hex, 2, 1 ), 2 ) );
		} else {
			$rgb['r'] = '0';
			$rgb['g'] = '0';
			$rgb['b'] = '0';
		}
		if ( $alpha ) {
			$rgb['a'] = $alpha;
		}

		return $rgb;
	}
}