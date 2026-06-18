<?php
/**
 * Instructions tab content.
 */

/**
 * Declare the Namespace.
 */
namespace azurecurve\StrongPasswordGenerator;

$tab_instructions_label = esc_html__( 'Instructions', 'azrcrv-spg' );

ob_start();
?>
<table class="form-table azrcrv-settings">

	<tr>
		<th scope="row" colspan="2" class="azrcrv-settings-section-heading">
			<h2 class="azrcrv-settings-section-heading"><?php esc_html_e( 'Strong Password Generator Shortcode', 'azrcrv-spg' ); ?></h2>
		</th>
	</tr>

	<tr>
		<td scope="row" colspan="2">
			<p><?php esc_html_e( 'Use the following shortcode to display the strong password generator on any page or post:', 'azrcrv-spg' ); ?></p>
			<p><code>[strong-password-generator id="my-password-generator"]</code></p>
		</td>
	</tr>

	<tr>
		<th scope="row">
			<?php esc_html_e( 'Attribute', 'azrcrv-spg' ); ?>
		</th>
		<th scope="row">
			<?php esc_html_e( 'Description', 'azrcrv-spg' ); ?>
		</th>
	</tr>

	<tr>
		<td><code>id</code></td>
		<td><?php esc_html_e( 'Required. A unique identifier for the form on the page.', 'azrcrv-spg' ); ?></td>
	</tr>

	<tr>
		<td><code>password-length</code></td>
		<td><?php esc_html_e( 'Default password length (overrides plugin setting).', 'azrcrv-spg' ); ?></td>
	</tr>

	<tr>
		<td><code>password-number</code></td>
		<td><?php esc_html_e( 'Default number of passwords to generate (overrides plugin setting).', 'azrcrv-spg' ); ?></td>
	</tr>

	<tr>
		<td><code>text-before</code></td>
		<td><?php esc_html_e( 'Introductory text displayed above the form (overrides plugin setting).', 'azrcrv-spg' ); ?></td>
	</tr>

	<tr>
		<td><code>text-after</code></td>
		<td><?php esc_html_e( 'Text displayed below the form (overrides plugin setting).', 'azrcrv-spg' ); ?></td>
	</tr>

	<tr>
		<td colspan="2">
			<p><?php esc_html_e( 'Example with attributes:', 'azrcrv-spg' ); ?></p>
			<p><code>[strong-password-generator id="my-password-generator" password-length="24" password-number="5" text-before="Generate a secure password below."]</code></p>
		</td>
	</tr>

	<tr>
		<th scope="row" colspan="2" class="azrcrv-settings-section-heading">
			<h2 class="azrcrv-settings-section-heading"><?php esc_html_e( 'htpasswd Generator Shortcode', 'azrcrv-spg' ); ?></h2>
		</th>
	</tr>

	<tr>
		<td scope="row" colspan="2">
			<p><?php esc_html_e( 'Use the following shortcode to display the htpasswd entry generator on any page or post:', 'azrcrv-spg' ); ?></p>
			<p><code>[htpasswd-generator id="my-htpasswd"]</code></p>
			<p><?php esc_html_e( 'The generator prompts for a username, password and encryption method, then produces a ready-to-use htpasswd line (username:hash) which can be copied to the clipboard.', 'azrcrv-spg' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row">
			<?php esc_html_e( 'Attribute', 'azrcrv-spg' ); ?>
		</th>
		<th scope="row">
			<?php esc_html_e( 'Description', 'azrcrv-spg' ); ?>
		</th>
	</tr>

	<tr>
		<td><code>id</code></td>
		<td><?php esc_html_e( 'Required. A unique identifier for the form on the page.', 'azrcrv-spg' ); ?></td>
	</tr>

	<tr>
		<td><code>default-encryption</code></td>
		<td><?php esc_html_e( 'Pre-selected encryption method. Accepted values: bcrypt (default), apr1, sha1.', 'azrcrv-spg' ); ?></td>
	</tr>

	<tr>
		<td><code>text-before</code></td>
		<td><?php esc_html_e( 'Introductory text displayed above the form (overrides plugin setting).', 'azrcrv-spg' ); ?></td>
	</tr>

	<tr>
		<td><code>text-after</code></td>
		<td><?php esc_html_e( 'Text displayed below the form (overrides plugin setting).', 'azrcrv-spg' ); ?></td>
	</tr>

	<tr>
		<td><code>label-username</code></td>
		<td><?php esc_html_e( 'Label for the username field (overrides plugin setting).', 'azrcrv-spg' ); ?></td>
	</tr>

	<tr>
		<td><code>label-password</code></td>
		<td><?php esc_html_e( 'Label for the password field (overrides plugin setting).', 'azrcrv-spg' ); ?></td>
	</tr>

	<tr>
		<td><code>label-encryption</code></td>
		<td><?php esc_html_e( 'Label for the encryption select field (overrides plugin setting).', 'azrcrv-spg' ); ?></td>
	</tr>

	<tr>
		<td colspan="2">
			<p><?php esc_html_e( 'Supported encryption methods:', 'azrcrv-spg' ); ?></p>
			<ul style="list-style: disc; margin-left: 1.5em;">
				<li><strong>bcrypt</strong> &mdash; <?php esc_html_e( 'Recommended. Supported by Apache 2.4+ and nginx. Uses PHP\'s native password_hash().', 'azrcrv-spg' ); ?></li>
				<li><strong>apr1</strong> &mdash; <?php esc_html_e( 'Apache\'s MD5-APR1 format ($apr1$). Widely compatible but MD5-based; use bcrypt for new deployments.', 'azrcrv-spg' ); ?></li>
				<li><strong>sha1</strong> &mdash; <?php esc_html_e( 'SHA-1 ({SHA} prefix). Unsalted; included for legacy compatibility only.', 'azrcrv-spg' ); ?></li>
			</ul>
		</td>
	</tr>

	<tr>
		<td colspan="2">
			<p><?php esc_html_e( 'Example with attributes:', 'azrcrv-spg' ); ?></p>
			<p><code>[htpasswd-generator id="my-htpasswd" default-encryption="apr1" text-before="Generate an htpasswd entry below."]</code></p>
		</td>
	</tr>

</table>
<?php
$tab_instructions = ob_get_clean();
