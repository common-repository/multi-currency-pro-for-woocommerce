<?php
// Prevent direct access to file.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooMC_Frontend_Location', false ) ) {

	class WooMC_Frontend_Location {

		/**
		 * Country code.
		 * @var $country_code
		 */
		public $country_code;

		/**
		 * WooMC_Frontend_Location constructor.
		 */
		function __construct() {
			add_action( 'wp_loaded', [ $this, 'init' ] );
		}

		/**
		 * Initialize location module.
		 */
		function init() {
			global $woomc;

			if( 'no' === $woomc->settings->auto_detect_location || 'no' === $woomc->settings->currency_by_country ) {
				return;
			}

			$geolocation     = new WC_Geolocation();
			$geolocate_ip    = $geolocation->geolocate_ip( $geolocation::get_ip_address(), true );
			$location_cookie = $woomc->currency->getcookie( 'woomc_location_currency' );
			$currency        = ( ! empty( $_GET['woomc_currency'] ) && $woomc->currency->is_valid_currency( $_GET['woomc_currency'] ) ) ? $_GET['woomc_currency'] : null;

			/**
			 * `woomc_currency` parameter has more priority over
			 *  settings currency by location.
			 */
			if ( ! empty( $geolocate_ip['country'] ) && is_null( $currency ) && empty( $location_cookie ) ) {

				$this->country_code = $geolocate_ip['country'];
				$currency           = $this->maybe_get_currency_by_country();
			}

			if ( $currency ) {
				$woomc->currency->set_current_currency( $currency );
				$woomc->currency->setcookie( 'woomc_location_currency', $currency, time() + WEEK_IN_SECONDS * 1, '/' );
				$woomc->frontend->session->reset_woocommerce_session();
			}

		}

		/**
		 * Check currency by country code.
		 */
		function maybe_get_currency_by_country() {
			global $woomc;

			$country_currency = $woomc->settings->country_currency;
			$currency         = false;

			if ( empty( $this->country_code ) || empty( $country_currency ) ) {
				return false;
			}

			foreach ( $country_currency as $currency_code => $countries ) {

				if ( in_array( $this->country_code, $countries ) ) {

					$currency = $currency_code;
					break;
				}
			}

			return $currency;
		}

	}
}
