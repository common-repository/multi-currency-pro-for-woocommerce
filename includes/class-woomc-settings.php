<?php

defined( 'ABSPATH' ) || exit;

class WooMC_Settings {

	private $settings;

	function __construct() {

		$this->settings = $this->get();
		add_action( 'admin_post_woomc_settings_save', [ $this, 'process_settings_submission' ] );
	}

	/**
	 * Gets the property.
	 *
	 * @param string $name Property name.
	 *
	 * @return mixed Value of property.
	 */
	function __get( $name ) {

		if ( empty( $this->settings ) ) {
			return false;
		}

		if ( ! is_array( $this->settings ) || ! isset( $this->settings[ $name ] ) ) {
			return false;
		}

		return $this->settings[ $name ];
	}

	/**
	 * Gets the plugin settings array.
	 */
	function get() {
		return get_option( 'woomc_settings', [] );
	}

	/**
	 * Updates the settings
	 */
	function save_settings() {
		update_option( 'woomc_settings', $this->settings, 'no' );
	}

	/**
	 * Gets currencies set up in WooMC
	 */
	function get_currencies() {

		return isset( $this->settings['currencies'] ) ? $this->settings['currencies'] : [];
	}

	/**
	 * Checks if Multi currency is enabled
	 */
	function enabled() {

		return !empty( $this->settings['enable'] ) ? 1 : 0;
	}

	/**
	 * Get default currency.
	 */
	function get_default_currency() {

		$default_currency = empty( $this->settings['default'] ) ? get_woocommerce_currency() : $this->settings['default'];
		return apply_filters( 'woomc_default_currency', $default_currency );
	}

	/**
	 * Get switcher style
	 */
	function get_switcher_style() {

		$switcher_style = empty( $this->settings['switcher_style'] ) ? 0 : $this->settings['switcher_style'];
		return apply_filters( 'woomc_switcher_style', $switcher_style );
	}

	/**
	 * Validates and saves settings.
	 */
	function process_settings_submission() {

		$settings = ( empty( $_POST['woomc_settings'] ) || ! is_array( $_POST['woomc_settings'] ) ) ? [] : $_POST['woomc_settings'];
		$tab      = filter_input( INPUT_POST, 'woomctab', FILTER_SANITIZE_STRING );
		$tab      = empty( $tab ) ? 'general' : $tab;

		$validation_success = apply_filters( 'woomc_settings_submit_validate', true, $settings );

		if( $validation_success ) {

			/**
			 * Actions to perform before settings are saved.
			 *
			 * @param array $settings Array of settings options.
			 */
			do_action_ref_array( 'woomc_before_settings_save', [ &$settings ] );

			$this->settings = $settings;
			$this->save_settings();

			/**
			 * Actions to perform after settings are saved.
			 *
			 * @param array $settings Array of settings options.
			 */
			do_action( 'woomc_before_settings_save', $settings );

			wp_redirect( add_query_arg( [ 'page' => 'woomc-settings', 'tab' => $tab ], admin_url('admin.php') ) );
			exit;
		}

	}

}