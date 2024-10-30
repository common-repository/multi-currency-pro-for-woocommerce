<?php
defined( 'ABSPATH' ) || exit;

/**
 * Main WooCommerce Multi Currency Class
 */
class WooMC {

	/**
	 * WooMC Instance.
	 * @var WooMC
	 */
	protected static $_instance;

	/**
	 * Plugin version number.
	 * @var Version
	 */
	public $version = '1.0.0';

	/**
	 * Main WooCommerce Multi Currency Instance.
	 *
	 * Ensures only one instance of WooCommerce MC is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return WooCommerce MC - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	function __construct() {

		$this->define_constants();
		$this->hooks();
		add_action( 'woocommerce_loaded', array( $this, 'includes' ) );
	}

	/**
	 * Define constats for WooCommerce Multi Currency plugin.
	 */
	private function define_constants() {

		$this->define( 'WOOMC_ABSPATH', dirname( WOOMC_PLUGIN_FILE ) . '/' );
		$this->define( 'WOOMC_PLUGIN_BASENAME', plugin_basename( WOOMC_PLUGIN_FILE ) );
		$this->define( 'WOOMC_VERSION', $this->version );
	}

	/**
	 * Hooks to run on particular instances.
	 */
	function hooks() {

		add_action( 'wp_loaded', [ $this, 'init' ], 11 );
		add_action( 'wp_loaded', [ 'WooMC_Admin_Settings', 'init' ], 20 );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ] );

		if( $this->is_request( 'frontend' ) ) {
			add_action( 'wp_loaded', function() { $this->currency->get_current_currency(); }, 20 );
		}
	}

	/**
	 * Define constant if not already set.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Initialize the necessary properties.
	 */
	function init() {

		if ( $this->is_request( 'frontend' ) ) {
			$this->prices   = new WooMC_Prices();
			$this->currency = new WooMC_Currency();
			$this->frontend = new WooMC_Frontend();
		}
	}

	/**
	 * Enqueue scripts for admin.
	 *
	 * @param $hook
	 */
	function admin_scripts( $hook ) {
		wp_enqueue_style( 'woomc-select2-style', plugins_url( 'assets/css/select2.css', WOOMC_PLUGIN_FILE ), array(), false, 'all' );

		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'woomc-select2', plugins_url( 'assets/js/admin/select2.js', WOOMC_PLUGIN_FILE ), array( 'jquery' ), false, true );
		wp_enqueue_script( 'woomc-jscolor', plugins_url( 'assets/js/admin/jscolor.js', WOOMC_PLUGIN_FILE ), array( 'jquery' ), false, true );
		wp_enqueue_script( 'admin-woomc-rate', plugins_url( 'assets/js/admin/woomc-update-rate.js', WOOMC_PLUGIN_FILE ), array( 'jquery' ), false, true );
		wp_enqueue_script( 'admin-woomc', plugins_url( 'assets/js/admin/woomc.js', WOOMC_PLUGIN_FILE ), array(
			'woomc-jscolor',
			'woomc-select2'
		), false, true );
	}

	/**
	 * Enqueue scripts for frontend.
	 */
	function scripts() {
		global $wp_scripts;
		//db($wp_scripts->get_data('wc-price-slider', 'data'));

		$price_slider_var = [
			'object_name' => 'woocommerce_price_slider_params',
			'data' => [
				'currency_format_num_decimals' => 0,
				'currency_format_symbol'       => get_woocommerce_currency_symbol(),
				'currency_format_decimal_sep'  => esc_attr( wc_get_price_decimal_separator() ),
				'currency_format_thousand_sep' => esc_attr( wc_get_price_thousand_separator() ),
				'currency_format'              => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ),
			]
		];

		$script = "var  " . $price_slider_var['object_name'] . " = " . wp_json_encode( $price_slider_var['data'] ) . ";";
		$wp_scripts->add_data('wc-price-slider', 'data', $script );

		wp_enqueue_style( 'woomc-style', plugins_url( 'assets/css/woomc.css', WOOMC_PLUGIN_FILE ), array(), false, 'all' );
	}

	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 * @return bool
	 */
	private function is_request( $type ) {

		global $woocommerce;

		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! $woocommerce->is_rest_api_request();
		}
	}

	/**
	 * Includes necessary files for woocommercce.
	 */
	function includes() {

		// Class autoloader.
		include_once WOOMC_ABSPATH . 'includes/class-woomc-autoload.php';
		$this->settings = new WooMC_Settings();
	}
}