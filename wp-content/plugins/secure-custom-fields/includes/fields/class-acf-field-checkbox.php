<?php

if ( ! class_exists( 'acf_field_checkbox' ) ) :

	class acf_field_checkbox extends acf_field {

		/**
		 * The values of the checkboxes.
		 *
		 * @var $values (string)
		 */
		public $values = '';
		/**
		 * Whether all checkboxes are checked.
		 *
		 * @var $all_checked (bool)
		 */
		public $all_checked = false;

		/**
		 * This function will setup the field type data
		 *
		 * @type    function
		 * @date    5/03/2014
		 * @since   ACF 5.0.0
		 *
		 * @param   n/a
		 * @return  n/a
		 */
		function initialize() {

			// vars
			$this->name          = 'checkbox';
			$this->label         = __( 'Checkbox', 'secure-custom-fields' );
			$this->category      = 'choice';
			$this->description   = __( 'A group of checkbox inputs that allow the user to select one, or multiple values that you specify.', 'secure-custom-fields' );
			$this->preview_image = acf_get_url() . '/assets/images/field-type-previews/field-preview-checkbox.png';
			$this->doc_url       = 'https://developer.wordpress.org/secure-custom-fields/features/fields/checkbox/';
			$this->tutorial_url  = 'https://developer.wordpress.org/secure-custom-fields/features/fields/checkbox/checkbox-tutorial/';
			$this->defaults      = array(
				'layout'                    => 'vertical',
				'choices'                   => array(),
				'default_value'             => '',
				'allow_custom'              => 0,
				'save_custom'               => 0,
				'toggle'                    => 0,
				'return_format'             => 'value',
				'custom_choice_button_text' => __( 'Add new choice', 'secure-custom-fields' ),
			);
		}


		/**
		 * Create the HTML interface for your field
		 *
		 * @param   $field (array) the $field being rendered
		 *
		 * @type    action
		 * @since   ACF 3.6
		 * @date    23/01/13
		 *
		 * @param   $field (array) the $field being edited
		 * @return  n/a
		 */
		function render_field( $field ) {

			// reset vars
			$this->values      = array();
			$this->all_checked = true;

			// ensure array
			$field['value']   = acf_get_array( $field['value'] );
			$field['choices'] = acf_get_array( $field['choices'] );

			// hidden input
			acf_hidden_input( array( 'name' => $field['name'] ) );

			// vars
			$li = '';
			$ul = array(
				'class' => 'acf-checkbox-list',
			);

			// append to class
			$ul['class'] .= ' ' . ( 'horizontal' === acf_maybe_get( $field, 'layout' ) ? 'acf-hl' : 'acf-bl' );
			$ul['class'] .= ' ' . acf_maybe_get( $field, 'class', '' );

			// checkbox saves an array
			if ( acf_maybe_get( $field, 'name' ) ) {
				$field['name'] .= '[]';
			}

			// choices
			$choices = acf_maybe_get( $field, 'choices', array() );
			if ( ! empty( $choices ) ) {

				// choices
				$li .= $this->render_field_choices( $field );

				// toggle
				if ( acf_maybe_get( $field, 'toggle' ) ) {
					$li = $this->render_field_toggle( $field ) . $li;
				}
			}

			// custom
			if ( isset( $field['allow_custom'] ) && $field['allow_custom'] ) {
				$li .= $this->render_field_custom( $field );
			}

			// return
			echo '<ul ' . acf_esc_attrs( $ul ) . '>' . "\n" . $li . '</ul>' . "\n"; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by specific render methods above.
		}


		/**
		 * description
		 *
		 * @type    function
		 * @date    15/7/17
		 * @since   ACF 5.6.0
		 *
		 * @param   $post_id (int)
		 * @return  $post_id (int)
		 */
		function render_field_choices( $field ) {

			// walk
			return $this->walk( $field['choices'], $field );
		}

		/**
		 * Validates values for the checkbox field
		 *
		 * @since ACF 6.0.0
		 *
		 * @param  boolean $valid If the field is valid.
		 * @param  mixed   $value The value to validate.
		 * @param  array   $field The main field array.
		 * @param  string  $input The input element's name attribute.
		 * @return boolean
		 */
		public function validate_value( $valid, $value, $field, $input ) {
			if ( ! is_array( $value ) || empty( $field['allow_custom'] ) ) {
				return $valid;
			}

			foreach ( $value as $value ) {
				if ( empty( $value ) && $value !== '0' ) {
					return __( 'Checkbox custom values cannot be empty. Uncheck any empty values.', 'secure-custom-fields' );
				}
			}

			return $valid;
		}

		/**
		 * description
		 *
		 * @type    function
		 * @date    15/7/17
		 * @since   ACF 5.6.0
		 *
		 * @param   $post_id (int)
		 * @return  $post_id (int)
		 */
		function render_field_toggle( $field ) {

			// vars
			$atts = array(
				'type'  => 'checkbox',
				'class' => 'acf-checkbox-toggle',
				'label' => __( 'Toggle All', 'secure-custom-fields' ),
			);

			// custom label
			$toggle = acf_maybe_get( $field, 'toggle' );
			if ( is_string( $toggle ) ) {
				$atts['label'] = $toggle;
			}

			// checked
			if ( $this->all_checked ) {
				$atts['checked'] = 'checked';
			}

			// return
			return '<li>' . acf_get_checkbox_input( $atts ) . '</li>' . "\n";
		}


		/**
		 * description
		 *
		 * @type    function
		 * @date    15/7/17
		 * @since   ACF 5.6.0
		 *
		 * @param   $post_id (int)
		 * @return  $post_id (int)
		 */
		function render_field_custom( $field ) {

			// vars
			$html = '';

			// loop
			$value = acf_maybe_get( $field, 'value', array() );
			if ( is_array( $value ) ) {
				foreach ( $value as $item ) {

					// ignore if already exists
					$choices = acf_maybe_get( $field, 'choices', array() );
					if ( isset( $choices[ $item ] ) ) {
						continue;
					}

					// vars
					$esc_value  = esc_attr( $item );
					$text_input = array(
						'name'  => acf_maybe_get( $field, 'name', '' ),
						'value' => $item,
					);

					// bail early if choice already exists
					if ( in_array( $esc_value, $this->values, true ) ) {
						continue;
					}

					// append
					$html .= '<li><input class="acf-checkbox-custom" type="checkbox" checked="checked" />' . acf_get_text_input( $text_input ) . '</li>' . "\n";
				}
			}

			// append button
			// We need to check if is better to just not display the li if the button text is empty. But for now let's keep it as stable as possible.
			$html .= '<li><a href="#" class="button acf-add-checkbox">' . esc_attr( acf_maybe_get( $field, 'custom_choice_button_text', '' ) ) . '</a></li>' . "\n";

			// return
			return $html;
		}


		function walk( $choices = array(), $args = array(), $depth = 0 ) {

			// bail early if no choices
			if ( empty( $choices ) ) {
				return '';
			}

			// defaults
			$args = wp_parse_args(
				$args,
				array(
					'id'       => '',
					'type'     => 'checkbox',
					'name'     => '',
					'value'    => array(),
					'disabled' => array(),
				)
			);

			// vars
			$html = '';

			// sanitize values for 'selected' matching
			if ( $depth == 0 ) {
				$args['value']    = array_map( 'esc_attr', $args['value'] );
				$args['disabled'] = array_map( 'esc_attr', $args['disabled'] );
			}

			// loop
			foreach ( $choices as $value => $label ) {

				// open
				$html .= '<li>';

				// optgroup
				if ( is_array( $label ) ) {
					$html .= '<ul>' . "\n";
					$html .= $this->walk( $label, $args, $depth + 1 );
					$html .= '</ul>';

					// option
				} else {

					// vars
					$esc_value = esc_attr( $value );
					$atts      = array(
						'id'    => $args['id'] . '-' . str_replace( ' ', '-', $value ),
						'type'  => $args['type'],
						'name'  => $args['name'],
						'value' => $value,
						'label' => $label,
					);

					// selected
					if ( in_array( $esc_value, $args['value'] ) ) {
						$atts['checked'] = 'checked';
					} else {
						$this->all_checked = false;
					}

					// disabled
					if ( in_array( $esc_value, $args['disabled'] ) ) {
						$atts['disabled'] = 'disabled';
					}

					// store value added
					$this->values[] = $esc_value;

					// append
					$html .= acf_get_checkbox_input( $atts );
				}

				// close
				$html .= '</li>' . "\n";
			}

			// return
			return $html;
		}



		/**
		 * Create extra options for your field. This is rendered when editing a field.
		 * The value of $field['name'] can be used (like bellow) to save extra data to the $field
		 *
		 * @type    action
		 * @since   ACF 3.6
		 * @date    23/01/13
		 *
		 * @param   $field  - an array holding all the field's data
		 */
		function render_field_settings( $field ) {
			// Encode choices (convert from array).
			$field['choices']       = acf_encode_choices( $field['choices'] );
			$field['default_value'] = acf_encode_choices( $field['default_value'], false );

			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Choices', 'secure-custom-fields' ),
					'instructions' => __( 'Enter each choice on a new line.', 'secure-custom-fields' ) . '<br />' . __( 'For more control, you may specify both a value and label like this:', 'secure-custom-fields' ) . '<br /><span class="acf-field-setting-example">' . __( 'red : Red', 'secure-custom-fields' ) . '</span>',
					'type'         => 'textarea',
					'name'         => 'choices',
				)
			);

			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Default Value', 'secure-custom-fields' ),
					'instructions' => __( 'Enter each default value on a new line', 'secure-custom-fields' ),
					'type'         => 'textarea',
					'name'         => 'default_value',
				)
			);

			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Return Value', 'secure-custom-fields' ),
					'instructions' => __( 'Specify the returned value on front end', 'secure-custom-fields' ),
					'type'         => 'radio',
					'name'         => 'return_format',
					'layout'       => 'horizontal',
					'choices'      => array(
						'value' => __( 'Value', 'secure-custom-fields' ),
						'label' => __( 'Label', 'secure-custom-fields' ),
						'array' => __( 'Both (Array)', 'secure-custom-fields' ),
					),
				)
			);
		}

		/**
		 * Renders the field settings used in the "Validation" tab.
		 *
		 * @since ACF 6.0
		 *
		 * @param array $field The field settings array.
		 * @return void
		 */
		function render_field_validation_settings( $field ) {
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Allow Custom Values', 'secure-custom-fields' ),
					'name'         => 'allow_custom',
					'type'         => 'true_false',
					'ui'           => 1,
					'instructions' => __( "Allow 'custom' values to be added", 'secure-custom-fields' ),
				)
			);

			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Save Custom Values', 'secure-custom-fields' ),
					'name'         => 'save_custom',
					'type'         => 'true_false',
					'ui'           => 1,
					'instructions' => __( "Save 'custom' values to the field's choices", 'secure-custom-fields' ),
					'conditions'   => array(
						'field'    => 'allow_custom',
						'operator' => '==',
						'value'    => 1,
					),
				)
			);
		}

		/**
		 * Renders the field settings used in the "Presentation" tab.
		 *
		 * @since ACF 6.0
		 *
		 * @param array $field The field settings array.
		 * @return void
		 */
		function render_field_presentation_settings( $field ) {
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Layout', 'secure-custom-fields' ),
					'instructions' => '',
					'type'         => 'radio',
					'name'         => 'layout',
					'layout'       => 'horizontal',
					'choices'      => array(
						'vertical'   => __( 'Vertical', 'secure-custom-fields' ),
						'horizontal' => __( 'Horizontal', 'secure-custom-fields' ),
					),
				)
			);

			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Add Toggle All', 'secure-custom-fields' ),
					'instructions' => __( 'Prepend an extra checkbox to toggle all choices', 'secure-custom-fields' ),
					'name'         => 'toggle',
					'type'         => 'true_false',
					'ui'           => 1,
				)
			);
		}

		/**
		 * This filter is applied to the $field before it is saved to the database
		 *
		 * @type    filter
		 * @since   ACF 3.6
		 * @date    23/01/13
		 *
		 * @param   $field - the field array holding all the field options
		 * @param   $post_id - the field group ID (post_type = acf)
		 *
		 * @return  $field - the modified field
		 */
		function update_field( $field ) {

			// Decode choices (convert to array).
			$field['choices']       = acf_decode_choices( $field['choices'] );
			$field['default_value'] = acf_decode_choices( $field['default_value'], true );
			return $field;
		}


		/**
		 * This filter is applied to the $value before it is updated in the db
		 *
		 * @type    filter
		 * @since   ACF 3.6
		 * @date    23/01/13
		 *
		 * @param   $value - the value which will be saved in the database
		 * @param   $post_id - the post_id of which the value will be saved
		 * @param   $field - the field array holding all the field options
		 *
		 * @return  $value - the modified value
		 */
		function update_value( $value, $post_id, $field ) {

			// bail early if is empty
			if ( empty( $value ) ) {
				return $value;
			}

			// select -> update_value()
			$value = acf_get_field_type( 'select' )->update_value( $value, $post_id, $field );

			// save_other_choice
			if ( $field['save_custom'] ) {

				// get raw $field (may have been changed via repeater field)
				// if field is local, it won't have an ID
				$selector = $field['ID'] ? $field['ID'] : $field['key'];
				$field    = acf_get_field( $selector );
				if ( ! $field ) {
					return false;
				}

				// bail early if no ID (JSON only)
				if ( ! $field['ID'] ) {
					return $value;
				}

				// loop
				foreach ( $value as $v ) {

					// ignore if already exists
					if ( isset( $field['choices'][ $v ] ) ) {
						continue;
					}

					// unslash (fixes serialize single quote issue)
					$v = wp_unslash( $v );

					// sanitize (remove tags)
					$v = sanitize_text_field( $v );

					// append
					$field['choices'][ $v ] = $v;
				}

				// save
				acf_update_field( $field );
			}

			// return
			return $value;
		}


		/**
		 * This function will translate field settings
		 *
		 * @type    function
		 * @date    8/03/2016
		 * @since   ACF 5.3.2
		 *
		 * @param   $field (array)
		 * @return  $field
		 */
		function translate_field( $field ) {

			return acf_get_field_type( 'select' )->translate_field( $field );
		}


		/**
		 * This filter is applied to the $value after it is loaded from the db and before it is returned to the template
		 *
		 * @type    filter
		 * @since   ACF 3.6
		 * @date    23/01/13
		 *
		 * @param   $value (mixed) the value which was loaded from the database
		 * @param   $post_id (mixed) the post_id from which the value was loaded
		 * @param   $field (array) the field array holding all the field options
		 *
		 * @return  $value (mixed) the modified value
		 */
		function format_value( $value, $post_id, $field ) {

			// Bail early if is empty.
			if ( acf_is_empty( $value ) ) {
				return array();
			}

			// Always convert to array of items.
			$value = acf_array( $value );

			// Return.
			return acf_get_field_type( 'select' )->format_value( $value, $post_id, $field );
		}

		/**
		 * Return the schema array for the REST API.
		 *
		 * @param array $field
		 * @return array
		 */
		public function get_rest_schema( array $field ) {
			$schema = array(
				'type'     => array( 'integer', 'string', 'array', 'null' ),
				'required' => isset( $field['required'] ) && $field['required'],
				'items'    => array(
					'type' => array( 'string', 'integer' ),
				),
			);

			if ( isset( $field['default_value'] ) && '' !== $field['default_value'] ) {
				$schema['default'] = $field['default_value'];
			}

			// If we allow custom values, nothing else to do here.
			if ( ! empty( $field['allow_custom'] ) ) {
				return $schema;
			}

			$schema['items']['enum'] = acf_get_field_type( 'select' )->format_rest_choices( $field['choices'] );

			return $schema;
		}
	}


	// initialize
	acf_register_field_type( 'acf_field_checkbox' );
endif; // class_exists check
