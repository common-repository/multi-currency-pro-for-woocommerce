<?php
defined( 'ABSPATH' ) || exit;

global $woomc;

// Enable auto detect location.
WooMC_Admin_Settings_Markup::woomc_setting_row(

	[
		'wrapper_classes' => 'woomc-row',

		'label' => [
			'id'    => 'auto-detect-location',
			'class' => 'custom-label',
			'text'  => __( 'Auto Detect Location', 'woomc' )
		],

		'input' => [
			'field_type' => 'select',
			'class'      => 'form-control',
			'id'         => 'auto-detect-location',
			'name'       => 'woomc_settings[auto_detect_location]',
			'value'      => $woomc->settings->auto_detect_location,
			'options'    => [
				'yes' => __( 'Yes', 'woomc' ),
				'no'  => __( 'No', 'woomc' ),
			]
		]
	]
);

// Enable setting up currency by country.
WooMC_Admin_Settings_Markup::woomc_setting_row(

	[
		'wrapper_classes' => 'woomc-row',

		'label' => [
			'id'    => 'currency-by-country',
			'class' => 'custom-label',
			'text'  => __( 'Currency by country', 'woomc' )
		],

		'input' => [
			'field_type' => 'select',
			'class'      => 'form-control',
			'id'         => 'currency-by-country',
			'name'       => 'woomc_settings[currency_by_country]',
			'value'      => $woomc->settings->currency_by_country,
			'options'    => [
				'yes' => __( 'Yes', 'woomc' ),
				'no'  => __( 'No', 'woomc' ),
			]
		]
	]
);

// Display currency table.
include_once WOOMC_ABSPATH. 'includes/admin/settings/views/currency-by-country.php';
