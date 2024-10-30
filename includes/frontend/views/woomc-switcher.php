<?php
defined( 'ABSPATH' ) || exit;

global $woomc;
$switcher_style = $woomc->settings->get_switcher_style();

if ( 0 !== $switcher_style ) { ?>

    <div class="woomc-switcher-container"><?php

	if ( ! empty( $prices_in_currencies ) ) { ?>

        <ul class="woomc-switcher"><?php

		foreach ( $prices_in_currencies as $currency => $prices ) {

		    $country_data = get_country_data( $currency ); ?>

            <li class="woomc-switcher-item">
            <a data-currency="<?php echo esc_attr( $currenct_currency ); ?>" title="<?php echo $country_data['name'] ?>"
               href="<?php echo woomc_product_link( $product, $currency ); ?>"><?php

				if ( in_array( $switcher_style, [ 1, 3 ] ) ) {

					$flag_data = get_country_flag( $country_data['code'] ); ?>
                    <span class="switcher-element flag-element">
                    <img src="<?php echo $flag_data['url'] ?>" width="<?php echo $flag_data['width'] ?>"
                         height="<?php echo $flag_data['height'] ?>"/>
                    </span><?php
				}

				if ( in_array( $switcher_style, [ 2, 3 ] ) ) { ?>
                    <span class="switcher-element price-element"><?php echo implode( '&nbsp;-&nbsp;', $prices_in_currencies[ $currency ] ); ?></span><?php
				}
				?>
            </a>
            </li><?php

		} ?>

        </ul><?php

	} ?>

    </div><?php

}
