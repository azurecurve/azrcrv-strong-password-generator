<?php
/**
 * ------------------------------------------------------------------------------
 * Plugin Name:		Strong Password Generator
 * Description:		Generate strong passwords.
 * Version:			1.0.4
 * Requires CP:		1.0
 * Requires PHP:	7.4
 * Author:			azurecurve
 * Author URI:		https://development.azurecurve.co.uk/classicpress-plugins/
 * Plugin URI:		https://development.azurecurve.co.uk/classicpress-plugins/link-managements/
 * Donate link:		https://development.azurecurve.co.uk/support-development/
 * Text Domain:		azrcrv-spg
 * Domain Path:		/languages
 * License:			GPLv2 or later
 * License URI:		http://www.gnu.org/licenses/gpl-2.0.html
 * ------------------------------------------------------------------------------
 * This is free software released under the terms of the General Public License,
 * version 2, or later. It is distributed WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Full
 * text of the license is available at https://www.gnu.org/licenses/rrl-2.0.html.
 * ------------------------------------------------------------------------------
 */

// Declare the namespace.
namespace azurecurve\StrongPasswordGenerator;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

// include plugin menu.
require_once dirname( __FILE__ ) . '/pluginmenu/menu.php';
add_action( 'admin_init', 'azrcrv_create_plugin_menu_spg' );

// include update client
require_once dirname( __FILE__ ) . '/libraries/updateclient/UpdateClient.class.php';

/**
 * Setup registration activation hook, actions, filters and shortcodes.
 *
 * @since 1.0.0
 */

// add actions.
add_action( 'admin_menu', __NAMESPACE__ . '\\create_admin_menu' );
add_action( 'admin_init', __NAMESPACE__ . '\\register_admin_styles' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_admin_styles' );
add_action( 'admin_init', __NAMESPACE__ . '\\register_admin_scripts' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_admin_scripts' );
add_action( 'init', __NAMESPACE__ . '\\register_frontend_styles' );
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_frontend_styles' );
add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_languages' );
add_action( 'admin_post_azrcrv_spg_save_options', __NAMESPACE__ . '\\save_options' );

// add filters.
add_filter( 'plugin_action_links', __NAMESPACE__ . '\\add_plugin_action_link', 10, 2 );

// add shortcodes
add_shortcode( 'strong-password-generator', __NAMESPACE__ . '\\display_form' );

/**
 * Register admin styles.
 *
 * @since 1.0.0
 */
function register_admin_styles() {
	wp_register_style( 'azrcrv-spg-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), '1.0.0' );
	wp_register_style( 'azrcrv-pluginmenu-admin-styles', plugins_url( 'pluginmenu/css/style.css', __FILE__ ), array(), '1.0.0' );
}

/**
 * Enqueue admin styles.
 *
 * @since 1.0.0
 */
function enqueue_admin_styles() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'azrcrv-spg' ) ) {
		wp_enqueue_style( 'azrcrv-spg-admin-styles' );
		wp_enqueue_style( 'azrcrv-pluginmenu-admin-styles' );
	}
}

/**
 * Register admin scripts.
 *
 * @since 1.0.0
 */
function register_admin_scripts() {
	wp_register_script( 'azrcrv-spg-admin-jquery', plugins_url( 'assets/jquery/admin.js', __FILE__ ), array(), '1.0.0', true );
}

/**
 * Enqueue admin styles.
 *
 * @since 1.0.0
 */
function enqueue_admin_scripts() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'azrcrv-spg' ) ) {
		wp_enqueue_script( 'azrcrv-spg-admin-jquery' );
	}
}

/**
 * Register frontend styles.
 *
 * @since 1.0.0
 */
function register_frontend_styles() {
	wp_register_style( 'azrcrv-spg-styles', plugins_url( 'assets/css/styles.css', __FILE__ ), array(), '1.0.0' );
}

/**
 * Enqueue frontend styles.
 *
 * @since 1.0.0
 */
function enqueue_frontend_styles() {
	wp_enqueue_style( 'azrcrv-spg-styles' );
}

/**
 * Load language files.
 *
 * @since 1.0.0
 */
function load_languages() {
	$plugin_rel_path = basename( dirname( __FILE__ ) ) . '/languages';
	load_plugin_textdomain( 'azrcrv-spg', false, $plugin_rel_path );
}

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
 * Add action link on plugins page.
 *
 * @since 1.0.0
 */
function add_plugin_action_link( $links, $file ) {
	static $this_plugin;

	if ( ! $this_plugin ) {
		$this_plugin = plugin_basename( __FILE__ );
	}

	if ( $file == $this_plugin ) {
		$settings_link = '<a href="' . esc_url_raw( admin_url( 'admin.php?page=azrcrv-spg' ) ) . '"><img src="' . esc_url_raw( plugins_url( '/pluginmenu/images/logo.svg', __FILE__ ) ) . '" style="padding-top: 2px; margin-right: -5px; height: 16px; width: 16px;" alt="azurecurve" />' . esc_html__( 'Settings', 'azrcrv-spg' ) . '</a>';
		array_unshift( $links, $settings_link );
	}

	return $links;
}

/**
 * Add to menu.
 *
 * @since 1.0.0
 */
function create_admin_menu() {

	add_submenu_page(
		'azrcrv-plugin-menu',
		esc_html__( 'Strong Password Generator Settings', 'azrcrv-spg' ),
		esc_html__( 'Strong Password Generator', 'azrcrv-spg' ),
		'manage_options',
		'azrcrv-spg',
		__NAMESPACE__ . '\\display_options'
	);

}

/**
 * Load admin css.
 *
 * @since 1.0.0
 */
function load_admin_style() {
	wp_register_style( 'r-css', plugins_url( 'assets/css/admin.css', __FILE__ ), false, '1.0.0' );
	wp_enqueue_style( 'r-css' );
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

	global $wpdb;

	// Retrieve plugin configuration options from database.
	$options = get_option_with_defaults( 'azrcrv-spg' );

	echo '<div id="azrcrv-spg-general" class="wrap">';

	?>
		<h1>
			<?php
				echo '<a href="https://development.azurecurve.co.uk/classicpress-plugins/"><img src="' . esc_html( plugins_url( '/pluginmenu/images/logo.svg', __FILE__ ) ) . '" style="padding-right: 6px; height: 20px; width: 20px;" alt="azurecurve | Development" /></a>';
				echo esc_html( get_admin_page_title() );
			?>
		</h1>
		<?php

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['settings-updated'] ) ) {
			echo '<div class="notice notice-success is-dismissible">
					<p><strong>' . esc_html__( 'Settings have been saved.', 'azrcrv-spg' ) . '</strong></p>
				</div>';
		}

		$tab_1_label = esc_html__( 'Password Options', 'azrcrv-spg' );
		$tab_1       = '<table class="form-table azrcrv-spg">
		
					<tr>
					
						<th scope="row" colspan=2 class="section-heading">
							
								<h2 class="azrcrv-spg">' . esc_html__( 'Passwords', 'azrcrv-spg' ) . '</h2>
							
						</th>
	
					</tr>
		
					<tr>
					
						<th scope="row">
							
								' . esc_html__( 'Default Length', 'azrcrv-spg' ) . '
							
						</th>
					
						<td>
							
							<input name="password-length" type="number" min=5 max=256 step=1 id="password-length" value="' . esc_html( wp_unslash( $options['password']['length'] ) ) . '" class="small-text" />
							
						</td>
	
					</tr>
		
					<tr>
					
						<th scope="row">
							
								' . esc_html__( 'Minimum Length', 'azrcrv-spg' ) . '
							
						</th>
					
						<td>
							
							<input name="password-minimum-length" type="number" min=5 step=1 id="password-minimum-length" value="' . esc_html( wp_unslash( $options['password']['minimum-length'] ) ) . '" class="small-text" />
							
						</td>
	
					</tr>
		
					<tr>
					
						<th scope="row">
							
								' . esc_html__( 'Maximum Length', 'azrcrv-spg' ) . '
							
						</th>
					
						<td>
							
							<input name="password-maximum-length" type="number" min=6 step=1 id="password-maximum-length" value="' . esc_html( wp_unslash( $options['password']['maximum-length'] ) ) . '" class="small-text" />
							
						</td>
	
					</tr>
		
					<tr>
					
						<th scope="row">
							
								' . esc_html__( 'Number of Passwords', 'azrcrv-spg' ) . '
							
						</th>
					
						<td>
							
							<input name="password-number" type="number" min=1 step=1 id="password-number" value="' . esc_html( wp_unslash( $options['password']['number'] ) ) . '" class="small-text" />
							
						</td>
	
					</tr>
		
					<tr>
					
						<th scope="row">
							
								' . esc_html__( 'Maximum Number of Passwords', 'azrcrv-spg' ) . '
							
						</th>
					
						<td>
							
							<input name="password-maximum-number" type="number" min=1 step=1 id="password-maximum-number" value="' . esc_html( wp_unslash( $options['password']['maximum-number'] ) ) . '" class="small-text" />
							
						</td>
	
					</tr>
		
					<tr>
					
						<th scope="row" colspan=2 class="section-heading">
							
								<h2 class="azrcrv-spg">' . esc_html__( 'Allowed', 'azrcrv-spg' ) . '</h2>
							
						</th>
	
					</tr>
		
					<tr>
					
						<th scope="row">
							
								' . esc_html__( 'Numeric', 'azrcrv-spg' ) . '
							
						</th>
					
						<td>
							
							<input name="allowed-numeric" type="checkbox" id="allowed-numeric" value="1" ' . checked( '1', esc_attr( $options['allowed']['numeric'] ), false ) . ' />&nbsp;
							<input name="valid-numeric" type="number" id="valid-numeric" value="' . esc_html( wp_unslash( $options['valid']['numeric'] ) ) . '" class="regular-text" />
							
						</td>
	
					</tr>
		
					<tr>
					
						<th scope="row">
							
								' . esc_html__( 'Uppercase', 'azrcrv-spg' ) . '
							
						</th>
					
						<td>
							
							<input name="allowed-uppercase" type="checkbox" id="allowed-uppercase" value="1" ' . checked( '1', esc_attr( $options['allowed']['uppercase'] ), false ) . ' />&nbsp;
							<input name="valid-uppercase" type="text" id="valid-uppercase" value="' . esc_html( wp_unslash( $options['valid']['uppercase'] ) ) . '" class="regular-text" />
							
						</td>
	
					</tr>
		
					<tr>
					
						<th scope="row">
							
								' . esc_html__( 'Lowercase', 'azrcrv-spg' ) . '
							
						</th>
					
						<td>
							
							<input name="allowed-lowercase" type="checkbox" id="allowed-lowercase" value="1" ' . checked( '1', esc_attr( $options['allowed']['lowercase'] ), false ) . ' />&nbsp;
							<input name="valid-lowercase" type="text" id="valid-lowercase" value="' . esc_html( wp_unslash( $options['valid']['lowercase'] ) ) . '" class="regular-text" />
							
						</td>
	
					</tr>
		
					<tr>
					
						<th scope="row">
							
								' . esc_html__( 'Symbols', 'azrcrv-spg' ) . '
							
						</th>
					
						<td>
							
							<input name="allowed-symbols" type="checkbox" id="allowed-symbols" value="1" ' . checked( '1', esc_attr( $options['allowed']['symbols'] ), false ) . ' />&nbsp;
							<input name="valid-symbols" type="text" id="valid-symbols" value="' . esc_html( wp_unslash( $options['valid']['symbols'] ) ) . '" class="regular-text" />
							
						</td>
	
					</tr>
					
				</table>';

		$tab_2_label = esc_html__( 'Labels & Text', 'azrcrv-spg' );
		$tab_2       = '<table class="form-table azrcrv-spg">
		
					<tr>
					
						<th scope="row" colspan=2 class="section-heading">
							
								<h2 class="azrcrv-spg">' . esc_html__( 'Labels', 'azrcrv-spg' ) . '</h2>
							
						</th>
	
					</tr>
		
					<tr>
					
						<th scope="row">
							
								' . esc_html__( 'Password Length', 'azrcrv-spg' ) . '
							
						</th>
					
						<td>
							
							<input name="label-password-length" type="text" id="label-password-length" value="' . esc_html( wp_unslash( $options['labels']['password-length'] ) ) . '" class="regular-text" />
							
						</td>
	
					</tr>
		
					<tr>
					
						<th scope="row">
							
								' . esc_html__( 'Number of Passwords', 'azrcrv-spg' ) . '
							
						</th>
					
						<td>
							
							<input name="label-password-number" type="text" id="label-password-number" value="' . esc_html( wp_unslash( $options['labels']['password-number'] ) ) . '" class="regular-text" />
							
						</td>
	
		
					<tr>
					
						<th scope="row">
							
								' . esc_html__( 'Lowercase', 'azrcrv-spg' ) . '
							
						</th>
					
						<td>
							
							<input name="label-lowercase" type="text" id="label-lowercase" value="' . esc_html( wp_unslash( $options['labels']['lowercase'] ) ) . '" class="regular-text" />
							
						</td>
	
					</tr>
		
					<tr>
					
						<th scope="row">
							
								' . esc_html__( 'Uppercase', 'azrcrv-spg' ) . '
							
						</th>
					
						<td>
							
							<input name="label-uppercase" type="text" id="label-uppercase" value="' . esc_html( wp_unslash( $options['labels']['uppercase'] ) ) . '" class="regular-text" />
							
						</td>
	
					</tr>
		
					<tr>
					
						<th scope="row">
							
								' . esc_html__( 'Numeric', 'azrcrv-spg' ) . '
							
						</th>
					
						<td>
							
							<input name="label-numeric" type="text" id="label-numeric" value="' . esc_html( wp_unslash( $options['labels']['numeric'] ) ) . '" class="regular-text" />
							
						</td>
	
					</tr>
		
					<tr>
					
						<th scope="row">
							
								' . esc_html__( 'Symbols', 'azrcrv-spg' ) . '
							
						</th>
					
						<td>
							
							<input name="label-symbols" type="text" id="label-symbols" value="' . esc_html( wp_unslash( $options['labels']['symbols'] ) ) . '" class="regular-text" />
							
						</td>
	
					</tr>
		
					<tr>
					
						<th scope="row" colspan=2 class="section-heading">
							
								<h2 class="azrcrv-spg">' . esc_html__( 'Text', 'azrcrv-spg' ) . '</h2>
							
						</th>
	
					</tr>
		
					<tr>
					
						<th scope="row">
							
								' . esc_html__( 'Before Form', 'azrcrv-spg' ) . '
							
						</th>
					
						<td>
							
							<textarea name="text-before" rows="10" cols="50" id="text-before" class="large-text">' . esc_textarea( $options['text']['before'] ) . '</textarea>
							
						</td>
	
					</tr>
		
					<tr>
					
						<th scope="row">
							
								' . esc_html__( 'After Form', 'azrcrv-spg' ) . '
							
						</th>
					
						<td>
							
							<textarea name="text-after" rows="10" cols="50" id="text-after" class="large-text">' . esc_textarea( $options['text']['after'] ) . '</textarea>
							
						</td>
	
					</tr>
					
				</table>';
				
		$tab_3_label =  esc_html__( 'Instructions', 'azrcrv-spg' );
		$tab_3       = '<table class="form-table azrcrv-spg">
		
					<tr>
					
						<th scope="row" colspan=2 class="section-heading">
							
								<h2 class="azrcrv-spg">' . esc_html__( 'Shortcode Usage', 'azrcrv-spg' ) . '</h2>
							
						</th>
	
					</tr>
		
					<tr>
					
						<td scope="row" colspan=2>
						
							<p>' .
								sprintf( esc_html__( 'Password forms are placed using the %s shortcode and can have a number of parameters supplied to override the defaults from the options page; each shortcode must have an %s parameter supplied. Available parameters are:', 'azrcrv-spg' ), '<code>&lsqb;strong-password-generator&rsqb;</code>', '<code>id</code>' ) . '
									 <ul>
										<li><code>password-length</code> - ' . esc_html__( 'length of password to be generated.' , 'azrcrv-spg' ) . '
										<li><code>password-minimum-length</code> - ' . esc_html__( 'minimum length of passwords which can be generated.' , 'azrcrv-spg' ) . '
										<li><code>password-maximum-length</code> - ' . esc_html__( 'maximum length of passwords which can be generated.' , 'azrcrv-spg' ) . '
										<li><code>password-number</code> - ' . esc_html__( 'number of passwords to generate.' , 'azrcrv-spg' ) . '
										<li><code>password-maximum-number</code> - ' . esc_html__( 'maximum umber of passwords whichuser can generate.' , 'azrcrv-spg' ) . '
										<li><code>text-before</code> - ' . esc_html__( 'text to display before password form.' , 'azrcrv-spg' ) . '
										<li><code>text-after</code> - ' . esc_html__( 'text to display after password form.' , 'azrcrv-spg' ) . '
										<li><code>label-password-length</code> - ' . esc_html__( 'label for password length field.' , 'azrcrv-spg' ) . '
										<li><code>label-password-number</code> - ' . esc_html__( 'label for number of passwords to generate field.' , 'azrcrv-spg' ) . '
										<li><code>label-lowercase</code> - ' . esc_html__( 'label for valid lowercase field.' , 'azrcrv-spg' ) . '
										<li><code>label-uppercase</code> - ' . esc_html__( 'label for valid uppercase field.' , 'azrcrv-spg' ) . '
										<li><code>label-numeric</code> - ' . esc_html__( 'label for valid numbers field.' , 'azrcrv-spg' ) . '
										<li><code>label-symbols</code> - ' . esc_html__( 'label for valid symbols field.' , 'azrcrv-spg' ) . '
										<li><code>allow-lowercase</code> - ' . esc_html__( 'allow user to include uppercase characters.' , 'azrcrv-spg' ) . '
										<li><code>allow-uppercase</code> - ' . esc_html__( 'allow user to include uppercase characters.' , 'azrcrv-spg' ) . '
										<li><code>allow-numeric</code> - ' . esc_html__( 'allow user to include number.' , 'azrcrv-spg' ) . '
										<li><code>allow-symbols</code> - ' . esc_html__( 'allow user to include symbols.' , 'azrcrv-spg' ) . '
										<li><code>valid-lowercase</code> - ' . esc_html__( 'list of valid lowercase characters.' , 'azrcrv-spg' ) . '
										<li><code>valid-uppercase</code> - ' . esc_html__( 'list of valid uppercase characters.' , 'azrcrv-spg' ) . '
										<li><code>valid-numeric</code> - ' . esc_html__( 'list of valid numbers.' , 'azrcrv-spg' ) . '
										<li><code>valid-symbols</code> - ' . esc_html__( 'list of valid symbols.' , 'azrcrv-spg' ) . '
									</ul>

									<p>' . esc_html__( 'Example shortcode usage:', 'azrcrv-spg' ) . '</p>
									
									<p><code>[strong-password-generator id="password-1"  text-before="The password generator below can be used to produce passwords compatible with Microsoft Dynamics GP." allow-symbols=1]</code></p>
									
							</p>
						
						</td>
					
					</tr>
					
				</table>';
				
		$plugin_array = get_option( 'azrcrv-plugin-menu' );

		$tab_4_plugins = '';
		foreach ( $plugin_array as $plugin_name => $plugin_details ) {
			if ( $plugin_details['retired'] == 0 ) {
				$alternative_color = '';
				if ( isset( $plugin_details['bright'] ) and $plugin_details['bright'] == 1 ) {
					$alternative_color = 'bright-';
				}
				if ( isset( $plugin_details['premium'] ) and $plugin_details['premium'] == 1 ) {
					$alternative_color = 'premium-';
				}
				if ( is_plugin_active( $plugin_details['plugin_link'] ) ) {
					$tab_4_plugins .= "<a href='{$plugin_details['admin_URL']}' class='azrcrv-{$alternative_color}plugin-index'>{$plugin_name}</a>";
				}else{
					$tab_4_plugins .= "<a href='{$plugin_details['dev_URL']}' class='azrcrv-{$alternative_color}plugin-index'>{$plugin_name}</a>";
				}
			}
		}

		$tab_4_label =  esc_html__( 'Other Plugins', 'azrcrv-spg' );
		$tab_4       = '<table class="form-table azrcrv-spg">
		
					<tr>
					
						<td scope="row" colspan=2>
						
							<p>' .
								sprintf( esc_html__( '%1$s was one of the first plugin developers to start developing for ClassicPress; all plugins are available from %2$s and are integrated with the %3$s plugin for fully integrated, no hassle, updates.', 'azrcrv-spg' ), '<strong>azurecurve | Development</strong>', '<a href="https://development.azurecurve.co.uk/classicpress-plugins/">azurecurve | Development</a>', '<a href="https://directory.classicpress.net/plugins/update-manager/">Update Manager</a>' )
							. '</p>
							<p>' .
								sprintf( esc_html__( 'Other plugins available from %s are:', 'azrcrv-spg' ), '<strong>azurecurve | Development</strong>' )
							. '</p>
						
						</td>
					
					</tr>
					
					<tr>
					
						<td scope="row" colspan=2>
						
							' . $tab_4_plugins . '
							
						</td>
	
					</tr>
					
				</table>';

		?>
		<form method="post" action="admin-post.php">

				<input type="hidden" name="action" value="azrcrv_spg_save_options" />

				<?php
					// <!-- Adding security through hidden referer field -->.
					wp_nonce_field( 'azrcrv-spg', 'azrcrv-spg-nonce' );
				?>
				
				
				<div id="tabs" class="azrcrv-ui-tabs">
					<ul class="azrcrv-ui-tabs-nav azrcrv-ui-widget-header" role="tablist">
						<li class="azrcrv-ui-state-default azrcrv-ui-state-active" aria-controls="tab-panel-1" aria-labelledby="tab-1" aria-selected="true" aria-expanded="true" role="tab">
							<a id="tab-1" class="azrcrv-ui-tabs-anchor" href="#tab-panel-1"><?php echo $tab_1_label; ?></a>
						</li>
						<li class="azrcrv-ui-state-default" aria-controls="tab-panel-2" aria-labelledby="tab-2" aria-selected="false" aria-expanded="false" role="tab">
							<a id="tab-2" class="azrcrv-ui-tabs-anchor" href="#tab-panel-2"><?php echo $tab_2_label; ?></a>
						</li>
						<li class="azrcrv-ui-state-default" aria-controls="tab-panel-3" aria-labelledby="tab-3" aria-selected="false" aria-expanded="false" role="tab">
							<a id="tab-3" class="azrcrv-ui-tabs-anchor" href="#tab-panel-3"><?php echo $tab_3_label; ?></a>
						</li>
						<li class="azrcrv-ui-state-default" aria-controls="tab-panel-4" aria-labelledby="tab-4" aria-selected="false" aria-expanded="false" role="tab">
							<a id="tab-4" class="azrcrv-ui-tabs-anchor" href="#tab-panel-4"><?php echo $tab_4_label; ?></a>
						</li>
					</ul>
					<div id="tab-panel-1" class="azrcrv-ui-tabs-scroll" role="tabpanel" aria-hidden="false">
						<fieldset>
							<legend class='screen-reader-text'>
								<?php echo $tab_1_label; ?>
							</legend>
							<?php echo $tab_1; ?>
						</fieldset>
					</div>
					<div id="tab-panel-2" class="azrcrv-ui-tabs-scroll azrcrv-ui-tabs-hidden" role="tabpanel" aria-hidden="true">
						<fieldset>
							<legend class='screen-reader-text'>
								<?php echo $tab_2_label; ?>
							</legend>
							<?php echo $tab_2; ?>
						</fieldset>
					</div>
					<div id="tab-panel-3" class="azrcrv-ui-tabs-scroll azrcrv-ui-tabs-hidden" role="tabpanel" aria-hidden="true">
						<fieldset>
							<legend class='screen-reader-text'>
								<?php echo $tab_3_label; ?>
							</legend>
							<?php echo $tab_3; ?>
						</fieldset>
					</div>
					<div id="tab-panel-4" class="azrcrv-ui-tabs-scroll azrcrv-ui-tabs-hidden" role="tabpanel" aria-hidden="true">
						<fieldset>
							<legend class='screen-reader-text'>
								<?php echo $tab_4_label; ?>
							</legend>
							<?php echo $tab_4; ?>
						</fieldset>
					</div>
				</div>

			<input type="submit" name="btn_save" value="<?php esc_html_e( 'Save Settings', 'azrcrv-spg' ); ?>" class="button-primary"/>
		</form>
		<div class='azrcrv-spg-donate'>
			<?php
				esc_html_e( 'Support' , 'azrcrv-spg' );
			?>
			azurecurve | Development
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="MCJQN9SJZYLWJ">
				<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
				<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
			</form>
			<span>
				<?php
				esc_html_e( 'You can help support the development of our free plugins by donating a small amount of money.' , 'azrcrv-spg' );
				?>
			</span>
		</div>
	</div>
	<?php

}

/**
 * Check if function active (included due to standard function failing due to order of load).
 *
 * @since 1.0.0
 */
function is_azrcrv_plugin_active( $plugin ) {
	return in_array( $plugin, (array) get_option( 'active_plugins', array() ) );
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
	if ( ! empty( $_POST ) && check_admin_referer( 'azrcrv-spg', 'azrcrv-spg-nonce' ) ) {

		// Retrieve original plugin options array.
		$options = get_option( 'azrcrv-spg' );

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
		// labels
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
		// text
		$option_name = 'text-before';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['text']['before'] = wp_kses( wp_unslash( $_POST[ $option_name ] ), wp_kses_allowed_html( 'post' ) );
		}
		$option_name = 'text-after';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['text']['after'] = wp_kses( wp_unslash( $_POST[ $option_name ] ), wp_kses_allowed_html( 'post' ) );
		}

		/*
		Allowed
		*/
		$option_name = 'allowed-numeric';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['allowed']['numeric'] = 1;
		} else {
			$options['allowed']['numeric'] = 0;
		}
		$option_name = 'valid-numeric';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['valid']['numeric'] = (int) sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
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

		// Store updated options array to database.
		update_option( 'azrcrv-spg', $options );

		// Redirect the page to the configuration form that was processed.
		wp_safe_redirect( add_query_arg( 'page', 'azrcrv-spg&settings-updated', admin_url( 'admin.php' ) ) );
		exit;
	}
}

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
	$options = get_option_with_defaults( 'azrcrv-spg' );

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

	$messages = '';
	if ( $id == '' ) {
		// is this a valid password form?

		$password_form = '<div class="azrcrv-spg-form">
			<div class="azrcrv-spg-error">
				' . esc_html__( 'Strong Password Generator form cannot be displayed; an id must be provided.', 'azrcrv-spg' ) . '
			</div>
		</div>';

	} else {

		if ( isset( $responses ) && is_array( $responses ) ) {
			// form has been submitted so responses to be processed.

			if ( isset( $responses['id'] ) && $id == $responses['id'] ) {
				/*
					load user inputs if form previously submitted.
				*/
				// password fields
				if ( isset( $responses['fields']['password-length'] ) ) {
					$password_length = sanitize_text_field( wp_unslash( $responses['fields']['password-length'] ) );
				}
				if ( isset( $responses['fields']['password-number'] ) ) {
					$password_number = sanitize_text_field( wp_unslash( $responses['fields']['password-number'] ) );
				}
				// include fields
				if ( isset( $responses['fields']['include-lowercase'] ) && $responses['fields']['include-lowercase'] == 1 ) {
					$include_lowercase = sanitize_text_field( wp_unslash( $responses['fields']['include-lowercase'] ) );
				}else{
					$include_lowercase = 0;
				}
				if ( isset( $responses['fields']['include-uppercase'] ) && $responses['fields']['include-uppercase'] == 1 ) {
					$include_uppercase = sanitize_text_field( wp_unslash( $responses['fields']['include-uppercase'] ) );
				}else{
					$include_uppercase = 0;
				}
				if ( isset( $responses['fields']['include-numeric'] ) && $responses['fields']['include-numeric'] == 1 ) {
					$include_numeric = sanitize_text_field( wp_unslash( $responses['fields']['include-numeric'] ) );
				}else{
					$include_numeric = 0;
				}
				if ( isset( $responses['fields']['include-symbols'] ) && $responses['fields']['include-symbols'] == 1 ) {
					$include_symbols = sanitize_text_field( wp_unslash( $responses['fields']['include-symbols'] ) );
				}else{
					$include_symbols = 0;
				}
				// valid fields
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
					// valid messages to display.

					foreach ( $responses['messages'] as $response ) {
						// failure.
						if ( $response == 'error-invalid-nonce' ) {
							$messages .= '<div class="azrcrv-spg-error">' . esc_html__( 'Password could not be generated.', 'azrcrv-spg' ) . '</div>';
						}
						if ( $response == 'error-no-includes' ) {
							$messages .= '<div class="azrcrv-spg-error">' . esc_html__( 'Password could not be generated as no character sets were included.', 'azrcrv-spg' ) . '</div>';
						}
						if ( $response == 'error-processing' ) {
							$messages .= '<div class="azrcrv-spg-error">' . esc_html__( 'There was an error processing your request. Wait a moment and try again.', 'azrcrv-spg' ) . '</div>';
						}
						// success.
						if ( $response == 'success-password-generated' ) {
							$messages .= '<div class="azrcrv-spg-success">' . esc_html__( 'Password generation completed successfully.', 'azrcrv-spg' ) . '</div>';
							$success   = true;
						}
					}
				}
			}
		}

		
		// set url for resirect
		$current_url = home_url( add_query_arg( array(), $wp->request ) );

		$length_of_password = '';
		if ( isset( $password_length ) ) {
			$length_of_password = '<tr>
				<th>' . esc_html( wp_unslash( $label_password_length ) ) . '</th>
				<td>
					<input name="password-length" type="number" id="password-length" min="' . $password_minimum_length . '" max="' . $password_maximum_length . '" step=1 value="' . esc_attr( wp_unslash( $password_length ) ) . '" class="small-text" /></td>
			</tr>';
		}
		$number_of_passwords = '';
		if ( isset( $password_number ) ) {
			$number_of_passwords = '<tr>
				<th>' . esc_html( wp_unslash( $label_password_number ) ) . '</th>
				<td>
					<input name="password-number" type="number" id="password-number" min=1 max="' . $password_maximum_number . '" step=1 value="' . esc_attr( wp_unslash( $password_number ) ) . '" class="small-text" /></td>
			</tr>';
		}

		$lowercase = '';
		if ( isset( $allow_lowercase ) && $allow_lowercase == 1 ) {
			$lowercase = '<tr>
				<th>' . esc_html( wp_unslash( $label_lowercase ) ) . '</th>
				<td>
					<input name="include-lowercase" type="checkbox" id="include-lowercase" value="1" ' . checked( '1', esc_attr( $include_lowercase ), false ) . ' /><label for="include-lowercase">' . esc_html__( 'Include lowercase characters', 'azrcrv-spg' ) . '</label>&nbsp;
					<input name="valid-lowercase" type="text" id="valid-lowercase" value="' . esc_attr( wp_unslash( $valid_lowercase ) ) . '" class="regular-text" /></td>
			</tr>';
		}
		$uppercase = '';
		if ( isset( $allow_uppercase ) && $allow_uppercase == 1 ) {
			$uppercase = '<tr>
				<th>' . esc_html( wp_unslash( $label_uppercase ) ) . '</th>
				<td>
					<input name="include-uppercase" type="checkbox" id="include-uppercase" value="1" ' . checked( '1', esc_attr( $include_uppercase ), false ) . ' /><label for="include-uppercase">' . esc_html__( 'Include uppercase characters', 'azrcrv-spg' ) . '</label>&nbsp;
					<input name="valid-uppercase" type="text" id="valid-uppercase" value="' . esc_html( wp_unslash( $valid_uppercase ) ) . '" class="regular-text" /></td>
			</tr>';
		}
		$numeric = '';
		if ( isset( $allow_numeric ) && $allow_numeric == 1 ) {
			$numeric = '<tr>
				<th>' . esc_html( wp_unslash( $label_numeric ) ) . '</th>
				<td>
					<input name="include-numeric" type="checkbox" id="include-numeric" value="1" ' . checked( '1', esc_attr( $include_numeric ), false ) . ' /><label for="include-numeric">' . esc_html__( 'Include numeric characters', 'azrcrv-spg' ) . '</label>&nbsp;
					<input name="valid-numeric" type="number" id="valid-numeric" value="' . esc_attr( wp_unslash( $valid_numeric ) ) . '" class="regular-text" /></td>
			</tr>';
		}
		$symbols = '';
		if ( isset( $allow_symbols ) && $allow_symbols == 1 ) {
			$symbols = '<tr>
				<th>' . esc_html( wp_unslash( $label_symbols ) ) . '</th>
				<td>
					<input name="include-symbols" type="checkbox" id="include-symbols" value="1" ' . checked( '1', esc_attr( $include_symbols ), false ) . ' /><label for="include-symbols">' . esc_html__( 'Include symbols', 'azrcrv-spg' ) . '</label>&nbsp;
					<input name="valid-symbols" type="text" id="valid-symbols" value="' . esc_attr( wp_unslash( $valid_symbols ) ) . '" class="regular-text" /></td>
			</tr>';
		}

		if ( strlen( $text_before ) > 0 ) {
			$text_before = '<p class="azrcrv-spg">' . esc_html( $text_before ) . '</p>';
		}
		if ( strlen( $text_after ) > 0 ) {
			$text_after = '<p class="azrcrv-spg">' . esc_html( $text_after ) . '</p>';
		}

		$passwords = '';
		if ( isset( $responses ) && is_array( $responses ) ) {
			if ( count( $responses['passwords'] ) > 0 ) {
				if ( count( $responses['passwords'] ) == 1 ) {
					$password_lede = '<p /><table class="azrcrv-spg-password"><thead><tr><th>' . esc_html__( 'Your password is: ', 'azrcrv-spg' ) . '</th></tr></thead>';
				} else {
					$password_lede = '<p /><table class="azrcrv-spg-password"><thead><tr><th>' . esc_html__( 'Your passwords are: ', 'azrcrv-spg' ) . '</th></tr></thead>';
				}
				foreach ( $responses['passwords'] as $password ) {
					// $passwords = implode( ',', $responses['passwords'] );
					$passwords .= '<tr><td>' . $password . '</td></tr>';
				}
				$passwords = $password_lede . '<tbody>' . $passwords . '<tbody></table>';
			}
		}

		// build form.
		$password_form = '<div class="azrcrv-spg-form">
		
			' . $messages . '
		
			<form method="post" id="azrcrv-password-form" action="' . esc_attr( $current_url ) . '">
			
				<fieldset>
					' . $text_before . '
					<input name="password-form-id" type="hidden" value="' . $id . '" />' .
						wp_nonce_field( 'azrcrv-spg-password-form', 'azrcrv-spg-password-form-nonce', true, false )
					. '<table class="azrcrv-spg"><body>
						' . $number_of_passwords . '
						' . $length_of_password . '
						' . $lowercase . '
						' . $uppercase . '
						' . $numeric . '
						' . $symbols . '
					</tbody></table>
					' . $text_after . '
				</fieldset>
				
				<input type="submit" name="submit" value="' . esc_html__( 'Generate', 'azrcrv-spg' ) . '" class="button-primary"/>
				
			</form>
			
			' . $passwords . '
				
		</div>';
	}
	if ( isset( $_POST['password-form-id'] ) ){
		$password_form .= '<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>';
	}

	return $password_form;

}


/**
 * Process password form after submit.
 *
 * @since 1.0.0
 */
function process_form() {
	// was a password form id included in POST?
	if ( ! isset( $_POST['password-form-id'] ) ) {
		return;
	}

	// get options
	$options = get_option_with_defaults( 'azrcrv-spg' );

	// create responses array.
	$responses = array(
		'id'        => sanitize_text_field( wp_unslash( $_POST['password-form-id'] ) ),
		'messages'  => array(),
		'passwords' => array(),
	);

	// Check that the nonce was set and valid.
	if ( ! isset( $_POST['azrcrv-spg-password-form-nonce'] ) || ! wp_verify_nonce( $_POST['azrcrv-spg-password-form-nonce'], 'azrcrv-spg-password-form' ) ) {
		$responses['messages'][] = 'error-invalid-nonce';
	}

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
	if ( isset( $_POST['include-lowercase'] ) ) {
		$responses['fields']['include-lowercase'] = (int) sanitize_text_field( wp_unslash( $_POST['include-lowercase'] ) );
	} else {
		$responses['fields']['include-lowercase'] = 0;
	}
	if ( isset( $_POST['valid-lowercase'] ) ) {
		$responses['fields']['valid-lowercase'] = sanitize_text_field( wp_unslash( $_POST['valid-lowercase'] ) );
	} else {
		$responses['fields']['valid-lowercase'] = '';
	}
	if ( isset( $_POST['include-uppercase'] ) ) {
		$responses['fields']['include-uppercase'] = (int) sanitize_text_field( wp_unslash( $_POST['include-uppercase'] ) );
	} else {
		$responses['fields']['include-uppercase'] = 0;
	}
	if ( isset( $_POST['valid-uppercase'] ) ) {
		$responses['fields']['valid-uppercase'] = sanitize_text_field( wp_unslash( $_POST['valid-uppercase'] ) );
	} else {
		$responses['fields']['valid-uppercase'] = '';
	}
	if ( isset( $_POST['include-numeric'] ) ) {
		$responses['fields']['include-numeric'] = (int) sanitize_text_field( wp_unslash( $_POST['include-numeric'] ) );
	} else {
		$responses['fields']['include-numeric'] = 0;
	}
	if ( isset( $_POST['valid-numeric'] ) ) {
		$responses['fields']['valid-numeric'] = sanitize_text_field( wp_unslash( $_POST['valid-numeric'] ) );
	} else {
		$responses['fields']['valid-numeric'] = '';
	}
	if ( isset( $_POST['include-symbols'] ) ) {
		$responses['fields']['include-symbols'] = (int) sanitize_text_field( wp_unslash( $_POST['include-symbols'] ) );
	} else {
		$responses['fields']['include-symbols'] = 0;
	}
	if ( isset( $_POST['valid-symbols'] ) ) {
		$responses['fields']['valid-symbols'] = sanitize_text_field( wp_unslash( $_POST['valid-symbols'] ) );
	} else {
		$responses['fields']['valid-symbols'] = '';
	}

	if ( count( $responses['messages'] ) == 0 ) {

		// process if we have no errors in responses.
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

		// send email.
		$response = true;

		// check response from wp_mail and set flag.
		if ( $response == true ) {
			$responses['messages'][] = 'success-password-generated';
		} else {
			$responses['messages'][] = 'error-generation-failed';
		}
	}

	return $responses;

}

/**
 * Generate password.
 *
 * @since 1.0.0
 */
function generate_password( $number_of_passwords, $length_of_passwords, $allow_lowercase, $valid_lowercase, $allow_uppercase, $valid_uppercase, $allow_numeric, $valid_numeric, $allow_symbols, $valid_symbols ) {

	$options = get_option_with_defaults( 'azrcrv-spg' );

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

	$passwords = array();

	for ( $password_loop = 0; $password_loop < $number_of_passwords; $password_loop++ ) {
		$password = '';
		for ( $length_loop = 0; $length_loop < $length_of_passwords; $length_loop++ ) {
			//mb_substr used to allow for £
			$character = mb_substr( $usable_characters, wp_rand( 1, strlen( $usable_characters ) ) - 1, 1 );
			$password .= $character;
		}
		$passwords[] = $password;
	}
	update_option( 'azrcrv-spg-g', $passwords );

	return $passwords;

}
