<?php
/**
 * Project: MinervaKB Lite.
 * Copyright: 2015-2017 @KonstruktStudio
 */

require_once(MINERVA_KB_PLUGIN_DIR . 'lib/vendor/Mobile_Detect.php');

/**
 * Class MinervaKB_Info
 * Holds and caches all needed info for currently rendered entity
 */
class MinervaKB_Info {
	
	private $is_tag;

	private $is_topic;

	private $is_article_archive;

	private $is_archive;

	private $is_single;

	private $is_search;

	private $is_home;
	
	private $is_settings_home;

	private $is_admin;

	private $is_client;
	
	private $is_desktop;
	
	private $is_tablet;
	
	private $is_mobile;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->get_initial_info();
	}

	/**
	 * Gets all non-lazy properties
	 */
	private function get_initial_info() {
		$this->get_device_info();
	}

	/**
	 * Gets device info
	 */
	private function get_device_info() {
		$detect = new Mobile_Detect();

		$this->is_desktop = false;
		$this->is_tablet = false;
		$this->is_mobile = false;

		if ( $detect->isTablet() ) {
			$this->is_tablet = true;
		} else if($detect->isMobile() ) {
			$this->is_mobile = true;
		} else {
			$this->is_desktop = true;
		}

		$detect = null;
	}

	/**
	 * Detects KB home page built with plugin settings
	 * @return bool
	 */
	public function is_single() {
		if (isset($this->is_single)) {
			return $this->is_single;
		}

		global $post;

		$this->is_single = is_single() && $post->post_type == MKB_Options::option( 'article_cpt' );

		return $this->is_single;
	}

	/**
	 * Detects any KB archive page
	 * @return bool
	 */
	public function is_archive() {
		if (isset($this->is_archive)) {
			return $this->is_archive;
		}

		$this->is_archive = $this->is_topic() || $this->is_article_archive() || $this->is_tag();

		return $this->is_archive;
	}

	/**
	 * Detects topic loop
	 * @return bool
	 */
	public function is_topic() {
		if (isset($this->is_topic)) {
			return $this->is_topic;
		}

		$this->is_topic = is_tax( MKB_Options::option( 'article_cpt_category' ) );

		return $this->is_topic;
	}

	/**
	 * Detects article archive loop
	 * @return bool
	 */
	public function is_article_archive() {
		if (isset($this->is_article_archive)) {
			return $this->is_article_archive;
		}

		$this->is_article_archive = is_post_type_archive( MKB_Options::option( 'article_cpt' ) );

		return $this->is_article_archive;
	}

	/**
	 * Detects tag loop
	 * @return bool
	 */
	public function is_tag() {
		if (isset($this->is_tag)) {
			return $this->is_tag;
		}

		$this->is_tag = is_tax( MKB_Options::option( 'article_cpt_tag' ));

		return $this->is_tag;
	}

	/**
	 * Detects search results loop
	 * @return bool
	 */
	public function is_search() {
		if (isset($this->is_search)) {
			return $this->is_search;
		}

		global $wp_query;

		$this->is_search = $wp_query->is_search;

		return $this->is_search;
	}

	/**
	 * Detects any KB home page
	 * @return mixed
	 */
	public function is_home() {
		if (isset($this->is_home)) {
			return $this->is_home;
		}

		$this->is_home = $this->is_settings_home();

		return $this->is_home;
	}

	/**
	 * Detects KB home page built with page builder, stub
	 * @return bool
	 */
	public function is_builder_home() {
		return false;
	}

	/**
	 * Detects KB home page built with plugin settings
	 * @return bool
	 */
	public function is_settings_home() {
		if (isset($this->is_settings_home)) {
			return $this->is_settings_home;
		}

		global $post;

		$this->is_settings_home = get_post_type() === 'page' &&
		       MKB_Options::option( 'kb_page' ) &&
		       $post->ID === (int) MKB_Options::option( 'kb_page' );

		return $this->is_settings_home;
	}

	/**
	 * Detects admin side
	 * @return bool
	 */
	public function is_admin() {
		if (isset($this->is_admin)) {
			return $this->is_admin;
		}

		$this->is_admin = is_admin();

		return $this->is_admin;
	}

	/**
	 * Detects client side
	 * @return bool
	 */
	public function is_client() {
		if (isset($this->is_client)) {
			return $this->is_client;
		}

		$this->is_client = !$this->is_admin();

		return $this->is_client;
	}

	/**
	 * Flag for desktop devices
	 * @return mixed
	 */
	public function is_desktop () {
		return $this->is_desktop;
	}

	/**
	 * Flag for desktop devices
	 * @return mixed
	 */
	public function is_tablet () {
		return $this->is_tablet;
	}

	/**
	 * Flag for desktop devices
	 * @return mixed
	 */
	public function is_mobile () {
		return $this->is_mobile;
	}

	/**
	 * Returns platform string
	 * @return string
	 */
	public function platform() {
		if ($this->is_mobile()) {
			return 'mobile';
		} else if ($this->is_tablet()) {
			return 'tablet';
		} else {
			return 'desktop';
		}
	}
}