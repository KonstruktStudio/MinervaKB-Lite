<?php
/**
 * Project: Minerva KB Lite
 * Copyright: 2015-2017 @KonstruktStudio
 */


class MKB_Options {

	const OPTION_KEY = 'minerva-kb-options';

	public function __construct() {
		self::register();
	}

	public static function register() {
		add_option( self::OPTION_KEY, '{}');

		add_option('_mkb_flushed_rewrite_cpt', '');
		add_option('_mkb_flushed_rewrite_topic', '');
		add_option('_mkb_flushed_rewrite_tag', '');
	}

	public static function apply_defaults() {
		$data = self::get();

		self::save(wp_parse_args($data, self::get_options_defaults()));
	}

	public static function get_options_defaults() {
		return array_reduce(self::get_non_ui_options(), function($defaults, $option) {
			$defaults[$option["id"]] = $option["default"];
			return $defaults;
		}, array());
	}

	public static function get_options() {

		return array(
			/**
			 * Home page
			 */
			array(
				'id' => 'home_tab',
				'type' => 'tab',
				'label' => __( 'Home page', 'minerva-kb' ),
				'icon' => 'fa-home'
			),
			array(
				'id' => 'home_content_title',
				'type' => 'title',
				'label' => __( 'Home page content', 'minerva-kb' ),
				'description' => __( 'Configure the content to display on home KB page', 'minerva-kb' )
			),
			array(
				'id' => 'kb_page',
				'type' => 'page_select',
				'label' => __( 'Select page to display KB content', 'minerva-kb' ),
				'options' => self::get_pages_options(),
				'default' => '',
				'description' => __( 'Don\'t forget to save settings before page preview', 'minerva-kb' )
			),
			array(
				'id' => 'show_page_content',
				'type' => 'select',
				'label' => __( 'Show page content?', 'minerva-kb' ),
				'options' => array(
					'no' => __( 'No', 'minerva-kb' ),
					'before' => __( 'Before KB', 'minerva-kb' ),
					'after' => __( 'After KB', 'minerva-kb' )
				),
				'default' => 'no'
			),
			array(
				'id' => 'home_view',
				'type' => 'image_select',
				'label' => __( 'Home view', 'minerva-kb' ),
				'options' => array(
					'list' => array(
						'label' => __( 'List view', 'minerva-kb' ),
						'img' => MINERVA_KB_IMG_URL . 'list-view.png'
					),
					'box' => array(
						'label' => __( 'Box view', 'minerva-kb' ),
						'img' => MINERVA_KB_IMG_URL . 'box-view.png'
					)
				),
				'default' => 'list'
			),
			array(
				'id' => 'home_layout',
				'type' => 'image_select',
				'label' => __( 'Page layout', 'minerva-kb' ),
				'options' => array(
					'2col' => array(
						'label' => __( '2 columns', 'minerva-kb' ),
						'img' => MINERVA_KB_IMG_URL . 'col-2.png'
					),
					'3col' => array(
						'label' => __( '3 columns', 'minerva-kb' ),
						'img' => MINERVA_KB_IMG_URL . 'col-3.png'
					),
					'4col' => array(
						'label' => __( '4 columns', 'minerva-kb' ),
						'img' => MINERVA_KB_IMG_URL . 'col-4.png'
					),
				),
				'default' => '3col'
			),
			array(
				'id' => 'home_topics',
				'type' => 'layout_select',
				'label' => __( 'Select topics to display on home page', 'minerva-kb' ),
				'default' => '',
				'options' => self::get_topics_options(),
				'description' => __( 'You can leave it empty to display all recent topics. NOTE: dynamic topics only work for list view', 'minerva-kb' )
			),
			array(
				'id' => 'home_topics_articles_limit',
				'type' => 'input',
				'label' => __( 'Number of article to display', 'minerva-kb' ),
				'default' => 5,
				'description' => __( 'You can use -1 to display all', 'minerva-kb' )
			),
			array(
				'id' => 'show_all_switch',
				'type' => 'checkbox',
				'label' => __( 'Add "Show all" link?', 'minerva-kb' ),
				'default' => true
			),
			array(
				'id' => 'page_sidebar',
				'type' => 'image_select',
				'label' => __( 'Page sidebar position', 'minerva-kb' ),
				'options' => array(
					'none' => array(
						'label' => __( 'None', 'minerva-kb' ),
						'img' => MINERVA_KB_IMG_URL . 'no-sidebar.png'
					),
					'left' => array(
						'label' => __( 'Left', 'minerva-kb' ),
						'img' => MINERVA_KB_IMG_URL . 'left-sidebar.png'
					),
					'right' => array(
						'label' => __( 'Right', 'minerva-kb' ),
						'img' => MINERVA_KB_IMG_URL . 'right-sidebar.png'
					),
				),
				'default' => 'none'
			),
			array(
				'id' => 'show_articles_count',
				'type' => 'checkbox',
				'label' => __( 'Show articles count?', 'minerva-kb' ),
				'default' => true
			),

			// ICONS
			array(
				'id' => 'home_topic_icons_title',
				'type' => 'title',
				'label' => __( 'Topic icons', 'minerva-kb' ),
				'description' => __( 'Configure topic icons settings', 'minerva-kb' )
			),
			array(
				'id' => 'show_topic_icons',
				'type' => 'checkbox',
				'label' => __( 'Show topic icons?', 'minerva-kb' ),
				'default' => true
			),
			array(
				'id' => 'topic_icon',
				'type' => 'icon_select',
				'label' => __( 'Default topic icon', 'minerva-kb' ),
				'default' => 'fa-list-alt',
				'description' => __( 'Note, that topic icon can be changed for each topic individually on topic edit page', 'minerva-kb' ),
				'dependency' => array(
					'target' => 'show_topic_icons',
					'type' => 'EQ',
					'value' => true
				)
			),
			array(
				'id' => 'show_minerva_link_switch',
				'type' => 'checkbox',
				'label' => __( 'Help us spread the word! Add a tiny styled "MinervaKB" link under KB content', 'minerva-kb' ),
				'default' => false,
				'description' => __( 'This plugin is free, but it uses many features from commercial version, tested on many real sites. In order to continue to update and support free version, we need any little help we can get to promote it. If you find this plugin helpful, please enable this option. Thank you! :)', 'minerva-kb' ),
			),

			// ARTICLES
			array(
				'id' => 'home_articles_title',
				'type' => 'title',
				'label' => __( 'Articles settings', 'minerva-kb' ),
				'description' => __( 'Configure how articles list should look on home KB page', 'minerva-kb' )
			),
			array(
				'id' => 'show_article_icons',
				'type' => 'checkbox',
				'label' => __( 'Show article icons?', 'minerva-kb' ),
				'default' => true
			),
			array(
				'id' => 'article_icon',
				'type' => 'icon_select',
				'label' => __( 'Article icon', 'minerva-kb' ),
				'default' => 'fa-book',
				'dependency' => array(
					'target' => 'show_article_icons',
					'type' => 'EQ',
					'value' => true
				)
			),
			/**
			 * Search home
			 */
			array(
				'id' => 'search_home_tab',
				'type' => 'tab',
				'label' => __( 'Home Search', 'minerva-kb' ),
				'icon' => 'fa-search'
			),
			array(
				'id' => 'search_title',
				'type' => 'input',
				'label' => __( 'Search title', 'minerva-kb' ),
				'default' => __( 'Need some help?', 'minerva-kb' )
			),
			array(
				'id' => 'search_placeholder',
				'type' => 'input',
				'label' => __( 'Search placeholder', 'minerva-kb' ),
				'default' => __( 'ex.: Installation', 'minerva-kb' )
			),
			array(
				'id' => 'disable_autofocus',
				'type' => 'checkbox',
				'label' => __( 'Disable search field autofocus?', 'minerva-kb' ),
				'default' => false
			),
			array(
				'id' => 'show_search_tip',
				'type' => 'checkbox',
				'label' => __( 'Show search tip?', 'minerva-kb' ),
				'default' => true
			),
			array(
				'id' => 'search_tip',
				'type' => 'input',
				'label' => __( 'Search tip (under the input)', 'minerva-kb' ),
				'default' => __( 'Tip: Use arrows to navigate results, ESC to focus search input', 'minerva-kb' ),
				'dependency' => array(
					'target' => 'show_search_tip',
					'type' => 'EQ',
					'value' => true
				)
			),
			array(
				'id' => 'show_topic_in_results',
				'type' => 'checkbox',
				'label' => __( 'Show topic in results?', 'minerva-kb' ),
				'default' => true
			),
			array(
				'id' => 'search_result_topic_label',
				'type' => 'input',
				'label' => __( 'Search result topic label', 'minerva-kb' ),
				'default' => __( 'Topic', 'minerva-kb' ),
				'dependency' => array(
					'target' => 'show_topic_in_results',
					'type' => 'EQ',
					'value' => true
				)
			),
			// ICONS
			array(
				'id' => 'home_search_icons_title',
				'type' => 'title',
				'label' => __( 'Search icons', 'minerva-kb' ),
				'description' => __( 'Configure search icons', 'minerva-kb' )
			),
			array(
				'id' => 'show_search_icon',
				'type' => 'checkbox',
				'label' => __( 'Show search icon?', 'minerva-kb' ),
				'default' => true
			),
			array(
				'id' => 'search_icon',
				'type' => 'icon_select',
				'label' => __( 'Search icon', 'minerva-kb' ),
				'default' => 'fa-search',
				'dependency' => array(
					'target' => 'show_search_icon',
					'type' => 'EQ',
					'value' => true
				)
			),
			array(
				'id' => 'search_clear_icon',
				'type' => 'icon_select',
				'label' => __( 'Search clear icon', 'minerva-kb' ),
				'default' => 'fa-times-circle'
			),

			/**
			 * Styles
			 */
			array(
				'id' => 'styles_tab',
				'type' => 'tab',
				'label' => __( 'Styles', 'minerva-kb' ),
				'icon' => 'fa-paint-brush'
			),
			array(
				'id' => 'text_color',
				'type' => 'color',
				'label' => __( 'Article text color', 'minerva-kb' ),
				'default' => '#333'
			),
			array(
				'id' => 'text_link_color',
				'type' => 'color',
				'label' => __( 'Article text link color', 'minerva-kb' ),
				'default' => '#007acc'
			),
			array(
				'id' => 'search_results_topic_bg',
				'type' => 'color',
				'label' => __( 'Search results topic background', 'minerva-kb' ),
				'default' => '#4a90e2'
			),
			array(
				'id' => 'search_results_topic_color',
				'type' => 'color',
				'label' => __( 'Search results topic color', 'minerva-kb' ),
				'default' => '#ffffff'
			),
			array(
				'id' => 'topic_color',
				'type' => 'color',
				'label' => __( 'Default topic color', 'minerva-kb' ),
				'default' => '#4a90e2'
			),
			array(
				'id' => 'box_view_item_bg',
				'type' => 'color',
				'label' => __( 'Box view items background', 'minerva-kb' ),
				'default' => '#ffffff'
			),
			array(
				'id' => 'box_view_item_hover_bg',
				'type' => 'color',
				'label' => __( 'Box view items hover background', 'minerva-kb' ),
				'default' => '#f8f8f8'
			),
			array(
				'id' => 'articles_count_bg',
				'type' => 'color',
				'label' => __( 'List view articles count background', 'minerva-kb' ),
				'default' => '#4a90e2'
			),
			array(
				'id' => 'articles_count_color',
				'type' => 'color',
				'label' => __( 'List view articles count color', 'minerva-kb' ),
				'default' => '#ffffff'
			),

			/**
			 * Post type
			 */
			array(
				'id' => 'cpt_tab',
				'type' => 'tab',
				'label' => __( 'Post type', 'minerva-kb' ),
				'icon' => 'fa-address-card-o'
			),
			// cpt
			array(
				'id' => 'article_cpt_title',
				'type' => 'title',
				'label' => __( 'Article post type and URL', 'minerva-kb' ),
				'description' => __( 'Configure WordPress post type name and URL slug for KB articles', 'minerva-kb' )
			),
			array(
				'id' => 'article_cpt',
				'type' => 'input',
				'label' => __( 'Article post type name', 'minerva-kb' ),
				'default' => 'kb',
				'description' => __( 'Use only lowercase letters. Note, that if you have already added articles changing this setting will make them invisible.', 'minerva-kb' )
			),
			array(
				'id' => 'article_cpt_category',
				'type' => 'input',
				'label' => __( 'Article category taxonomy name', 'minerva-kb' ),
				'default' => 'kbtopic',
				'description' => __( 'Use only lowercase letters. Do not use "category", as it is reserved for standard posts. Note, that if you have already added topics changing this setting will make them invisible.', 'minerva-kb' )
			),
			array(
				'id' => 'article_cpt_tag',
				'type' => 'input',
				'label' => __( 'Article tag name', 'minerva-kb' ),
				'default' => 'kbtag',
				'description' => __( 'Use only lowercase letters. Do not use "tag", as it is reserved for standard posts. Note, that if you have already added tags changing this setting will make them invisible.', 'minerva-kb' )
			),

			/**
			 * Search global
			 */
			array(
				'id' => 'search_global_tab',
				'type' => 'tab',
				'label' => __( 'Search (global)', 'minerva-kb' ),
				'icon' => 'fa-search'
			),
			/**
			 * Search results page
			 */
			array(
				'id' => 'search_results_title',
				'type' => 'title',
				'label' => __( 'Search results page settings', 'minerva-kb' ),
				'description' => __( 'Configure appearance and display mode of search results page', 'minerva-kb' )
			),
			array(
				'id' => 'search_sidebar',
				'type' => 'image_select',
				'label' => __( 'Search results page sidebar position', 'minerva-kb' ),
				'options' => array(
					'none' => array(
						'label' => __( 'None', 'minerva-kb' ),
						'img' => MINERVA_KB_IMG_URL . 'no-sidebar.png'
					),
					'left' => array(
						'label' => __( 'Left', 'minerva-kb' ),
						'img' => MINERVA_KB_IMG_URL . 'left-sidebar.png'
					),
					'right' => array(
						'label' => __( 'Right', 'minerva-kb' ),
						'img' => MINERVA_KB_IMG_URL . 'right-sidebar.png'
					),
				),
				'default' => 'right'
			),
			array(
				'id' => 'search_results_per_page',
				'type' => 'input',
				'label' => __( 'Number of search results per page. Use -1 to show all', 'minerva-kb' ),
				'default' => __( '10', 'minerva-kb' )
			),
			array(
				'id' => 'show_breadcrumbs_search',
				'type' => 'checkbox',
				'label' => __( 'Show breadcrumbs on search results page?', 'minerva-kb' ),
				'default' => true,
				'description' => __( 'Enable/disable breadcrumbs for search results page', 'minerva-kb' ),
			),
			array(
				'id' => 'search_results_breadcrumbs_label',
				'type' => 'input',
				'label' => __( 'Breadcrumbs label', 'minerva-kb' ),
				'default' => __( 'Search results for %s', 'minerva-kb' ),
				'description' => __( '%s will be replaced with search term', 'minerva-kb' ),
			),
			array(
				'id' => 'search_results_page_title',
				'type' => 'input',
				'label' => __( 'Search page title', 'minerva-kb' ),
				'default' => __( 'Found %s results for: %s', 'minerva-kb' ),
				'description' => __( '%s will be replaced with number of results and search term', 'minerva-kb' ),
			),
			array(
				'id' => 'search_no_results_title',
				'type' => 'input',
				'label' => __( 'Search no results page title', 'minerva-kb' ),
				'default' => __( 'Nothing Found', 'minerva-kb' )
			),
			array(
				'id' => 'search_no_results_subtitle',
				'type' => 'input',
				'label' => __( 'Search no results page subtitle', 'minerva-kb' ),
				'default' => __( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'minerva-kb' )
			),

			/**
			 * Article
			 */
			array(
				'id' => 'single_tab',
				'type' => 'tab',
				'label' => __( 'Article', 'minerva-kb' ),
				'icon' => 'fa-file-text-o'
			),
			array(
				'id' => 'show_reading_estimate',
				'type' => 'checkbox',
				'label' => __( 'Show estimated reading time?', 'minerva-kb' ),
				'default' => true
			),
			array(
				'id' => 'estimated_time_text',
				'type' => 'input',
				'label' => __( 'Estimated reading time text', 'minerva-kb' ),
				'default' => __( 'Estimated reading time:', 'minerva-kb' ),
				'dependency' => array(
					'target' => 'show_reading_estimate',
					'type' => 'EQ',
					'value' => true
				)
			),
			array(
				'id' => 'estimated_time_less_than_min',
				'type' => 'input',
				'label' => __( 'Estimated reading less than 1 minute text', 'minerva-kb' ),
				'default' => __( '< 1 min', 'minerva-kb' ),
				'dependency' => array(
					'target' => 'show_reading_estimate',
					'type' => 'EQ',
					'value' => true
				)
			),
			array(
				'id' => 'estimated_time_min',
				'type' => 'input',
				'label' => __( 'Estimated reading minute text', 'minerva-kb' ),
				'default' => __( 'min', 'minerva-kb' ),
				'dependency' => array(
					'target' => 'show_reading_estimate',
					'type' => 'EQ',
					'value' => true
				)
			),
			array(
				'id' => 'estimated_time_icon',
				'type' => 'icon_select',
				'label' => __( 'Estimated time icon', 'minerva-kb' ),
				'default' => 'fa-clock-o',
				'dependency' => array(
					'target' => 'show_reading_estimate',
					'type' => 'EQ',
					'value' => true
				)
			),
			array(
				'id' => 'show_pageviews',
				'type' => 'checkbox',
				'label' => __( 'Show pageviews count?', 'minerva-kb' ),
				'default' => true
			),
			array(
				'id' => 'pageviews_label',
				'type' => 'input',
				'label' => __( 'Views label', 'minerva-kb' ),
				'default' => __( 'Views:', 'minerva-kb' ),
				'dependency' => array(
					'target' => 'show_pageviews',
					'type' => 'EQ',
					'value' => true
				)
			),
			array(
				'id' => 'article_sidebar',
				'type' => 'image_select',
				'label' => __( 'Article sidebar position', 'minerva-kb' ),
				'options' => array(
					'none' => array(
						'label' => __( 'None', 'minerva-kb' ),
						'img' => MINERVA_KB_IMG_URL . 'no-sidebar.png'
					),
					'left' => array(
						'label' => __( 'Left', 'minerva-kb' ),
						'img' => MINERVA_KB_IMG_URL . 'left-sidebar.png'
					),
					'right' => array(
						'label' => __( 'Right', 'minerva-kb' ),
						'img' => MINERVA_KB_IMG_URL . 'right-sidebar.png'
					),
				),
				'default' => 'right'
			),
			array(
				'id' => 'show_article_tags',
				'type' => 'checkbox',
				'label' => __( 'Show article tags?', 'minerva-kb' ),
				'default' => false
			),
			array(
				'id' => 'show_article_tags_icon',
				'type' => 'checkbox',
				'label' => __( 'Show article tags icon?', 'minerva-kb' ),
				'default' => true,
				'dependency' => array(
					'target' => 'show_article_tags',
					'type' => 'EQ',
					'value' => true
				)
			),
			array(
				'id' => 'article_tags_icon',
				'type' => 'icon_select',
				'label' => __( 'Article tags icon', 'minerva-kb' ),
				'default' => 'fa-tag',
				'dependency' => array(
					'target' => 'show_article_tags',
					'type' => 'EQ',
					'value' => true
				)
			),
			array(
				'id' => 'article_tags_label',
				'type' => 'input',
				'label' => __( 'Tags label', 'minerva-kb' ),
				'default' => __( 'Tags:', 'minerva-kb' ),
				'description' => __( 'Set this field empty to remove text label', 'minerva-kb' ),
				'dependency' => array(
					'target' => 'show_article_tags',
					'type' => 'EQ',
					'value' => true
				)
			),

			/**
			 * Topics
			 */
			array(
				'id' => 'topic_tab',
				'type' => 'tab',
				'label' => __( 'Topics', 'minerva-kb' ),
				'icon' => 'fa-address-book-o'
			),
			array(
				'id' => 'topic_articles_per_page',
				'type' => 'input',
				'label' => __( 'Number of articles per page. Use -1 to show all', 'minerva-kb' ),
				'default' => __( '10', 'minerva-kb' )
			),
			array(
				'id' => 'topic_sidebar',
				'type' => 'image_select',
				'label' => __( 'Topic sidebar position', 'minerva-kb' ),
				'options' => array(
					'none' => array(
						'label' => __( 'None', 'minerva-kb' ),
						'img' => MINERVA_KB_IMG_URL . 'no-sidebar.png'
					),
					'left' => array(
						'label' => __( 'Left', 'minerva-kb' ),
						'img' => MINERVA_KB_IMG_URL . 'left-sidebar.png'
					),
					'right' => array(
						'label' => __( 'Right', 'minerva-kb' ),
						'img' => MINERVA_KB_IMG_URL . 'right-sidebar.png'
					),
				),
				'default' => 'right'
			),
			array(
				'id' => 'topic_children_layout',
				'type' => 'image_select',
				'label' => __( 'Sub-topics', 'minerva-kb' ),
				'options' => array(
					'2col' => array(
						'label' => __( '2 columns', 'minerva-kb' ),
						'img' => MINERVA_KB_IMG_URL . 'col-2.png'
					),
					'3col' => array(
						'label' => __( '3 columns', 'minerva-kb' ),
						'img' => MINERVA_KB_IMG_URL . 'col-3.png'
					),
					'4col' => array(
						'label' => __( '4 columns', 'minerva-kb' ),
						'img' => MINERVA_KB_IMG_URL . 'col-4.png'
					),
				),
				'default' => '2col'
			),
			array(
				'id' => 'topic_children_view',
				'type' => 'image_select',
				'label' => __( 'Sub-topics view', 'minerva-kb' ),
				'options' => array(
					'list' => array(
						'label' => __( 'List view', 'minerva-kb' ),
						'img' => MINERVA_KB_IMG_URL . 'list-view.png'
					),
					'box' => array(
						'label' => __( 'Box view', 'minerva-kb' ),
						'img' => MINERVA_KB_IMG_URL . 'box-view.png'
					)
				),
				'default' => 'box'
			),
			array(
				'id' => 'topic_no_results_subtitle',
				'type' => 'input',
				'label' => __( 'Text to display for empty archives', 'minerva-kb' ),
				'default' => __( 'We can&rsquo;t find what you&rsquo;re looking for. Try searching maybe.', 'minerva-kb' )
			),
			/**
			 * Tags
			 */
			array(
				'id' => 'tags_tab',
				'type' => 'tab',
				'label' => __( 'Tags', 'minerva-kb' ),
				'icon' => 'fa-tags'
			),
			array(
				'id' => 'tag_articles_per_page',
				'type' => 'input',
				'label' => __( 'Number of articles per tag page. Use -1 to show all', 'minerva-kb' ),
				'default' => __( '10', 'minerva-kb' )
			),
			array(
				'id' => 'tag_sidebar',
				'type' => 'image_select',
				'label' => __( 'Tag sidebar position', 'minerva-kb' ),
				'options' => array(
					'none' => array(
						'label' => __( 'None', 'minerva-kb' ),
						'img' => MINERVA_KB_IMG_URL . 'no-sidebar.png'
					),
					'left' => array(
						'label' => __( 'Left', 'minerva-kb' ),
						'img' => MINERVA_KB_IMG_URL . 'left-sidebar.png'
					),
					'right' => array(
						'label' => __( 'Right', 'minerva-kb' ),
						'img' => MINERVA_KB_IMG_URL . 'right-sidebar.png'
					),
				),
				'default' => 'right'
			),
			/**
			 * Breadcrumbs
			 */
			array(
				'id' => 'breadcrumbs_tab',
				'type' => 'tab',
				'label' => __( 'Breadcrumbs', 'minerva-kb' ),
				'icon' => 'fa-ellipsis-h'
			),
			array(
				'id' => 'breadcrumbs_home_label',
				'type' => 'input',
				'label' => __( 'Breadcrumbs home page label', 'minerva-kb' ),
				'default' => __( 'KB Home', 'minerva-kb' )
			),
			array(
				'id' => 'breadcrumbs_label',
				'type' => 'input',
				'label' => __( 'Breadcrumbs label', 'minerva-kb' ),
				'default' => __( 'You are here:', 'minerva-kb' )
			),
			array(
				'id' => 'breadcrumbs_separator_icon',
				'type' => 'icon_select',
				'label' => __( 'Breadcrumbs separator', 'minerva-kb' ),
				'default' => 'fa-caret-right'
			),
			array(
				'id' => 'show_breadcrumbs_category',
				'type' => 'checkbox',
				'label' => __( 'Show breadcrumbs in category?', 'minerva-kb' ),
				'default' => true
			),
			array(
				'id' => 'show_breadcrumbs_single',
				'type' => 'checkbox',
				'label' => __( 'Show breadcrumbs in article?', 'minerva-kb' ),
				'default' => true
			),

			/**
			 * Localization
			 */
			array(
				'id' => 'localization_tab',
				'type' => 'tab',
				'label' => __( 'Localization', 'minerva-kb' ),
				'icon' => 'fa-language'
			),
			array(
				'id' => 'localization_title',
				'type' => 'title',
				'label' => __( 'Plugin localization', 'minerva-kb' ),
				'description' => __( 'Here will be general text strings used in plugin. Section specific texts are found in appropriate sections. Alternative you can use WPML or other plugin to translate KB text fields', 'minerva-kb' )
			),
			array(
				'id' => 'show_all_label',
				'type' => 'input',
				'label' => __( 'Show all link label', 'minerva-kb' ),
				'default' => __( 'Show all', 'minerva-kb' )
			),
			array(
				'id' => 'articles_text',
				'type' => 'input',
				'label' => __( 'Article plural text', 'minerva-kb' ),
				'default' => __( 'articles', 'minerva-kb' )
			),
			array(
				'id' => 'article_text',
				'type' => 'input',
				'label' => __( 'Article singular text', 'minerva-kb' ),
				'default' => __( 'article', 'minerva-kb' )
			),
			array(
				'id' => 'cpt_labels_title',
				'type' => 'title',
				'label' => __( 'Post type labels', 'minerva-kb' ),
				'description' => __( 'Change post type labels text', 'minerva-kb' )
			),
			array(
				'id' => 'cpt_label_name',
				'type' => 'input',
				'label' => __( 'Post type name', 'minerva-kb' ),
				'default' => __( 'KB Articles', 'minerva-kb' ),
			),
			array(
				'id' => 'cpt_label_singular_name',
				'type' => 'input',
				'label' => __( 'Post type singular name', 'minerva-kb' ),
				'default' => __( 'KB Article', 'minerva-kb' )
			),
			array(
				'id' => 'cpt_label_menu_name',
				'type' => 'input',
				'label' => __( 'Post type menu name', 'minerva-kb' ),
				'default' => __( 'Knowledge Base', 'minerva-kb' )
			),
			array(
				'id' => 'cpt_label_all_articles',
				'type' => 'input',
				'label' => __( 'Post type: All articles', 'minerva-kb' ),
				'default' => __( 'All Articles', 'minerva-kb' )
			),
			array(
				'id' => 'cpt_label_view_item',
				'type' => 'input',
				'label' => __( 'Post type: View item', 'minerva-kb' ),
				'default' => __( 'View Article', 'minerva-kb' )
			),
			array(
				'id' => 'cpt_label_add_new_item',
				'type' => 'input',
				'label' => __( 'Post type: Add new item', 'minerva-kb' ),
				'default' => __( 'Add New Article', 'minerva-kb' )
			),
			array(
				'id' => 'cpt_label_add_new',
				'type' => 'input',
				'label' => __( 'Post type: Add new', 'minerva-kb' ),
				'default' => __( 'Add New', 'minerva-kb' )
			),
			array(
				'id' => 'cpt_label_edit_item',
				'type' => 'input',
				'label' => __( 'Post type: Edit item', 'minerva-kb' ),
				'default' => __( 'Edit Article', 'minerva-kb' )
			),
			array(
				'id' => 'cpt_label_update_item',
				'type' => 'input',
				'label' => __( 'Post type: Update item', 'minerva-kb' ),
				'default' => __( 'Update Article', 'minerva-kb' )
			),
			array(
				'id' => 'cpt_label_search_items',
				'type' => 'input',
				'label' => __( 'Post type: Search items', 'minerva-kb' ),
				'default' => __( 'Search Articles', 'minerva-kb' )
			),
			array(
				'id' => 'cpt_label_not_found',
				'type' => 'input',
				'label' => __( 'Post type: Not found', 'minerva-kb' ),
				'default' => __( 'Not Found', 'minerva-kb' )
			),
			array(
				'id' => 'cpt_label_not_found_in_trash',
				'type' => 'input',
				'label' => __( 'Post type: Not found in trash', 'minerva-kb' ),
				'default' => __( 'Not Found In Trash', 'minerva-kb' )
			),
			array(
				'id' => 'cpt_topic_labels_title',
				'type' => 'title',
				'label' => __( 'Post type category labels', 'minerva-kb' ),
				'description' => __( 'Change post type category labels text', 'minerva-kb' )
			),
			array(
				'id' => 'cpt_topic_label_name',
				'type' => 'input',
				'label' => __( 'Post type category name', 'minerva-kb' ),
				'default' => __( 'Topics', 'minerva-kb' ),
			),
			array(
				'id' => 'cpt_topic_label_add_new',
				'type' => 'input',
				'label' => __( 'Post type category: Add new', 'minerva-kb' ),
				'default' => __( 'Add New Topic', 'minerva-kb' ),
			),
			array(
				'id' => 'cpt_topic_label_new_item_name',
				'type' => 'input',
				'label' => __( 'Post type category: New item name', 'minerva-kb' ),
				'default' => __( 'New Topic', 'minerva-kb' ),
			),
			array(
				'id' => 'cpt_tag_labels_title',
				'type' => 'title',
				'label' => __( 'Post type tag labels', 'minerva-kb' ),
				'description' => __( 'Change post type tag labels text', 'minerva-kb' )
			),
			array(
				'id' => 'cpt_tag_label_name',
				'type' => 'input',
				'label' => __( 'Post type tag name', 'minerva-kb' ),
				'default' => __( 'Tags', 'minerva-kb' ),
			),
			array(
				'id' => 'cpt_tag_label_add_new',
				'type' => 'input',
				'label' => __( 'Post type tag: Add new', 'minerva-kb' ),
				'default' => __( 'Add New Tag', 'minerva-kb' ),
			),
			array(
				'id' => 'cpt_tag_label_new_item_name',
				'type' => 'input',
				'label' => __( 'Post type tag: New item name', 'minerva-kb' ),
				'default' => __( 'New Tag', 'minerva-kb' ),
			),
			array(
				'id' => 'localization_search_title',
				'type' => 'title',
				'label' => __( 'Search labels', 'minerva-kb' )
			),
			array(
				'id' => 'search_results_text',
				'type' => 'input',
				'label' => __( 'Search multiple results text', 'minerva-kb' ),
				'default' => __( 'results', 'minerva-kb' )
			),
			array(
				'id' => 'search_result_text',
				'type' => 'input',
				'label' => __( 'Search single result text', 'minerva-kb' ),
				'default' => __( 'result', 'minerva-kb' )
			),
			array(
				'id' => 'search_no_results_text',
				'type' => 'input',
				'label' => __( 'Search no results text', 'minerva-kb' ),
				'default' => __( 'No results', 'minerva-kb' )
			),
			array(
				'id' => 'search_clear_icon_tooltip',
				'type' => 'input',
				'label' => __( 'Clear icon tooltip', 'minerva-kb' ),
				'default' => __( 'Clear search', 'minerva-kb' )
			)
		);
	}

	protected static function get_pages_options() {
		$result = array("" => __('Please select page', 'minerva-kb'));

		$pages_args = array(
			'sort_order' => 'asc',
			'sort_column' => 'post_title',
			'hierarchical' => 1,
			'exclude' => '',
			'include' => '',
			'meta_key' => '',
			'meta_value' => '',
			'authors' => '',
			'child_of' => 0,
			'parent' => -1,
			'exclude_tree' => '',
			'number' => '',
			'offset' => 0,
			'post_type' => 'page',
			'post_status' => 'publish'
		);

		$pages = get_pages($pages_args);

		if ($pages) {
			$result = array_reduce($pages, function($all, $page) {
				$all[$page->ID] = $page->post_title;

				return $all;
			}, $result);
		}

		return $result;
	}

	protected static function get_home_layout_options() {
		return array(
			array(
				'key' => 'search',
				'label' => __('Search', 'minerva-kb'),
				'icon' => 'fa-eye'
			),
			array(
				'key' => 'topics',
				'label' => __('Topics', 'minerva-kb'),
				'icon' => 'fa-eye'
			),
			array(
				'key' => 'tagcloud',
				'label' => __('Tag cloud', 'minerva-kb'),
				'icon' => 'fa-eye'
			),
			array(
				'key' => 'top_articles',
				'label' => __('Top articles', 'minerva-kb'),
				'icon' => 'fa-eye'
			)
		);
	}

	public static function get_topics_options() {
		$saved = self::get_saved_values();
		$category = isset($saved['article_cpt_category']) ?
			$saved['article_cpt_category'] :
			'topic'; // TODO: use separate defaults

		$options = array();

		$topics = get_terms( $category, array(
			'hide_empty' => false,
		) );

		if (isset($topics) && !is_wp_error($topics) && !empty($topics)) {
			foreach ( $topics as $item ):
				array_push($options, array(
					'key' => $item->term_id,
					'label' => $item->name,
				));
			endforeach;
		}

		return $options;
	}

	public static function get_search_topics_options() {
		$saved = self::get_saved_values();
		$category = isset($saved['article_cpt_category']) ?
			$saved['article_cpt_category'] :
			'topic'; // TODO: use separate defaults

		$options = array();

		$topics = get_terms( $category, array(
			'hide_empty' => false,
		) );

		if (isset($topics) && !is_wp_error($topics) && !empty($topics)) {
			foreach ( $topics as $item ):
				array_push($options, array(
					'key' => $item->term_id,
					'label' => $item->name,
				));
			endforeach;
		}

		return $options;
	}

	protected static function get_non_ui_options() {
		return array_filter(self::get_options(), function($option) {
			return $option['type'] !== 'tab' &&
			       $option['type'] !== 'title' &&
			       $option['type'] !== 'description' &&
			       $option['type'] !== 'code';
		});
	}

	public static function save($options) {
		update_option(self::OPTION_KEY, json_encode($options));
	}

	public static function reset() {
		update_option(self::OPTION_KEY, json_encode(self::get_options_defaults()));
	}

	public static function get() {
		global $minerva_kb_options_cache;

		if (!$minerva_kb_options_cache) {
			$minerva_kb_options_cache = wp_parse_args(self::get_saved_values(), self::get_options_defaults());
		}

		return $minerva_kb_options_cache;
	}

	public static function get_saved_values() {
		return self::normalize_values(stripslashes_deep(json_decode(get_option(self::OPTION_KEY), true)));
	}

	public static function normalize_values($settings) {
		return array_map(function($value) {
			if ($value === 'true') {
				return true;
			} else if ($value === 'false') {
				return false;
			} else {
				return $value;
			}
		}, $settings);
	}

	public static function option($key) {
		$all_options = self::get();

		return isset($all_options[$key]) ? $all_options[$key] : null;
	}

	/**
	 * Detects if flush rules was called for current set of CPT slugs
	 * @return bool
	 */
	public static function need_to_flush_rules() {
		$flushed_cpt = get_option('_mkb_flushed_rewrite_cpt');
		$flushed_topic = get_option('_mkb_flushed_rewrite_topic');
		$flushed_tag = get_option('_mkb_flushed_rewrite_tag');

		$cpt_slug = self::option('cpt_slug_switch') ? self::option('article_slug') : self::option('article_cpt');
		$cpt_category_slug = self::option('cpt_category_slug_switch') ? self::option('category_slug') : self::option('article_cpt_category');
		$cpt_tag_slug = self::option('cpt_tag_slug_switch') ? self::option('tag_slug') : self::option('article_cpt_tag');

		return $cpt_slug != $flushed_cpt ||
		       $cpt_category_slug != $flushed_topic ||
		       $cpt_tag_slug != $flushed_tag;
	}

	/**
	 * Sets flush flags not to flush on every load
	 */
	public static function update_flush_flags() {
		$cpt_slug = self::option('cpt_slug_switch') ? self::option('article_slug') : self::option('article_cpt');
		$cpt_category_slug = self::option('cpt_category_slug_switch') ? self::option('category_slug') : self::option('article_cpt_category');
		$cpt_tag_slug = self::option('cpt_tag_slug_switch') ? self::option('tag_slug') : self::option('article_cpt_tag');

		update_option('_mkb_flushed_rewrite_cpt', $cpt_slug);
		update_option('_mkb_flushed_rewrite_topic', $cpt_category_slug);
		update_option('_mkb_flushed_rewrite_tag', $cpt_tag_slug);
	}
}

global $minerva_kb_options;

$minerva_kb_options = new MKB_Options();