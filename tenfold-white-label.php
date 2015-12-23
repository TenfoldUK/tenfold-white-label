<?php
/******************************************************************
Plugin Name:       Tenfold White Label
Plugin URI:        http://tenfold.media
Description:       This plugin customises WordPress for Tenfold Media clients, adding features, cleaning up unneccesary things and generally improving WordPress. Make sure the plugin 'GitHub Updater' is activated to recieve updates to this plugin.
Author:            Tim Rye
Author URI:        http://tenfold.media/tim
Version:           1.0.0
GitHub Plugin URI: TenfoldMedia/tenfold-white-label
GitHub Branch:     master
******************************************************************/


/*********************
FRONT-END HELPERS
*********************/

function tf_the_footer_credit($context_pre = 'Web Design by', $link_title = 'Web Design by Tenfold Media', $context_post = '', $chars = 5) {
	$url = 'http://tenfold.media/referral/?ref='.substr(preg_replace('#^www\.(.+\.)#i', '$1', $_SERVER['HTTP_HOST']), 0, $chars);
	echo ($context_pre ? $context_pre . ' ' : '') . '<a href="' . $url . '" rel="nofollow" target="_blank" title="' . $link_title . '">Tenfold Media</a>' . ($context_post ? ' ' . $context_post : '');
}


/*********************
CUSTOMISE LOGIN AREA
*********************/

// calling your own login css so you can style it
function tf_custom_login_logo() {
    echo '<style type="text/css">';
	echo '.login h1 a {
		background: url(' . plugins_url('login-logo.png', __FILE__) . ') no-repeat top center;
		width: 320px;
		height: 146px;
		text-indent: -9999px;
		overflow: hidden;
		padding-bottom: 15px;
		display: block;
	}';
	echo '#wp-submit {
		background: #d72020;
		border-color: #be0707;
		-webkit-box-shadow: none;
		-moz-box-shadow: none;
		box-shadow: none;
	}';
	echo '</style>';
}
add_action('login_head', 'tf_custom_login_logo');

// changing the logo link from wordpress.org to your site
function tf_login_url() { return 'http://www.tenfold.media'; }
add_filter('login_headerurl', 'tf_login_url');

// changing the alt text on the logo to show your site name
function tf_login_title() { return 'Website by Tenfold Media'; }
add_filter('login_headertitle', 'tf_login_title');

// set 'remember me' to checked
function tf_login_check_remember_me_script() { echo "<script>document.getElementById('rememberme').checked = true;</script>"; }
function tf_login_check_remember_me_setup() { add_filter('login_footer', 'tf_login_check_remember_me_script'); }
add_action('init', 'tf_login_check_remember_me_setup');


/*********************
CUSTOMISE ADMIN AREA
*********************/

// Custom Backend Footer
function tf_custom_admin_footer() { echo '<span id="footer-thankyou">Website by <a href="http://www.tenfold.media" target="_blank">Tenfold Media</a></span>.'; }
add_filter('admin_footer_text', 'tf_custom_admin_footer');

// Show page / post ID column in admin
function tf_posts_columns_id($defaults) { $defaults['tf_post_id'] = 'ID'; return $defaults; }
function tf_posts_custom_id_columns($column_name, $id) { if ($column_name === 'tf_post_id') { echo $id; } }
add_filter('manage_posts_columns', 'tf_posts_columns_id', 5);
add_action('manage_posts_custom_column', 'tf_posts_custom_id_columns', 5, 2);
add_filter('manage_pages_columns', 'tf_posts_columns_id', 5);
add_action('manage_pages_custom_column', 'tf_posts_custom_id_columns', 5, 2);
add_filter('manage_media_columns', 'tf_posts_columns_id', 5);
add_action('manage_media_custom_column', 'tf_posts_custom_id_columns', 5, 2);

// Remove the ' - Wordpress' from the page title
function tf_admin_title($admin_title, $title) { return $title.'|'.$admin_title; }
add_filter('admin_title', 'tf_admin_title', 10, 2);


/*********************
CUSTOMISE ADMIN BAR
*********************/

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


/*********************
CLEANUP
*********************/

// remove WP version from RSS
function tf_remove_wp_ver_rss() { return ''; }
// remove WP version from scripts
function tf_remove_wp_ver_css_js($src) { if (strpos($src, 'ver=')) $src = remove_query_arg('ver', $src); return $src; }

// remove the p from around imgs
function tf_filter_ptags_on_images($content){ return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content); }

function tf_cleanup() {
	remove_action('wp_head', 'feed_links_extra', 3);					// category feeds
	remove_action('wp_head', 'feed_links', 2);							// post and comment feeds
	remove_action('wp_head', 'rsd_link');								// EditURI link
	remove_action('wp_head', 'wlwmanifest_link');						// windows live writer
	remove_action('wp_head', 'index_rel_link');							// index link
	remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);	// links for adjacent posts
	remove_action('wp_head', 'wp_generator');							// WP version
	
	add_filter('the_generator', 'tf_remove_wp_ver_rss');				// remove WP version from RSS
	add_filter('style_loader_src', 'tf_remove_wp_ver_css_js', 9999);	// remove WP version from css
	add_filter('script_loader_src', 'tf_remove_wp_ver_css_js', 9999);	// remove WP version from scripts
	
	add_filter('the_content', 'tf_filter_ptags_on_images');				// cleaning up random code around images
}
add_action('after_setup_theme', 'tf_cleanup', 11);

// disable default dashboard widgets
function tf_disable_dashboard_widgets() {
	remove_meta_box('dashboard_quick_press', 'dashboard', 'core');			// Quick Draft widget
	remove_meta_box('dashboard_primary', 'dashboard', 'core');				// WordPress News widget
    
	update_user_meta(get_current_user_id(), 'show_welcome_panel', false);	// Remove the welcome panel
}
add_action('admin_menu', 'tf_disable_dashboard_widgets');

// dequeue Jetpack's 'devicepx' script (which is totally unneccesary and is a blocking script)
function remove_jetpack_devicepx() { wp_dequeue_script('devicepx'); }
add_action('wp_enqueue_scripts', 'remove_jetpack_devicepx', 20);
