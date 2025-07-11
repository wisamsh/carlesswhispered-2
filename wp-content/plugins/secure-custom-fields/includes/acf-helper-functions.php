<?php

/**
 * Returns true if the value provided is considered "empty". Allows numbers such as 0.
 *
 * @date    6/7/16
 * @since   ACF 5.4.0
 *
 * @param   mixed $var The value to check.
 * @return  boolean
 */
function acf_is_empty( $var ) {
	return ( ! $var && ! is_numeric( $var ) );
}

/**
 * Returns true if the value provided is considered "not empty". Allows numbers such as 0.
 *
 * @date    15/7/19
 * @since   ACF 5.8.1
 *
 * @param   mixed $var The value to check.
 * @return  boolean
 */
function acf_not_empty( $var ) {
	return ( $var || is_numeric( $var ) );
}

/**
 * Returns a unique numeric based id.
 *
 * @date    9/1/19
 * @since   ACF 5.7.10
 *
 * @param   string $prefix The id prefix. Defaults to 'acf'.
 * @return  string
 */
function acf_uniqid( $prefix = 'acf' ) {

	// Instantiate global counter.
	global $acf_uniqid;
	if ( ! isset( $acf_uniqid ) ) {
		$acf_uniqid = 1;
	}

	// Return id.
	return $prefix . '-' . $acf_uniqid++;
}

/**
 * Merges together two arrays but with extra functionality to append class names.
 *
 * @date    22/1/19
 * @since   ACF 5.7.10
 *
 * @param   array $array1 An array of attributes.
 * @param   array $array2 An array of attributes.
 * @return  array
 */
function acf_merge_attributes( $array1, $array2 ) {

	// Merge together attributes.
	$array3 = array_merge( $array1, $array2 );

	// Append together special attributes.
	foreach ( array( 'class', 'style' ) as $key ) {
		if ( isset( $array1[ $key ] ) && isset( $array2[ $key ] ) ) {
			$array3[ $key ] = trim( $array1[ $key ] ) . ' ' . trim( $array2[ $key ] );
		}
	}

	// Return.
	return $array3;
}

/**
 * acf_cache_key
 *
 * Returns a filtered cache key.
 *
 * @date    25/1/19
 * @since   ACF 5.7.11
 *
 * @param   string $key The cache key.
 * @return  string
 */
function acf_cache_key( $key = '' ) {

	/**
	 * Filters the cache key.
	 *
	 * @date    25/1/19
	 * @since   ACF 5.7.11
	 *
	 * @param   string $key The cache key.
	 * @param   string $original_key The original cache key.
	 */
	return apply_filters( 'acf/get_cache_key', $key, $key );
}

/**
 * acf_request_args
 *
 * Returns an array of $_REQUEST values using the provided defaults.
 *
 * @date    28/2/19
 * @since   ACF 5.7.13
 *
 * @param   array $args An array of args.
 * @return  array
 */
function acf_request_args( $args = array() ) {
	foreach ( $args as $k => $v ) {
		$args[ $k ] = isset( $_REQUEST[ $k ] ) ? acf_sanitize_request_args( $_REQUEST[ $k ] ) : $args[ $k ]; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Verified elsewhere.
	}
	return $args;
}

/**
 * Returns a single $_REQUEST arg with fallback.
 *
 * @date    23/10/20
 * @since   ACF 5.9.2
 *
 * @param   string $key     The property name.
 * @param   mixed  $default The default value to fallback to.
 * @return  mixed
 */
function acf_request_arg( $name = '', $default = null ) {
	return isset( $_REQUEST[ $name ] ) ? acf_sanitize_request_args( $_REQUEST[ $name ] ) : $default; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}

// Register store.
acf_register_store( 'filters' );

/**
 * acf_enable_filter
 *
 * Enables a filter with the given name.
 *
 * @date    14/7/16
 * @since   ACF 5.4.0
 *
 * @param   string $name The modifier name.
 * @return  void
 */
function acf_enable_filter( $name = '' ) {
	acf_get_store( 'filters' )->set( $name, true );
}

/**
 * acf_disable_filter
 *
 * Disables a filter with the given name.
 *
 * @date    14/7/16
 * @since   ACF 5.4.0
 *
 * @param   string $name The modifier name.
 * @return  void
 */
function acf_disable_filter( $name = '' ) {
	acf_get_store( 'filters' )->set( $name, false );
}

/**
 * acf_is_filter_enabled
 *
 * Returns the state of a filter for the given name.
 *
 * @date    14/7/16
 * @since   ACF 5.4.0
 *
 * @param   string $name The modifier name.
 * @return  array
 */
function acf_is_filter_enabled( $name = '' ) {
	return acf_get_store( 'filters' )->get( $name );
}

/**
 * acf_get_filters
 *
 * Returns an array of filters in their current state.
 *
 * @date    14/7/16
 * @since   ACF 5.4.0
 *
 * @return  array
 */
function acf_get_filters() {
	return acf_get_store( 'filters' )->get();
}

/**
 * acf_set_filters
 *
 * Sets an array of filter states.
 *
 * @date    14/7/16
 * @since   ACF 5.4.0
 *
 * @param   array $filters An Array of modifiers.
 * @return  void
 */
function acf_set_filters( $filters = array() ) {
	acf_get_store( 'filters' )->set( $filters );
}

/**
 * acf_disable_filters
 *
 * Disables all filters and returns the previous state.
 *
 * @date    14/7/16
 * @since   ACF 5.4.0
 *
 * @return  array
 */
function acf_disable_filters() {

	// Get state.
	$prev_state = acf_get_filters();

	// Set all modifiers as false.
	acf_set_filters( array_map( '__return_false', $prev_state ) );

	// Return prev state.
	return $prev_state;
}

/**
 * acf_enable_filters
 *
 * Enables all or an array of specific filters and returns the previous state.
 *
 * @date    14/7/16
 * @since   ACF 5.4.0
 *
 * @param   array $filters An Array of modifiers.
 * @return  array
 */
function acf_enable_filters( $filters = array() ) {

	// Get state.
	$prev_state = acf_get_filters();

	// Allow specific filters to be enabled.
	if ( $filters ) {
		acf_set_filters( $filters );

		// Set all modifiers as true.
	} else {
		acf_set_filters( array_map( '__return_true', $prev_state ) );
	}

	// Return prev state.
	return $prev_state;
}

/**
 * acf_idval
 *
 * Parses the provided value for an ID.
 *
 * @date    29/3/19
 * @since   ACF 5.7.14
 *
 * @param   mixed $value A value to parse.
 * @return  integer
 */
function acf_idval( $value ) {

	// Check if value is numeric.
	if ( is_numeric( $value ) ) {
		return (int) $value;

		// Check if value is array.
	} elseif ( is_array( $value ) ) {
		return (int) isset( $value['ID'] ) ? $value['ID'] : 0;

		// Check if value is object.
	} elseif ( is_object( $value ) ) {
		return (int) isset( $value->ID ) ? $value->ID : 0;
	}

	// Return default.
	return 0;
}

/**
 * acf_maybe_idval
 *
 * Checks value for potential id value.
 *
 * @date    6/4/19
 * @since   ACF 5.7.14
 *
 * @param   mixed $value A value to parse.
 * @return  mixed
 */
function acf_maybe_idval( $value ) {
	if ( $id = acf_idval( $value ) ) {
		return $id;
	}
	return $value;
}

/**
 * Convert any numeric strings into their equivalent numeric type. This function will
 * work with both single values and arrays.
 *
 * @param mixed $value Either a single value or an array of values.
 * @return mixed
 */
function acf_format_numerics( $value ) {
	if ( is_array( $value ) ) {
		return array_map(
			function ( $v ) {
				return is_numeric( $v ) ? $v + 0 : $v;
			},
			$value
		);
	}

	return is_numeric( $value ) ? $value + 0 : $value;
}

/**
 * acf_numval
 *
 * Casts the provided value as eiter an int or float using a simple hack.
 *
 * @date    11/4/19
 * @since   ACF 5.7.14
 *
 * @param   mixed $value A value to parse.
 * @return  (int|float)
 */
function acf_numval( $value ) {
	return ( intval( $value ) == floatval( $value ) ) ? intval( $value ) : floatval( $value );
}

/**
 * acf_idify
 *
 * Returns an id attribute friendly string.
 *
 * @date    24/12/17
 * @since   ACF 5.6.5
 *
 * @param   string $str The string to convert.
 * @return  string
 */
function acf_idify( $str = '' ) {
	return str_replace( array( '][', '[', ']' ), array( '-', '-', '' ), strtolower( $str ) );
}

/**
 * Returns a slug friendly string.
 *
 * @date    24/12/17
 * @since   ACF 5.6.5
 *
 * @param   string $str  The string to convert.
 * @param   string $glue The glue between each slug piece.
 * @return  string
 */
function acf_slugify( $str = '', $glue = '-' ) {
	$raw  = $str;
	$slug = str_replace( array( '_', '-', '/', ' ' ), $glue, strtolower( remove_accents( $raw ) ) );
	$slug = preg_replace( '/[^A-Za-z0-9' . preg_quote( $glue ) . ']/', '', $slug );

	/**
	 * Filters the slug created by acf_slugify().
	 *
	 * @since ACF 5.11.4
	 *
	 * @param string $slug The newly created slug.
	 * @param string $raw  The original string.
	 * @param string $glue The separator used to join the string into a slug.
	 */
	return apply_filters( 'acf/slugify', $slug, $raw, $glue );
}

/**
 * Returns a string with correct full stop punctuation.
 *
 * @date    12/7/19
 * @since   ACF 5.8.2
 *
 * @param   string $str The string to format.
 * @return  string
 */
function acf_punctify( $str = '' ) {
	if ( substr( trim( strip_tags( $str ) ), -1 ) !== '.' ) {
		return trim( $str ) . '.';
	}
	return trim( $str );
}

/**
 * acf_did
 *
 * Returns true if ACF already did an event.
 *
 * @date    30/8/19
 * @since   ACF 5.8.1
 *
 * @param   string $name The name of the event.
 * @return  boolean
 */
function acf_did( $name ) {

	// Return true if already did the event (preventing event).
	if ( acf_get_data( "acf_did_$name" ) ) {
		return true;

		// Otherwise, update store and return false (allowing event).
	} else {
		acf_set_data( "acf_did_$name", true );
		return false;
	}
}

/**
 * Returns the length of a string that has been submitted via $_POST.
 *
 * Uses the following process:
 * 1. Unslash the string because posted values will be slashed.
 * 2. Decode special characters because wp_kses() will normalize entities.
 * 3. Treat line-breaks as a single character instead of two.
 * 4. Use mb_strlen() to accommodate special characters.
 *
 * @date    04/06/2020
 * @since   ACF 5.9.0
 *
 * @param   string $str The string to review.
 * @return  integer
 */
function acf_strlen( $str ) {
	return mb_strlen( str_replace( "\r\n", "\n", wp_specialchars_decode( wp_unslash( $str ) ) ) );
}

/**
 * Returns a value with default fallback.
 *
 * @date    6/4/20
 * @since   ACF 5.9.0
 *
 * @param   mixed $value         The value.
 * @param   mixed $default_value The default value.
 * @return  mixed
 */
function acf_with_default( $value, $default_value ) {
	return $value ? $value : $default_value;
}

/**
 * Returns the current priority of a running action.
 *
 * @date    14/07/2020
 * @since   ACF 5.9.0
 *
 * @param   string $action The action name.
 * @return  integer|boolean
 */
function acf_doing_action( $action ) {
	global $wp_filter;
	if ( isset( $wp_filter[ $action ] ) ) {
		return $wp_filter[ $action ]->current_priority();
	}
	return false;
}

/**
 * Returns the current URL.
 *
 * @date    23/01/2015
 * @since   ACF 5.1.5
 *
 * @return  string
 */
function acf_get_current_url() {
	// Ensure props exist to avoid PHP Notice during CLI commands.
	if ( isset( $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI'] ) ) {
		return ( is_ssl() ? 'https' : 'http' ) . '://' . filter_var( $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL );
	}
	return '';
}

/**
 * Sanitizes request arguments.
 *
 * @param mixed $args The data to sanitize.
 *
 * @return array|boolean|float|integer|mixed|string
 */
function acf_sanitize_request_args( $args = array() ) {
	switch ( gettype( $args ) ) {
		case 'boolean':
			return (bool) $args;
		case 'integer':
			return (int) $args;
		case 'double':
			return (float) $args;
		case 'array':
			$sanitized = array();
			foreach ( $args as $key => $value ) {
				$key               = sanitize_text_field( $key );
				$sanitized[ $key ] = acf_sanitize_request_args( $value );
			}
			return $sanitized;
		case 'object':
			return wp_kses_post_deep( $args );
		case 'string':
		default:
			return wp_kses( $args, 'acf' );
	}
}

/**
 * Sanitizes file upload arrays.
 *
 * @since ACF 6.0.4
 *
 * @param array $args The file array.
 *
 * @return array
 */
function acf_sanitize_files_array( array $args = array() ) {
	$defaults = array(
		'name'     => '',
		'tmp_name' => '',
		'type'     => '',
		'size'     => 0,
		'error'    => '',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( empty( $args['name'] ) ) {
		return $defaults;
	}

	if ( is_array( $args['name'] ) ) {
		$files             = array();
		$files['name']     = acf_sanitize_files_value_array( $args['name'], 'sanitize_file_name' );
		$files['tmp_name'] = acf_sanitize_files_value_array( $args['tmp_name'], 'sanitize_text_field' );
		$files['type']     = acf_sanitize_files_value_array( $args['type'], 'sanitize_text_field' );
		$files['size']     = acf_sanitize_files_value_array( $args['size'], 'absint' );
		$files['error']    = acf_sanitize_files_value_array( $args['error'], 'absint' );
		return $files;
	}

	$file             = array();
	$file['name']     = sanitize_file_name( $args['name'] );
	$file['tmp_name'] = sanitize_text_field( $args['tmp_name'] );
	$file['type']     = sanitize_text_field( $args['type'] );
	$file['size']     = absint( $args['size'] );
	$file['error']    = absint( $args['error'] );

	return $file;
}

/**
 * Sanitizes file upload values within the array.
 *
 * This addresses nested file fields within repeaters and groups.
 *
 * @since ACF 6.0.5
 *
 * @param array  $array             The file upload array.
 * @param string $sanitize_function Callback used to sanitize array value.
 * @return array
 */
function acf_sanitize_files_value_array( $array, $sanitize_function ) {
	if ( ! function_exists( $sanitize_function ) ) {
		return $array;
	}

	if ( ! is_array( $array ) ) {
		return $sanitize_function( $array );
	}

	foreach ( $array as $key => $value ) {
		if ( is_array( $value ) ) {
			$array[ $key ] = acf_sanitize_files_value_array( $value, $sanitize_function );
		} else {
			$array[ $key ] = $sanitize_function( $value );
		}
	}

	return $array;
}

/**
 * Maybe unserialize, but don't allow any classes.
 *
 * @since ACF 6.1
 *
 * @param string $data String to be unserialized, if serialized.
 * @return mixed The unserialized, or original data.
 */
function acf_maybe_unserialize( $data ) {
	if ( is_serialized( $data ) ) { // Don't attempt to unserialize data that wasn't serialized going in.
		return @unserialize( trim( $data ), array( 'allowed_classes' => false ) ); //phpcs:ignore -- allowed classes is false.
	}

	return $data;
}

/**
 * Check if ACF is a beta-like release.
 *
 * @since ACF 6.3
 *
 * @return boolean True if the current install version contains a dash, indicating a alpha, beta or RC release.
 */
function acf_is_beta() {
	return defined( 'ACF_VERSION' ) && strpos( ACF_VERSION, '-' ) !== false;
}

/**
 * Returns the version of ACF when it was first activated.
 * However, if ACF was first activated prior to the introduction of the acf_first_activated_version option,
 * this function returns false (boolean) to indicate that the version could not be determined.
 *
 * @since ACF 6.3
 *
 * @return string|boolean The (string) version of ACF when it was first activated, or false (boolean) if the version could not be determined.
 */
function acf_get_version_when_first_activated() {
	// Check if ACF is network-activated on a multisite.
	if ( is_multisite() ) {
		$acf_dir_and_filename = basename( ACF_PATH ) . '/acf.php';
		$plugins              = get_site_option( 'active_sitewide_plugins' );

		if ( isset( $plugins[ $acf_dir_and_filename ] ) ) {
			$main_site_id = get_main_site_id();

			if ( empty( $main_site_id ) ) {
				return false;
			}

			// ACF is network activated, so get the version from main site's options.
			return get_blog_option( $main_site_id, 'acf_first_activated_version', false );
		}
	}

	// Check if ACF is activated on this single site.
	return get_option( 'acf_first_activated_version', false );
}
