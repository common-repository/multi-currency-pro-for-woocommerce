<?php

defined( 'ABSPATH' ) || exit;

class WooMC_Admin_Settings_Markup {

	static $tabs = [];

	/**
	 * Renders the menu tab in settings page.
	 *
	 * @param array $args Arguments to add in menu item.
	 */
	static function menu_tab( $args ) {

		$default = [
			'href'  => '#',
			'class' => 'nav-link',
			'id'    => "tab-" . (string) ( count( self::$tabs ) + 1 ),
			'text'  => __( 'Tab', 'woomc' )
		];

		$active_tab    = WooMC_Admin_Settings::current_tab();
		$args          = wp_parse_args( $args, $default );
		$args['class'] .= ( ! empty( $args['tabname'] ) && ( $args['tabname'] == $active_tab ) ) ? ' active' : '';
		?>

		<li class="nav-item">
			<a class="<?php echo $args['class']; ?> text-dark" id="<?php echo $args['id']; ?>" data-tab="<?php echo $args['tabname'] ?>" data-toggle="tab"
			   href="<?php echo $args['href']; ?>" role="tab" aria-controls="<?php echo strtolower( $args['id'] ); ?>"
			   aria-selected="<?php echo ( 0 !== strpos( $args['class'], ' active' ) ) ? 'true' : 'false' ?>">
				<?php echo $args['text']; ?>
			</a>
		</li><?php

		self::$tabs[] = $args;
	}

	/**
	 * Renders the tab content
	 */
	static function tab_content() {

		foreach ( self::$tabs as $key => $value ) {

			$tab                 = str_replace( '-tab', '', $value['id'] );
			$tab_content_classes = 'container no-gutters p-0 tab-pane fade show';
			$active_tab          = WooMC_Admin_Settings::current_tab();
			$tab_content_classes .= ( $tab === $active_tab ) ? ' active' : '';
			?>

        <div class="<?php echo $tab_content_classes; ?>" id="<?php echo $tab; ?>" role="tabpanel"
             aria-labelledby="<?php echo $tab ?>-tab">
			<?php do_action( 'woomc_tab_content_' . $tab ); ?>
            </div><?php
		}
	}

	/**
	 * Input
	 */
	static function render_inputs( $inputs ) {

		if( !is_array( $inputs ) ) {
			return;
		}

		foreach( $inputs as $input ) {

			/**
			 * Ensure that $input is single element. If it is array of array,
			 * recurse the $input, so that element can be rendered.
			 */
			if( 1 < count( $input ) && !isset( $input['type'] ) ) {
				self::render_inputs( $input );
			}

			/**
			 * Setting type is necessary to identify which element to render.
			 */
			if( !isset( $input['type'] ) ) {
				continue;
			}

			if( is_callable( [ __CLASS__, $input['type'] ] ) ) {

				call_user_func( [ __CLASS__, $input['type'] ], $input );

			}elseif ( is_callable( $input['type'] ) ) {

				call_user_func( $input['type'], $input );

			}
		}
	}

	/**
	 * Render input group.
	 */
	static function element_start( $arg ) {

		$output = '';
		$output .= '<' . $arg['element'] . ' ';

		if ( isset( $arg['class'] ) ) {
			$output .= 'class="' . $arg['class'] . '" ';
		}

		if ( isset( $arg['id'] ) ) {
			$output .= 'id="' . $arg['id'] . '" ';
		}

		if ( isset( $arg['custom_attributes'] ) && is_array( $arg['custom_attributes'] ) ) {

			foreach ( $arg['custom_attributes'] as $attr_key => $attr_val ) {

				$output .= $attr_key . '="' . $attr_val . '"';
			}

			$output .= ' ';
		}

		$output .= '>';

		if ( isset( $arg['return'] ) ) {
			return $output;
		}

		echo $output;
	}

	/**
	 * Render element end.
	 */
	static function element_end( $arg ) {

		$output = '';

		$output .= '</' .$arg['element'] .'>' ;

		if ( isset( $arg['return'] ) ) {
			return $output;
		}

		echo $output;
	}

	/**
	 * Render checkbox
	 */
	static function input( $arg ) {

		$output = '';

		$output .= '<input type="' . $arg["input_type"] . '" ';

		if ( isset( $arg['class'] ) ) {
			$output .= 'class="' . $arg['class'] . '" ';
		}

		if ( isset( $arg['id'] ) ) {
			$output .= 'id="' . $arg['id'] . '" ';
		}

		if ( isset( $arg['name'] ) ) {
			$output .= 'name="' . $arg['name'] . '" ';
		}

		if ( isset( $arg['value'] ) ) {
			$output .= 'value="' . $arg['value'] . '" ';
		}

		if ( !empty( $arg['checked'] ) ) {
			$output .= 'checked ';
		}

		if ( isset( $arg['custom_attributes'] ) && is_array( $arg['custom_attributes'] ) ) {

			foreach ( $arg['custom_attributes'] as $attr_key => $attr_val ) {

				$output .= $attr_key . '="' . $attr_val . '"';
			}

			$output .= ' ';
		}

		$output .= '/>';

		if ( isset( $arg['return'] ) ) {
			return $output;
		}

		echo $output;
	}

	/**
	 * Renders select input field
	 */
	static function select( $arg ) {

	    $output = '';

	    $output .= '<select ';

		if ( isset( $arg['class'] ) ) {
			$output .= 'class="' . $arg['class'] . '" ';
		}

		if ( isset( $arg['id'] ) ) {
			$output .= 'id="' . $arg['id'] . '" ';
		}

		if ( isset( $arg['name'] ) ) {
			$output .= 'name="' . $arg['name'] . '" ';
		}

		if ( isset( $arg['custom_attributes'] ) && is_array( $arg['custom_attributes'] ) ) {

			foreach ( $arg['custom_attributes'] as $attr_key => $attr_val ) {

				$output .= $attr_key . '="' . $attr_val . '"';
			}

			$output .= ' ';
		}

		$output .= '>';

		$value = empty( $arg['value'] ) ? '' : $arg['value'];

		foreach( $arg['options'] as $val => $label ) {

		    $output .= '<option ' . selected( $val, $value, false ) . ' value="'. $val .'">' . $label . '</option>';
        }

		$output .= '</select>';

		if ( isset( $arg['return'] ) ) {
			return $output;
		}

		echo $output;

	}

	/**
	 * Renders the label.
	 *
	 * @param array $arg Array of arguments for label
	 *
	 * @return string
	 */
	static function label( $arg ) {

		$output = '';

		$output .= '<label ';

		if ( isset( $arg['class'] ) ) {
			$output .= 'class="' . $arg['class'] . '" ';
		}

		if ( isset( $arg['id'] ) ) {
			$output .= 'for="' . $arg['id'] . '" ';
		}

		if ( isset( $arg['custom_attributes'] ) && is_array( $arg['custom_attributes'] ) ) {

			foreach ( $arg['custom_attributes'] as $attr_key => $attr_val ) {

				$output .= $attr_key . '="' . $attr_val . '"';
			}

			$output .= ' ';
		}

		$output .= '>';

		if( isset( $arg['text'] ) ) {
			$output .= $arg['text'];
		}

		$output .= '</label>';

		if ( isset( $arg['return'] ) ) {
			return $output;
		}

		echo $output;
	}

	/**
	 * Renders a single row of settings with label and field.
	 */
	public static function woomc_setting_row( $arg ) { ?>

        <div class="row form-row mb-4 <?php $arg['wrapper_classes']; ?>">

            <div class="col-md-3">
                <?php self::label( $arg['label'] ); ?>
            </div><?php

            if( isset( $arg['input'] ) ) {?>

                <div class="col-9"><?php

                switch ( $arg['input']['field_type'] ) {

                    case 'input':
	                    self::input( $arg['input'] );
                    break;

                    case 'select':
	                    self::select( $arg['input'] );
                    break;

                }?>

                </div><?php
            }
            ?>

        </div><?php
    }
}
