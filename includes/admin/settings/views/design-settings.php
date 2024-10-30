<?php
defined( 'ABSPATH' ) || exit;

global $woomc;
?>

<h5 style="margin-bottom: 20px;"><?php _e( 'Currency Bar', 'woomc' ) ?></h5><?php

// Enable disable currency bar.
WooMC_Admin_Settings_Markup::woomc_setting_row(

	[
		'wrapper_classes' => 'woomc-row',

		'label' => [
			'id'    => 'currency_bar',
			'class' => 'custom-label',
			'text'  => __( 'Enable Currency Bar', 'woomc' ),
		],

		'input' => [
			'field_type'        => 'input',
			'input_type'        => 'checkbox',
			'class'             => 'form-control',
			'id'                => 'currency_bar',
			'checked'           => ( $woomc->settings->currency_bar == 1 ) ? 1 : 0,
			'custom_attributes' => [
				'name'  => 'woomc_settings[currency_bar]',
				'value' => 1,
			]
		]
	]
);

// Title for currency bar
WooMC_Admin_Settings_Markup::woomc_setting_row(

	[
		'wrapper_classes' => 'woomc-row',

		'label' => [
			'id'    => 'currency-bar-title',
			'class' => 'custom-label',
			'text'  => __( 'Title for currency bar', 'woomc' )
		],

		'input' => [
			'field_type'        => 'input',
			'input_type'        => 'text',
			'class'             => 'form-control',
			'id'                => 'currency-bar-title',
			'value'             => $woomc->settings->currency_bar_title,
			'custom_attributes' => [
				'name' => 'woomc_settings[currency_bar_title]',
                'autocomplete' => 'off'
			]
		]
	]
);

// Currency bar position.
WooMC_Admin_Settings_Markup::woomc_setting_row(

	[
		'wrapper_classes' => 'woomc-row',

		'label' => [
			'id'    => 'currency-bar-position',
			'class' => 'custom-label',
			'text'  => __( 'Currency bar position', 'woomc' )
		],

		'input' => [
			'field_type' => 'select',
			'input_type' => 'checkbox',
			'class'      => 'form-control',
			'id'         => 'currency-bar-positionz',
			'name'       => 'woomc_settings[currency_bar_position]',
			'value'      => $woomc->settings->currency_bar_position,
			'options'    => [
				'left'  => __( 'Left', 'woomc' ),
				'right' => __( 'Right', 'woomc' ),
			]
		]
	]
);

// Text color for currency bar
WooMC_Admin_Settings_Markup::woomc_setting_row(

	[
		'wrapper_classes' => 'woomc-row',

		'label' => [
			'id'    => 'currency-bar-text-color',
			'class' => 'custom-label',
			'text'  => __( 'Text color for currency bar', 'woomc' )
		],

		'input' => [
			'field_type'        => 'input',
			'input_type'        => 'text',
			'class'             => 'form-control jscolor',
			'id'                => 'currency-bar-text-color',
			'value'             => $woomc->settings->currency_bar_text_color,
			'custom_attributes' => [
				'name' => 'woomc_settings[currency_bar_text_color]',
				'autocomplete' => 'off'
			]
		]
	]
);

// Main color for currency bar
WooMC_Admin_Settings_Markup::woomc_setting_row(

	[
		'wrapper_classes' => 'woomc-row',

		'label' => [
			'id'    => 'currency-bar-main-color',
			'class' => 'custom-label',
			'text'  => __( 'Main color for currency bar', 'woomc' )
		],

		'input' => [
			'field_type'        => 'input',
			'input_type'        => 'text',
			'class'             => 'form-control jscolor',
			'id'                => 'currency-bar-main-color',
			'value'             => $woomc->settings->currency_bar_main_color,
			'custom_attributes' => [
				'name' => 'woomc_settings[currency_bar_main_color]',
				'autocomplete' => 'off'
			]
		]
	]
);

// Background color for currency bar
WooMC_Admin_Settings_Markup::woomc_setting_row(

	[
		'wrapper_classes' => 'woomc-row',

		'label' => [
			'id'    => 'currency-bar-bg-color',
			'class' => 'custom-label',
			'text'  => __( 'Background color for currency bar', 'woomc' )
		],

		'input' => [
			'field_type'        => 'input',
			'input_type'        => 'text',
			'class'             => 'form-control jscolor',
			'id'                => 'currency-bar-bg-color',
			'value'             => $woomc->settings->currency_bar_bg_color,
			'custom_attributes' => [
				'name' => 'woomc_settings[currency_bar_bg_color]',
				'autocomplete' => 'off'
			]
		]
	]
);
