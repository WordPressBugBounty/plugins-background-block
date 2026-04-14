<?php
namespace EVBB\Admin;

if ( !defined( 'ABSPATH' ) ) { exit; }

if ( !class_exists( 'Submenu' ) ) {
	class Submenu {
		public function __construct() {
			add_action( 'admin_menu', [ $this, 'adminMenu' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'adminEnqueueScripts' ] );
		}

		function adminMenu() {
			add_submenu_page(
				'tools.php',
				'Section Builder with Backgrounds - Plugin Envision',
				'Section Backgrounds',
				'manage_options',
				'background-block',
				function() {
				?>
					<div id='evbbDashboard' data-info='<?php echo esc_attr( json_encode( [ 'version' => EVBB_VERSION, 'plan' => [ 'name' => 'free' ] ] ) ); ?>'></div>
				<?php }
			);
		}

		function adminEnqueueScripts( $path ){
			if( strpos( $path, 'background-block' ) !== false ) {
				wp_enqueue_style( 'evbb-admin-dashboard', EVBB_DIR_URL . 'build/admin/dashboard.css', [], EVBB_VERSION );

				$dependencies = [ 'react', 'react-dom', 'wp-i18n', 'wp-data', 'wp-util' ];

				$asset_file = EVBB_DIR_PATH . 'build/admin/dashboard.asset.php';
				if ( file_exists( $asset_file ) ) {
					$asset = require_once $asset_file;
					$dependencies = array_merge( $asset['dependencies'], [ 'wp-util' ] );
				}

				wp_enqueue_script( 'evbb-admin-dashboard', EVBB_DIR_URL . 'build/admin/dashboard.js', $dependencies, EVBB_VERSION, true );

				wp_set_script_translations( 'evbb-admin-dashboard', 'background-block', EVBB_DIR_PATH . 'languages' );
			}
		}
	}
	new Submenu();
}