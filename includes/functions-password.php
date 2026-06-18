<?php
/**
 * Password generator shortcode functions.
 */

/**
 * Declare the Namespace.
 */
namespace azurecurve\StrongPasswordGenerator;

/**
 * Display form.
 *
 * @since 1.0.0
 */
function display_form( $atts, $content = null ) {

	global $wp;

	if ( isset( $_POST['password-form-id'] ) ) {
		$responses = process_form();
	}

	// get options with defaults.
	$options = get_option_with_defaults( PLUGIN_HYPHEN );

	// get shortcode attributes.
	$args = shortcode_atts(
		array(
			'id'                      => '',
			'password-length'         => $options['password']['length'],
			'password-minimum-length' => $options['password']['minimum-length'],
			'password-maximum-length' => $options['password']['maximum-length'],
			'password-number'         => $options['password']['number'],
			'password-maximum-number' => $options['password']['maximum-number'],
			'text-before'             => $options['text']['before'],
			'text-after'              => $options['text']['after'],
			'label-password-length'   => $options['labels']['password-length'],
			'label-password-number'   => $options['labels']['password-number'],
			'label-lowercase'         => $options['labels']['lowercase'],
			'label-uppercase'         => $options['labels']['uppercase'],
			'label-numeric'           => $options['labels']['numeric'],
			'label-symbols'           => $options['labels']['symbols'],
			'allow-lowercase'         => $options['allowed']['lowercase'],
			'allow-uppercase'         => $options['allowed']['uppercase'],
			'allow-numeric'           => $options['allowed']['numeric'],
			'allow-symbols'           => $options['allowed']['symbols'],
			'valid-lowercase'         => $options['valid']['lowercase'],
			'valid-uppercase'         => $options['valid']['uppercase'],
			'valid-numeric'           => $options['valid']['numeric'],
			'valid-symbols'           => $options['valid']['symbols'],
		),
		$atts
	);

	// sanitize shortcode attributes.
	$id                      = sanitize_text_field( wp_unslash( $args['id'] ) );
	$password_length         = (int) sanitize_text_field( wp_unslash( $args['password-length'] ) );
	$password_minimum_length = (int) sanitize_text_field( wp_unslash( $args['password-minimum-length'] ) );
	$password_maximum_length = (int) sanitize_text_field( wp_unslash( $args['password-maximum-length'] ) );
	$password_number         = (int) sanitize_text_field( wp_unslash( $args['password-number'] ) );
	$password_maximum_number = (int) sanitize_text_field( wp_unslash( $args['password-maximum-number'] ) );
	$allow_lowercase         = (int) sanitize_text_field( wp_unslash( $args['allow-lowercase'] ) );
	$allow_uppercase         = (int) sanitize_text_field( wp_unslash( $args['allow-uppercase'] ) );
	$allow_numeric           = (int) sanitize_text_field( wp_unslash( $args['allow-numeric'] ) );
	$allow_symbols           = (int) sanitize_text_field( wp_unslash( $args['allow-symbols'] ) );
	$text_before             = sanitize_text_field( wp_unslash( $args['text-before'] ) );
	$text_after              = sanitize_text_field( wp_unslash( $args['text-after'] ) );
	$label_password_length   = sanitize_text_field( wp_unslash( $args['label-password-length'] ) );
	$label_password_number   = sanitize_text_field( wp_unslash( $args['label-password-number'] ) );
	$label_lowercase         = sanitize_text_field( wp_unslash( $args['label-lowercase'] ) );
	$label_uppercase         = sanitize_text_field( wp_unslash( $args['label-uppercase'] ) );
	$label_numeric           = sanitize_text_field( wp_unslash( $args['label-numeric'] ) );
	$label_symbols           = sanitize_text_field( wp_unslash( $args['label-symbols'] ) );
	$include_lowercase       = 1;
	$include_uppercase       = 1;
	$include_numeric         = 1;
	$include_symbols         = 1;
	$valid_lowercase         = sanitize_text_field( wp_unslash( $args['valid-lowercase'] ) );
	$valid_uppercase         = sanitize_text_field( wp_unslash( $args['valid-uppercase'] ) );
	$valid_numeric           = sanitize_text_field( wp_unslash( $args['valid-numeric'] ) );
	$valid_symbols           = sanitize_text_field( wp_unslash( $args['valid-symbols'] ) );

	// is this a valid password form?
	if ( $id === '' ) {
		return '<div class="azrcrv-spg-form">
			<div class="azrcrv-spg-notice azrcrv-spg-notice--error">
				' . esc_html__( 'Strong Password Generator form cannot be displayed; an id must be provided.', 'azrcrv-spg' ) . '
			</div>
		</div>';
	}

	$messages = '';

	if ( isset( $responses ) && is_array( $responses ) ) {

		if ( isset( $responses['id'] ) && $id === $responses['id'] ) {
			/*
				load user inputs if form previously submitted.
			*/
			if ( isset( $responses['fields']['password-length'] ) ) {
				$password_length = sanitize_text_field( wp_unslash( $responses['fields']['password-length'] ) );
			}
			if ( isset( $responses['fields']['password-number'] ) ) {
				$password_number = sanitize_text_field( wp_unslash( $responses['fields']['password-number'] ) );
			}
			$include_lowercase = ( isset( $responses['fields']['include-lowercase'] ) && $responses['fields']['include-lowercase'] == 1 ) ? 1 : 0;
			$include_uppercase = ( isset( $responses['fields']['include-uppercase'] ) && $responses['fields']['include-uppercase'] == 1 ) ? 1 : 0;
			$include_numeric   = ( isset( $responses['fields']['include-numeric'] ) && $responses['fields']['include-numeric'] == 1 ) ? 1 : 0;
			$include_symbols   = ( isset( $responses['fields']['include-symbols'] ) && $responses['fields']['include-symbols'] == 1 ) ? 1 : 0;
			if ( isset( $responses['fields']['valid-lowercase'] ) ) {
				$valid_lowercase = sanitize_text_field( wp_unslash( $responses['fields']['valid-lowercase'] ) );
			}
			if ( isset( $responses['fields']['valid-uppercase'] ) ) {
				$valid_uppercase = sanitize_text_field( wp_unslash( $responses['fields']['valid-uppercase'] ) );
			}
			if ( isset( $responses['fields']['valid-numeric'] ) ) {
				$valid_numeric = sanitize_text_field( wp_unslash( $responses['fields']['valid-numeric'] ) );
			}
			if ( isset( $responses['fields']['valid-symbols'] ) ) {
				$valid_symbols = sanitize_text_field( wp_unslash( $responses['fields']['valid-symbols'] ) );
			}

			if ( is_array( $responses['messages'] ) ) {
				foreach ( $responses['messages'] as $response ) {
					if ( $response === 'error-invalid-nonce' ) {
						$messages .= '<div class="azrcrv-spg-notice azrcrv-spg-notice--error">' . esc_html__( 'Password could not be generated.', 'azrcrv-spg' ) . '</div>';
					}
					if ( $response === 'error-no-includes' ) {
						$messages .= '<div class="azrcrv-spg-notice azrcrv-spg-notice--error">' . esc_html__( 'Password could not be generated as no character sets were included.', 'azrcrv-spg' ) . '</div>';
					}
					if ( $response === 'error-processing' ) {
						$messages .= '<div class="azrcrv-spg-notice azrcrv-spg-notice--error">' . esc_html__( 'There was an error processing your request. Wait a moment and try again.', 'azrcrv-spg' ) . '</div>';
					}
					if ( $response === 'success-password-generated' ) {
						$messages .= '<div class="azrcrv-spg-notice azrcrv-spg-notice--success">' . esc_html__( 'Password generation completed successfully.', 'azrcrv-spg' ) . '</div>';
					}
				}
			}
		}
	}

	$password_form = render_form(
		$id,
		$messages,
		$text_before,
		$text_after,
		$label_password_number,
		$password_number,
		$password_maximum_number,
		$label_password_length,
		$password_length,
		$password_minimum_length,
		$password_maximum_length,
		$allow_lowercase,
		$label_lowercase,
		$include_lowercase,
		$valid_lowercase,
		$allow_uppercase,
		$label_uppercase,
		$include_uppercase,
		$valid_uppercase,
		$allow_numeric,
		$label_numeric,
		$include_numeric,
		$valid_numeric,
		$allow_symbols,
		$label_symbols,
		$include_symbols,
		$valid_symbols,
		isset( $responses ) && is_array( $responses ) ? $responses : array()
	);

	if ( isset( $_POST['password-form-id'] ) ) {
		$password_form .= '<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>';
	}

	return $password_form;
}

/**
 * Render the password form HTML.
 *
 * @since 1.0.0
 */
function render_form(
	$id,
	$messages,
	$text_before,
	$text_after,
	$label_password_number,
	$password_number,
	$password_maximum_number,
	$label_password_length,
	$password_length,
	$password_minimum_length,
	$password_maximum_length,
	$allow_lowercase,
	$label_lowercase,
	$include_lowercase,
	$valid_lowercase,
	$allow_uppercase,
	$label_uppercase,
	$include_uppercase,
	$valid_uppercase,
	$allow_numeric,
	$label_numeric,
	$include_numeric,
	$valid_numeric,
	$allow_symbols,
	$label_symbols,
	$include_symbols,
	$valid_symbols,
	$responses
) {
	global $wp;

	$current_url = home_url( add_query_arg( array(), $wp->request ) );

	$field_number = '<div class="azrcrv-spg-field">
		<label class="azrcrv-spg-field__label" for="password-number">' . esc_html( wp_unslash( $label_password_number ) ) . '</label>
		<div class="azrcrv-spg-field__input">
			<input name="password-number" type="number" id="password-number" min="1" max="' . esc_attr( $password_maximum_number ) . '" step="1" value="' . esc_attr( wp_unslash( $password_number ) ) . '" />
		</div>
	</div>';

	$field_length = '<div class="azrcrv-spg-field">
		<label class="azrcrv-spg-field__label" for="password-length">' . esc_html( wp_unslash( $label_password_length ) ) . '</label>
		<div class="azrcrv-spg-field__input">
			<input name="password-length" type="number" id="password-length" min="' . esc_attr( $password_minimum_length ) . '" max="' . esc_attr( $password_maximum_length ) . '" step="1" value="' . esc_attr( wp_unslash( $password_length ) ) . '" />
		</div>
	</div>';

	$field_lowercase = '';
	if ( $allow_lowercase == 1 ) {
		$field_lowercase = '<div class="azrcrv-spg-field">
			<span class="azrcrv-spg-field__label">' . esc_html( wp_unslash( $label_lowercase ) ) . '</span>
			<div class="azrcrv-spg-field__input">
				<label class="azrcrv-spg-field__checkbox-label">
					<input name="include-lowercase" type="checkbox" id="include-lowercase" value="1" ' . checked( '1', esc_attr( $include_lowercase ), false ) . ' />
					' . esc_html__( 'Include lowercase', 'azrcrv-spg' ) . '
				</label>
				<input name="valid-lowercase" type="text" id="valid-lowercase" value="' . esc_attr( wp_unslash( $valid_lowercase ) ) . '" class="azrcrv-spg-field__charset" />
			</div>
		</div>';
	}

	$field_uppercase = '';
	if ( $allow_uppercase == 1 ) {
		$field_uppercase = '<div class="azrcrv-spg-field">
			<span class="azrcrv-spg-field__label">' . esc_html( wp_unslash( $label_uppercase ) ) . '</span>
			<div class="azrcrv-spg-field__input">
				<label class="azrcrv-spg-field__checkbox-label">
					<input name="include-uppercase" type="checkbox" id="include-uppercase" value="1" ' . checked( '1', esc_attr( $include_uppercase ), false ) . ' />
					' . esc_html__( 'Include uppercase', 'azrcrv-spg' ) . '
				</label>
				<input name="valid-uppercase" type="text" id="valid-uppercase" value="' . esc_attr( wp_unslash( $valid_uppercase ) ) . '" class="azrcrv-spg-field__charset" />
			</div>
		</div>';
	}

	$field_numeric = '';
	if ( $allow_numeric == 1 ) {
		$field_numeric = '<div class="azrcrv-spg-field">
			<span class="azrcrv-spg-field__label">' . esc_html( wp_unslash( $label_numeric ) ) . '</span>
			<div class="azrcrv-spg-field__input">
				<label class="azrcrv-spg-field__checkbox-label">
					<input name="include-numeric" type="checkbox" id="include-numeric" value="1" ' . checked( '1', esc_attr( $include_numeric ), false ) . ' />
					' . esc_html__( 'Include numbers', 'azrcrv-spg' ) . '
				</label>
				<input name="valid-numeric" type="text" id="valid-numeric" pattern="[0-9]*" value="' . esc_attr( wp_unslash( $valid_numeric ) ) . '" class="azrcrv-spg-field__charset" />
			</div>
		</div>';
	}

	$field_symbols = '';
	if ( $allow_symbols == 1 ) {
		$field_symbols = '<div class="azrcrv-spg-field">
			<span class="azrcrv-spg-field__label">' . esc_html( wp_unslash( $label_symbols ) ) . '</span>
			<div class="azrcrv-spg-field__input">
				<label class="azrcrv-spg-field__checkbox-label">
					<input name="include-symbols" type="checkbox" id="include-symbols" value="1" ' . checked( '1', esc_attr( $include_symbols ), false ) . ' />
					' . esc_html__( 'Include symbols', 'azrcrv-spg' ) . '
				</label>
				<input name="valid-symbols" type="text" id="valid-symbols" value="' . esc_attr( wp_unslash( $valid_symbols ) ) . '" class="azrcrv-spg-field__charset" />
			</div>
		</div>';
	}

	$text_before_html = '';
	if ( strlen( $text_before ) > 0 ) {
		$text_before_html = '<p class="azrcrv-spg-text">' . esc_html( $text_before ) . '</p>';
	}
	$text_after_html = '';
	if ( strlen( $text_after ) > 0 ) {
		$text_after_html = '<p class="azrcrv-spg-text">' . esc_html( $text_after ) . '</p>';
	}

	$passwords = '';
	if ( ! empty( $responses['passwords'] ) ) {
		$count         = count( $responses['passwords'] );
		$heading       = $count === 1
			? esc_html__( 'Your password', 'azrcrv-spg' )
			: esc_html__( 'Your passwords', 'azrcrv-spg' );
		$password_rows = '';
		foreach ( $responses['passwords'] as $index => $password ) {
			$password_rows .= '<div class="azrcrv-spg-password-row">
				<span class="azrcrv-spg-password-text" id="azrcrv-spg-pw-' . $index . '">' . esc_html( $password ) . '</span>
				<button type="button" class="azrcrv-spg-copy-btn" onclick="azrcrvSpgCopy(' . $index . ', this)" aria-label="' . esc_attr__( 'Copy password', 'azrcrv-spg' ) . '">&#x1F4CB;</button>
			</div>';
		}
		$passwords = '<div class="azrcrv-spg-passwords">
			<h4 class="azrcrv-spg-passwords__heading">' . $heading . '</h4>
			' . $password_rows . '
		</div>
		<script>
		function azrcrvSpgCopy( index, btn ) {
			var text = document.getElementById( "azrcrv-spg-pw-" + index ).innerText;
			navigator.clipboard.writeText( text ).then( function() {
				btn.innerHTML = "&#x2713;";
				btn.classList.add( "azrcrv-spg-copy-btn--copied" );
				setTimeout( function() {
					btn.innerHTML = "&#x1F4CB;";
					btn.classList.remove( "azrcrv-spg-copy-btn--copied" );
				}, 2000 );
			} );
		}
		</script>';
	}

	$messages_html = ! empty( $messages ) ? $messages : '';

	return '<div class="azrcrv-spg-form">

		' . $messages_html . '

		' . $passwords . '

		<form method="post" id="azrcrv-password-form" action="' . esc_attr( $current_url ) . '">

			' . $text_before_html . '

			<input name="password-form-id" type="hidden" value="' . esc_attr( $id ) . '" />' .
			wp_nonce_field( 'azrcrv-spg-password-form', 'azrcrv-spg-password-form-nonce', true, false )
		. '<div class="azrcrv-spg-fields">
				' . $field_number . '
				' . $field_length . '
				' . $field_lowercase . '
				' . $field_uppercase . '
				' . $field_numeric . '
				' . $field_symbols . '
			</div>

			' . $text_after_html . '

			<div class="azrcrv-spg-actions">
				<input type="submit" name="submit" value="' . esc_html__( 'Generate', 'azrcrv-spg' ) . '" class="button-primary" />
			</div>

		</form>

	</div>';
}

/**
 * Process password form after submit.
 *
 * @since 1.0.0
 */
function process_form() {
	if ( ! isset( $_POST['password-form-id'] ) ) {
		return;
	}

	if ( ! isset( $_POST['azrcrv-spg-password-form-nonce'] ) || ! wp_verify_nonce( $_POST['azrcrv-spg-password-form-nonce'], 'azrcrv-spg-password-form' ) ) {
		return array(
			'id'        => sanitize_text_field( wp_unslash( $_POST['password-form-id'] ) ),
			'messages'  => array( 'error-invalid-nonce' ),
			'passwords' => array(),
			'fields'    => array(),
		);
	}

	$options = get_option_with_defaults( PLUGIN_HYPHEN );

	$responses = array(
		'id'        => sanitize_text_field( wp_unslash( $_POST['password-form-id'] ) ),
		'messages'  => array(),
		'passwords' => array(),
	);

	if ( ! isset( $_POST['include-lowercase'] ) && ! isset( $_POST['include-uppercase'] ) && ! isset( $_POST['include-numeric'] ) && ! isset( $_POST['include-symbols'] ) ) {
		$responses['messages'][] = 'error-no-includes';
	}

	if ( isset( $_POST['password-length'] ) && $_POST['password-length'] >= $options['password']['minimum-length'] && $_POST['password-length'] <= $options['password']['maximum-length'] ) {
		$responses['fields']['password-length'] = (int) sanitize_text_field( wp_unslash( $_POST['password-length'] ) );
	} else {
		$responses['fields']['password-length'] = (int) sanitize_text_field( wp_unslash( $options['password']['length'] ) );
	}
	if ( isset( $_POST['password-number'] ) && $_POST['password-number'] >= 1 && $_POST['password-number'] <= $options['password']['maximum-number'] ) {
		$responses['fields']['password-number'] = (int) sanitize_text_field( wp_unslash( $_POST['password-number'] ) );
	} else {
		$responses['fields']['password-number'] = (int) sanitize_text_field( wp_unslash( $options['password']['number'] ) );
	}

	$responses['fields']['include-lowercase'] = isset( $_POST['include-lowercase'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['include-lowercase'] ) ) : 0;
	$responses['fields']['valid-lowercase']   = isset( $_POST['valid-lowercase'] ) ? sanitize_text_field( wp_unslash( $_POST['valid-lowercase'] ) ) : '';
	$responses['fields']['include-uppercase'] = isset( $_POST['include-uppercase'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['include-uppercase'] ) ) : 0;
	$responses['fields']['valid-uppercase']   = isset( $_POST['valid-uppercase'] ) ? sanitize_text_field( wp_unslash( $_POST['valid-uppercase'] ) ) : '';
	$responses['fields']['include-numeric']   = isset( $_POST['include-numeric'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['include-numeric'] ) ) : 0;
	$responses['fields']['valid-numeric']     = isset( $_POST['valid-numeric'] ) ? sanitize_text_field( wp_unslash( $_POST['valid-numeric'] ) ) : '';
	$responses['fields']['include-symbols']   = isset( $_POST['include-symbols'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['include-symbols'] ) ) : 0;
	$responses['fields']['valid-symbols']     = isset( $_POST['valid-symbols'] ) ? sanitize_text_field( wp_unslash( $_POST['valid-symbols'] ) ) : '';

	if ( count( $responses['messages'] ) === 0 ) {
		$responses['passwords'] = generate_password(
			$responses['fields']['password-number'],
			$responses['fields']['password-length'],
			$responses['fields']['include-lowercase'],
			$responses['fields']['valid-lowercase'],
			$responses['fields']['include-uppercase'],
			$responses['fields']['valid-uppercase'],
			$responses['fields']['include-numeric'],
			$responses['fields']['valid-numeric'],
			$responses['fields']['include-symbols'],
			$responses['fields']['valid-symbols']
		);

		$responses['messages'][] = 'success-password-generated';
	}

	return $responses;
}

/**
 * Generate password.
 *
 * @since 1.0.0
 */
function generate_password( $number_of_passwords, $length_of_passwords, $allow_lowercase, $valid_lowercase, $allow_uppercase, $valid_uppercase, $allow_numeric, $valid_numeric, $allow_symbols, $valid_symbols ) {

	$usable_characters = '';
	if ( $allow_lowercase == 1 ) {
		$usable_characters .= $valid_lowercase;
	}
	if ( $allow_uppercase == 1 ) {
		$usable_characters .= $valid_uppercase;
	}
	if ( $allow_numeric == 1 ) {
		$usable_characters .= $valid_numeric;
	}
	if ( $allow_symbols == 1 ) {
		$usable_characters .= $valid_symbols;
	}

	$passwords      = array();
	$charset_length = mb_strlen( $usable_characters );

	for ( $password_loop = 0; $password_loop < $number_of_passwords; $password_loop++ ) {
		$password = '';
		for ( $length_loop = 0; $length_loop < $length_of_passwords; $length_loop++ ) {
			$password .= mb_substr( $usable_characters, wp_rand( 0, $charset_length - 1 ), 1 );
		}
		$passwords[] = $password;
	}

	return $passwords;
}
