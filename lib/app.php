<?php
/**
 * Project: MinervaKB Lite.
 * Copyright: 2015-2017 @KonstruktStudio
 */

// abstract
require_once(MINERVA_KB_PLUGIN_DIR . 'lib/abstract/admin-edit-screen.php');
require_once(MINERVA_KB_PLUGIN_DIR . 'lib/abstract/admin-menu-page.php');
require_once(MINERVA_KB_PLUGIN_DIR . 'lib/abstract/admin-submenu-page.php');

// global modules
require_once(MINERVA_KB_PLUGIN_DIR . 'lib/options.php');
require_once(MINERVA_KB_PLUGIN_DIR . 'lib/info.php');

// helpers
require_once(MINERVA_KB_PLUGIN_DIR . 'lib/helpers/settings-builder.php');
require_once(MINERVA_KB_PLUGIN_DIR . 'lib/helpers/template-helper.php');

// modules
require_once(MINERVA_KB_PLUGIN_DIR . 'lib/api.php');
require_once(MINERVA_KB_PLUGIN_DIR . 'lib/cpt.php');
require_once(MINERVA_KB_PLUGIN_DIR . 'lib/content.php');
require_once(MINERVA_KB_PLUGIN_DIR . 'lib/actions.php');
require_once(MINERVA_KB_PLUGIN_DIR . 'lib/assets.php');
require_once(MINERVA_KB_PLUGIN_DIR . 'lib/styles.php');
require_once(MINERVA_KB_PLUGIN_DIR . 'lib/ajax.php');
require_once(MINERVA_KB_PLUGIN_DIR . 'lib/widgets.php');
require_once(MINERVA_KB_PLUGIN_DIR . 'lib/shortcodes.php');

// admin menu pages and edit screens
require_once(MINERVA_KB_PLUGIN_DIR . 'lib/pages/admin.php');
require_once(MINERVA_KB_PLUGIN_DIR . 'lib/pages/settings.php');
require_once(MINERVA_KB_PLUGIN_DIR . 'lib/pages/dashboard.php');

/**
 * Class MinervaKB_App
 * Main App Controller,
 * creates all module instances and passes down dependencies
 */
class MinervaKB_App {

	// holds current render info
	public $info;

	// custom post types controller
	private $cpt;

	// manages content rendering
	private $content;

	// manages content parts rendering via actions
	public $actions;

	// inline styles manager
	private $inline_styles;

	// assets manager
	private $assets;

	// ajax manager
	private $ajax;

	// sidebars and widgets manager
	private $widgets;

	// shortcodes manager
	private $shortcodes;

	// admin menu controller
	private $admin_page;

	// settings menu page controller
	private $settings_page;

	// dashboard menu page controller
	private $dashboard_page;

	/**
	 * App entry
	 */
	public function __construct() {

		// global info model
		$this->info = new MinervaKB_Info();

		// custom post types
		$this->cpt = new MinervaKB_CPT(array(
			'info' => $this->info
		));

		if ($this->info->is_client()) {

			$this->content = new MinervaKB_Content(array(
				'info' => $this->info
			));

			// content hooks
			$this->actions = new MinervaKB_ContentHooks(array(
				'info' => $this->info
			));

			// inline styles module
			$this->inline_styles = new MinervaKB_DynamicStyles(array(
				'info' => $this->info
			));
		}

		// ajax manager
		$this->ajax = new MinervaKB_Ajax(array(
			'info' => $this->info
		));

		// assets manager
		$this->assets = new MinervaKB_Assets(array(
			'info' => $this->info,
			'inline_styles' => $this->inline_styles,
			'ajax' => $this->ajax
		));

		// widgets manager
		$this->widgets = new MinervaKB_Widgets();

		// shortcodes manager
		$this->shortcodes = new MinervaKB_Shortcodes();

		/**
		 * Admin menu pages
		 */
		if ($this->info->is_admin()) {
			// admin menu page
			$this->admin_page = new MinervaKB_AdminPage();

			// NOTE: dashboard must be right after the main admin menu to replace it
			// admin dashboard menu page
			$this->dashboard_page = new MinervaKB_DashboardPage();

			// admin settings menu page
			$this->settings_page = new MinervaKB_SettingsPage( array(
				'info' => $this->info,
				'ajax' => $this->ajax
			) );
		}
	}
}