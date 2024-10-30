<?php
// Prevent direct access to file.
defined( 'ABSPATH' ) || exit;

if( ! class_exists( 'WooMC_Frontend_Session', false ) ) {

	class WooMC_Frontend_Session extends  WC_Session_Handler {

		/**
		 * WooMC_Frontend_Session constructor.
		 */
		function __construct() {
			add_action( 'woomc_currency_switched', [ $this, 'reset_woocommerce_session' ], 10, 1 );
		}

		/**
		 * If currency is switched, update session with new prices.
		 */
		function reset_woocommerce_session() {

			WC()->cart->calculate_totals();
			$session = new WC_Cart_Session( WC()->cart );
			$session->set_session();
			$this->_dirty = true;
			WC()->session->save_data();

			add_action( 'wp_enqueue_scripts', 'woomc_refresh_fragments' );
		}
	}
}
