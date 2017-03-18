<?php
/**
 * Project: Minerva KB Lite
 * Copyright: 2015-2017 @KonstruktStudio
 */

class MinervaKB_Assets {

	private $info;

	private $inline_styles;

	private $ajax;

	/**
	 * Constructor
	 * @param $deps
	 */
	public function __construct($deps) {

		$this->setup_dependencies( $deps );

		add_action( 'wp_enqueue_scripts', array($this, 'client_assets'), 100 );
		add_action( 'admin_enqueue_scripts', array($this, 'admin_assets'), 100 );
	}

	/**
	 * Sets up dependencies
	 * @param $deps
	 */
	private function setup_dependencies($deps) {
		if (isset($deps['info'])) {
			$this->info = $deps['info'];
		}

		if (isset($deps['inline_styles'])) {
			$this->inline_styles = $deps['inline_styles'];
		}

		if (isset($deps['ajax'])) {
			$this->ajax = $deps['ajax'];
		}
	}

	/**
	 * Client-side assets
	 */
	public function client_assets() {

		wp_enqueue_style( 'minerva-kb/css', MINERVA_KB_PLUGIN_URL . 'assets/css/dist/minerva-kb.css', false, MINERVA_KB_VERSION );
		wp_enqueue_style( 'minerva-kb/fa-css', MINERVA_KB_PLUGIN_URL . 'assets/css/vendor/font-awesome.css', false, null );

		// dynamic styles
		wp_add_inline_style( 'minerva-kb/css', $this->inline_styles->get_css());

		wp_enqueue_script( 'minerva-kb/js', MINERVA_KB_PLUGIN_URL . 'assets/js/minerva-kb.js', array( 'jquery' ), MINERVA_KB_VERSION, true );

		wp_localize_script( 'minerva-kb/js', 'MinervaKB', $this->get_client_js_data() );
	}

	/**
	 * Gets client side JS data
	 */
	private function get_client_js_data() {
		return array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'siteUrl' => site_url(),
			'platform' => $this->info->platform(),
			'info' => array(
				'isSingle' => $this->info->is_single()
			),
			'nonce' => array(
				'nonce' => wp_create_nonce( $this->ajax->get_nonce() ),
				'nonceKey' =>$this->ajax->get_nonce_key(),
			),
			'settings' => array(),
			'i18n' => array(
				'no-results' => MKB_Options::option( 'search_no_results_text' ),
				'results' => MKB_Options::option( 'search_results_text' ),
				'result' => MKB_Options::option( 'search_result_text' )
			)
		);
	}

	/**
	 * Assets required for admin
	 */
	public function admin_assets() {
		wp_enqueue_style( 'minerva-kb/admin-css', MINERVA_KB_PLUGIN_URL . 'assets/css/dist/minerva-kb-admin.css', false, MINERVA_KB_VERSION );
		wp_enqueue_style( 'minerva-kb/admin-fa-css', MINERVA_KB_PLUGIN_URL . 'assets/css/vendor/font-awesome.css', false, null );

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script('jquery-ui-sortable');

		wp_enqueue_script( 'minerva-kb/admin-ui-js', MINERVA_KB_PLUGIN_URL . 'assets/js/minerva-kb-ui.js', array(
			'underscore',
			'jquery',
			'wp-color-picker'
		), MINERVA_KB_VERSION, true );

		wp_localize_script( 'minerva-kb/admin-ui-js', 'MinervaKB', $this->get_admin_js_data() );
	}

	/**
	 * Data for admin js
	 * @return array
	 */
	private function get_admin_js_data() {
		return array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'siteUrl' => site_url(),
			'nonce' => array(
				'nonce' => wp_create_nonce( $this->ajax->get_nonce() ),
				'nonceKey' => $this->ajax->get_nonce_key(),
			),
			'i18n' => array(
				'no-results' => MKB_Options::option('search_no_results_text'),
				'results' => MKB_Options::option('search_results_text'),
				'result' => MKB_Options::option('search_result_text'),
				'loading' => __('Loading...', 'minerva-kb' ),
			),
			'optionPrefix' => MINERVA_KB_OPTION_PREFIX,
			'settings' => array(
				'article_cpt' => MKB_Options::option('article_cpt'),
				'article_cpt_category' => MKB_Options::option('article_cpt_category'),
				'article_cpt_tag' => MKB_Options::option('article_cpt_tag'),
			)
		);
	}
}