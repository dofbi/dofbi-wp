<?php
/*
Plugin Name: SFC - Fan Box Widget
Plugin URI: http://ottodestruct.com/blog/wordpress-plugins/simple-facebook-connect/
Description: Create a Fan Box for your site's sidebar.
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

// checks for sfc on activation
function sfc_fan_box_activation_check(){
	if (function_exists('sfc_version')) {
		if (version_compare(sfc_version(), '0.4', '>=')) {
			return;
		}
	}
	deactivate_plugins(basename(__FILE__)); // Deactivate ourself
	wp_die("The base SFC plugin must be activated before this plugin will run.");
}
register_activation_hook(__FILE__, 'sfc_fan_box_activation_check');

// Shortcode for putting it into pages or posts directly
// profile id is required. Won't work without it.
function sfc_fanbox_shortcode($atts) {
	$options = get_option('sfc_options');
	extract(shortcode_atts(array(
		'stream' => 1,
		'connections' => 10,
		'width' => 200,
		'height' => 0,
		'logobar' => 1,
	), $atts));
	
	if ($options['fanpage']) $id = $options['fanpage'];
	else $id = $options['appid'];
	
	$retvar = '<fb:fan profile_id="'.$id.'" logobar="'.$logobar.'" stream="'.$stream.'" connections="'.$connections.'" width="'.$width.'"';
	if ($options['fanbox_css']) {
		$retvar .= ' css="'.get_bloginfo('url').'/?sfc-fanbox-css=1&time='.$options['fanbox_time'].'"';
	}
	if ($height) echo ' height="'.$height.'"';
	$retvar .= '></fb:fan>';
	
	return $retvar;
}
add_shortcode('fb-fanbox', 'sfc_fanbox_shortcode');

class SFC_Fan_Box_Widget extends WP_Widget {
	function SFC_Fan_Box_Widget() {
		$widget_ops = array('classname' => 'widget_sfc-fanbox', 'description' => 'Facebook Fan Box');
		$this->WP_Widget('sfc-fanbox', 'Facebook Fan Box (SFC)', $widget_ops);
	}

	function widget($args, $instance) {
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		$instance['stream'] = isset($instance['stream']) ? $instance['stream'] : 1;
		$instance['logobar'] = isset($instance['logobar']) ? $instance['logobar'] : 1;
		$instance['connections'] = intval($instance['connections']);
		$instance['width'] = intval($instance['width']);
		?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<?php echo sfc_fanbox_shortcode($instance); ?>
		<?php echo $after_widget; ?>
		<?php
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, array( 'title' => '', 'connections' => '0', 'logobar'=> 0, 'stream' => 0, 'width'=>200) );
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['connections'] = intval($new_instance['connections']);
		$instance['width'] = intval($new_instance['width']);
		if ($instance['width'] < 200) $instance['width'] = 200;
		$instance['stream'] = $new_instance['stream'] ? 1 : 0;	
		$instance['logobar'] = $new_instance['logobar'] ? 1 : 0;	
		return $instance;
	}

	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'connections' => '0', 'logobar'=> 0, 'stream' => 0, 'width'=>200) );
		$title = strip_tags($instance['title']);
		$connections = intval($instance['connections']);
		$width = intval($instance['width']);
		$stream = $instance['stream'] ? true : false;
		$logobar = $instance['logobar'] ? true : false;
		?>
<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> 
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</label></p>
<p><label for="<?php echo $this->get_field_id('logobar'); ?>">Show Facebook Logo Bar? 
<input class="checkbox" id="<?php echo $this->get_field_id('logobar'); ?>" name="<?php echo $this->get_field_name('logobar'); ?>" type="checkbox" <?php checked($logobar, true); ?> />
</label></p>
<p><label for="<?php echo $this->get_field_id('stream'); ?>">Show Stream Stories? 
<input class="checkbox" id="<?php echo $this->get_field_id('stream'); ?>" name="<?php echo $this->get_field_name('stream'); ?>" type="checkbox" <?php checked($stream, true); ?> />
</label></p>
<p><label for="<?php echo $this->get_field_id('connections'); ?>">Number of Fans to Show:
<input class="widefat" id="<?php echo $this->get_field_id('connections'); ?>" name="<?php echo $this->get_field_name('connections'); ?>" type="text" value="<?php echo $connections; ?>" />
</label></p>
<p><label for="<?php echo $this->get_field_id('width'); ?>">Width of the widget in pixels (minimum 200):
<input class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $width; ?>" />
</label></p>

		<?php
	}
}
add_action('widgets_init', create_function('', 'return register_widget("SFC_Fan_Box_Widget");'));

// add the admin sections to the sfc page
add_action('admin_init', 'sfc_fanbox_admin_init');
function sfc_fanbox_admin_init() {
	add_settings_section('sfc_fanbox', 'Fan Box Settings', 'sfc_fanbox_section_callback', 'sfc');
	add_settings_field('sfc_fanbox_css', 'Fanbox Custom CSS', 'sfc_fanbox_css_callback', 'sfc', 'sfc_fanbox');
}

function sfc_fanbox_section_callback() {
	echo '<p>Use this area to add any custom CSS you like to the Facebook Fan Box display.</p>';
}

function sfc_fanbox_css_callback() {
	$options = get_option('sfc_options');
	if (!$options['fanbox_css']) $options['fanbox_css'] = ''; 
	/* good default CSS to use:

	.connect_widget .connect_widget_facebook_logo_menubar {
	}
	.fan_box .full_widget .connect_top {
	}
	.fan_box .full_widget .page_stream {
	}
	.fan_box .full_widget .connections {
	}
	*/
	
	?>
	<p><label><textarea name="sfc_options[fanbox_css]" class="large-text code" rows="10"><?php echo $options['fanbox_css']; ?></textarea></label></p>
<?php 
}

add_filter('sfc_validate_options','sfc_fanbox_validate_options');
function sfc_fanbox_validate_options($input) {
	$input['fanbox_css'] = strip_tags($input['fanbox_css']);
	$input['fanbox_time'] = time();
	return $input;
}


// add in the query var for fanbox-css code (so we dont need a separate file for it)
add_filter('query_vars','sfc_fanbox_css_vars');
function sfc_fanbox_css_vars($vars) {
    $vars[] = 'sfc-fanbox-css';
    return $vars;
}

// output the fanbox css code
add_action('template_redirect', 'sfc_fanbox_css_output');
function sfc_fanbox_css_output() {
	if(intval(get_query_var('sfc-fanbox-css')) == 1) {
		$options = get_option('sfc_options');
		header( 'Content-Type: text/css; charset=utf-8' );
		echo $options['fanbox_css'];
		exit; // stop normal WordPress execution
	}
}
