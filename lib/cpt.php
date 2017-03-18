<?php
/**
 * Project: Minerva KB Lite
 * Copyright: 2015-2017 @KonstruktStudio
 */

require_once(MINERVA_KB_PLUGIN_DIR . 'lib/helpers/icon-options.php');

/**
 * Class MinervaKB_CPT
 * Manages custom post type creation and edit pages
 */
class MinervaKB_CPT {

	private $info;

	/**
	 * Constructor
	 */
	public function __construct($deps) {

		$this->setup_dependencies($deps);

		$article_cpt = MKB_Options::option('article_cpt');

		// post types
		add_action('init', array($this, 'register_post_types'), 0);

		// extra post list columns
		add_filter('manage_' . $article_cpt . '_posts_columns', array($this, 'set_custom_edit_kb_columns'));
		add_action('manage_' . $article_cpt . '_posts_custom_column' , array($this, 'custom_kb_column'), 0, 2);
		add_filter('manage_edit-' . $article_cpt . '_sortable_columns', array($this, 'sortable_kb_column'));
		add_action('pre_get_posts', array($this, 'kb_list_orderby'));
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
	 * Registers all configured custom post types
	 */
	public function register_post_types() {

		$this->register_article_cpt();
		$this->register_topic_taxonomy();
		$this->register_tag_taxonomy();

		// flush rewrite rules for CPT that have public URLs
		$this->maybe_flush_rules();
	}

	/**
	 * Flush rewrite rules if never flushed
	 */
	private function maybe_flush_rules () {
		// NOTE: needed to make CPT visible after register (force WP rewrite rules flush)
		if (MKB_Options::need_to_flush_rules()) {
			flush_rewrite_rules(false);

			MKB_Options::update_flush_flags();
		}
	}

	/**
	 * Registers KB article custom post type
	 */
	private function register_article_cpt() {
		$labels = array(
			'name' => MKB_Options::option( 'cpt_label_name' ),
			'singular_name' => MKB_Options::option( 'cpt_label_singular_name' ),
			'menu_name' => MKB_Options::option( 'cpt_label_menu_name' ),
			'all_items' => MKB_Options::option( 'cpt_label_parent_all_articles' ),
			'view_item' => MKB_Options::option( 'cpt_label_view_item' ),
			'add_new_item' => MKB_Options::option( 'cpt_label_add_new_item' ),
			'add_new' => MKB_Options::option( 'cpt_label_add_new' ),
			'edit_item' => MKB_Options::option( 'cpt_label_edit_item' ),
			'update_item' => MKB_Options::option( 'cpt_label_update_item' ),
			'search_items' => MKB_Options::option( 'cpt_label_search_items' ),
			'not_found' => MKB_Options::option( 'cpt_label_not_found' ),
			'not_found_in_trash' => MKB_Options::option( 'cpt_label_not_found_in_trash' ),
		);

		$args = array(
			'description' => __( 'KB Articles', 'minerva-kb' ),
			'labels' => $labels,
			'supports' => array(
				'title',
				'editor',
				'excerpt',
				'thumbnail',
				'author',
				'comments',
				'revisions',
				'custom-fields',
			),
			'taxonomies' => array(
				MKB_Options::option( 'article_cpt_category' ),
				MKB_Options::option( 'article_cpt_tag' )
			),
			'hierarchical' => false,
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => true,
			'show_in_admin_bar' => true,
			'menu_position' => 5,
			'menu_icon' => 'dashicons-welcome-learn-more',
			'can_export' => true,
			'has_archive' => true,
			'exclude_from_search' => false,
			'publicly_queryable' => true,
			'capability_type' => 'post',
		);

		register_post_type( MKB_Options::option( 'article_cpt' ), $args );
	}

	/**
	 * Registers KB topic custom taxonomy
	 */
	private function register_topic_taxonomy() {
		$args = array(
			'labels' => array(
				'name' => MKB_Options::option( 'cpt_topic_label_name' ),
				'add_new_item' => MKB_Options::option( 'cpt_topic_label_add_new' ),
				'new_item_name' => MKB_Options::option( 'cpt_topic_label_new_item_name' )
			),
			'show_ui' => true,
			'show_tagcloud' => false,
			'hierarchical' => true
		);

		register_taxonomy(
			MKB_Options::option( 'article_cpt_category' ),
			MKB_Options::option( 'article_cpt' ),
			$args
		);
	}

	/**
	 * Registers KB tag custom taxonomy
	 */
	private function register_tag_taxonomy() {
		$args = array(
			'labels' => array(
				'name' => MKB_Options::option( 'cpt_tag_label_name' ),
				'add_new_item' => MKB_Options::option( 'cpt_tag_label_add_new' ),
				'new_item_name' => MKB_Options::option( 'cpt_tag_label_new_item_name' )
			),
			'show_ui' => true,
			'publicly_queryable' => !MKB_Options::option( 'tags_disable' ),
			'show_tagcloud' => true,
			'hierarchical' => false
		);

		register_taxonomy(
			MKB_Options::option( 'article_cpt_tag' ),
			MKB_Options::option( 'article_cpt' ),
			$args
		);
	}

	/**
	 * Admin articles list custom columns
	 */
	public function set_custom_edit_kb_columns($columns) {

		unset($columns['author']);
		unset($columns['date']);
		unset($columns['comments']);

		$columns['views'] = __( 'Views', 'minerva-kb' );

		$columns['author'] = __( 'Author', 'minerva-kb' );
		$columns['date'] = __( 'Date', 'minerva-kb' );
		$columns['comments'] = '<span class="vers comment-grey-bubble" title="Comments"><span class="screen-reader-text">Comments</span></span>';

		return $columns;
	}

	public function custom_kb_column( $column, $post_id ) {
		switch ( $column ) {

			case 'views':
				$views = get_post_meta($post_id, '_mkb_views', true);
				echo esc_html($views > 0 ? $views : 0);
				break;

			default:
				break;
		}
	}

	/**
	 * Make custom columns sortable
	 */
	public function sortable_kb_column( $columns ) {
		$columns['views'] = 'views';

		return $columns;
	}

	/**
	 * Order by custom columns
	 */
	public function kb_list_orderby( $query ) {
		if( !$this->info->is_admin() )
			return;

		$orderby = $query->get( 'orderby');

		if ( 'views' == $orderby ) {
			$query->set('orderby','meta_value_num title');
			$query->set('meta_query', array(
				'relation' => 'OR',
				array(
					'key' => '_mkb_views',
					'compare' => 'EXISTS',
				),
				array(
					'key' => '_mkb_views',
					'compare' => 'NOT EXISTS'
				)
			));
		}
	}
}
