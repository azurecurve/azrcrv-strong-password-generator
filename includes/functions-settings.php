<?php
/**
 * Settings functions.
 */

/**
 * Declare the Namespace.
 */
namespace azurecurve\StrongPasswordGenerator;

/**
 * Get options including defaults.
 *
 * @since 1.0.0
 */
function get_option_with_defaults( $option_name ) {

	$defaults = array(
		'text'     => array(
			'before' => esc_html__( 'To generate a strong password, select your options and click the generate button.', 'azrcrv-spg' ),
			'after'  => '',
		),
		'labels'   => array(
			'password-length' => esc_html__( 'Password Length', 'azrcrv-spg' ),
			'password-number' => esc_html__( 'Number of Passwords', 'azrcrv-spg' ),
			'numeric'         => esc_html__( 'Numeric', 'azrcrv-spg' ),
			'lowercase'       => esc_html__( 'Lowercase', 'azrcrv-spg' ),
			'uppercase'       => esc_html__( 'Uppercase', 'azrcrv-spg' ),
			'symbols'         => esc_html__( 'Symbols', 'azrcrv-spg' ),
		),
		'allowed'  => array(
			'numeric'   => 1,
			'lowercase' => 1,
			'uppercase' => 1,
			'symbols'   => 1,
		),
		'valid'    => array(
			'numeric'   => '1234567890',
			'lowercase' => 'abcdefghijklmnopqrstuvwxyz',
			'uppercase' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
			'symbols'   => '!^*()_+-=@#~<>?|',
		),
		'password' => array(
			'number'         => 3,
			'maximum-number' => 10,
			'length'         => 20,
			'minimum-length' => 5,
			'maximum-length' => 128,
		),
		'htpasswd' => array(
			'default-encryption' => 'bcrypt',
			'text'               => array(
				'before' => '',
				'after'  => '',
			),
			'labels'             => array(
				'username'   => esc_html__( 'Username', 'azrcrv-spg' ),
				'password'   => esc_html__( 'Password', 'azrcrv-spg' ),
				'encryption' => esc_html__( 'Encryption', 'azrcrv-spg' ),
			),
		),
	);

	$options = get_option( $option_name, $defaults );

	$options = recursive_parse_args( $options, $defaults );

	return $options;
}

/**
 * Recursively parse options to merge with defaults.
 *
 * @since 1.0.0
 */
function recursive_parse_args( $args, $defaults ) {
	$new_args = (array) $defaults;

	foreach ( $args as $key => $value ) {
		if ( is_array( $value ) && isset( $new_args[ $key ] ) ) {
			$new_args[ $key ] = recursive_parse_args( $value, $new_args[ $key ] );
		} else {
			$new_args[ $key ] = $value;
		}
	}

	return $new_args;
}

/**
 * Display Settings page.
 *
 * @since 1.0.0
 */
function display_options() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'azrcrv-spg' ) );
	}

	// Retrieve plugin configuration options from database.
	$options = get_option_with_defaults( PLUGIN_HYPHEN );

	echo '<div id="' . esc_attr( PLUGIN_HYPHEN ) . '-general" class="wrap">';

		echo '<h1>';
			echo '<a href="' . esc_url_raw( DEVELOPER_RAW_LINK . PLUGIN_SHORT_SLUG . '/' ) . '"><img src="' . esc_url_raw( plugins_url( '../assets/images/logo.svg', __FILE__ ) ) . '" style="padding-right: 6px; height: 20px; width: 20px;" alt="azurecurve" /></a>';
			echo esc_html( get_admin_page_title() );
		echo '</h1>';

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['settings-updated'] ) ) {
		echo '<div class="notice notice-success is-dismissible">
				<p><strong>' . esc_html__( 'Settings have been saved.', 'azrcrv-spg' ) . '</strong></p>
			</div>';
	}

		require_once 'tab-settings.php';
		require_once 'tab-htpasswd-settings.php';
		require_once 'tab-instructions.php';
		require_once 'tab-other-plugins.php';
		require_once 'tabs-output.php';
	?>

	</div>
	<?php
}

/**
 * Save settings.
 *
 * @since 1.0.0
 */
function save_options() {
	// Check that user has proper security level.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permissions to perform this action', 'azrcrv-spg' ) );
	}

	// Check that nonce field created in configuration form is present.
	if ( ! empty( $_POST ) && check_admin_referer( PLUGIN_HYPHEN, PLUGIN_HYPHEN . '-nonce' ) ) {

		// Retrieve original plugin options array.
		$options = get_option( PLUGIN_HYPHEN );

		/*
		Password
		*/
		$option_name = 'password-length';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['password']['length'] = (int) sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
		}
		$option_name = 'password-minimum-length';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['password']['minimum-length'] = (int) sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
		}
		$option_name = 'password-maximum-length';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['password']['maximum-length'] = (int) sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
		}
		$option_name = 'password-number';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['password']['number'] = (int) sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
		}
		$option_name = 'password-maximum-number';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['password']['maximum-number'] = (int) sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
		}

		/*
		Text and Labels
		*/
		// labels.
		$option_name = 'label-password-length';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['labels']['password-length'] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
		}
		$option_name = 'label-password-number';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['labels']['password-number'] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
		}
		$option_name = 'label-lowercase';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['labels']['lowercase'] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
		}
		$option_name = 'label-uppercase';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['labels']['uppercase'] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
		}
		$option_name = 'label-numeric';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['labels']['numeric'] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
		}
		$option_name = 'label-symbols';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['labels']['symbols'] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
		}
		// text.
		$option_name = 'text-before';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['text']['before'] = wp_kses( wp_unslash( $_POST[ $option_name ] ), wp_kses_allowed_html( 'post' ) );
		}
		$option_name = 'text-after';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['text']['after'] = wp_kses( wp_unslash( $_POST[ $option_name ] ), wp_kses_allowed_html( 'post' ) );
		}

		/*
		Allowed character sets
		*/
		$option_name = 'allowed-numeric';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['allowed']['numeric'] = 1;
		} else {
			$options['allowed']['numeric'] = 0;
		}
		$option_name = 'valid-numeric';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['valid']['numeric'] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
		}

		$option_name = 'allowed-lowercase';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['allowed']['lowercase'] = 1;
		} else {
			$options['allowed']['lowercase'] = 0;
		}
		$option_name = 'valid-lowercase';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['valid']['lowercase'] = strtolower( sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) ) );
		}

		$option_name = 'allowed-uppercase';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['allowed']['uppercase'] = 1;
		} else {
			$options['allowed']['uppercase'] = 0;
		}
		$option_name = 'valid-uppercase';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['valid']['uppercase'] = strtoupper( sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) ) );
		}

		$option_name = 'allowed-symbols';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['allowed']['symbols'] = 1;
		} else {
			$options['allowed']['symbols'] = 0;
		}
		$option_name = 'valid-symbols';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['valid']['symbols'] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
		}

		/*
		htpasswd settings
		*/
		$option_name = 'htpasswd-default-encryption';
		if ( isset( $_POST[ $option_name ] ) ) {
			$allowed_encryptions = array( 'bcrypt', 'apr1', 'sha1' );
			$submitted           = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
			$options['htpasswd']['default-encryption'] = in_array( $submitted, $allowed_encryptions, true ) ? $submitted : 'bcrypt';
		}
		$option_name = 'htpasswd-text-before';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['htpasswd']['text']['before'] = wp_kses( wp_unslash( $_POST[ $option_name ] ), wp_kses_allowed_html( 'post' ) );
		}
		$option_name = 'htpasswd-text-after';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['htpasswd']['text']['after'] = wp_kses( wp_unslash( $_POST[ $option_name ] ), wp_kses_allowed_html( 'post' ) );
		}
		$option_name = 'htpasswd-label-username';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['htpasswd']['labels']['username'] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
		}
		$option_name = 'htpasswd-label-password';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['htpasswd']['labels']['password'] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
		}
		$option_name = 'htpasswd-label-encryption';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['htpasswd']['labels']['encryption'] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
		}

		// Store updated options array to database.
		update_option( PLUGIN_HYPHEN, $options );

		// Redirect the page to the configuration form that was processed.
		wp_safe_redirect( add_query_arg( array( 'page' => PLUGIN_HYPHEN, 'settings-updated' => 'true' ), admin_url( 'admin.php' ) ) );
		exit;
	}
}
