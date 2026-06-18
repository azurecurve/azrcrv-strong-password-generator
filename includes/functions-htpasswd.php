<?php
/**
 * htpasswd generator shortcode functions.
 */

/**
 * Declare the Namespace.
 */
namespace azurecurve\StrongPasswordGenerator;

/**
 * Display htpasswd form.
 *
 * @since 2.0.0
 */
function display_htpasswd_form( $atts, $content = null ) {

	global $wp;

	if ( isset( $_POST['htpasswd-form-id'] ) ) {
		$responses = process_htpasswd_form();
	}

	// get options with defaults.
	$options = get_option_with_defaults( PLUGIN_HYPHEN );

	// get shortcode attributes.
	$args = shortcode_atts(
		array(
			'id'                 => '',
			'default-encryption' => $options['htpasswd']['default-encryption'],
			'text-before'        => $options['htpasswd']['text']['before'],
			'text-after'         => $options['htpasswd']['text']['after'],
			'label-username'     => $options['htpasswd']['labels']['username'],
			'label-password'     => $options['htpasswd']['labels']['password'],
			'label-encryption'   => $options['htpasswd']['labels']['encryption'],
		),
		$atts
	);

	// sanitize shortcode attributes.
	$id                 = sanitize_text_field( wp_unslash( $args['id'] ) );
	$allowed_encryptions = array( 'bcrypt', 'apr1', 'sha1' );
	$submitted_enc      = sanitize_text_field( wp_unslash( $args['default-encryption'] ) );
	$default_encryption = in_array( $submitted_enc, $allowed_encryptions, true ) ? $submitted_enc : 'bcrypt';
	$text_before        = sanitize_text_field( wp_unslash( $args['text-before'] ) );
	$text_after         = sanitize_text_field( wp_unslash( $args['text-after'] ) );
	$label_username     = sanitize_text_field( wp_unslash( $args['label-username'] ) );
	$label_password     = sanitize_text_field( wp_unslash( $args['label-password'] ) );
	$label_encryption   = sanitize_text_field( wp_unslash( $args['label-encryption'] ) );

	// is this a valid htpasswd form?
	if ( $id === '' ) {
		return '<div class="azrcrv-spg-form">
			<div class="azrcrv-spg-notice azrcrv-spg-notice--error">
				' . esc_html__( 'htpasswd Generator form cannot be displayed; an id must be provided.', 'azrcrv-spg' ) . '
			</div>
		</div>';
	}

	$messages        = '';
	$repopulate_user = '';
	$selected_enc    = $default_encryption;
	$htpasswd_entry  = '';

	if ( isset( $responses ) && is_array( $responses ) ) {

		if ( isset( $responses['id'] ) && $id === $responses['id'] ) {

			// repopulate username; never repopulate password.
			if ( isset( $responses['fields']['username'] ) ) {
				$repopulate_user = $responses['fields']['username'];
			}
			// restore selected encryption.
			if ( isset( $responses['fields']['encryption'] ) ) {
				$selected_enc = $responses['fields']['encryption'];
			}
			// htpasswd result.
			if ( ! empty( $responses['htpasswd'] ) ) {
				$htpasswd_entry = $responses['htpasswd'];
			}

			if ( is_array( $responses['messages'] ) ) {
				foreach ( $responses['messages'] as $response ) {
					if ( $response === 'error-invalid-nonce' ) {
						$messages .= '<div class="azrcrv-spg-notice azrcrv-spg-notice--error">' . esc_html__( 'The request could not be verified. Please try again.', 'azrcrv-spg' ) . '</div>';
					}
					if ( $response === 'error-empty-username' ) {
						$messages .= '<div class="azrcrv-spg-notice azrcrv-spg-notice--error">' . esc_html__( 'A username is required.', 'azrcrv-spg' ) . '</div>';
					}
					if ( $response === 'error-invalid-username' ) {
						$messages .= '<div class="azrcrv-spg-notice azrcrv-spg-notice--error">' . esc_html__( 'Username must not contain a colon character.', 'azrcrv-spg' ) . '</div>';
					}
					if ( $response === 'error-empty-password' ) {
						$messages .= '<div class="azrcrv-spg-notice azrcrv-spg-notice--error">' . esc_html__( 'A password is required.', 'azrcrv-spg' ) . '</div>';
					}
					if ( $response === 'error-invalid-encryption' ) {
						$messages .= '<div class="azrcrv-spg-notice azrcrv-spg-notice--error">' . esc_html__( 'An invalid encryption method was selected.', 'azrcrv-spg' ) . '</div>';
					}
					if ( $response === 'error-processing' ) {
						$messages .= '<div class="azrcrv-spg-notice azrcrv-spg-notice--error">' . esc_html__( 'There was an error processing your request. Wait a moment and try again.', 'azrcrv-spg' ) . '</div>';
					}
					if ( $response === 'success-htpasswd-generated' ) {
						$messages .= '<div class="azrcrv-spg-notice azrcrv-spg-notice--success">' . esc_html__( 'htpasswd entry generated successfully.', 'azrcrv-spg' ) . '</div>';
					}
				}
			}
		}
	}

	$htpasswd_form = render_htpasswd_form(
		$id,
		$messages,
		$text_before,
		$text_after,
		$label_username,
		$label_password,
		$label_encryption,
		$default_encryption,
		$selected_enc,
		$repopulate_user,
		$htpasswd_entry
	);

	if ( isset( $_POST['htpasswd-form-id'] ) ) {
		$htpasswd_form .= '<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>';
	}

	return $htpasswd_form;
}

/**
 * Render the htpasswd form HTML.
 *
 * @since 2.0.0
 */
function render_htpasswd_form(
	$id,
	$messages,
	$text_before,
	$text_after,
	$label_username,
	$label_password,
	$label_encryption,
	$default_encryption,
	$selected_enc,
	$repopulate_user,
	$htpasswd_entry
) {
	global $wp;

	$current_url = home_url( add_query_arg( array(), $wp->request ) );

	// Build result output.
	$result_html = '';
	if ( $htpasswd_entry !== '' ) {
		$result_html = '<div class="azrcrv-spg-passwords">
			<h4 class="azrcrv-spg-passwords__heading">' . esc_html__( 'Your htpasswd entry', 'azrcrv-spg' ) . '</h4>
			<div class="azrcrv-spg-password-row">
				<code class="azrcrv-spg-password-text" id="azrcrv-spg-htpasswd-entry">' . esc_html( $htpasswd_entry ) . '</code>
				<button type="button" class="azrcrv-spg-copy-btn" onclick="azrcrvSpgCopyHtpasswd(this)" aria-label="' . esc_attr__( 'Copy htpasswd entry', 'azrcrv-spg' ) . '">&#x1F4CB;</button>
			</div>
		</div>
		<script>
		function azrcrvSpgCopyHtpasswd( btn ) {
			var text = document.getElementById( "azrcrv-spg-htpasswd-entry" ).innerText;
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

	// Build text before/after.
	$text_before_html = '';
	if ( strlen( $text_before ) > 0 ) {
		$text_before_html = '<p class="azrcrv-spg-text">' . esc_html( $text_before ) . '</p>';
	}
	$text_after_html = '';
	if ( strlen( $text_after ) > 0 ) {
		$text_after_html = '<p class="azrcrv-spg-text">' . esc_html( $text_after ) . '</p>';
	}

	// Build encryption select options.
	$enc_options = array(
		'bcrypt' => esc_html__( 'bcrypt (recommended)', 'azrcrv-spg' ),
		'apr1'   => esc_html__( 'MD5-APR1', 'azrcrv-spg' ),
		'sha1'   => esc_html__( 'SHA-1', 'azrcrv-spg' ),
	);
	$select_options = '';
	foreach ( $enc_options as $value => $label ) {
		$select_options .= '<option value="' . esc_attr( $value ) . '"' . selected( $selected_enc, $value, false ) . '>' . $label . '</option>';
	}

	$messages_html = ! empty( $messages ) ? $messages : '';

	return '<div class="azrcrv-spg-form">

		' . $messages_html . '

		' . $result_html . '

		<form method="post" id="azrcrv-htpasswd-form-' . esc_attr( $id ) . '" action="' . esc_attr( $current_url ) . '">

			' . $text_before_html . '

			<input name="htpasswd-form-id" type="hidden" value="' . esc_attr( $id ) . '" />' .
			wp_nonce_field( 'azrcrv-spg-htpasswd-form', 'azrcrv-spg-htpasswd-form-nonce', true, false )
		. '<div class="azrcrv-spg-fields">

				<div class="azrcrv-spg-field">
					<label class="azrcrv-spg-field__label" for="htpasswd-username-' . esc_attr( $id ) . '">' . esc_html( $label_username ) . '</label>
					<div class="azrcrv-spg-field__input">
						<input name="htpasswd-username" type="text" id="htpasswd-username-' . esc_attr( $id ) . '" value="' . esc_attr( $repopulate_user ) . '" maxlength="72" autocomplete="off" />
					</div>
				</div>

				<div class="azrcrv-spg-field">
					<label class="azrcrv-spg-field__label" for="htpasswd-password-' . esc_attr( $id ) . '">' . esc_html( $label_password ) . '</label>
					<div class="azrcrv-spg-field__input">
						<input name="htpasswd-password" type="password" id="htpasswd-password-' . esc_attr( $id ) . '" value="" maxlength="72" autocomplete="new-password" />
					</div>
				</div>

				<div class="azrcrv-spg-field">
					<label class="azrcrv-spg-field__label" for="htpasswd-encryption-' . esc_attr( $id ) . '">' . esc_html( $label_encryption ) . '</label>
					<div class="azrcrv-spg-field__input">
						<select name="htpasswd-encryption" id="htpasswd-encryption-' . esc_attr( $id ) . '">
							' . $select_options . '
						</select>
					</div>
				</div>

			</div>

			' . $text_after_html . '

			<div class="azrcrv-spg-actions">
				<input type="submit" name="submit" value="' . esc_html__( 'Generate', 'azrcrv-spg' ) . '" class="button-primary" />
			</div>

		</form>

	</div>';
}

/**
 * Process htpasswd form after submit.
 *
 * @since 2.0.0
 */
function process_htpasswd_form() {
	if ( ! isset( $_POST['htpasswd-form-id'] ) ) {
		return;
	}

	// Verify nonce.
	if ( ! isset( $_POST['azrcrv-spg-htpasswd-form-nonce'] ) || ! wp_verify_nonce( $_POST['azrcrv-spg-htpasswd-form-nonce'], 'azrcrv-spg-htpasswd-form' ) ) {
		return array(
			'id'       => sanitize_text_field( wp_unslash( $_POST['htpasswd-form-id'] ) ),
			'messages' => array( 'error-invalid-nonce' ),
			'htpasswd' => '',
			'fields'   => array(),
		);
	}

	$responses = array(
		'id'       => sanitize_text_field( wp_unslash( $_POST['htpasswd-form-id'] ) ),
		'messages' => array(),
		'htpasswd' => '',
		'fields'   => array(),
	);

	// Validate and sanitize username.
	$username = isset( $_POST['htpasswd-username'] ) ? sanitize_text_field( wp_unslash( $_POST['htpasswd-username'] ) ) : '';
	$username = substr( $username, 0, 255 );
	$responses['fields']['username'] = $username;

	if ( $username === '' ) {
		$responses['messages'][] = 'error-empty-username';
	} elseif ( strpos( $username, ':' ) !== false ) {
		$responses['messages'][] = 'error-invalid-username';
	}

	// Validate password (never stored, never repopulated).
	$password = isset( $_POST['htpasswd-password'] ) ? wp_unslash( $_POST['htpasswd-password'] ) : '';
	$password = substr( $password, 0, 72 );

	if ( $password === '' ) {
		$responses['messages'][] = 'error-empty-password';
	}

	// Validate encryption selection.
	$allowed_encryptions = array( 'bcrypt', 'apr1', 'sha1' );
	$encryption          = isset( $_POST['htpasswd-encryption'] ) ? sanitize_text_field( wp_unslash( $_POST['htpasswd-encryption'] ) ) : '';
	$responses['fields']['encryption'] = $encryption;

	if ( ! in_array( $encryption, $allowed_encryptions, true ) ) {
		$responses['messages'][] = 'error-invalid-encryption';
	}

	// If no errors, generate the hash.
	if ( count( $responses['messages'] ) === 0 ) {
		$hash = generate_htpasswd_hash( $password, $encryption );

		if ( $hash === false ) {
			$responses['messages'][] = 'error-processing';
		} else {
			$responses['htpasswd']   = $username . ':' . $hash;
			$responses['messages'][] = 'success-htpasswd-generated';
		}
	}

	return $responses;
}

/**
 * Generate an htpasswd hash string for the given password and encryption method.
 *
 * @since 2.0.0
 *
 * @param string $password   The plain-text password.
 * @param string $encryption One of: bcrypt, apr1, sha1.
 * @return string|false The hash string on success, false on failure.
 */
function generate_htpasswd_hash( $password, $encryption ) {
	switch ( $encryption ) {

		case 'bcrypt':
			/*
			 * PHP's native bcrypt via password_hash().
			 * Apache 2.4+ accepts the $2y$ prefix directly.
			 */
			$hash = password_hash( $password, PASSWORD_BCRYPT );
			return ( $hash !== false ) ? $hash : false;

		case 'apr1':
			/*
			 * Apache APR1-MD5 format ($apr1$salt$hash).
			 * PHP's native md5() does not produce this format; a custom
			 * implementation of the APR1 algorithm is required.
			 * Algorithm based on the well-known reference implementation
			 * (see: https://www.php.net/manual/en/function.md5.php#104301).
			 */
			$salt = apr1_generate_salt();
			return apr1_md5( $password, $salt );

		case 'sha1':
			/*
			 * Apache SHA-1 format: {SHA} followed by base64-encoded raw SHA-1 digest.
			 * No salt; included for legacy compatibility only.
			 */
			return '{SHA}' . base64_encode( sha1( $password, true ) );

		default:
			return false;
	}
}

/**
 * Generate a cryptographically random 8-character APR1 salt.
 *
 * The salt alphabet for APR1 is the itoa64 set: [./0-9A-Za-z].
 *
 * @since 2.0.0
 *
 * @return string 8-character salt string.
 */
function apr1_generate_salt() {
	$itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	$salt   = '';
	$bytes  = random_bytes( 8 );
	for ( $i = 0; $i < 8; $i++ ) {
		$salt .= $itoa64[ ord( $bytes[ $i ] ) & 0x3f ];
	}
	return $salt;
}

/**
 * Encode a value in APR1's modified base-64 (itoa64) scheme.
 *
 * @since 2.0.0
 *
 * @param string $value  Binary string to encode.
 * @param int    $length Number of output characters to produce.
 * @return string        Encoded string.
 */
function apr1_to64( $value, $length ) {
	$itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	$result = '';
	while ( -- $length >= 0 ) {
		$result .= $itoa64[ $value & 0x3f ];
		$value >>= 6;
	}
	return $result;
}

/**
 * Compute an Apache APR1-MD5 password hash.
 *
 * This implements the multi-round MD5 construction used by Apache's
 * htpasswd tool for the $apr1$ format. The algorithm is identical to
 * the FreeBSD MD5 crypt but uses the "$apr1$" prefix instead of "$1$".
 *
 * Reference: Apache HTTP Server source (apr_md5.c) and the PHP manual
 * user-contributed note by 'thefrox' at php.net/function.md5 (#104301).
 *
 * @since 2.0.0
 *
 * @param string $password Plain-text password.
 * @param string $salt     8-character salt (itoa64 alphabet).
 * @return string          Full APR1 hash string including $apr1$ prefix and salt.
 */
function apr1_md5( $password, $salt ) {
	$apr1_id = '$apr1$';
	$len     = strlen( $password );

	// Start digest A.
	$ctx = $password . $apr1_id . $salt;

	// Digest B: password + salt + password.
	$final = md5( $password . $salt . $password, true );

	// Add bytes from digest B to digest A, cycling through the password length.
	for ( $i = $len; $i > 0; $i -= 16 ) {
		$ctx .= substr( $final, 0, min( 16, $i ) );
	}

	// For each bit in the password length, add either a NUL byte or the first
	// character of the password to digest A.
	$i = $len;
	while ( $i > 0 ) {
		if ( $i & 1 ) {
			$ctx .= chr( 0 );
		} else {
			$ctx .= $password[0];
		}
		$i >>= 1;
	}

	$final = md5( $ctx, true );

	// 1000 rounds of further mixing.
	for ( $i = 0; $i < 1000; $i++ ) {
		$ctx1 = '';
		if ( $i & 1 ) {
			$ctx1 .= $password;
		} else {
			$ctx1 .= $final;
		}
		if ( $i % 3 ) {
			$ctx1 .= $salt;
		}
		if ( $i % 7 ) {
			$ctx1 .= $password;
		}
		if ( $i & 1 ) {
			$ctx1 .= $final;
		} else {
			$ctx1 .= $password;
		}
		$final = md5( $ctx1, true );
	}

	// Encode the 16 digest bytes in APR1's byte-ordering into itoa64.
	$f = $final;
	$encoded  = apr1_to64( ( ord( $f[0] ) << 16 ) | ( ord( $f[6] ) << 8 ) | ord( $f[12] ), 4 );
	$encoded .= apr1_to64( ( ord( $f[1] ) << 16 ) | ( ord( $f[7] ) << 8 ) | ord( $f[13] ), 4 );
	$encoded .= apr1_to64( ( ord( $f[2] ) << 16 ) | ( ord( $f[8] ) << 8 ) | ord( $f[14] ), 4 );
	$encoded .= apr1_to64( ( ord( $f[3] ) << 16 ) | ( ord( $f[9] ) << 8 ) | ord( $f[15] ), 4 );
	$encoded .= apr1_to64( ( ord( $f[4] ) << 16 ) | ( ord( $f[10] ) << 8 ) | ord( $f[5] ), 4 );
	$encoded .= apr1_to64( ord( $f[11] ), 2 );

	return $apr1_id . $salt . '$' . $encoded;
}
