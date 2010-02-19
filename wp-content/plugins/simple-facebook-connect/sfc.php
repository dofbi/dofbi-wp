<?php
/*
Plugin Name: Simple Facebook Connect
Plugin URI: http://ottodestruct.com/blog/wordpress-plugins/simple-facebook-connect/
Description: Makes it easy for your site to use Facebook Connect, in a wholly modular way.
Author: Otto
Version: 0.12
Author URI: http://ottodestruct.com

    Copyright 2009-2010  Samuel Wood  (email : otto@ottodestruct.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2, 
    as published by the Free Software Foundation. 
    
    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    
    The license for this software can likely be found here: 
    http://www.gnu.org/licenses/gpl-2.0.html
    
*/

// fast check for xd_receiver request on plugin load.
if ($_GET['xd_receiver'] == 1) { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><title>xd</title></head>
<body>
<?php 
if ($_SERVER['HTTPS'] == 'on')
	echo '<script src="https://ssl.connect.facebook.com/js/api_lib/v0.4/XdCommReceiver.js" type="text/javascript"></script>';
else
	echo '<script src="http://static.ak.facebook.com/js/api_lib/v0.4/XdCommReceiver.js" type="text/javascript"></script>';
?>
</body>
</html>
<?php
exit; // stop normal WordPress execution
}

// require PHP 5
function sfc_activation_check(){
	if (version_compare(PHP_VERSION, '5.0.0', '<')) {
		deactivate_plugins(basename(__FILE__)); // Deactivate ourself
		wp_die("Sorry, Simple Facebook Connect requires PHP 5 or higher. Ask your host how to enable PHP 5 as the default on your servers.");
	}
}
register_activation_hook(__FILE__, 'sfc_activation_check');

function sfc_version() {
	return '0.12';
}

// load the FB script into the head 
// (yes, I know its supposed to be in the body.. I have not found any issues with it being in the head yet)
add_action('wp_enqueue_scripts','sfc_featureloader');
function sfc_featureloader() {
	if ($_SERVER['HTTPS'] == 'on')
		wp_enqueue_script( 'fb-featureloader', 'https://ssl.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php', array(), '0.4', false);
	else
		wp_enqueue_script( 'fb-featureloader', 'http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php', array(), '0.4', false);		
}

// fix up the html tag to have the FBML extensions
add_filter('language_attributes','sfc_lang_atts');
function sfc_lang_atts($lang) {
    return ' xmlns:fb="http://www.facebook.com/2008/fbml" '.$lang;
}

// basic XFBML load into footer
add_action('wp_footer','sfc_add_base_js',20); // 20, to put it at the end of the footer insertions. sub-plugins should use 30 for their code
function sfc_add_base_js() {
	$options = get_option('sfc_options');
	sfc_load_api($options['api_key']);
};

function sfc_load_api($key) {
$reload = apply_filters('sfc_reload_state_change',false);
?>
<script type="text/javascript">
FB_RequireFeatures(["XFBML"], function() {
  	FB.init("<?php echo $key; ?>", "<?php bloginfo('url'); ?>/?xd_receiver=1"<?php if ($reload) echo ', {"reloadIfSessionStateChanged":true}'; ?>);
});
</script>
<?php
}

// plugin row links
add_filter('plugin_row_meta', 'sfc_donate_link', 10, 2);
function sfc_donate_link($links, $file) {
	if ($file == plugin_basename(__FILE__)) {
		$links[] = '<a href="'.admin_url('options-general.php?page=sfc').'">Settings</a>';
		$links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=otto%40ottodestruct%2ecom">Donate</a>';
	}
	return $links;
}

// action links
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'sfc_settings_link', 10, 1);
function sfc_settings_link($links) {
	$links[] = '<a href="'.admin_url('options-general.php?page=sfc').'">Settings</a>';
	return $links;
}

// add the admin settings and such
add_action('admin_init', 'sfc_admin_init',9); // 9 to force it first, subplugins should use default
function sfc_admin_init(){
	$options = get_option('sfc_options');
	if (!empty($options['api_key'])) {
		sfc_featureloader();
		add_action('admin_footer','sfc_add_base_js',20);
	} else {
		add_action('admin_notices', create_function( '', "echo '<div class=\"error\"><p>".sprintf('Simple Facebook Connect needs an API key entered on its <a href="%s">settings</a> page.', admin_url('options-general.php?page=sfc'))."</p></div>';" ) );
	}
	wp_enqueue_script('jquery');
	register_setting( 'sfc_options', 'sfc_options', 'sfc_options_validate' );
	add_settings_section('sfc_main', 'Main Settings', 'sfc_section_text', 'sfc');
	add_settings_field('sfc_api_key', 'Facebook API Key', 'sfc_setting_api_key', 'sfc', 'sfc_main');
	add_settings_field('sfc_app_secret', 'Facebook Application Secret', 'sfc_setting_app_secret', 'sfc', 'sfc_main');
	add_settings_field('sfc_appid', 'Facebook Application ID', 'sfc_setting_appid', 'sfc', 'sfc_main');
	add_settings_field('sfc_fanpage', 'Facebook Fan Page', 'sfc_setting_fanpage', 'sfc', 'sfc_main');
}

// add the admin options page
add_action('admin_menu', 'sfc_admin_add_page');
function sfc_admin_add_page() {
	$mypage = add_options_page('Simple Facebook Connect', 'Simple Facebook Connect', 'manage_options', 'sfc', 'sfc_options_page');
}

// display the admin options page
function sfc_options_page() {
?>
	<div class="wrap">
	<h2>Simple Facebook Connect</h2>
	<p>Options relating to the Simple Facebook Connect plugins.</p>
	<form method="post" action="options.php">
	<?php settings_fields('sfc_options'); ?>
	<table><tr><td>
	<?php do_settings_sections('sfc'); ?>
	</td><td style='vertical-align:top;'>
	<div style='width:20em; float:right; background: #ffc; border: 1px solid #333; margin: 2px; padding: 5px'>
			<h3 align='center'>About the Author</h3>
		<p><a href="http://ottodestruct.com/blog/wordpress-plugins/simple-facebook-connect/">Simple Facebook Connect</a> is developed and maintained by <a href="http://ottodestruct.com">Otto</a>.</p>
			<p>He blogs at <a href="http://ottodestruct.com">Nothing To See Here</a>, posts photos on <a href="http://www.flickr.com/photos/otto42/">Flickr</a>, and chats on <a href="http://twitter.com/otto42">Twitter</a>.</p>
			<p>You can follow his site on either <a href="http://www.facebook.com/apps/application.php?id=116002660893">Facebook</a> or <a href="http://twitter.com/ottodestruct">Twitter</a>, if you like.</p>
			<p>If you'd like to <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=otto%40ottodestruct%2ecom">buy him a beer</a>, then he'd be perfectly happy to drink it.</p>
		</div>
	</tr></table>
	<p class="submit">
	<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
	</p>
	</form>
	</div>
	
<?php
}

function sfc_section_text() {
	$options = get_option('sfc_options');
	if (empty($options['api_key']) || empty($options['app_secret']) || empty($options['appid'])) {
?>
<p>To connect your site to Facebook, you will need a Facebook Application. 
If you have already created one, please insert your API key and Application ID below.</p>
<p><strong>Can't find your key?</strong></p>
<ol>
<li>Get a list of your applications from here: <a target="_blank" href="http://www.facebook.com/developers/apps.php">Facebook Application List</a></li>
<li>Select the application you want, then copy and paste the API key, Application Secret, and Application ID from there.</li>
</ol>

<p><strong>Haven't created an application yet?</strong> Don't worry, it's easy!</p>
<ol>
<li>Go to this link to create your application: <a target="_blank" href="http://developers.facebook.com/setup.php">Facebook Connect Setup</a></li>
<li>When it tells you to "Upload a file" on step 2, just hit the "Upload Later" button. This plugin takes care of that part for you!</li>
<li>On the final screen, there will be an API Key field, in the yellow box. Copy and paste that information into here.</li>
<li>You can get the rest of the information from the application on the 
<a target="_blank" href="http://www.facebook.com/developers/apps.php">Facebook Application List</a> page.</li>
<li>Select the application you want, then copy and paste the API key, Application Secret, and Application ID from there.</li>
</ol>
<?php
		// look for an FBFoundations key if we dont have one of our own, 
		// to better facilitate switching from that plugin to this one.
		$fbfoundations_settings = get_option('fbfoundations_settings');
		if (isset($fbfoundations_settings['api_key']) && !empty($fbfoundations_settings['api_key'])) {
			$options['api_key'] = $fbfoundations_settings['api_key'];
		}
	}
}

function sfc_setting_api_key() {
	$options = get_option('sfc_options');
	echo "<input type='text' id='sfcapikey' name='sfc_options[api_key]' value='{$options['api_key']}' size='40' />";	
}
function sfc_setting_app_secret() {
	$options = get_option('sfc_options');
	echo "<input type='text' id='sfcappsecret' name='sfc_options[app_secret]' value='{$options['app_secret']}' size='40' />";	
}
function sfc_setting_appid() {
	$options = get_option('sfc_options');
	echo "<input type='text' id='sfcappid' name='sfc_options[appid]' value='{$options['appid']}' size='40' />";	
	if (!empty($options['appid'])) echo "<p>Here is a <a href='http://www.facebook.com/apps/application.php?id={$options['appid']}&amp;v=wall'>link to your applications wall</a>. There you can give it a name, upload a profile picture, things like that. Look for the \"Edit Application\" link to modify the application.</p>";	
}
function sfc_setting_fanpage() {
	$options = get_option('sfc_options'); ?>

<p>Some sites use Fan Pages on Facebook to connect with their users. The Application wall acts as a 
Fan Page in all respects, however some sites have been using Fan Pages previously, and already have 
communities and content built around them. Facebook offers no way to migrate these, so the option to 
use an existing Fan Page is offered for people with this situation. Note that this doesn't <em>replace</em> 
the application, as that is not optional. However, you can use a Fan Page for specific parts of the 
SFC plugin, such as the Fan Box, the Publisher, and the Chicklet.</p>

<p>If you have a <a href="http://www.facebook.com/pages/manage/">Fan Page</a> that you want to use for 
your site, enter the ID of the page here. Most users should leave this blank.</p>

<?php
	echo "<input type='text' id='sfcfanpage' name='sfc_options[fanpage]' value='{$options['fanpage']}' size='40' />";
}

// validate our options
function sfc_options_validate($input) {
	// api keys are 32 bytes long and made of hex values
	$input['api_key'] = trim($input['api_key']);
	if(! preg_match('/^[a-f0-9]{32}$/i', $input['api_key'])) {
	  $input['api_key'] = '';
	}

	// api keys are 32 bytes long and made of hex values
	$input['app_secret'] = trim($input['app_secret']);
	if(! preg_match('/^[a-f0-9]{32}$/i', $input['app_secret'])) {
	  $input['app_secret'] = '';
	}
	
	// app ids are big integers
	$input['appid'] = trim($input['appid']);
	if(! preg_match('/^[0-9]+$/i', $input['appid'])) {
	  $input['appid'] = '';
	}
	
	// fanpage ids are big integers
	$input['fanpage'] = trim($input['fanpage']);
		if(! preg_match('/^[0-9]+$/i', $input['fanpage'])) {
		  $input['fanpage'] = '';
	}

	$input = apply_filters('sfc_validate_options',$input); // filter to let sub-plugins validate their options too
	return $input;
}
