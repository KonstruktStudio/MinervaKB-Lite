<?php
/**
 * Project: MinervaKB Lite.
 * Copyright: 2015-2017 @KonstruktStudio
 */

/**
 * Interface KST_MenuPage_Interface
 * Common WP Dashboard Edit Screen with meta boxes
 */
interface KST_SubmenuPage_Interface {

	/**
	 * Registers admin submenu page
	 * @return mixed
	 */
	public function add_submenu_page ();

	/**
	 * Submenu page html
	 * @return mixed
	 */
	public function submenu_html ();

	/**
	 * Loads submenu page assets
	 * @return mixed
	 */
	public function load_assets ();
}