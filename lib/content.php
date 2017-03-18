<?php
/**
 * Project: Minerva KB Lite
 * Copyright: 2015-2017 @KonstruktStudio
 */

/**
 * Class MinervaKB_Content
 * Manges KB content rendering
 */
class MinervaKB_Content {

	private $info;

	/**
	 * Constructor
	 * @param $deps
	 */
	public function __construct($deps) {

		$this->setup_dependencies($deps);

		// body classes
		add_filter('body_class', array($this, 'body_class_filter'));

		// templates
		add_filter('single_template', array($this, 'single_template_filter'), 999);
		add_filter('archive_template', array($this, 'archive_template_filter'), 999);
		add_filter('taxonomy_template', array($this, 'archive_template_filter'), 999);
		add_filter('page_template', array($this, 'page_template_filter'), 999);
		add_filter('template_include', array($this, 'page_template_filter'), 999);
		add_filter('template_include', array($this, 'search_template_filter'));

		// search results
		add_filter('pre_get_posts', array($this, 'search_filter'));

		// topic loop
		add_filter('pre_get_posts', array($this, 'topic_filter'));

		// tag loop
		add_filter('pre_get_posts', array($this, 'tag_filter'));
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
	 * Single template
	 * @param $single_template
	 *
	 * @return string
	 */
	public function single_template_filter($single_template) {

		if ($this->info->is_single()) {
			return $this->locate_template('single');
		}

		return $single_template;
	}

	/**
	 * Archives template
	 * @param $archive_template
	 *
	 * @return string
	 */
	public function archive_template_filter( $template ) {

		/**
		 * KB Tags
		 */
		if ($this->info->is_tag()) {
			return $this->locate_template('tag');
		}

		/**
		 * KB Category
		 */
		if ($this->info->is_topic()) {
			return $this->locate_template('category');
		}

		/**
		 * KB Archive
		 */
		if ( $this->info->is_article_archive()) {
			return $this->locate_template('archive');
		}

		/**
		 * Default template
		 */
		return $template;
	}

	/**
	 * Page template
	 * @param $page_template
	 *
	 * @return string
	 */
	public function page_template_filter($page_template) {

		if ($this->info->is_home()) {
			return $this->locate_template('page');
		}

		return $page_template;
	}

	/**
	 * Gets template path looking for theme override
	 * @param $template
	 * @return string
	 */
	private function locate_template($template) {
		$theme_override = MINERVA_THEME_DIR . '/minerva-kb/' . $template . '.php';

		if (file_exists($theme_override)) {
			return $theme_override;
		}

		return MINERVA_KB_PLUGIN_DIR . 'lib/templates/' . $template . '.php';
	}

	/**
	 * Search template filter
	 * @param $template
	 * @return string
	 */
	public function search_template_filter($template){
		if (!$this->info->is_search() || !isset($_REQUEST['source']) || $_REQUEST['source'] !== 'kb') {
			return $template;
		}

		return $this->locate_template('search');
	}

	/**
	 * Search results filter
	 * @param $query
	 * @return mixed
	 */
	public function search_filter($query) {

		// NOTE, cannot use info here, runs before query is set
		if (!$query->is_search || !isset($_REQUEST['source']) || $_REQUEST['source'] !== 'kb') {
			return $query;
		}

		$query->set('post_type', array( MKB_Options::option( 'article_cpt' ) ));
		$query->set('order_by', 'relevance');
		$query->set('posts_per_page', (int) MKB_Options::option('search_results_per_page'));

		return $query;
	}

	/**
	 * Topic items filter
	 * @param $query
	 * @return mixed
	 */
	public function topic_filter($query) {

		// NOTE, cannot use info here, runs before query is set
		if ( !$query->is_main_query() || !$query->is_tax || !is_tax(MKB_Options::option('article_cpt_category')) ) {
			return $query;
		}

		$query->set('posts_per_page', (int) MKB_Options::option('topic_articles_per_page'));

		return $query;
	}

	/**
	 * Topic items filter
	 * @param $query
	 * @return mixed
	 */
	public function tag_filter($query) {

		if ( !$query->is_main_query() || !$query->is_tax || !is_tax(MKB_Options::option('article_cpt_tag')) ) {
			return $query;
		}

		$query->set('posts_per_page', (int) MKB_Options::option('tag_articles_per_page'));

		return $query;
	}

	/**
	 * Body extra classes
	 * @param $classes
	 * @return array
	 */
	public function body_class_filter($classes) {

		// device classes
		if ( $this->info->is_tablet() ) {
			$classes[] = 'mkb-tablet';
		} else if ( $this->info->is_mobile() ) {
			$classes[] = 'mkb-mobile';
		} else {
			$classes[] = 'mkb-desktop';
		}

		// KB template classes
		if ($this->info->is_home()) {
			$classes[] = 'mkb-home-page';
		}

		if ( $this->info->is_builder_home() ) {
			$classes[] = 'mkb-builder-home-page';
		} else if ( $this->info->is_settings_home() ) {
			$classes[] = 'mkb-settings-home-page';
		} else if ( $this->info->is_archive() ) {
			$classes[] = 'mkb-archive';
		} else if ( $this->info->is_single() ) {
			$classes[] = 'mkb-single';
		} else if ( $this->info->is_search() ) {
			$classes[] = 'mkb-search';
		}

		return $classes;
	}
}
