<?php
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

$domain = 'rfe';
$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
$path   = plugins_url('registration-form-eu/language/'.$domain.'-'.$locale.'.mo');
$loaded = load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/language/' );
if ( !$loaded )
{
	$path   = plugins_url('registration-form-eu/language/'.$domain.'-en_US.mo');
	$loaded = load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/language/' );
}

if (isset($_POST['senddata'])) {
	global $wpdb;

	$url = $_POST["url"];
    $user_email = sanitize_user( wp_slash($_POST["email"]), true );
	$user_login = sanitize_user( wp_slash($_POST["username"]), true );

	$error_message = "";
	if (!email_exists($user_email)) {
		$error_message = str_replace(' ', '%20', __( 'Registration complete, verify our email', 'rfe' ));
	    $userdata = compact('user_login', 'user_email');
	    $user_id = wp_insert_user($userdata);
	    $user = get_userdata($user_id);
	    $registration_form_eu_options_cfg = get_option('registration_form_eu_options_cfg');

		// Genera una nuova password
		$key = wp_generate_password( 20, false );
		$hash = wp_hash_password( $key );

		$hashed = time() . ':' . $hash;
		$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );

		$message  = __( 'New user registration on your site:' ) . "\r\n\r\n";
		$message .= sprintf( __( 'Username: %s' ), $user->user_login ) . "\r\n\r\n";
		$message .= sprintf( __( 'Email: %s' ), $user->user_email ) . "\r\n";

		wp_mail( get_option( 'admin_email' ), __( 'New User Registration' ), $message );

		$message  = $registration_form_eu_options_cfg['rfe_message'] . "\r\n\r\n";
		$message .= sprintf(__('Username: %s', 'rfe'), $user->user_login) . "\r\n\r\n";
		$message .= __('To set your password, visit the following address:', 'rfe') . "\r\n\r\n";
		$message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login') . ">\r\n\r\n";

		wp_mail($user->user_email, $registration_form_eu_options_cfg[rfe_subject], $message);
	} else {
		$error_message = str_replace(' ', '%20', __( 'Email already exist', 'rfe' ));
	}
	$url .= "?status=".$error_message;
	wp_redirect( $url );
  	exit;
}

add_shortcode( 'registration-form-eu', 'registration_form_shortcode' );

function registration_form_shortcode()
{
	$registration_form_eu_options = get_option('registration_form_eu_options');
	$registration_form_eu_options_cfg = get_option('registration_form_eu_options_cfg');
	if ( $registration_form_eu_options['rfe_enable'] ) {
?>
<div class="tabs-content-1" style="clear: both;">
<?php
	$error_message = $_GET["status"];
?>
	<form method="post" action="">
		<input type="hidden" name="url" value="<?php echo "http://".$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]; ?>">
		<div class="form-group-1">
			<?php echo $error_message; ?>
		</div>
		<div class="form-group-1">
			<input name="email" placeholder="<?php _e( 'Email *', 'rfe' ); ?>" class="form-control" type="text" required="required" style="<?php echo $registration_form_eu_options_cfg['rfe_style_field']; ?>">
		</div>
		<div class="form-group-1">
			<input name="username" placeholder="<?php _e( 'Username *', 'rfe' ); ?>" class="form-control" type="text" required="required" style="<?php echo $registration_form_eu_options_cfg['rfe_style_field']; ?>">
		</div>
		<div class="form-group-1">
			<button name="senddata" class="train-button" style="<?php echo $registration_form_eu_options_cfg['rfe_style_button']; ?>"><?php _e( 'Send', 'rfe' ); ?></button>
		</div>
	</form>
</div>
<?php
	}
}
?>
