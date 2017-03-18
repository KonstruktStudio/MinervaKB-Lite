<?php
/**
 * Project: Minerva KB Lite
 * Copyright: 2015-2016 @KonstruktStudio
 */

/*
Plugin Name: Minerva KB Lite
Description: Minerva KB Lite - Knowledge Base for WordPress
Author: KonstruktStudio
Version: 1.0
*/

define('MINERVA_KB_VERSION', '1.0');
define('MINERVA_KB_OPTION_PREFIX', 'mkb_option_');
define('MINERVA_KB_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('MINERVA_KB_IMG_URL', MINERVA_KB_PLUGIN_URL . 'assets/img/');
define('MINERVA_KB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MINERVA_THEME_DIR', get_template_directory());

// init app
require_once(MINERVA_KB_PLUGIN_DIR . 'lib/app.php');

global $minerva_kb;
$minerva_kb = new MinervaKB_App();
