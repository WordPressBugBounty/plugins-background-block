<?php
/*
 * Plugin Name:			Section Builder with Backgrounds
 * Plugin URI:			https://pluginenvision.com/plugins/background-block
 * Description:			Customize your WordPress section easily! Choose backgrounds with color, gradient, image. Add parallax effects, and more for a stunning layout.
 * Version:				0.17
 * Requires at least:	6.5
 * Requires PHP:		7.2
 * Author:				Plugin Envision
 * Author URI:			https://pluginenvision.com
 * License:				GPLv3 or later
 * License URI:			https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:			background-block
 * Domain Path:			/languages
*/

if ( !defined( 'ABSPATH' ) ) { exit; }

define( 'EVBB_VERSION', isset( $_SERVER['HTTP_HOST'] ) && 'localhost' === $_SERVER['HTTP_HOST'] ? time() : '0.17' );
define( 'EVBB_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'EVBB_DIR_PATH', plugin_dir_path( __FILE__ ) );

if( !class_exists( 'EVBBPlugin' ) ){
	class EVBBPlugin{
		function __construct(){
			add_action( 'init', [ $this, 'onInit' ] );
		}

		function onInit(){
			register_block_type( __DIR__ . '/build' );
		}
	}
	new EVBBPlugin();
}