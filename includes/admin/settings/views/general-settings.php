<?php
defined( 'ABSPATH' ) || exit;

global $woomc;

// Enable disable multi currency.
WooMC_Admin_Settings_Markup::woomc_setting_row(

	[
		'wrapper_classes' => 'woomc-row',

		'label' => [
			'id'    => 'enable',
			'class' => 'custom-label',
			'text'  => __( 'Enable Multi Currency', 'woomc' )
		],

		'input' => [
			'field_type'        => 'input',
			'input_type'        => 'checkbox',
			'class'             => 'form-control',
			'id'                => 'enable',
			'checked' => ( $woomc->settings->enabled() === 1 ) ? 1 : 0,
			'custom_attributes' => [
				'name'    => 'woomc_settings[enable]',
				'value'   => 1,
			]
		]
	]
);

// Currency switcher style.
WooMC_Admin_Settings_Markup::woomc_setting_row(

	[
		'wrapper_classes' => 'woomc-row',

		'label' => [
			'id'    => 'woomc-switcher-style',
			'class' => 'custom-label',
			'text'  => __( 'Currency Switcher Style', 'woomc' )
		],

		'input' => [
			'field_type'        => 'select',
			'input_type'        => 'checkbox',
			'class'             => 'form-control',
			'id'                => 'woomc-switcher-style',
			'custom_attributes' => [ 'name' => 'woomc_settings[switcher_style]' ],
			'value'             => $woomc->settings->get_switcher_style(),
			'options'           => [
				0 => __( 'Do not show', 'woomc' ),
				1 => __( 'Flag only', 'woomc' ),
				2 => __( 'Price only', 'woomc' ),
				3 => __( 'Flag + Price', 'woomc' ),
			]
		]
	]
);

// Display currency table.
include_once WOOMC_ABSPATH. 'includes/admin/settings/views/currency-table.php';
