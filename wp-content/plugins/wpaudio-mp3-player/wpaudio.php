<?php

/*
Plugin Name: WPaudio
Plugin URI: http://wpaudio.com
Description: Play mp3s and podcasts in your posts by converting links and tags into a simple, customizable audio player.
Version: 2.2.0
Author: Todd Iceton
Author URI: http://ticeton.com

Copyright 2009 Todd Iceton (email: t@ticeton.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

## WPaudio version
$wpa_version = '2.2.0';

## Pre-2.6 compatibility (from WP codex)
if ( ! defined( 'WP_CONTENT_URL' ) )
	define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
	define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
	define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
if ( ! defined( 'WPA_URL' ) )
	define( 'WPA_URL', WP_PLUGIN_URL . '/wpaudio-mp3-player' );

## WPA options and defaults
$wpa_options = Array(
	'wpa_pref_link_mp3' => 0,
	'wpa_tag_audio' => 0,
	'wpa_track_permalink' => 1,
	'wpa_style_text_font' => 'Arial, Sans-serif',
	'wpa_style_text_size' => '18px',
	'wpa_style_text_weight' => 'bold',
	'wpa_style_text_letter_spacing' => '-1px',
	'wpa_style_text_color' => 'inherit',
	'wpa_style_link_color' => '#24f',
	'wpa_style_link_hover_color' => '#02f',
	'wpa_style_bar_base_bg' => '#eee',
	'wpa_style_bar_load_bg' => '#ccc',
	'wpa_style_bar_position_bg' => '#46f',
	'wpa_style_sub_color' => '#aaa'
);
function wpaOptions(){
	global $wpa_options;
	# Get options and fix if any are blank
	if ($wpa_options_db = get_option('wpaudio_options')) {
		foreach ($wpa_options as $key => $value) {
			if (isset($wpa_options_db[$key]) && !is_null($wpa_options_db[$key]) && $wpa_options_db[$key] != '')
				$wpa_options[$key] = $wpa_options_db[$key];
		}
	}
	# If wpaudio_options doesn't exist, get and remove legacy options
	else {
		# Get legacy options and remove if they exist
		if (get_option('wpa_tag_audio')) {
			foreach ($wpa_options as $key => $value) {
				$wpa_option_old_db = get_option($key);
				if ($wpa_option_old_db !== false && $wpa_option_old_db != '')
					$wpa_options[$key] = $wpa_option_old_db;
				delete_option($key);
			}
		}
		# Create wpaudio_options
		add_option('wpaudio_options', '', '', 'no');
		update_option('wpaudio_options', $wpa_options);
	}
}
wpaOptions();

## WP handlers
# If it's not an admin page, get everything for the player
if (!is_admin()) {
	# Calling scripts
	add_action('init', 'wpaLibraries');
	# Add header action to include CSS and JS vars
	add_action('wp_head', 'wpaHead');
	# Add shortcode for WPaudio player
	add_shortcode('wpaudio', 'wpaShortcode');
	# Add filter for shortcode in excerpt and widgets
	add_filter('the_excerpt', 'do_shortcode');
	add_filter('widget_text', 'do_shortcode');
	# Add filter for non-shortcode substitutes (including excerpts and widgets)
	if ($wpa_options['wpa_tag_audio']) {
		add_filter('the_content', 'wpaFilter');
		add_filter('the_excerpt', 'wpaFilter');
		add_filter('widget_text', 'wpaFilter');
	}
}
# Add admin
add_action('admin_menu', 'wpa_menu');
# Add track
if ($wpa_options['wpa_track_permalink']) add_action('publish_post', 'wpaPostNew');

## Built-in libraries
function wpaLibraries(){
	wp_enqueue_script('soundmanager', WPA_URL . '/sm2/soundmanager2-nodebug-jsmin.js');
	if (version_compare($wp_version, '2.8', '>='))
		wp_enqueue_script('wpaudio', WPA_URL . '/wpaudio.js', array('jquery'), false, true);
	else {
		wp_enqueue_script('jquery');
		add_action('wp_footer', 'wpaFooterForOldVersions');
	}
}

## WPaudio style, jQuery, SWFObject
function wpaHead(){
	global $wpa_options;
	# Player CSS
	$head = <<<WPA
<style type="text/css">
.wpa_container {display: inline-block; vertical-align: top; text-align: left; color: WPA_STYLE_TEXT_COLOR;}
.wpa_container a {text-decoration: none; color: WPA_STYLE_LINK_COLOR;}
.wpa_container a:hover {text-decoration: none; color: WPA_STYLE_LINK_HOVER_COLOR;}
.wpa_container, .wpa_container div, .wpa_container span, .wpa_container a {margin: 0; border: 0; padding: 0; font-weight: normal; letter-spacing: normal; line-height: normal;}
.wpa_container img.wpa_play {width: 16px; height: 14px; margin: 0 5px 0 0; border: 0; padding: 0; vertical-align: baseline; background: #888;}
.wpa_container span.wpa_text {font-family: WPA_STYLE_TEXT_FONT; font-size: WPA_STYLE_TEXT_SIZE; font-weight: WPA_STYLE_TEXT_WEIGHT; letter-spacing: WPA_STYLE_TEXT_LETTER_SPACING;}
.wpa_container div.wpa_bar, .wpa_container div.wpa_bar div {height: 5px; font-size: 1px; line-height: 1px; overflow: hidden;}
.wpa_container div.wpa_bar {display: none; position: relative; margin: 0 0 0 21px; background: WPA_STYLE_BAR_BASE_BG;}
.wpa_container div.wpa_bar div {position: absolute; top: 0px; left: 0px;}
.wpa_container div.wpa_bar div.wpa_bar_load {width: 0; z-index: 10; background: WPA_STYLE_BAR_LOAD_BG;}
.wpa_container div.wpa_bar div.wpa_bar_position {width: 0; z-index: 11; background: WPA_STYLE_BAR_POSITION_BG;}
.wpa_container div.wpa_bar div.wpa_bar_click {width: 100%; z-index: 12; background: transparent; cursor: pointer;}
.wpa_container div.wpa_sub {display: none; position: relative; margin: 0 0 0 22px; color: WPA_STYLE_SUB_COLOR;}
.wpa_container div.wpa_sub, .wpa_container div.wpa_sub span.wpa_time, .wpa_container div.wpa_sub span.wpa_dl {font-family: Arial, Sans-serif; font-size: 11px;}
.wpa_container div.wpa_sub a.wpa_dl {position: absolute; top: 0; right: 0;}
.wpa_container div.wpa_sub span.wpa_dl_info {display: none; position: absolute; top: -130%; left: 105%; border: 1px solid #ddf; padding: 5px; background: #f2f2ff;}
</style>
WPA;
	$head = str_replace('WPA_STYLE_TEXT_FONT', $wpa_options['wpa_style_text_font'], $head);
	$head = str_replace('WPA_STYLE_TEXT_SIZE', $wpa_options['wpa_style_text_size'], $head);
	$head = str_replace('WPA_STYLE_TEXT_WEIGHT', $wpa_options['wpa_style_text_weight'], $head);
	$head = str_replace('WPA_STYLE_TEXT_LETTER_SPACING', $wpa_options['wpa_style_text_letter_spacing'], $head);
	$head = str_replace('WPA_STYLE_TEXT_COLOR', $wpa_options['wpa_style_text_color'], $head);
	$head = str_replace('WPA_STYLE_LINK_COLOR', $wpa_options['wpa_style_link_color'], $head);
	$head = str_replace('WPA_STYLE_LINK_HOVER_COLOR', $wpa_options['wpa_style_link_hover_color'], $head);
	$head = str_replace('WPA_STYLE_BAR_BASE_BG', $wpa_options['wpa_style_bar_base_bg'], $head);
	$head = str_replace('WPA_STYLE_BAR_LOAD_BG', $wpa_options['wpa_style_bar_load_bg'], $head);
	$head = str_replace('WPA_STYLE_BAR_POSITION_BG', $wpa_options['wpa_style_bar_position_bg'], $head);
	$head = str_replace('WPA_STYLE_SUB_COLOR', $wpa_options['wpa_style_sub_color'], $head);
	# If IE, make inline instead of inline-block
	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
		$head = str_replace('.wpa_container {display: inline-block; ', '.wpa_container {', $head);
	# Common JS
	$head .= <<<WPA
<script type="text/javascript">
/* <![CDATA[ */
var wpa_url = 'WPA_URL';
var wpa_urls = [];
var wpa_pref_link_mp3 = WPA_PREF_LINK_MP3;
/* ]]> */
</script>
WPA;
	$head = str_replace('WPA_URL', WPA_URL, $head);
	$wpa_pref_link_mp3 = ($wpa_options['wpa_pref_link_mp3']) ? 'true' : 'false';
	$head = str_replace('WPA_PREF_LINK_MP3', $wpa_pref_link_mp3, $head);
	echo $head;
}

function wpaFooterForOldVersions() {
	echo '<script type="text/javascript" src="' . WPA_URL . '/wpaudio.js"></script>';
}

# Used only for wpaudio shortcode tags
function wpaShortcode($atts){
	# Convert shortcodes to WPaudio player depending on settings
	extract(shortcode_atts(Array(
		'url' => false,
		'text' => false,
		'dl' => true,
		'autoplay' => false
	), $atts));
	# If no url, return with nothing
	if (!$url)
		return;
	# Get player HTML and JS
	return wpaLink($url, $text, $dl, $autoplay);
}

# For dl/dl=0 URLs
$wpa_urls = 0;

# Make WPA link
function wpaLink($url, $text = false, $dl = true, $autoplay = false) {
	global $wpa_urls;
	$class = 'wpaudio';
	$html = '';
	# Handle dl URLs and no dl players
	if ($dl == '0') {
		$js_url = wpaUnicode($url);
		$href = '#';
	}
	elseif (is_string($dl)) {
		$js_url = wpaUnicode($url);
		$href = $dl;
	}
	else {
		$href = $url;
	}
	if (isset($js_url)) {
		$class .= ' wpaudio_url_' . $wpa_urls++;
		$html .= "<script type='text/javascript'>wpa_urls.push('$js_url');</script>";
	}
	# Handle blank text
	if (!$text) {
		$text = basename($url);
		$class .= ' wpaudio_readid3';
	}
	# Autoplay
	if ($autoplay == '1') $class .= ' wpaudio_autoplay';
	$html .= "<a class='$class' href='$href'>$text</a>";
	return $html;
}

# Used for audio tags
function wpaFilter($content){
	## Convert audio tags and links to WPaudio player depending on settings
	$tag_regex = '/\[audio:(.*?)\]/';
	$tag_match = preg_match_all($tag_regex, $content, $tag_matches);
	# Replace audio tags with player links
	if ($tag_match){
		foreach ($tag_matches[1] as $key => $value){
			# This is one tag, first get parameters and URLs
			$params = explode('|', $value);
			$clips = Array('urls' => Array(), 'titles' => Array(), 'artists' => Array());
			$clips['urls'] = explode(',', $params[0]);
			# Process extra parameters if they exist
			for ($i=1; $i<count($params); $i++) {
				# Get the parameter name and value
				$param = explode('=', $params[$i]);
				if ($param[0] == 'titles' || $param[0] == 'artists')
					$clips[$param[0]] = explode(',', $param[1]);
			}
			# Get player(s)
			$player = '';
			foreach ($clips['urls'] as $ukey => $uvalue) {
				$text = '';
				$text .= (isset($clips['artists'][$ukey])) ? $clips['artists'][$ukey] : '';
				$text .= (isset($clips['artists'][$ukey]) && isset($clips['titles'][$ukey])) ? ' - ' : '';
				$text .= (isset($clips['titles'][$ukey])) ? $clips['titles'][$ukey] : '';
				if (!$text) $text = false;
				$player .= wpaLink($uvalue, $text);
			}
			$content = str_replace($tag_matches[0][$key], $player, $content);
		}
	}
	return $content;
}

# Convert string to unicode (to conceal mp3 URLs)
include 'php-utf8/utf8.inc';
function wpaUnicode($str){
	$uni = utf8ToUnicode(utf8_encode($str));
	$output = '';
	foreach ($uni as $value){
		$output .= '\u' . str_pad(dechex($value), 4, '0', STR_PAD_LEFT);
	}
	return $output;
}

## WP admin menu
function wpa_menu() {
	add_options_page('WPaudio Options', 'WPaudio', 10, __FILE__, 'wpa_menu_page');
}
function wpa_menu_page() {
	global $wpa_options;
	if ($_POST) {
		# Checkboxes need values
		$wpa_checkboxes = Array(
			'wpa_pref_link_mp3',
			'wpa_tag_audio',
			'wpa_track_permalink'
		);
		foreach ($wpa_checkboxes as $value) {
			$_POST[$value] = (isset($_POST[$value]) && $_POST[$value]) ? 1 : 0;
		}
		# Now process and save all options
		foreach ($wpa_options as $key => $value) {
			if (isset($_POST[$key]) && !is_null($_POST[$key]) && $_POST[$key] !== '')
				$wpa_options[$key] = $_POST[$key];
		}
		update_option('wpaudio_options', $wpa_options);
	}
	wpaOptions();
	?>
<!-- wpa menu begin -->
<div class="wrap">
<h2>WPaudio Options</h2>
<form method="POST" action="">
<?php wp_nonce_field('update-options'); ?>

<div id="poststuff" class="metabox-holder">
	<div class="meta-box-sortables">
		<div class="postbox">
			<h3 class="hndle"><span>Links</span></h3>
			<div class="inside">
				<ul>
					<li>WPaudio will always convert links with the <span style="font-family: Courier, Serif">wpaudio</span> class.  You optionally handle ALL mp3 links too.</li>
					<li><label for="wpa_pref_link_mp3"><input name="wpa_pref_link_mp3" id="wpa_pref_link_mp3" type="checkbox" <?php if ($wpa_options['wpa_pref_link_mp3']) echo ' checked="yes"'; ?>>
						Convert all mp3 links - <span style="font-family: Courier, Serif">&lt;a href="http://domain.com/song.mp3"&gt;Link&lt;/a&gt;</span></label></li>
				</ul>
			</div>
		</div>
		<div class="postbox">
			<h3 class="hndle"><span>Tags</span></h3>
			<div class="inside">
				<ul>
					<li>WPaudio will always convert <span style="font-family: Courier, Serif">[wpaudio]</span> tags, but it can also handle tags from other audio players.</li>
					<li><label for="wpa_tag_audio"><input name="wpa_tag_audio" id="wpa_tag_audio" type="checkbox" <?php if ($wpa_options['wpa_tag_audio']) echo ' checked="yes"'; ?>>
						Handle Audio Player tags - <span style="font-family: Courier, Serif">[audio:http://domain.com/song.mp3]</span></label></li>
				</ul>
			</div>
		</div>
		<div class="postbox">
			<h3 class="hndle"><span>Style</span></h3>
			<div class="inside">
				<ul>
					<li><a href="#" onclick="jQuery('.wpa_style_advanced').css('display', 'block');">It's not necessary to adjust these settings, but click here for advanced options.</a></li>
				</ul>
				<ul class="wpa_style_advanced" style="display: none;">
					<li>Optionally customize WPaudio's font</li>
					<li><label for="wpa_style_text_font"><input type="text" name="wpa_style_text_font" id="wpa_style_text_font" value="<?php echo $wpa_options['wpa_style_text_font']; ?>"> Font face</label></li>
					<li><label for="wpa_style_text_size"><input type="text" name="wpa_style_text_size" id="wpa_style_text_size" value="<?php echo $wpa_options['wpa_style_text_size']; ?>"> Font size</label></li>
					<li><label for="wpa_style_text_weight"><select name="wpa_style_text_weight" id="wpa_style_text_weight">
						<option value="inherit" <?php if ($wpa_options['wpa_style_text_weight'] == 'inherit') echo ' selected'; ?>>Inherit</option>
						<option value="normal" <?php if ($wpa_options['wpa_style_text_weight'] == 'normal') echo ' selected'; ?>>Normal</option>
						<option value="bold" <?php if ($wpa_options['wpa_style_text_weight'] == 'bold') echo ' selected'; ?>>Bold</option>
						</select> Font weight</label></li>
					<li><label for="wpa_style_text_letter_spacing"><input type="text" name="wpa_style_text_letter_spacing" id="wpa_style_text_letter_spacing" value="<?php echo $wpa_options['wpa_style_text_letter_spacing']; ?>"> Letter spacing</label></li>
				</ul>
				<ul class="wpa_style_advanced" style="display: none;">
					<li>Optionally customize colors (Most commonly 3 or 6 character <a href="http://en.wikipedia.org/wiki/Web_colors#Color_table" target="_blank">hex codes</a>.  For example: <span style="font-family: Courier, Serif">#2244ff</span>)</li>
					<li><label for="wpa_style_text_color"><input type="text" name="wpa_style_text_color" id="wpa_style_text_color" value="<?php echo $wpa_options['wpa_style_text_color']; ?>" size="7"> Text color</label></li>
					<li><label for="wpa_style_link_color"><input type="text" name="wpa_style_link_color" id="wpa_style_link_color" value="<?php echo $wpa_options['wpa_style_link_color']; ?>" size="7"> Link color</label></li>
					<li><label for="wpa_style_link_hover_color"><input type="text" name="wpa_style_link_hover_color" id="wpa_style_link_hover_color" value="<?php echo $wpa_options['wpa_style_link_hover_color']; ?>" size="7"> Link hover color</label></li>
					<li><label for="wpa_style_bar_base_bg"><input type="text" name="wpa_style_bar_base_bg" id="wpa_style_bar_base_bg" value="<?php echo $wpa_options['wpa_style_bar_base_bg']; ?>" size="7"> Bar base background</label></li>
					<li><label for="wpa_style_bar_load_bg"><input type="text" name="wpa_style_bar_load_bg" id="wpa_style_bar_load_bg" value="<?php echo $wpa_options['wpa_style_bar_load_bg']; ?>" size="7"> Bar load background</label></li>
					<li><label for="wpa_style_bar_position_bg"><input type="text" name="wpa_style_bar_position_bg" id="wpa_style_bar_position_bg" value="<?php echo $wpa_options['wpa_style_bar_position_bg']; ?>" size="7"> Bar position background</label></li>
				</ul>
			</div>
		</div>
		<div class="postbox">
			<h3 class="hndle"><span>Notification</span></h3>
			<div class="inside">
				<ul>
					<li>I love seeing who's using my plugin!  Please select this option to enable a notification when a post containing the player is published so I can come check out your site.  Your blog may even be featured on WPaudio.com.  Thanks!</li>
					<li><label for="wpa_track_permalink"><input name="wpa_track_permalink" id="wpa_track_permalink" type="checkbox" <?php if ($wpa_options['wpa_track_permalink']) echo ' checked="yes"'; ?>>
						Allow WPaudio notification</label></li>
				</ul>
			</div>
		</div>
	</div>
</div>

<p class="submit">
	<input class="button-primary" type="submit" value="Save Changes">
</p>

</form>
</div>
<!-- wpa menu end -->
<?php
}

## WP new post - add ping if contains wpaudio
function wpaPostNew($id) {
	$post = get_post($id);
	if (strpos(strtolower($post->post_content), 'wpaudio') !== false) {
		$permalink = rawurlencode(get_permalink($id));
		if (function_exists('curl_init') && function_exists('curl_setopt') && function_exists('curl_exec') && function_exists('curl_close')) {
			$ch = curl_init("http://wpaudio.com/t/?url_post=$permalink");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_exec($ch);
			curl_close($ch);
		}
	}
}

?>
