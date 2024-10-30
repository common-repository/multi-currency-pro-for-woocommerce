<?php
// Prevent direct access to file.
defined( 'ABSPATH' ) || exit;

if( ! class_exists( 'WooMC_Frontend', false ) ) {

	/**
	 * Class WooMC_Frontend
	 */
	class WooMC_Frontend {

		/**
		 * Cart functionality handler.
		 *
		 * @var $cart
		 */
		public $cart;

		/**
		 * Coupon functionality handler.
		 *
		 * @var $cart
		 */
		public $coupon;

		/**
		 * WooCommerce session functionality handler.
		 *
		 * @var $cart
		 */
		public $session;

		/**
		 * WooMC_Frontend constructor.
		 */
		function __construct() {
			$this->cart     = new WooMC_Frontend_Cart();
			$this->coupon   = new WooMC_Frontend_Coupon();
			$this->session  = new WooMC_Frontend_Session();
			$this->location = new WooMC_Frontend_Location();

			add_filter( 'woocommerce_price_filter_widget_min_amount', [$this, 'woomc_price_filter_widget_min_amount'] );
			add_filter( 'woocommerce_price_filter_widget_max_amount', [$this, 'woomc_price_filter_widget_min_amount'] );
			add_action( 'pre_get_posts', [$this, 'woomc_pre_get_posts'], 12 );
			add_filter( 'the_posts', [$this, 'woomc_the_posts'], 12 );
		}

		/**
		 * Convert min/max price of price filter widget according
		 * to current currency.
		 *
		 * @param int/float $price Min/Max filter price.
		 *
		 * @return int/float Calculated min/max price.
		 */
		function woomc_price_filter_widget_min_amount( $price ) {
			global $woomc;

			$price = $woomc->prices->calculate_price( $price );
			$price = ( current_filter() == 'woocommerce_price_filter_widget_min_amount' ) ? floor( $price ) : ceil ( $price );

			return $price;
		}

		function woomc_the_posts( $posts ) {
			remove_filter( 'posts_clauses', [ $this, 'price_filter_post_clauses' ], 20 );
			return $posts;
		}

		/**
		 * pre_get_posts filter to add price filter.
		 */
		function woomc_pre_get_posts( $q ) {
			// We only want to affect the main query.
			if ( ! $q->is_main_query() ) {
				return;
			}

			if ( 'product_query' === $q->get( 'wc_query' ) ) {
				global $woocommerce;
				remove_filter( 'posts_clauses', [ $woocommerce->query, 'price_filter_post_clauses' ], 10 );
				add_filter( 'posts_clauses', [ $this, 'price_filter_post_clauses' ], 20, 2 );
			}

			return $q;
		}

		/**
		 * Join wc_product_meta_lookup to posts if not already joined.
		 *
		 * @param string $sql SQL join.
		 * @return string
		 */
		private function append_product_sorting_table_join( $sql ) {
			global $wpdb;

			if ( ! strstr( $sql, 'wc_product_meta_lookup' ) ) {
				$sql .= " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id ";
			}
			return $sql;
		}

		/**
		 * Custom query used to filter products by price.
		 *
		 * @since 3.6.0
		 *
		 * @param array    $args Query args.
		 * @param WC_Query $wp_query WC_Query object.
		 *
		 * @return array
		 */
		public function price_filter_post_clauses( $args, $wp_query ) {
			global $wpdb, $woomc;

			if ( ! $wp_query->is_main_query() || ( ! isset( $_GET['max_price'] ) && ! isset( $_GET['min_price'] ) ) ) {
				return $args;
			}

			$current_currency = $woomc->currency->get_currency_data( $woomc->currency->get_current_currency() );

			$current_min_price = isset( $_GET['min_price'] ) ? floatval( wp_unslash( $_GET['min_price'] ) ) : 0; // WPCS: input var ok, CSRF ok.
			$current_max_price = isset( $_GET['max_price'] ) ? floatval( wp_unslash( $_GET['max_price'] ) ) : PHP_INT_MAX; // WPCS: input var ok, CSRF ok.

			$current_min_price = floatval($current_min_price / $current_currency['rate']);
			$current_max_price = floatval($current_max_price / $current_currency['rate']);

			/**
			 * Adjust if the store taxes are not displayed how they are stored.
			 * Kicks in when prices excluding tax are displayed including tax.
			 */
			if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
				$tax_class = apply_filters( 'woocommerce_price_filter_widget_tax_class', '' ); // Uses standard tax class.
				$tax_rates = WC_Tax::get_rates( $tax_class );

				if ( $tax_rates ) {
					$current_min_price -= WC_Tax::get_tax_total( WC_Tax::calc_inclusive_tax( $current_min_price, $tax_rates ) );
					$current_max_price -= WC_Tax::get_tax_total( WC_Tax::calc_inclusive_tax( $current_max_price, $tax_rates ) );
				}
			}

			$args['join']   = $this->append_product_sorting_table_join( $args['join'] );
			$args['where'] .= $wpdb->prepare(
				' AND wc_product_meta_lookup.min_price >= %f AND wc_product_meta_lookup.max_price <= %f ',
				$current_min_price,
				$current_max_price
			);

			return $args;
		}

	}
}
