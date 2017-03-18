<?php

/**
 * Dashboard page controller
 * Class MinervaKB_DashboardPage
 */

class MinervaKB_DashboardPage {

	const SCREEN_BASE = 'toplevel_page_minerva-kb-menu';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_submenu' ) );
	}

	/**
	 * Adds dashboard submenu page
	 */
	public function add_submenu() {
		add_submenu_page(
			'minerva-kb-menu',
			__( 'Upgrade', 'minerva-kb' ),
			__( 'Upgrade', 'minerva-kb' ),
			'manage_options',
			'minerva-kb-menu',
			array( $this, 'submenu_html' )
		);
	}

	/**
	 * Gets dashboard page html
	 */
	public function submenu_html() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'minerva-kb' ) );
		}

		?>
		<div class="mkb-admin-page-header">
			<span class="mkb-header-logo mkb-header-item" data-version="v<?php esc_attr_e(MINERVA_KB_VERSION); ?>">
				<img class="logo-img" src="<?php echo esc_attr( MINERVA_KB_IMG_URL . 'logo.png' ); ?>" title="logo"/>
			</span>
			<span class="mkb-header-title mkb-header-item"><?php _e( 'MinervaKB Pro version', 'minerva-kb' ); ?></span>
		</div>

		<div id="mkb-promo">
			<div class="mkb-promo__content">
				<div id="promo" class="mkb-promo-page">
					<img class="promo-img" src="<?php esc_attr_e( MINERVA_KB_IMG_URL . 'banner-promo.jpg' ); ?>" title="MinervaKB Pro Version"/>
					<p>If you like this plugin, try <a href="https://www.minerva-kb.com" target="_blank">MinervaKB Pro</a>!</p>
					<p>It has all the features of Lite version, plus:</p>
					<ul>
						<li>Built-in Analytics dashboard</li>
						<li>7 beautiful search themes</li>
						<li>Articles ratings and feedback</li>
						<li>KB home page builder</li>
						<li>Dynamic table of contents in articles with ScrollSpy</li>
						<li>Content restriction by user role</li>
						<li>Related articles</li>
						<li>Articles drag-n-drop reorder</li>
						<li>Extended styles and typography options</li>
						<li>Dynamic topics (most liked, most viewed, recent)</li>
						<li>Custom settings for each topic, such as images, colors and icons</li>
						<li>Content shortcodes, like tip, related links, warning and others</li>
						<li>Search on article and topic pages</li>
						<li>Options, options and more options. You can configure almost everything from options panel</li>
						<li>Google Analytics integration</li>
						<li>Professional quick support</li>
						<li>Regular updates and bugfixes</li>
						<li>...many more</li>
					</ul>
					<a href="https://www.minerva-kb.com" class="mkb-action-button mkb-action-default" target="_blank"
					   title="<?php echo esc_attr(__( 'See the demos', 'minerva-kb' )); ?>"><?php echo __( 'See the demos', 'minerva-kb' ); ?></a>
				</div>
			</div>
		</div>
	<?php
	}
}