<?php
/*
Plugin Name: Quick Flickr Widget
Plugin URI: http://kovshenin.com/wordpress/plugins/quick-flickr-widget/
Description: Display up to 20 of your latest Flickr submissions in your sidebar.
Author: Konstantin Kovshenin
Version: 1.2.10
Author URI: http://kovshenin.com/

/* License

    Quick Flickr Widget
    Copyright (C) 2009 Konstantin Kovshenin (kovshenin@live.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
    
*/

$flickr_api_key = "d348e6e1216a46f2a4c9e28f93d75a48"; // You can use your own if you like

function widget_quickflickr($args) {
	extract($args);
	
	$options = get_option("widget_quickflickr");
	if( $options == false ) {
		$options["title"] = "Flickr Photos";
		$options["rss"] = "";
		$options["items"] = 3;
		$options["view"] = "_t"; // Thumbnail
		$options["before_item"] = "";
		$options["after_item"] = "";
		$options["before_flickr_widget"] = "";
		$options["after_flickr_widget"] = "";
		$options["more_title"] = "";
		$options["target"] = "";
		$options["show_titles"] = "";
		$options["username"] = "";
		$options["user_id"] = "";
		$options["error"] = "";
		$options["thickbox"] = "";
		$options["tags"] = "";
		$options["random"] = "";
		$options["javascript"] = "";
	}
	
	$title = $options["title"];
	$items = $options["items"];
	$view = $options["view"];
	$before_item = $options["before_item"];
	$after_item = $options["after_item"];
	$before_flickr_widget = $options["before_flickr_widget"];
	$after_flickr_widget = $options["after_flickr_widget"];
	$more_title = $options["more_title"];
	$target = $options["target"];
	$show_titles = $options["show_titles"];
	$username = $options["username"];
	$user_id = $options["user_id"];
	$error = $options["error"];
	$rss = $options["rss"];
	$thickbox = $options["thickbox"];
	$tags = $options["tags"];
	$random = $options["random"];
	$javascript = $options["javascript"];
	
	if (empty($error))
	{	
		$target = ($target == "checked") ? "target=\"_blank\"" : "";
		$show_titles = ($show_titles == "checked") ? true : false;
		$thickbox = ($thickbox == "checked") ? true : false;
		$tags = (strlen($tags) > 0) ? "&tags=" . urlencode($tags) : "";
		$random = ($random == "checked") ? true : false;
		$javascript = ($javascript == "checked") ? true : false;
		
		if ($javascript) $flickrformat = "json"; else $flickrformat = "php";
		
		if (empty($items) || $items < 1 || $items > 20) $items = 3;
		
		// Screen name or RSS in $username?
		if (!ereg("http://api.flickr.com/services/feeds", $username))
			$url = "http://api.flickr.com/services/feeds/photos_public.gne?id=".urlencode($user_id)."&format=".$flickrformat."&lang=en-us".$tags;
		else
			$url = $username."&format=".$flickrformat.$tags;
		
		// Output via php or javascript?
		if (!$javascript)
		{
			eval("?>". file_get_contents($url) . "<?");
			$photos = $feed;
			
			if ($random) shuffle($photos["items"]);
			
			if ($photos)
			{
				foreach($photos["items"] as $key => $value)
				{
					if (--$items < 0) break;
					
					$photo_title = $value["title"];
					$photo_link = $value["url"];
					ereg("<img[^>]* src=\"([^\"]*)\"[^>]*>", $value["description"], $regs);
					$photo_url = $regs[1];
					$photo_description = str_replace("\n", "", strip_tags($value["description"]));
					
					//$photo_url = $value["media"]["m"];
					$photo_medium_url = str_replace("_m.jpg", ".jpg", $photo_url);
					$photo_url = str_replace("_m.jpg", "$view.jpg", $photo_url);
					
					$thickbox_attrib = ($thickbox) ? "class=\"thickbox\" rel=\"flickr-gallery\" title=\"$photo_title: $photo_description &raquo;\"" : "";
					$href = ($thickbox) ? $photo_medium_url : $photo_link;
					
					$photo_title = ($show_titles) ? "<div class=\"qflickr-title\">$photo_title</div>" : "";
					$out .= $before_item . "<a $thickbox_attrib $target href=\"$href\"><img class=\"flickr_photo\" alt=\"$photo_description\" title=\"$photo_description\" src=\"$photo_url\" /></a>$photo_title" . $after_item;
				}
				$flickr_home = $photos["link"];
			}
			else
			{
				$out = "Something went wrong with the Flickr feed! Please check your configuration and make sure that the Flickr username or RSS feed exists";
			}
		}
		else // via javascript
		{
			$out = "<script type=\"text/javascript\" src=\"$url\"></script>";
		}
		?>
<!-- Quick Flickr start -->
	<?php echo $before_widget.$before_flickr_widget; ?>
		<?php if(!empty($title)) { $title = apply_filters('localization', $title); echo $before_title . $title . $after_title; } ?>
		<?php echo $out ?>
		<?php if (!empty($more_title) && !$javascript) echo "<a href=\"" . strip_tags($flickr_home) . "\">$more_title</a>"; ?>
	<?php echo $after_flickr_widget.$after_widget; ?>
<!-- Quick Flickr end -->
	<?php
	}
	else // error
	{
		$out = $error;
	}
}

function widget_quickflickr_control() {
	$options = $newoptions = get_option("widget_quickflickr");
	if( $options == false ) {
		$newoptions["title"] = "Flickr photostream";
		$newoptions["view"] = "_t";
		$newoptions["before_flickr_widget"] = "<div class=\"flickr\">";
		$newoptions["after_flickr_widget"] = "</div>";
		$newoptions["error"] = "Your Quick Flickr Widget needs to be configured";
	}
	if ( $_POST["flickr-submit"] ) {
		$newoptions["title"] = strip_tags(stripslashes($_POST["flickr-title"]));
		$newoptions["items"] = strip_tags(stripslashes($_POST["rss-items"]));
		$newoptions["view"] = strip_tags(stripslashes($_POST["flickr-view"]));
		$newoptions["before_item"] = stripslashes($_POST["flickr-before-item"]);
		$newoptions["after_item"] = stripslashes($_POST["flickr-after-item"]);
		$newoptions["before_flickr_widget"] = stripslashes($_POST["flickr-before-flickr-widget"]);
		$newoptions["after_flickr_widget"] = stripslashes($_POST["flickr-after-flickr-widget"]);
		$newoptions["more_title"] = strip_tags(stripslashes($_POST["flickr-more-title"]));
		$newoptions["target"] = strip_tags(stripslashes($_POST["flickr-target"]));
		$newoptions["show_titles"] = strip_tags(stripslashes($_POST["flickr-show-titles"]));
		$newoptions["username"] = strip_tags(stripslashes($_POST["flickr-username"]));
		$newoptions["thickbox"] = strip_tags(stripslashes($_POST["flickr-thickbox"]));
		$newoptions["tags"] = strip_tags(stripslashes($_POST["flickr-tags"]));
		$newoptions["random"] = strip_tags(stripslashes($_POST["flickr-random"]));
		$newoptions["javascript"] = strip_tags(stripslashes($_POST["flickr-javascript"]));
		
		if (!empty($newoptions["username"]) && $newoptions["username"] != $options["username"])
		{
			if (!ereg("http://api.flickr.com/services/feeds", $newoptions["username"])) // Not a feed
			{
				global $flickr_api_key;
				$str = @file_get_contents("http://api.flickr.com/services/rest/?method=flickr.people.findByUsername&api_key=".$flickr_api_key."&username=".urlencode($newoptions["username"])."&format=rest");
				ereg("<rsp stat=\\\"([A-Za-z]+)\\\"", $str, $regs); $findByUsername["stat"] = $regs[1];

				if ($findByUsername["stat"] == "ok")
				{
					ereg("<username>(.+)</username>", $str, $regs);
					$findByUsername["username"] = $regs[1];
					
					ereg("<user id=\\\"(.+)\\\" nsid=\\\"(.+)\\\">", $str, $regs);
					$findByUsername["user"]["id"] = $regs[1];
					$findByUsername["user"]["nsid"] = $regs[2];
					
					$flickr_id = $findByUsername["user"]["nsid"];
					$newoptions["error"] = "";
				}
				else
				{
					$flickr_id = "";
					$newoptions["username"] = ""; // reset
					
					ereg("<err code=\\\"(.+)\\\" msg=\\\"(.+)\\\"", $str, $regs);
					$findByUsername["message"] = $regs[2] . "(" . $regs[1] . ")";
					
					$newoptions["error"] = "Flickr API call failed! (findByUsername returned: ".$findByUsername["message"].")";
				}
				$newoptions["user_id"] = $flickr_id;
			}
			else
			{
				$newoptions["error"] = "";
			}
		}
		elseif (empty($newoptions["username"]))
			$newoptions["error"] = "Flickr RSS or Screen name empty. Please reconfigure.";
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option("widget_quickflickr", $options);
	}
	$title = wp_specialchars($options["title"]);
	$items = wp_specialchars($options["items"]);
	$view = wp_specialchars($options["view"]);
	if ( empty($items) || $items < 1 ) $items = 3;
	
	$before_item = htmlspecialchars($options["before_item"]);
	$after_item = htmlspecialchars($options["after_item"]);
	$before_flickr_widget = htmlspecialchars($options["before_flickr_widget"]);
	$after_flickr_widget = htmlspecialchars($options["after_flickr_widget"]);
	$more_title = wp_specialchars($options["more_title"]);
	
	$target = wp_specialchars($options["target"]);
	$show_titles = wp_specialchars($options["show_titles"]);
	$flickr_username = wp_specialchars($options["username"]);
	
	$thickbox = wp_specialchars($options["thickbox"]);
	$tags = wp_specialchars($options["tags"]);
	$random = wp_specialchars($options["random"]);
	$javascript = wp_specialchars($options["javascript"]);
	
	?>
	<p><label for="flickr-title"><?php _e("Title:"); ?> <input class="widefat" id="flickr-title" name="flickr-title" type="text" value="<?php echo $title; ?>" /></label></p>
	<p><label for="flickr-username"><?php _e("Flickr RSS URL or Screen name:"); ?> <input class="widefat" id="flickr-username" name="flickr-username" type="text" value="<?php echo $flickr_username; ?>" /></label></p>
	<p><label for="flickr-view"><?php _e("Thumbnail, square or medium?"); ?>
			<select class="widefat" id="flickr-view" name="flickr-view">
			<option value="_t" <?=($view=="_t" ? "selected=\"selected\"" : "");?>>Thumbnail</option>
			<option value="_s" <?=($view=="_s" ? "selected=\"selected\"" : "");?>>Square</option>
			<option value="_m" <?=($view=="_m" ? "selected=\"selected\"" : "");?>>Small</option>
			<option value="" <?=($view=="" ? "selected=\"selected\"" : "");?>>Medium</option>
		</select>
	</label></p>
	<p><label for="flickr-before-item"><?php _e("Before item:"); ?> <input class="widefat" id="flickr-before-item" name="flickr-before-item" type="text" value="<?php echo $before_item; ?>" /></label></p>
	<p><label for="flickr-after-item"><?php _e("After item:"); ?> <input class="widefat" id="flickr-after-item" name="flickr-after-item" type="text" value="<?php echo $after_item; ?>" /></label></p>
	<p><label for="flickr-before-flickr-widget"><?php _e("Before widget:"); ?> <input class="widefat" id="flickr-before-flickr-widget" name="flickr-before-flickr-widget" type="text" value="<?php echo $before_flickr_widget; ?>" /></label></p>
	<p><label for="flickr-after-flickr-widget"><?php _e("After widget:"); ?> <input class="widefat" id="flickr-after-flickr-widget" name="flickr-after-flickr-widget" type="text" value="<?php echo $after_flickr_widget; ?>" /></label></p>
	<p><label for="flickr-items"><?php _e("How many items?"); ?> <select class="widefat" id="rss-items" name="rss-items"><?php for ( $i = 1; $i <= 20; ++$i ) echo "<option value=\"$i\" ".($items==$i ? "selected=\"selected\"" : "").">$i</option>"; ?></select></label></p>
	<p><label for="flickr-more-title"><?php _e("More link anchor text:"); ?> <input class="widefat" id="flickr-more-title" name="flickr-more-title" type="text" value="<?php echo $more_title; ?>" /></label></p>
	<p><label for="flickr-tags"><?php _e("Filter by tags (comma seperated):"); ?> <input class="widefat" id="flickr-tags" name="flickr-tags" type="text" value="<?php echo $tags; ?>" /></label></p>
	<p><label for="flickr-target"><input id="flickr-target" name="flickr-target" type="checkbox" value="checked" <?php echo $target; ?> /> <?php _e("Target: _blank"); ?></label></p>
	<p><label for="flickr-show-titles"><input id="flickr-show-titles" name="flickr-show-titles" type="checkbox" value="checked" <?php echo $show_titles; ?> /> <?php _e("Display titles"); ?></label></p>
	<p><label for="flickr-thickbox"><input id="flickr-thickbox" name="flickr-thickbox" type="checkbox" value="checked" <?php echo $thickbox; ?> /> <?php _e("Use Thickbox"); ?></label></p>
	<p><label for="flickr-random"><input id="flickr-random" name="flickr-random" type="checkbox" value="checked" <?php echo $random; ?> /> <?php _e("Random pick"); ?></label></p>
	<p><label for="flickr-javascript"><input id="flickr-javascript" name="flickr-javascript" type="checkbox" value="checked" <?php echo $javascript; ?> /> <?php _e("Use javascript (Careful here!)"); ?></label></p>
	<input type="hidden" id="flickr-submit" name="flickr-submit" value="1" />
	<?php
}

function quickflickr_thickbox_inject() {
    ?>
    <link rel="stylesheet" href="<?= get_option("home"); ?>/<?= WPINC; ?>/js/thickbox/thickbox.css" type="text/css" media="screen" />

    <script type="text/javascript">
    var tb_pathToImage = "<?= get_option("siteurl"); ?>/<?= WPINC; ?>/js/thickbox/loadingAnimation.gif";
    var tb_closeImage = "<?= get_option("siteurl"); ?>/<?= WPINC; ?>/js/thickbox/tb-close.png"
    </script>

    <?php
}

function quickflickr_widgets_init() {
	register_widget_control("Quick Flickr", "widget_quickflickr_control");
	register_sidebar_widget("Quick Flickr", "widget_quickflickr");
	
	$options = get_option("widget_quickflickr");
	if ($options["thickbox"] == "checked")
	{
		//global $wp_version;
		//if ($wp_version == "2.8") wp_enqueue_script("thickbox28", "/wp-includes/js/thickbox/thickbox.js", array("jquery"));
		//else 
		wp_enqueue_script("thickbox");
		
		add_action("wp_head", "quickflickr_thickbox_inject", 10);
	}
	
	if ($options["javascript"] == "checked")
	{
		$quick_flickr_plugin_url = trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'. dirname( plugin_basename(__FILE__) );
		
		wp_enqueue_script('quick_flickr_widget', $quick_flickr_plugin_url.'/quick_flickr_widget.js');
		wp_localize_script('quick_flickr_widget', 'FlickrOptions', $options);
	}
}
add_action("init", "quickflickr_widgets_init");
?>