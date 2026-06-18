<?php
/**
 * Password Options and Labels & Text tab content.
 */

/**
 * Declare the Namespace.
 */
namespace azurecurve\StrongPasswordGenerator;

$tab_settings_label = esc_html__( 'Password Options', 'azrcrv-spg' );

ob_start();
?>
<table class="form-table azrcrv-settings">

	<tr>
		<th scope="row" colspan="2" class="azrcrv-settings-section-heading">
			<h2 class="azrcrv-settings-section-heading"><?php esc_html_e( 'Passwords', 'azrcrv-spg' ); ?></h2>
		</th>
	</tr>

	<tr>
		<th scope="row">
			<label for="password-length"><?php esc_html_e( 'Default Length', 'azrcrv-spg' ); ?></label>
		</th>
		<td>
			<input name="password-length" type="number" min="5" max="256" step="1" id="password-length" value="<?php echo esc_attr( $options['password']['length'] ); ?>" class="small-text" />
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="password-minimum-length"><?php esc_html_e( 'Minimum Length', 'azrcrv-spg' ); ?></label>
		</th>
		<td>
			<input name="password-minimum-length" type="number" min="5" step="1" id="password-minimum-length" value="<?php echo esc_attr( $options['password']['minimum-length'] ); ?>" class="small-text" />
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="password-maximum-length"><?php esc_html_e( 'Maximum Length', 'azrcrv-spg' ); ?></label>
		</th>
		<td>
			<input name="password-maximum-length" type="number" min="6" step="1" id="password-maximum-length" value="<?php echo esc_attr( $options['password']['maximum-length'] ); ?>" class="small-text" />
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="password-number"><?php esc_html_e( 'Number of Passwords', 'azrcrv-spg' ); ?></label>
		</th>
		<td>
			<input name="password-number" type="number" min="1" step="1" id="password-number" value="<?php echo esc_attr( $options['password']['number'] ); ?>" class="small-text" />
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="password-maximum-number"><?php esc_html_e( 'Maximum Number of Passwords', 'azrcrv-spg' ); ?></label>
		</th>
		<td>
			<input name="password-maximum-number" type="number" min="1" step="1" id="password-maximum-number" value="<?php echo esc_attr( $options['password']['maximum-number'] ); ?>" class="small-text" />
		</td>
	</tr>

	<tr>
		<th scope="row" colspan="2" class="azrcrv-settings-section-heading">
			<h2 class="azrcrv-settings-section-heading"><?php esc_html_e( 'Allowed', 'azrcrv-spg' ); ?></h2>
		</th>
	</tr>

	<tr>
		<th scope="row">
			<?php esc_html_e( 'Numeric', 'azrcrv-spg' ); ?>
		</th>
		<td>
			<input name="allowed-numeric" type="checkbox" id="allowed-numeric" value="1" <?php checked( '1', $options['allowed']['numeric'] ); ?> />&nbsp;
			<input name="valid-numeric" type="text" pattern="[0-9]*" id="valid-numeric" value="<?php echo esc_attr( $options['valid']['numeric'] ); ?>" class="regular-text" />
		</td>
	</tr>

	<tr>
		<th scope="row">
			<?php esc_html_e( 'Uppercase', 'azrcrv-spg' ); ?>
		</th>
		<td>
			<input name="allowed-uppercase" type="checkbox" id="allowed-uppercase" value="1" <?php checked( '1', $options['allowed']['uppercase'] ); ?> />&nbsp;
			<input name="valid-uppercase" type="text" id="valid-uppercase" value="<?php echo esc_attr( $options['valid']['uppercase'] ); ?>" class="regular-text" />
		</td>
	</tr>

	<tr>
		<th scope="row">
			<?php esc_html_e( 'Lowercase', 'azrcrv-spg' ); ?>
		</th>
		<td>
			<input name="allowed-lowercase" type="checkbox" id="allowed-lowercase" value="1" <?php checked( '1', $options['allowed']['lowercase'] ); ?> />&nbsp;
			<input name="valid-lowercase" type="text" id="valid-lowercase" value="<?php echo esc_attr( $options['valid']['lowercase'] ); ?>" class="regular-text" />
		</td>
	</tr>

	<tr>
		<th scope="row">
			<?php esc_html_e( 'Symbols', 'azrcrv-spg' ); ?>
		</th>
		<td>
			<input name="allowed-symbols" type="checkbox" id="allowed-symbols" value="1" <?php checked( '1', $options['allowed']['symbols'] ); ?> />&nbsp;
			<input name="valid-symbols" type="text" id="valid-symbols" value="<?php echo esc_attr( $options['valid']['symbols'] ); ?>" class="regular-text" />
		</td>
	</tr>

	<tr>
		<th scope="row" colspan="2" class="azrcrv-settings-section-heading">
			<h2 class="azrcrv-settings-section-heading"><?php esc_html_e( 'Labels', 'azrcrv-spg' ); ?></h2>
		</th>
	</tr>

	<tr>
		<th scope="row">
			<label for="label-password-length"><?php esc_html_e( 'Password Length', 'azrcrv-spg' ); ?></label>
		</th>
		<td>
			<input name="label-password-length" type="text" id="label-password-length" value="<?php echo esc_attr( $options['labels']['password-length'] ); ?>" class="regular-text" />
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="label-password-number"><?php esc_html_e( 'Number of Passwords', 'azrcrv-spg' ); ?></label>
		</th>
		<td>
			<input name="label-password-number" type="text" id="label-password-number" value="<?php echo esc_attr( $options['labels']['password-number'] ); ?>" class="regular-text" />
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="label-lowercase"><?php esc_html_e( 'Lowercase', 'azrcrv-spg' ); ?></label>
		</th>
		<td>
			<input name="label-lowercase" type="text" id="label-lowercase" value="<?php echo esc_attr( $options['labels']['lowercase'] ); ?>" class="regular-text" />
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="label-uppercase"><?php esc_html_e( 'Uppercase', 'azrcrv-spg' ); ?></label>
		</th>
		<td>
			<input name="label-uppercase" type="text" id="label-uppercase" value="<?php echo esc_attr( $options['labels']['uppercase'] ); ?>" class="regular-text" />
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="label-numeric"><?php esc_html_e( 'Numeric', 'azrcrv-spg' ); ?></label>
		</th>
		<td>
			<input name="label-numeric" type="text" id="label-numeric" value="<?php echo esc_attr( $options['labels']['numeric'] ); ?>" class="regular-text" />
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="label-symbols"><?php esc_html_e( 'Symbols', 'azrcrv-spg' ); ?></label>
		</th>
		<td>
			<input name="label-symbols" type="text" id="label-symbols" value="<?php echo esc_attr( $options['labels']['symbols'] ); ?>" class="regular-text" />
		</td>
	</tr>

	<tr>
		<th scope="row" colspan="2" class="azrcrv-settings-section-heading">
			<h2 class="azrcrv-settings-section-heading"><?php esc_html_e( 'Text', 'azrcrv-spg' ); ?></h2>
		</th>
	</tr>

	<tr>
		<th scope="row">
			<label for="text-before"><?php esc_html_e( 'Before Form', 'azrcrv-spg' ); ?></label>
		</th>
		<td>
			<textarea name="text-before" rows="5" cols="50" id="text-before" class="large-text"><?php echo esc_textarea( $options['text']['before'] ); ?></textarea>
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="text-after"><?php esc_html_e( 'After Form', 'azrcrv-spg' ); ?></label>
		</th>
		<td>
			<textarea name="text-after" rows="5" cols="50" id="text-after" class="large-text"><?php echo esc_textarea( $options['text']['after'] ); ?></textarea>
		</td>
	</tr>

</table>
<?php
$tab_settings = ob_get_clean();
