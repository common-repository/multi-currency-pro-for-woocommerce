<?php
// Prevent direct access to file.
defined( 'ABSPATH' ) || exit;

if( ! class_exists( 'WooMC_Frontend_Coupon', false ) ) {

	class WooMC_Frontend_Coupon {

		/**
		 * WooMC_Frontend_Coupon constructor.
		 */
		function __construct() {
			add_filter( 'woocommerce_coupon_get_amount', [ $this, 'woomc_coupon_get_amount' ], 10, 2 );
			add_filter( 'woocommerce_boost_sales_coupon_amount_price', [ $this, 'woomc_coupon_price' ], 10, 1 );

			$actions = [
				'get_minimum_amount',
				'get_maximum_amount',
				'get_minimum_amount',
			];

			foreach( $actions as $action ) {
				add_filter( 'woocommerce_coupon_'. $action, [ $this, 'woomc_coupon_price' ], 10 );
			}
		}

		/**
		 * Get coupon amount.
		 *
		 * @param float $value Value of coupon.
		 * @param object $object Couppon object.
		 *
		 * @return float Filtered value.
		 */
		function woomc_coupon_get_amount( $value, $object ) {
			global $woomc;

			if ( $object->is_type( array( 'percent' ) ) ) {
				return $value;
			}

			return $woomc->prices->calculate_price( $value );
		}

		/**
		 * Convert coupon amount to respective currency.
		 *
		 * @param float $value Coupon amount.
		 *
		 * @return float Filtered coupon amount.
		 */
		function woomc_coupon_price( $value ) {
			global $woomc;

			return $woomc->prices->calculate_price( $value );
		}
	}
}
