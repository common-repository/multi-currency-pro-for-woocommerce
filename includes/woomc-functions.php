<?php

defined( 'ABSPATH' ) || exit;

/**
 * Actions to perform when plugin is activated.
 */
function woomc_install() {

    $existing_settings = get_option( 'woomc_settings', false );

    if( empty( $existing_settings ) ) {

	    $defaults = [
		    'enable'         => 0,
		    'switcher_style' => 3,
		    'default'        => get_woocommerce_currency(),
		    'currencies'     => [
			    [
				    'currency'     => get_woocommerce_currency(),
				    'rate'         => 1,
				    'exchange'     => 0,
				    'decimals'     => wc_get_price_decimals(),
				    'currency_pos' => get_option( 'woocommerce_currency_pos' )
			    ]
		    ],

		    'currency_bar'            => 0,
		    'currency_bar_title'      => __( 'Select Currency', 'woomc' ),
		    'currency_bar_position'   => 'right',
		    'currency_bar_text_color' => 'FFFFFF',
		    'currency_bar_main_color' => '40A3FF',
		    'currency_bar_bg_color'   => '3780BD',
		    'auto_detect_location'    => 'no',
		    'currency_by_country'     => 'no',
		    'country_currency'        => [],
	    ];

	    update_option( 'woomc_settings', $defaults, 'no' );
    }
}

/**
 * Actions to run during plugin uninstall.
 */
function woomc_uninstall() {

	//delete_option( 'woomc_settings' );
}

/**
 * Validates settings
 */
function woomc_settings_submit_validate( $status, $settings ) {

	extract( $settings, EXTR_OVERWRITE );

	$submitted_currencies = wp_list_pluck( $currencies, 'currency' );

	if( ! in_array( $default, $submitted_currencies ) ) {
		$status = false;
	}

	return $status;
}
add_filter( 'woomc_settings_submit_validate', 'woomc_settings_submit_validate', 10, 2 );

/**
 * Change default woocommerce currency.
 */
function woomc_change_default_currency_in_woocommerce( &$settings ) {

	extract( $settings, EXTR_OVERWRITE );

	if( !empty( $default ) ) {
		update_option( 'woocommerce_currency', $default );
	}
}
add_action( 'woomc_before_settings_save', 'woomc_change_default_currency_in_woocommerce' );


/**
 * Assign rate and exchange to the default currency as
 * disabled inputs would not get submitted in form.
 */
function woomc_default_currency_rate_exchange( &$settings ) {

	extract( $settings, EXTR_OVERWRITE );

	if ( is_array( $currencies ) && ! empty( $currencies ) ) {

		foreach ( $currencies as $key => $currency ) {

			if ( $default === $currency['currency'] ) {
				$settings['currencies'][ $key ]['rate']     = 1;
				$settings['currencies'][ $key ]['exchange'] = 0;
			}
		}
	}
}

add_action( 'woomc_before_settings_save', 'woomc_default_currency_rate_exchange' );

/**
 * Display product price switcher on single product page.
 */
function woomc_product_price_switcher() {
	global $woomc, $product;

	/**
	 * Check if this is single product page and multi currency is enabled.
	 */
	if ( ! is_product() || ! $woomc->settings->enabled() ) {
		return;
	}

	$prices_in_currencies = WooMC_Prices::get_product_prices( $product );
	$currenct_currency    = $woomc->currency->get_current_currency();

	if ( ! empty( $prices_in_currencies ) ) {

		// Prepend current currency prices at the beginning of array.
		$current_currency_prices = $prices_in_currencies[$currenct_currency];
		unset( $prices_in_currencies[$currenct_currency] );
		$prices_in_currencies = [ $currenct_currency => $current_currency_prices ] + $prices_in_currencies;

		foreach ( $prices_in_currencies as $curr => $price ) {
			array_walk( $price, function ( &$element, $index, $currency ) {
				global $woomc;

				remove_filter( 'woocommerce_currency_symbol', [ $woomc->currency, 'currency_symbol' ], 20 );
				$element = $woomc->currency->get_formatted_price( $element, $currency );
				add_filter( 'woocommerce_currency_symbol', [ $woomc->currency, 'currency_symbol' ], 20, 2 );

			}, $curr );

			$prices_in_currencies[ $curr ] = $price;
		}

		include_once WOOMC_ABSPATH . 'includes/frontend/views/woomc-switcher.php';
	}

}
add_action( 'woocommerce_single_product_summary', 'woomc_product_price_switcher', 9 );

/**
 * Get product link for particular currency.
 *
 * @param object|int $product Product ID or object.
 * @param string $currency Currency code.
 *
 * @return string Product URL.
 */
function woomc_product_link( $product, $currency ) {

	if( empty( $product ) || ! wc_get_product( $product ) ) {
		return;
	}

	$product_id = is_object( $product ) ? $product->get_id() : $product;

	return add_query_arg( [ 'woomc_currency' => $currency ], get_permalink( $product_id ) );
}

/**
 * Get country code by currency
 *
 * @param string $currency_code Currency code.
 *
 * @return array $data Currency data.
 */
function get_country_data( $currency_code ) {

	$countries = array(
		'AFN' => 'AF',
		'ALL' => 'AL',
		'DZD' => 'DZ',
		'USD' => 'US',
		'EUR' => 'EU',
		'AOA' => 'AO',
		'XCD' => 'LC',
		'ARS' => 'AR',
		'AMD' => 'AM',
		'AWG' => 'AW',
		'AUD' => 'AU',
		'AZN' => 'AZ',
		'BSD' => 'BS',
		'BHD' => 'BH',
		'BDT' => 'BD',
		'BBD' => 'BB',
		'BYR' => 'BY',
		'BZD' => 'BZ',
		'XOF' => 'BJ',
		'BMD' => 'BM',
		'BTN' => 'BT',
		'BOB' => 'BO',
		'BAM' => 'BA',
		'BWP' => 'BW',
		'NOK' => 'NO',
		'BRL' => 'BR',
		'BND' => 'BN',
		'BGN' => 'BG',
		'BIF' => 'BI',
		'KHR' => 'KH',
		'XAF' => 'CM',
		'CAD' => 'CA',
		'CVE' => 'CV',
		'KYD' => 'KY',
		'CLP' => 'CL',
		'CNY' => 'CN',
		'HKD' => 'HK',
		'COP' => 'CO',
		'KMF' => 'KM',
		'CDF' => 'CD',
		'NZD' => 'NZ',
		'CRC' => 'CR',
		'HRK' => 'HR',
		'CUP' => 'CU',
		'CZK' => 'CZ',
		'DKK' => 'DK',
		'DJF' => 'DJ',
		'DOP' => 'DO',
		'ECS' => 'EC',
		'EGP' => 'EG',
		'SVC' => 'SV',
		'ERN' => 'ER',
		'ETB' => 'ET',
		'FKP' => 'FK',
		'FJD' => 'FJ',
		'GMD' => 'GM',
		'GEL' => 'GE',
		'GHS' => 'GH',
		'GIP' => 'GI',
		'QTQ' => 'GT',
		'GGP' => 'GG',
		'GNF' => 'GN',
		'GWP' => 'GW',
		'GYD' => 'GY',
		'HTG' => 'HT',
		'HNL' => 'HN',
		'HUF' => 'HU',
		'ISK' => 'IS',
		'INR' => 'IN',
		'IDR' => 'ID',
		'IRR' => 'IR',
		'IQD' => 'IQ',
		'GBP' => 'GB',
		'ILS' => 'IL',
		'JMD' => 'JM',
		'JPY' => 'JP',
		'JOD' => 'JO',
		'KZT' => 'KZ',
		'KES' => 'KE',
		'KPW' => 'KP',
		'KRW' => 'KR',
		'KWD' => 'KW',
		'KGS' => 'KG',
		'LAK' => 'LA',
		'LBP' => 'LB',
		'LSL' => 'LS',
		'LRD' => 'LR',
		'LYD' => 'LY',
		'CHF' => 'CH',
		'MKD' => 'MK',
		'MGF' => 'MG',
		'MWK' => 'MW',
		'MYR' => 'MY',
		'MVR' => 'MV',
		'MRO' => 'MR',
		'MUR' => 'MU',
		'MXN' => 'MX',
		'MDL' => 'MD',
		'MNT' => 'MN',
		'MAD' => 'MA',
		'MZN' => 'MZ',
		'MMK' => 'MM',
		'NAD' => 'NA',
		'NPR' => 'NP',
		'ANG' => 'AN',
		'XPF' => 'WF',
		'NIO' => 'NI',
		'NGN' => 'NG',
		'OMR' => 'OM',
		'PKR' => 'PK',
		'PAB' => 'PA',
		'PGK' => 'PG',
		'PYG' => 'PY',
		'PEN' => 'PE',
		'PHP' => 'PH',
		'PLN' => 'PL',
		'QAR' => 'QA',
		'RON' => 'RO',
		'RUB' => 'RU',
		'RWF' => 'RW',
		'SHP' => 'SH',
		'WST' => 'WS',
		'STD' => 'ST',
		'SAR' => 'SA',
		'RSD' => 'RS',
		'SCR' => 'SC',
		'SLL' => 'SL',
		'SGD' => 'SG',
		'SBD' => 'SB',
		'SOS' => 'SO',
		'ZAR' => 'ZA',
		'SSP' => 'SS',
		'LKR' => 'LK',
		'SDG' => 'SD',
		'SRD' => 'SR',
		'SZL' => 'SZ',
		'SEK' => 'SE',
		'SYP' => 'SY',
		'TWD' => 'TW',
		'TJS' => 'TJ',
		'TZS' => 'TZ',
		'THB' => 'TH',
		'TOP' => 'TO',
		'TTD' => 'TT',
		'TND' => 'TN',
		'TRY' => 'TR',
		'TMT' => 'TM',
		'UGX' => 'UG',
		'UAH' => 'UA',
		'AED' => 'AE',
		'UYU' => 'UY',
		'UZS' => 'UZ',
		'VUV' => 'VU',
		'VEF' => 'VE',
		'VND' => 'VN',
		'YER' => 'YE',
		'ZMW' => 'ZM',
		'ZWD' => 'ZW',
		'BTC' => 'XBT',
	);

	$countries = apply_filters( 'woomc_country_codes_from_currency', $countries );

	$country_names = WC()->countries->countries;
	$data          = array();

	if ( isset( $countries[ $currency_code ] ) && $currency_code ) {
		$data['code'] = $countries[ $currency_code ];
		switch ( $currency_code ) {
			case 'EUR':
				$data['name'] = esc_attr__( 'European Union', 'woomc' );
				break;
			default:
				$data['name'] = isset( $country_names[ $countries[ $currency_code ] ] ) ? $country_names[ $countries[ $currency_code ] ] : 'Unknown';
		}

	} else {
		$data['code'] = '_unknown';
		$data['name'] = 'Unknown';
	}

	return apply_filters( 'woomc_get_country_data', $data, $currency_code );
}

/**
 * Get flag URL for country.
 *
 * @param string $country_code Country code.
 * @param bool $size_256 Flag to check if we need bigger image.
 *
 * @return array $flag_data Flag data.
 */
function get_country_flag( $country_code, $size_256 = false ) {

	$flag_data = array();

	if( $size_256 ) {
		$filepath = WOOMC_ABSPATH . 'assets/images/flags/256/' . $country_code . '.png';
	}else {
		$filepath = WOOMC_ABSPATH . 'assets/images/flags/' . $country_code . '.png';
	}

	if ( file_exists( $filepath ) ) {
		$imagedata           = @getimagesize( $filepath );
		$flag_data['url']    = plugins_url( str_replace( WOOMC_ABSPATH, '', $filepath ) , WOOMC_PLUGIN_FILE );
		$flag_data['width']  = ! empty( $imagedata ) ? $imagedata[0] : 48;
		$flag_data['height'] = ! empty( $imagedata ) ? $imagedata[1] : 48;

	}

	return apply_filters( 'woomc_get_country_flag', $flag_data, $size_256, $country_code );
}

/**
 * Checks if currency bar is enabled.
 *
 */
function woomc_is_currency_bar_enabled(){
    global $woomc;

    $enabled = $woomc->settings->currency_bar;
    $enabled = ( $enabled == 1 ) ? true : false;

    return apply_filters( 'woomc_is_currency_bar_enabled', $enabled );

}

/**
 * Currency Bar
 */
if( !function_exists( 'woomc_currency_bar' ) ) {

	function woomc_currency_bar() {

		if ( ! woomc_is_currency_bar_enabled() ) {
			return;
		}
		global $woomc, $wp;
		$currencies = $woomc->settings->get_currencies();
		$currency_bar_position = $woomc->settings->currency_bar_position;
		?>

		<div class="woomc-currency-bar pos-<?php echo $currency_bar_position; ?>"><?php

			foreach( $currencies as $currency ) {
				$country_flag = get_country_flag( get_country_data( $currency['currency'] )['code'] );
				?>

                <div class="currency-bar-row">

                    <a  href="<?php echo add_query_arg( 'woomc_currency', $currency['currency'], home_url( $wp->request ) ); ?>">
                        <span><?php echo $currency['currency']; ?></span>
                        <img src="<?php echo $country_flag['url'] ?>" width="<?php echo $country_flag['width'] ?>" height="<?php echo $country_flag['height'] ?>" alt="<?php echo $currency['currency']; ?>" />
                    </a>
                </div><?php
			}?>

		</div><?php

	}
	add_action( 'wp_footer', 'woomc_currency_bar' );
}

/**
 * Currency bar styling
 */
if ( ! function_exists( 'woomc_currency_bar_styling' ) ) {

	function woomc_currency_bar_styling() {
		global $woomc;

		if ( ! woomc_is_currency_bar_enabled() ) {
			return;
		}

		$text_color = $woomc->settings->currency_bar_text_color;
		$main_color = $woomc->settings->currency_bar_main_color;
		$bg_color   = $woomc->settings->currency_bar_bg_color;
		?>
        <style>
        .woomc-currency-bar, .woomc-currency-bar a {
            color: #<?php echo $text_color; ?>
        }

        .currency-bar-row {
            background: #<?php echo $bg_color; ?>
        }

        .currency-bar-row:hover {
            background: #<?php echo $main_color; ?>
        }
        </style><?php
	}

	add_action( 'wp_head', 'woomc_currency_bar_styling' );
}

/**
 * Clear cart fragments.
 */
function woomc_refresh_fragments() {
    wp_add_inline_script( 'wc-cart-fragments', 'jQuery(document).ready(function(){ jQuery(document.body).trigger(\'wc_fragment_refresh\'); })' );
}

/**
 *
 */
function woomc_fetch_exchange_rate() {
	global $wp_version;

	$default_currency = filter_var( $_POST['default'], FILTER_SANITIZE_STRING );
	$other_currencies = ( empty( $_POST['targets'] ) || !is_array( $_POST['targets'] ) ) ? [] : $_POST['targets'];

	if ( empty( $default_currency ) || empty( $other_currencies ) ) {
		wp_send_json_error( __( 'Cannot process this request', 'woomc' ) );
	}

	$url = 'https://api.villatheme.com/wp-json/exchange/v1';

	$request = wp_remote_post(
		$url, [
			'user-agent' => 'WordPress/' . $wp_version . '; ' . get_site_url(),
			'timeout'    => 10,
			'body'       => [
				'from' => $default_currency,
				'to'   => implode( ',', $other_currencies ),
			]
		]
	);

	if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
		wp_send_json_success( $request['body'] );
	} else {
		wp_send_json_error( $request->get_error_message() );
	}

}
add_action( 'wp_ajax_woomc_exchange_rate', 'woomc_fetch_exchange_rate' );

/**
 * Debugging function
 */
function db( $data ) {
	echo '<pre>';
	print_r($data);
	echo '</pre>';
	die;
}
