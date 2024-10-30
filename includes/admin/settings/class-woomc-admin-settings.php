<?php

defined( 'ABSPATH' ) || exit;

class WooMC_Admin_Settings {

	static function init() {

		add_action( 'admin_menu', [ __CLASS__, 'menu' ] );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'scripts' ] );
		add_action( 'woomc_admin_settings_tabs_content', [ 'WooMC_Admin_Settings_Markup', 'tab_content' ] );
		add_action( 'woomc_tab_content_general', array( __CLASS__, 'general_settings' ) );
		add_action( 'woomc_tab_content_design', array( __CLASS__, 'design_settings' ) );
		add_action( 'woomc_tab_content_location', array( __CLASS__, 'location_settings' ) );
	}

	/**
	 * Initialization function for settings page.
	 */
	static function menu() {

		add_menu_page(
			'woomc',
			__( 'WOOMC', 'woomc' ),
			'manage_woocommerce',
			'woomc-settings',
			array( __CLASS__, 'callback' )
		);
	}

	/**
	 * Enqueue scripts and styles
	 */
	static function scripts( $hook ) {

		if ( 'toplevel_page_woomc-settings' === $hook ) {

			wp_enqueue_style( 'woomc-bootstrap', plugins_url( 'assets/css/bootstrap.min.css', WOOMC_PLUGIN_FILE ) );
			wp_enqueue_script( 'woomc-bootstrap-js', plugins_url( 'assets/js/bootstrap.min.js', WOOMC_PLUGIN_FILE ) );
		}
	}

	/**
	 * Get current tab.
	 */
	static function current_tab() {
		$active_tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );
		$active_tab = empty( $active_tab ) ? 'general' : $active_tab;

        return $active_tab;
	}

	/**
	 * Callback function for rendering settings page content.
	 */
	static function callback() {
	    $active_tab = self::current_tab();
	    ?>
        <div class="woomc-settings-wrapper">

        <h3><?php _e( 'WooCommerce Multi Currency', 'woomc' ); ?></h3>

        <form id="wooc-settings-form" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">

            <ul class="nav nav-tabs" id="woomc-admin-tabs" role="tablist"><?php

	            /**
	             * Add General Tab.
	             */
	            WooMC_Admin_Settings_Markup::menu_tab(

		            [
			            'class'   => 'nav-link woomc-admin-tab',
			            'href'    => '#general',
			            'id'      => 'general-tab',
			            'text'    => __( 'General', 'woomc' ),
			            'tabname' => 'general',
		            ]
	            );

	            /**
	             * Add Design Tab.
	             */
	            WooMC_Admin_Settings_Markup::menu_tab(

		            [
			            'class'   => 'nav-link',
			            'href'    => '#design',
			            'id'      => 'design-tab',
			            'text'    => __( 'Design', 'woomc' ),
			            'tabname' => 'design',
		            ]
	            );

	            /**
	             * Add Design Tab.
	             */
	            WooMC_Admin_Settings_Markup::menu_tab(

		            [
			            'class'   => 'nav-link',
			            'href'    => '#location',
			            'id'      => 'location-tab',
			            'text'    => __( 'Location', 'woomc' ),
			            'tabname' => 'location'
		            ]
	            );

	            /**
				 * Add custom tabs using theme/plugin code.
				 */
				do_action( 'wooc_admin_settings_tabs' );

				?>

            </ul>

            <div class="tab-content p-3">
				<?php do_action( 'woomc_admin_settings_tabs_content' ); ?>
            </div>

            <input name="action" value="woomc_settings_save" type="hidden" />
            <input name="woomctab" id="woomctab" type="hidden" value="<?php echo $active_tab; ?>" />
            <button class="button button-primary"><?php _e('Save', 'woomc') ?></button>

        </form>

        </div><?php
	}

	/**
	 * Render general settings
	 */
	static function general_settings() {
	    load_template( WOOMC_ABSPATH. 'includes/admin/settings/views/general-settings.php', false );
	}

	/**
     * Render Design related settings.
     */
	static function design_settings() {
		load_template( WOOMC_ABSPATH. 'includes/admin/settings/views/design-settings.php', false );
    }

    /**
     * Render locations related settings.
     */
    static function location_settings() {
	    load_template( WOOMC_ABSPATH. 'includes/admin/settings/views/location-settings.php', false );
    }
}