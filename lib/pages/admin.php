<?php
/**
 * Project: MinervaKB Lite.
 * Copyright: 2015-2017 @KonstruktStudio
 */

/**
 * Admin menu controller
 * Class MKB_SettingsPage
 */
class MinervaKB_AdminPage implements KST_MenuPage_Interface {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );

		if (!MKB_Options::option('kb_page')) {
			add_action( 'admin_notices', array( $this, 'admin_notices') );
		}
	}

	/**
	 * Top level admin menu
	 */
	public function add_menu_page() {
		add_menu_page(
			__( 'MinervaKB', 'minerva-kb' ),
			__( 'MinervaKB', 'minerva-kb' ),
			'manage_options',
			'minerva-kb-menu',
			null,
			'dashicons-welcome-learn-more'
		);
	}

	/**
	 * Plugin after install notices
	 */
	public function admin_notices() {
		$class = 'notice notice-success is-dismissible';
		$message = __( 'Congratulations! You have successfully installed and activated <b>MinervaKB</b> for WordPress.' .
		               ' To begin working with it, please <a href="' .
		               esc_attr(admin_url( 'admin.php?page=minerva-kb-submenu-settings' )) .
		               '">select Home KB page</a> (can be any new or existing page)', 'minerva-kb' );

		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
	}
}
