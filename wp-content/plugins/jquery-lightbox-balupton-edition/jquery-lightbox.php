<?php
/*
Plugin Name: jQuery Lightbox
Plugin URI: http://www.pedrolamas.com/projectos/jquery-lightbox
Description: Used to overlay images on the current page. Original jQuery Lightbox by <a href="http://plugins.jquery.com/project/jquerylightbox_bal" title="jQuery Lightbox">Balupton</a>.
Version: 0.9
Author: Pedro Lamas
Author URI: http://www.pedrolamas.com/
*/

if (!class_exists("jQueryLightbox")) {
  class jQueryLightbox {
    function jQueryLightbox()
    {
      if (is_admin() || !function_exists('plugins_url')) return;
      
      global $wp_version;
      
      $path = plugins_url('/jquery-lightbox-balupton-edition/js/');
      
      if (version_compare($wp_version, '2.8', '<')) {
        wp_deregister_script('jquery');
        wp_enqueue_script('jquery', $path.'jquery-1.3.2.min.js', false, '1.3.2');
      }
      wp_enqueue_script('jquery-lightbox', $path.'jquery.lightbox.min.js?ie6_upgrade=false', array('jquery'), '1.3.7');
      wp_enqueue_script('jquery-lightbox-plugin', $path.'jquery.lightbox.plugin.min.js', array('jquery', 'jquery-lightbox'), '1.0');
      
      add_filter('attachment_link', array(&$this, 'FixLink'), 10, 2);
    }
    
    function FixLink() {
      $post = get_post($id);
      
      if (substr($post->post_mime_type, 0, 6) == 'image/')
        return wp_get_attachment_url($id);
      else
        return $link;
    }
  }
}

if (class_exists("jQueryLightbox")) {
  //$jQueryLightbox = new jQueryLightbox();
  add_action('plugins_loaded', create_function('', 'global $jQueryLightbox; $jQueryLightbox = new jQueryLightbox();'));
}
?>