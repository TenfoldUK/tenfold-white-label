<?php
/******************************************************************
Plugin Name:       Tenfold White Label
Plugin URI:        https://tenfold.co.uk
Description:       This plugin customises WordPress for Tenfold clients, adding features, cleaning up unneccesary things and generally improving WordPress. Make sure the plugin 'GitHub Updater' is activated to recieve updates to this plugin.
Author:            Tim Rye
Author URI:        https://tenfold.co.uk/tim
Version:           1.0.3
GitHub Plugin URI: TenfoldMedia/tenfold-white-label
GitHub Branch:     master
******************************************************************/


/*********************
CUSTOMISE LOGIN AREA
*********************/

// adding our logo and changing the button colour
function tf_custom_login_logo() { ?>
    <style type="text/css">
		.login h1 a {
			width: 320px;
			height: 146px;
			background: url(<?php echo plugins_url('login-logo.png', __FILE__) ?>) no-repeat top center;
		}
		#wp-submit {
			background: #d72020;
			border-color: #be0707;
			-webkit-box-shadow: 0 1px 0 #8e0404;
			box-shadow: 0 1px 0 #8e0404;
			text-shadow: 0 -1px 1px #8e0404,1px 0 1px #8e0404,0 1px 1px #8e0404,-1px 0 1px #8e0404;
		}
	</style>
<?php }
add_action('login_head', 'tf_custom_login_logo');

// changing the logo link from wordpress.org to our site
function tf_login_url() { return 'https://tenfold.co.uk'; }
add_filter('login_headerurl', 'tf_login_url');

// changing the alt text on the logo to show our name
function tf_login_title() { return 'Website by Tenfold'; }
add_filter('login_headertitle', 'tf_login_title');

// set 'remember me' to checked
function tf_login_check_remember_me_script() { echo "<script>document.getElementById('rememberme').checked = true;</script>"; }
add_filter('login_footer', 'tf_login_check_remember_me_script');


/*********************
CUSTOMISE ADMIN AREA
*********************/

// Custom Backend Footer
function tf_custom_admin_footer() { echo '<span id="footer-thankyou">Website by <a href="https://tenfold.co.uk" target="_blank">Tenfold</a></span>.'; }
add_filter('admin_footer_text', 'tf_custom_admin_footer');

// Remove the ' - Wordpress' from the page title
function tf_admin_title($admin_title, $title) { return $title.'|'.$admin_title; }
add_filter('admin_title', 'tf_admin_title', 10, 2);

// Remove the WordPress logo from the admin bar
function tf_remove_admin_bar_wp_logo() { global $wp_admin_bar; $wp_admin_bar->remove_menu('wp-logo'); }
add_action('wp_before_admin_bar_render', 'tf_remove_admin_bar_wp_logo', 0);

function tf_change_howdy($wp_admin_bar) {
	$user_id = get_current_user_id();
	$current_user = wp_get_current_user();
	$profile_url = get_edit_profile_url($user_id);

	if ($user_id != 0) {
		$avatar = get_avatar($user_id, 28);
		$howdy = sprintf(__('Welcome, %1$s'), $current_user->display_name);
		$class = empty($avatar) ? '' : 'with-avatar';

		$wp_admin_bar->add_menu(array(
			'id' => 'my-account',
			'parent' => 'top-secondary',
			'title' => $howdy . $avatar,
			'href' => $profile_url,
			'meta' => array('class' => $class),
		));
	}
}
add_action('admin_bar_menu', 'tf_change_howdy', 11);
