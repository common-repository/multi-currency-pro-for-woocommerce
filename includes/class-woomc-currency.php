<?php
defined( 'ABSPATH' ) || exit;

class WooMC_Currency {

	/**
	 * @var string Current currency.
	 */
	protected $current_currency;

	/**
	 * WooMC_Currency constructor.
	 */
	function __construct() {

		add_filter( 'woocommerce_currency_symbol', [ $this, 'currency_symbol' ], 20, 2 );
		add_action( 'woocommerce_checkout_process', [ $this, 'checkout_currency' ], 20 );
	}

	/**
	 * Gets the currency symbol for current currency.
	 */
	function currency_symbol( $symbol, $currency ) {

		remove_filter( 'woocommerce_currency_symbol', [ $this, 'currency_symbol' ], 20 );
		$symbol = apply_filters( 'woomc_currency_symbol', get_woocommerce_currency_symbol( $this->get_current_currency() ), $currency );
		add_filter( 'woocommerce_currency_symbol', [ $this, 'currency_symbol' ], 20, 2 );

		return $symbol;
	}

	/**
	 * Actions to process when order is created before checkout.
	 */
	function checkout_currency() {

		if ( defined( 'WOOCOMMERCE_CHECKOUT' ) && WOOCOMMERCE_CHECKOUT ) {
			add_filter( 'woocommerce_currency', function ( $currency ) {
				$currency = $this->get_current_currency();

				return $currency;
			} );
		}
	}

	/**
	 * Get current currency.
	 */
	function get_current_currency() {
		global $woomc;

		if ( ! is_null( $this->current_currency ) ) {
			return $this->current_currency;
		}

		$requested_currency = filter_input( INPUT_GET, 'woomc_currency', FILTER_SANITIZE_STRING );
		$current_currency   = $this->is_valid_currency( $requested_currency ) ? $requested_currency : $this->getcookie( 'woomc_currency' );

		if ( empty( $current_currency ) ) {
			$current_currency = $woomc->settings->get_default_currency();
		}

		$set = $this->set_current_currency( $current_currency );

		if ( $set ) {
			do_action( 'woomc_currency_switched', $current_currency );
		}

		return $current_currency;
	}

	/**
	 * Check if the provided currency is valid currency.
	 *
	 * @param string $currency Currency code.
	 *
	 * @return bool $valid Valid or not.
	 */
	function is_valid_currency( $currency ) {
		global $woomc;

		$valid = false;

		if( empty ( $currency ) ) {
			return $valid;
		}

		$currencies = $woomc->settings->get_currencies();
		$currencies = wp_list_pluck( $currencies, 'currency' );

		return in_array( $currency, $currencies );
	}

	/**
	 * Set current currency.
	 *
	 * @param $currency Currency code.
	 *
	 * @return bool $set Whether cookie is set or not.
	 */
	function set_current_currency( $currency ) {

		$set = $this->setcookie( 'woomc_currency', $currency, time() + 24 * HOUR_IN_SECONDS, '/' );
		! empty( $set ) ? $this->current_currency = $currency : '';

		return $set;
	}

	/**
	 * Get settings for particular currency.
	 *
	 * @param string $currency Currency code to retrieve data for.
	 *
	 * @return array $data Currency data.
	 */
	function get_currency_data( $currency = '' ) {
		global $woomc;

		$data = [];
		$currencies = $woomc->settings->get_currencies();

		foreach ( $currencies as $curr ) {

			if ( $curr['currency'] == $currency ) {
				$data = $curr;
			}
		}

		return $data;
	}

	/**
	 * Sets the cookie
	 */
	public function setcookie( $name, $value, $expires, $path ) {

		return setcookie( $name, $value, $expires, $path );
	}

	function getcookie( $cookie_name = 'woomc_currency' ) {

		return empty( $_COOKIE[$cookie_name] ) ? '' : $_COOKIE[$cookie_name];
	}

	/**
	 * Get formatted price.
	 */
	function get_formatted_price( $price, $currency ) {

		$args = apply_filters( 'woomc_get_formatted_price', [ 'currency' => $currency ] );

		return apply_filters( 'woomc_get_formatted_price', wc_price( $price, $args ) );
	}
}