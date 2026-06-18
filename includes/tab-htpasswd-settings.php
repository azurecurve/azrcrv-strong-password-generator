<?php
/**
 * htpasswd Options tab content.
 */

/**
 * Declare the Namespace.
 */
namespace azurecurve\StrongPasswordGenerator;

$tab_htpasswd_label = esc_html__( 'htpasswd Options', 'azrcrv-spg' );

ob_start();
?>
<table class="form-table azrcrv-settings">

	<tr>
		<th scope="row" colspan="2" class="azrcrv-settings-section-heading">
			<h2 class="azrcrv-settings-section-heading"><?php esc_html_e( 'Defaults', 'azrcrv-spg' ); ?></h2>
		</th>
	</tr>

	<tr>
		<th scope="row">
			<label for="htpasswd-default-encryption"><?php esc_html_e( 'Default Encryption', 'azrcrv-spg' ); ?></label>
		</th>
		<td>
			<select name="htpasswd-default-encryption" id="htpasswd-default-encryption">
				<option value="bcrypt" <?php selected( $options['htpasswd']['default-encryption'], 'bcrypt' ); ?>><?php esc_html_e( 'bcrypt (recommended)', 'azrcrv-spg' ); ?></option>
				<option value="apr1" <?php selected( $options['htpasswd']['default-encryption'], 'apr1' ); ?>><?php esc_html_e( 'MD5-APR1', 'azrcrv-spg' ); ?></option>
				<option value="sha1" <?php selected( $options['htpasswd']['default-encryption'], 'sha1' ); ?>><?php esc_html_e( 'SHA-1', 'azrcrv-spg' ); ?></option>
			</select>
			<p class="description"><?php esc_html_e( 'bcrypt is the most secure option and is recommended for all new deployments.', 'azrcrv-spg' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row" colspan="2" class="azrcrv-settings-section-heading">
			<h2 class="azrcrv-settings-section-heading"><?php esc_html_e( 'Labels', 'azrcrv-spg' ); ?></h2>
		</th>
	</tr>

	<tr>
		<th scope="row">
			<label for="htpasswd-label-username"><?php esc_html_e( 'Username', 'azrcrv-spg' ); ?></label>
		</th>
		<td>
			<input name="htpasswd-label-username" type="text" id="htpasswd-label-username" value="<?php echo esc_attr( $options['htpasswd']['labels']['username'] ); ?>" class="regular-text" />
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="htpasswd-label-password"><?php esc_html_e( 'Password', 'azrcrv-spg' ); ?></label>
		</th>
		<td>
			<input name="htpasswd-label-password" type="text" id="htpasswd-label-password" value="<?php echo esc_attr( $options['htpasswd']['labels']['password'] ); ?>" class="regular-text" />
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="htpasswd-label-encryption"><?php esc_html_e( 'Encryption', 'azrcrv-spg' ); ?></label>
		</th>
		<td>
			<input name="htpasswd-label-encryption" type="text" id="htpasswd-label-encryption" value="<?php echo esc_attr( $options['htpasswd']['labels']['encryption'] ); ?>" class="regular-text" />
		</td>
	</tr>

	<tr>
		<th scope="row" colspan="2" class="azrcrv-settings-section-heading">
			<h2 class="azrcrv-settings-section-heading"><?php esc_html_e( 'Text', 'azrcrv-spg' ); ?></h2>
		</th>
	</tr>

	<tr>
		<th scope="row">
			<label for="htpasswd-text-before"><?php esc_html_e( 'Before Form', 'azrcrv-spg' ); ?></label>
		</th>
		<td>
			<textarea name="htpasswd-text-before" rows="5" cols="50" id="htpasswd-text-before" class="large-text"><?php echo esc_textarea( $options['htpasswd']['text']['before'] ); ?></textarea>
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="htpasswd-text-after"><?php esc_html_e( 'After Form', 'azrcrv-spg' ); ?></label>
		</th>
		<td>
			<textarea name="htpasswd-text-after" rows="5" cols="50" id="htpasswd-text-after" class="large-text"><?php echo esc_textarea( $options['htpasswd']['text']['after'] ); ?></textarea>
		</td>
	</tr>

</table>
<?php
$tab_htpasswd = ob_get_clean();
