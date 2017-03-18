<?php
/**
 * Project: MinervaKB Lite.
 * Copyright: 2015-2017 @KonstruktStudio
 */

/**
 * Interface KST_MenuPage_Interface
 * Common WP Dashboard Edit Screen with meta boxes
 */
interface KST_MenuPage_Interface {

	/**
	 * Registers admin menu page
	 * @return mixed
	 */
	public function add_menu_page ();
}