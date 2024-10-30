<?php
defined( 'ABSPATH' ) || exit;

global $woomc;

$wc_currencies = [ '' => __( 'Select Currency', 'woomc' ) ] + get_woocommerce_currencies();

$currency_pos = [
	'left'        => __( 'Left - $10', 'woomc' ),
	'right'       => __( 'Right 10$', 'woomc' ),
	'left_space'  => __( 'Left with space - $ 10', 'woomc' ),
	'right_space' => __( 'Right with space - 10 $', 'woomc' ),
];

?>

<table id="woomc-currency-table" class="table table-responsive-md table-bordered text-center">

    <thead class="thead-light">
    <tr>
        <th scope="col"><?php _e( 'Default', 'woomc' ) ?></th>
        <th scope="col"><?php _e( 'Currency', 'woomc' ) ?></th>
        <th colspan="2" scope="col"><?php _e( 'Rate + Exchange Charges', 'woomc' ) ?></th>
        <th scope="col"><?php _e( 'Decimals', 'woomc' ) ?></th>
        <th scope="col"><?php _e( 'Currency Position', 'woomc' ) ?></th>
        <th scope="col"><?php _e( 'Action', 'woomc' ) ?></th>
    </tr>
    </thead>

    <tbody class="woomc-clone-wrap"><?php

	foreach ( $woomc->settings->get_currencies() as $key => $val ) {

		$val = (object) $val; ?>

        <tr class="woomc-toclone">
        <td class="align-middle">
            <input class="form-check-input" type="radio" name="woomc_settings[default]"
                   value="<?php echo $val->currency; ?>"
                <?php checked( $val->currency, $woomc->settings->get_default_currency() ) ?> />
        </td>

        <td class="align-middle">
			<?php
			WooMC_Admin_Settings_Markup::select(
				[
					'options' => $wc_currencies,
					'class'   => 'custom-select woomc-indexed-input',
					'name'    => 'woomc_settings[currencies][' . $key . '][currency]',
                    'value'   => property_exists( $val, 'currency' ) ? $val->currency : '',
				]
			);
			?>
        </td>

        <td class="align-middle"><?php

			WooMC_Admin_Settings_Markup::input(
				[
					'input_type'        => 'text',
					'class'             => 'form-control woomc-rate',
					'name'              => 'woomc_settings[currencies][' . $key . '][rate]',
					'value'             => property_exists( $val, 'rate' ) ? $val->rate : 1,
					'custom_attributes' => [
						'maxlength'    => 1,
						'autocomplete' => 'off',
						'placeholder'  => __( 'Rate', 'woomc' )
					]
				]
			);
			?>
        </td>

        <td class="align-middle"><?php

			WooMC_Admin_Settings_Markup::input(
				[
					'input_type'        => 'text',
					'class'             => 'form-control woomc-exchange',
					'name'              => 'woomc_settings[currencies][' . $key . '][exchange]',
					'value'             => property_exists( $val, 'exchange' ) ? $val->exchange : 0,
					'custom_attributes' => [ 'autocomplete' => 'off', 'placeholder' => __( 'Exchange', 'woomc' ) ]
				]
			);
			?>
        </td>

        <td class="align-middle"><?php
			WooMC_Admin_Settings_Markup::input(
				[
					'input_type'        => 'text',
					'class'             => 'form-control',
					'name'              => 'woomc_settings[currencies][' . $key . '][decimals]',
					'value'             => property_exists( $val, 'decimals' ) ? $val->decimals : 0,
					'custom_attributes' => [ 'maxlength' => 1, 'autocomplete' => 'off' ]
				]
			);
			?>
        </td>

        <td class="align-middle">
			<?php
			WooMC_Admin_Settings_Markup::select(
				[
					'options' => $currency_pos,
					'class'   => 'custom-select',
					'name'    => 'woomc_settings[currencies][' . $key . '][currency_pos]',
					'value'   => $val->currency_pos,
				]
			);
			?>
        </td>

        <td class="align-middle">
            <a href="#" class="button woomc-delete"><?php _e( 'Delete', 'woomc' ); ?></a>
        </td>

        </tr><?php

	} ?>

    </tbody>

    <tfoot>
        <th colspan="7">
            <a href="#" class="button woomc-clone"><?php _e( 'Add Currency', 'woomc' ); ?></a>
            <a href="#" class="button woomc-update-rates"><?php _e( 'Update all rates', 'woomc' ); ?></a>
        </th>
    </tfoot>

</table>
