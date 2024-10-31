<?php
/*
Plugin Name:       Registration form eu
Plugin URI:        https://wordpress.org/plugins/registration-form-eu/
Description:       Plugin user signup and customize welcome email
Version:           1.0.3
Author:            Ilario Tresoldi
Author URI:        http://www.webcreates.eu
Textdomain:        rfe
Domain Path:       /language
License:           GPL-2.0+
License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
*/

/**
 * Registration form eu
 * Copyright (C) 2017 Ilario Tresoldi. All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Contact the author at ilario.tresoldi@gmail.com
 */

/**
 * Start the plugin
 */
function rfe_init() {
	$domain = 'rfe';
	$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
	$path   = plugins_url('registration-form-eu/language/'.$domain.'-'.$locale.'.mo');
	$loaded = load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/language/' );
	if ( !$loaded )
	{
		$path   = plugins_url('registration-form-eu/language/'.$domain.'-en_US.mo');
		$loaded = load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/language/' );
	}

	register_setting('registration-form-eu', 'registration_form_eu_options', 'rfe_registration_form_eu_options_sanitize');
	register_setting('registration-form-eu-cfg', 'registration_form_eu_options_cfg', 'rfe_registration_form_eu_options_cfg_sanitize');

	if ( !is_admin() ) {
		require_once( 'classes/class.rfe.php' );
	}
}
add_action( 'plugins_loaded', 'rfe_init' );

/**
 * Add in option menu
 */
function add_rfe( $methods ) {
	add_menu_page('Registration form eu', 'Registration form eu', 'manage_options', 'registration-form-eu', 'rfe_registration_form_eu_setup', plugins_url('registration-form-eu/images/signup.png'));
	add_submenu_page('registration-form-eu', 'Setup', 'Setup', 'manage_options', 'rfe_registration_form_eu_setup_opt', 'rfe_registration_form_eu_setup_opt');
}
add_filter( 'admin_menu', 'add_rfe' );

function rfe_registration_form_eu_options_sanitize($input){
    $input['rfe_enable'] = $input['rfe_enable'];
    return $input;
}

function rfe_registration_form_eu_options_cfg_sanitize($input){
    $input['rfe_style_field'] = sanitize_text_field($input['rfe_style_field']);
    $input['rfe_style_button'] = sanitize_text_field($input['rfe_style_button']);
    $input['rfe_subject'] = sanitize_text_field($input['rfe_subject']);
    $input['rfe_message'] = sanitize_text_field($input['rfe_message']);
    return $input;
}

function rfe_registration_form_eu_setup()
{
?>
	<h1>Registration form eu - <?php _e( 'Setup', 'rfe' ); ?></h1>
	<form method="post" action="options.php">
		<?php settings_fields( 'registration-form-eu' ); ?>
		<?php $registration_form_eu_options = get_option('registration_form_eu_options'); ?>
		<br>
		<b><?php _e( 'Plugin activation', 'rfe' ); ?></b>
		<br>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e( 'Active', 'rfe' ); ?>:</th>
				<td><input name="registration_form_eu_options[rfe_enable]" type="checkbox" value="1" <?php checked($registration_form_eu_options['rfe_enable']);?> /></td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" name="senddata" class="button-primary" value="<?php _e( 'Save changes', 'rfe' ) ?>" />
		</p>
	</form>
<?php
}

function rfe_registration_form_eu_setup_opt()
{
?>
	<h1>Registration form eu - <?php _e( 'Setup', 'rfe' ); ?></h1>
	<br>
	<form method="post" action="options.php">
		<?php settings_fields( 'registration-form-eu-cfg' ); ?>
		<?php $registration_form_eu_options_cfg = get_option('registration_form_eu_options_cfg'); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e( 'Field style', 'rfe' ); ?></th>
				<td><textarea cols="50" rows="5" name="registration_form_eu_options_cfg[rfe_style_field]"><?php echo $registration_form_eu_options_cfg['rfe_style_field']; ?></textarea></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Button style', 'rfe' ); ?></th>
				<td><textarea cols="50" rows="5" name="registration_form_eu_options_cfg[rfe_style_button]"><?php echo $registration_form_eu_options_cfg['rfe_style_button']; ?></textarea></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Subject', 'rfe' ); ?></th>
				<td><textarea cols="50" rows="5" name="registration_form_eu_options_cfg[rfe_subject]"><?php echo $registration_form_eu_options_cfg['rfe_subject']; ?></textarea></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Welcome message', 'rfe' ); ?></th>
				<td><textarea cols="50" rows="15" name="registration_form_eu_options_cfg[rfe_message]"><?php echo $registration_form_eu_options_cfg['rfe_message']; ?></textarea></td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" name="senddata" class="button-primary" value="<?php _e( 'Save changes', 'rfe' ) ?>" />
		</p>
		<br />
		<b>Shortcode: [registration-form-eu]</b>
	</form>
<?php
}
?>
