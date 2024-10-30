<?php
/**
 * Plugin Name: Multi Currency Pro for WooCommerce
 * Description: Enable multi currency for woocommerce.
 * Version: 1.0.0
 * Plugin URI: http://sharethingz.com
 * Author: Ankit Gade
 * Author URI: https://profiles.wordpress.org/wpgurudev
 * Text-domain: woomc
 */

defined( 'ABSPATH' ) || exit;

// Define WC_PLUGIN_FILE.
if ( ! defined( 'WOOMC_PLUGIN_FILE' ) ) {
	define( 'WOOMC_PLUGIN_FILE', __FILE__ );
}

// Include the main woomc class.
if ( ! class_exists( 'WooMC' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-woomc.php';
	include_once dirname( __FILE__ ) . '/includes/woomc-functions.php';
}

function WOOMC() {
	return WooMC::instance();
}

register_activation_hook( WOOMC_PLUGIN_FILE, 'woomc_install' );
register_deactivation_hook( WOOMC_PLUGIN_FILE, 'woomc_uninstall' );

// Global for backwards compatibility.
$GLOBALS['woomc'] = WOOMC();