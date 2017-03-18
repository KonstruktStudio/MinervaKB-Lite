<?php
/**
 * Project: Minerva KB Lite
 * Copyright: 2015-2017 @KonstruktStudio
 */

class MinervaKB_DynamicStyles {

	private $info;

	/**
	 * Constructor
	 * @param $deps
	 */
	public function __construct($deps) {
		$this->setup_dependencies( $deps );
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
	 * Returns minified inline css
	 * @return mixed
	 */
	public function get_css () {
		ob_start();
		$this->print_css();
		return $this->css_compress(ob_get_clean());
	}

	/**
	 * Outputs all inline styles
	 */
	public function print_css() {

?>

.kb-topic__count,
.mkb-article-item__topic {
	color: <?php echo esc_attr(MKB_Options::option( 'articles_count_color' )); ?>;
	background: <?php echo esc_attr(MKB_Options::option( 'articles_count_bg' )); ?>;
}

.kb-search .kb-search__result-topic {
	color: <?php echo esc_attr(MKB_Options::option( 'search_results_topic_color' )); ?>;
	background: <?php echo esc_attr(MKB_Options::option( 'search_results_topic_bg' )); ?>;
}

.kb-topic .kb-topic__box-header,
.kb-topic .kb-topic__title-link {
	color: <?php echo esc_attr(MKB_Options::option( 'topic_color' )); ?>;
}

/* Shortcodes */

.kb-topic.kb-topic--box-view .kb-topic__inner {
	background: <?php echo esc_attr(MKB_Options::option( 'box_view_item_bg' )); ?>;
}

.kb-topic.kb-topic--box-view .kb-topic__inner:hover {
	background: <?php echo esc_attr(MKB_Options::option( 'box_view_item_hover_bg' )); ?>;
}

.mkb-root .mkb-article-text,
.mkb-root .mkb-article-header,
.mkb-root .mkb-article-item__excerpt,
.mkb-widget.widget {
	color: <?php esc_attr_e(MKB_Options::option( 'text_color' )); ?>;
}

.mkb-root .mkb-article-text a,
.mkb-root .mkb-article-header a,
.mkb-article-item--detailed .mkb-entry-title a,
.mkb-breadcrumbs__list a,
.mkb-widget.widget a {
	color: <?php esc_attr_e(MKB_Options::option( 'text_link_color' )); ?>;
}

<?php
	}

	/**
	 * CSS minifier
	 * @param $minify
	 * @return mixed
	 */
	private function css_compress( $minify ) {
		/* remove tabs, newlines, and multiple spaces etc. */
		$minify = str_replace( array("\r\n", "\r", "\n", "\t"), '', $minify );
		$minify = str_replace( array("  ", "   ", "    "), ' ', $minify );

		return $minify;
	}
}
