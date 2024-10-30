<?php
/**
 * WooCommerce Multi Currency Autoload.
 */

defined( 'ABSPATH' ) || exit;

class WooMC_Autoload {

	private $include_path;

	function __construct() {

		$this->include_path = untrailingslashit( plugin_dir_path( WOOMC_PLUGIN_FILE ) ) . '/includes/';
		spl_autoload_register( array( $this, 'autoload' ) );
	}

	/**
	 * Determines files name from its classname.
	 */
	private function get_file_name_from_class( $class ) {
		return 'class-' . str_replace( '_', '-', $class ) . '.php';
	}

	/**
	 * Include a class file.
	 *
	 * @param  string $path File path.
	 * @return bool Successful or not.
	 */
	private function load_file( $path ) {
		if ( $path && is_readable( $path ) ) {
			include_once $path;
			return true;
		}
		return false;
	}

	/**
	 * Autoload function.
	 * Checkes name of class and determines which file to load.
	 * Reduces memory consumption.
	 *
	 * @param string $class Class name.
	 */
	function autoload( $class ) {

		$class = strtolower( $class );

		if ( 0 !== strpos( $class, 'woomc_' ) ) {
			return;
		}

		$file = $this->get_file_name_from_class( $class );
		$path = '';

		if ( 0 === strpos( $class, 'woomc_admin_' ) ) {
			$path = $this->include_path . 'admin/';
		}

		if ( 0 === strpos( $class, 'woomc_admin_settings' ) ) {
			$path = $this->include_path . 'admin/settings/';
		}

		if ( 0 === strpos( $class, 'woomc_frontend' ) ) {
			$path = $this->include_path . 'frontend/includes/';
		}

		if ( empty( $path ) || ! $this->load_file( $path . $file ) ) {
			$this->load_file( $this->include_path . $file );
		}

	}
}

new WooMC_Autoload();