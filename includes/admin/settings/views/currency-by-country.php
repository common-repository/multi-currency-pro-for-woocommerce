<?php
defined( 'ABSPATH' ) || exit;

global $woomc;
$currencies = $woomc->settings->get_currencies();
$countries  = WC()->countries->get_countries();
?>

<table id="woomc-location-table" class="table table-responsive-md table-bordered text-center">

    <thead class="thead-light">
	    <tr>
			<th scope="col"><?php _e( 'Currency', 'woomc' ) ?></th>
			<th scope="col" colspan="5"><?php _e( 'Countries', 'woomc' ) ?></th>
			<th scope="col"><?php _e( 'Actions', 'woomc' ) ?></th>
		</tr>
	</thead>

	<tbody><?php

	foreach ( $currencies as $currency ) {

	    $currency_countries = (array)$woomc->settings->country_currency;
	    $currency_countries = array_key_exists( $currency['currency'], $currency_countries ) ? $currency_countries[ $currency['currency'] ] : [];
	    ?>

		<tr>
			<td>
				<?php echo $currency['currency']; ?>
			</td>

			<td colspan="5">
				<select style="min-width: 250px;" class="woomc-currency-by-country" name="woomc_settings[country_currency][<?php echo $currency['currency'] ?>][]" multiple="multiple">
					<?php
					foreach( $countries as $country_code => $country_name ) {

					    $selected = in_array( $country_code, $currency_countries );
					    ?>
						<option <?php echo ( $selected ) ? 'selected="selected"' : ''; ?> value="<?php echo $country_code ?>"><?php echo $country_name; ?></option><?php
					}
					?>
				</select>
			</td>

			<td>
				<button class="button select-all"><?php _e('Select All', 'woomc' ) ?></button>
				<button class="button remove-all"><?php _e( 'Remove All', 'woomc' ); ?></button>
			</td>
		</tr><?php
	}

	?>
	</tbody>

</table>