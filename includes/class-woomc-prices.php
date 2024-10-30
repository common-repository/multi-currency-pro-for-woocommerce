<?php

defined( 'ABSPATH' ) || exit;

class WooMC_Prices{

	/**
	 * WooMC_Prices constructor.
	 */
	function __construct() {

		$this->pricing_methods = [
			'product_get_regular_price',
			'product_get_sale_price',
			'product_get_price',
			'product_variation_get_price',
			'product_variation_get_regular_price',
			'product_variation_get_sale_price',
			'variation_prices',
		];

		$this->toggle_prices_filters();

		add_filter( 'woocommerce_shipping_rate_cost', [ $this, 'shipping_cost_and_tax' ], 20, 2 );
		add_filter( 'woocommerce_get_shipping_tax', [ $this, 'shipping_cost_and_tax' ], 20, 2 );

		add_filter( 'wc_price_args', [ $this, 'woomc_wc_price_args' ] );
		add_filter( 'woocommerce_price_format', [ $this, 'woomc_woocommerce_price_format' ], 10 );
	}

	/**
	 * Magic method: __call
	 */
	function __call( $name, $arguments ) {

		if ( in_array( $name, $this->pricing_methods ) ) {

			$price = $this->filter_price( $arguments[0] );

			return apply_filters( 'woomc_' . $name, $price, $arguments[1] );
		}
	}

	/**
	 * Manipulate args for wc_price.
	 */
	function woomc_wc_price_args( $args ) {
		global $woomc;

		$current_currency = empty( $args['currency'] ) ? $woomc->currency->get_current_currency() : $args['currency'];
		$currency_data    = $woomc->currency->get_currency_data( $current_currency );

		$woomc_args = [
			'ex_tax_label'       => $args['ex_tax_label'],
			'currency'           => $current_currency,
			'decimal_separator'  => wc_get_price_decimal_separator(),
			'thousand_separator' => wc_get_price_thousand_separator(),
			'decimals'           => $currency_data['decimals'],
			'price_format'       => $this->woomc_woocommerce_price_format( $currency_data['currency_pos'] ),
		];

		return $woomc_args;
	}

	/**
	 * WooCommerce price format.
	 */
	function woomc_woocommerce_price_format( $currency_pos ) {

		$format = '%1$s%2$s';

		switch ( $currency_pos ) {
			case 'left':
				$format = '%1$s%2$s';
				break;
			case 'right':
				$format = '%2$s%1$s';
				break;
			case 'left_space':
				$format = '%1$s&nbsp;%2$s';
				break;
			case 'right_space':
				$format = '%2$s&nbsp;%1$s';
				break;
		}

		return apply_filters( 'woomc_woocommerce_price_format', $format, $currency_pos );
	}

	/**
	 * Add/Remove filters for product prices.
	 *
	 * @param bool $add Add filter. True if needs to be added, false if needs to be removed.
	 */
	function toggle_prices_filters( $add = true ) {

		foreach ( $this->pricing_methods as $method ) {
			$add ? add_filter( 'woocommerce_' . $method, [ $this, $method ], 20, 2 ) : remove_filter( 'woocommerce_' . $method, [ $this, $method ], 20 );
		}
	}

	/**
	 * Filter price to specific currency.
	 *
	 * @param float|array $price Price for product.
	 * @param object $product Product object.
	 *
	 * @return float|array Filtered price for product.
	 */
	function filter_price( $price ) {

		$price = $this->calculate_price( $price );

		return $price;
	}

	/**
	 * Calculate price for specific product in specific currency.
	 * If not currency give, current currency will be used.
	 */
	function calculate_price( $price, $currency = '' ) {
		global $woomc;

		$currency              = empty( $currency ) ? $woomc->currency->get_current_currency() : $currency;
		$current_currency_data = $woomc->currency->get_currency_data( $currency );
		$default_currency_data = $woomc->currency->get_currency_data( $woomc->settings->get_default_currency() );

		// If there are no rates for currency, do not do anything.
		if ( empty( $price ) || empty( $current_currency_data['rate'] ) ) {
			return $price;
		}

		$current_currency_data['exchange'] = ( empty( $current_currency_data['exchange'] ) || ! is_numeric( $current_currency_data['exchange'] ) ) ? 0 : $current_currency_data['exchange'];

		if ( is_array( $price ) ) {

			foreach ( $price as $key => $val ) {

				if ( ! is_array( $val ) ) {
					continue;
				}

				array_walk( $val, function ( &$element, $index, $current_currency_data ) {
					$element = ( $element * $current_currency_data['rate'] + $current_currency_data['exchange'] );
				}, $current_currency_data );

				$price[ $key ] = $val;
			}

		} else {

			// BODMAS rule.
			$price = ( $price * $current_currency_data['rate'] + $current_currency_data['exchange'] );
		}

		return $price;
	}

	/**
	 * Convert shipping cost to current currency.
	 *
	 * @param float $cost Shipping cost.
	 * @param object $shipping Shipping object.
	 *
	 * @return float $cost Shipping cost.
	 */
	function shipping_cost_and_tax( $cost, $shipping ) {
		global $woomc;

		$currency              = empty( $currency ) ? $woomc->currency->get_current_currency() : $currency;
		$current_currency_data = $woomc->currency->get_currency_data( $currency );

		$cost = ( $cost * $current_currency_data['rate'] + $current_currency_data['exchange'] );

		return $cost;
	}

	/**
	 * Get product prices in different currencies.
	 */
	static function get_product_prices( $product_id = 0 ) {
		global $woomc;

		$prices = [];

		if ( empty( $product_id ) || ! ( $product = wc_get_product( $product_id ) ) ) {
			return $prices;
		}

		$currencies = $woomc->settings->get_currencies();

		// Remove prices filters.
		$woomc->prices->toggle_prices_filters( false );

		foreach ( $currencies as $currency ) {

			$currency = $currency['currency'];

			if ( 'variable' === $product->get_type() ) {

				$min_price = $woomc->prices->calculate_price( $product->get_variation_price( 'min' ), $currency );
				$max_price = $woomc->prices->calculate_price( $product->get_variation_price( 'max' ), $currency );

				$prices[$currency] = [ $min_price, $max_price ];

			}else{
				// This is for non-veriable products.
				$prices[$currency] = [$woomc->prices->calculate_price( $product->get_price(), $currency )];
			}
		}

		$woomc->prices->toggle_prices_filters( true );

		return $prices;
	}

}