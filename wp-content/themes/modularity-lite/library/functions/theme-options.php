<?php
$themename = "Theme";
$shortname = "T";
$options = array (

        array(	"name" => "Contact Info",
						"type" => "subhead"),
						
		array(	"name" => "Email Address",
					    "id" => $shortname."_email",
						"desc" => "Your email address.",
					    "std" => "you@email.com",
					    "type" => "text"),
        
        array(	"name" => "Phone Number",
					    "id" => $shortname."_phone",
						"desc" => "Your phone number.",
					    "std" => "1-800-867-5309",
					    "type" => "text"),
						
        array(	"name" => "Layout and Colors",
						"type" => "subhead"),
            
        array(	"name" => "Customize layout and colors",
						"desc" => "If enabled the theme will use the layouts and colors you choose below.",
					    "id" => $shortname."_background_css",
					    "std" => "Disabled",
					    "type" => "select",
					    "options" => array("Disabled","Enabled")),
              
        array(	"name" => "Background color",
					    "id" => $shortname."_background_color",
						"desc" => "Your background color. Use Hex values and leave out the leading #.  <a href='http://www.colorjack.com/sphere/'>Choose a color scheme.</a>",
					    "std" => "111111",
					    "type" => "text"),
					    
		array(	"name" => "Page color",
					    "id" => $shortname."_page_color",
						"desc" => "Your page color. Use Hex values and leave out the leading #.  <a href='http://www.colorjack.com/sphere/'>Choose a color scheme.</a>",
					    "std" => "ffffff",
					    "type" => "text"),
					    
		array(	"name" => "Border color",
					    "id" => $shortname."_border_color",
						"desc" => "Your border and box color. Use Hex values and leave out the leading #.  <a href='http://www.colorjack.com/sphere/'>Choose a color scheme.</a>",
					    "std" => "cccccc",
					    "type" => "text"),
					    
		array(	"name" => "Footer color",
					    "id" => $shortname."_footer_color",
						"desc" => "Your footer background color. Use Hex values and leave out the leading #.  <a href='http://www.colorjack.com/sphere/'>Choose a color scheme.</a>",
					    "std" => "000000",
					    "type" => "text"),
        
        array(	"name" => "Font color",
					    "id" => $shortname."_font_color",
						"desc" => "Your font color. Use Hex values and leave out the leading #.  <a href='http://www.colorjack.com/sphere/'>Choose a color scheme.</a>",
					    "std" => "222222",
					    "type" => "text"),
					    
        array(	"name" => "Link color",
					    "id" => $shortname."_link_color",
						"desc" => "Your link color. Use Hex values and leave out the leading #.  <a href='http://www.colorjack.com/sphere/'>Choose a color scheme.</a>",
					    "std" => "428ce7",
					    "type" => "text"),
        
        array(	"name" => "Link hover color",
					    "id" => $shortname."_hover_color",
						"desc" => "Your link hover color. Use Hex values and leave out the leading #.  <a href='http://www.colorjack.com/sphere/'>Choose a color scheme.</a>",
					    "std" => "666666",
					    "type" => "text"),
                       
        array(	"name" => "Slideshow Options",
						"type" => "subhead"),
            
        array(	"name" => "Slideshow On/Off",
						"desc" => "If you want a slideshow on the homepage, enable this option.  By default, the slideshow pulls in the five photos in the images/slideshow/ folder inside your theme folder.  Replace the images with your own (950 pixels wide max, keep filenames the same). If you prefer to manage your slideshow images from within a Wordpress post, you can delete -static.php from the slideshow section on index.php, which would pull in the slideshow.php file instead of the slideshow-static.php file.  The slideshow.php file pulls in the latest photo uploaded using the 'Add Media' button into each of the latest five posts in the category that you choose on the Homepage Settings page.  You will then need to paste the url to your slideshow images to a custom field key called 'slideshow'. And lastly, don't forget to set the height of the slideshow images below.",
			    		"id" => $shortname."_slideshow_state",
			    		"std" => "On",
			    		"type" => "select",
			    		"options" => array("Off", "On")),
			    		
		array(	"name" => "Slideshow height",
					    "id" => $shortname."_slideshow_height",
						"desc" => "The height of your slideshow images on the homepage. If you want 35mm proportional images, enter 630 in the box above.  The width is fixed at 950px.",
					    "std" => "425",
					    "type" => "text"),
			    		
		array(	"name" => "Sidebar Options",
						"type" => "subhead"),
            
        array(	"name" => "Sidebar On/Off",
						"desc" => "If you want a sidebar on the right side of your site, enable this option.  By default, the sidebar is off, making the site a one-column 950px wide theme.",
			    		"id" => $shortname."_sidebar_state",
			    		"std" => "On",
			    		"type" => "select",
			    		"options" => array("Off", "On")),
              				
		array(	"name" => "Welcome Box",
						"type" => "subhead"),
            
        array(	"name" => "Welcome Box On/Off",
						"desc" => "Toggle the welcome box.  The welcome box appears just below your masthead and just above all content on the front page only.",
			    		"id" => $shortname."_welcomebox_state",
			    		"std" => "Off",
			    		"type" => "select",
			    		"options" => array("Off", "On")),
              
        array(	"name" => "Welcome Title",
					    "id" => $shortname."_welcomebox_title",
              "desc" => "The title of your welcome message.",
					    "std" => "Howdy folks!",
					    "type" => "text"),
        
        array(	"name" => "Welcome Message",
						"id" => $shortname."_welcomebox_content",
						"desc" => "Some HTML in the message is okay, including <code>&#60;p&#62;</code>, <code>&#60;b&#62;</code>, and <code>&#60;i&#62;</code> tags.",
						"std" => "<p>Hi, this is a quick message to introduce people to your site.  It can be short or long and contain some HTML.</p>",
						"type" => "textarea",
						"options" => array("rows" => "8",
										   "cols" => "70") ),
				
        array(	"name" => "Footer",
						"type" => "subhead"),
		
				
				array(	"name" => "Tracking code",
						"id" => $shortname."_tracking_code",
						"desc" => "If you use Google Analytics or need any other tracking script in your footer just copy and paste it here.<br /> The script will be inserted before the closing <code>&#60;/body&#62;</code> tag.",
						"std" => "",
						"type" => "textarea",
						"options" => array("rows" => "5",
										   "cols" => "40") ),
		  );

function mytheme_add_admin() {

    global $themename, $shortname, $options;

    if ( $_GET['page'] == basename(__FILE__) ) {
    
        if ( 'save' == $_REQUEST['action'] ) {

                foreach ($options as $value) {
                    update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }

                foreach ($options as $value) {
                    if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }

                header("Location: themes.php?page=theme-options.php&saved=true");
                die;

        } else if( 'reset' == $_REQUEST['action'] ) {

            foreach ($options as $value) {
                delete_option( $value['id'] ); }

            header("Location: themes.php?page=theme-options.php&reset=true");
            die;

        }
    }

    add_theme_page($themename." Options", "$themename Options", 'edit_themes', basename(__FILE__), 'mytheme_admin');

}

//add_theme_page($themename . 'Header Options', 'Header Options', 'edit_themes', basename(__FILE__), 'headimage_admin');

function headimage_admin(){
	
}

function mytheme_admin() {

    global $themename, $shortname, $options;

    if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
    if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';
    
?>
<div class="wrap">
<h2 class="updatehook" style=" padding-top: 20px; font-size: 2.8em;"><?php echo $themename; ?> Options</h2>
<div class="updated" style="margin: 15px 10px 15px 25px;"><p style="line-height: 1.6em; font-size: 1.2em; width: 75%;">On this page you can modify background colors, link colors, add your contact info, welcome message and tracking code.  If you have questions, please visit the <a href="http://graphpaperpress.com/forums/">forums</a> at <a href="http://graphpaperpress.com">GraphPaperPress.com</a>.  Happy publishing!</p>
</div>
<div class="error" style="margin: 15px 10px 15px 25px;"><h2>Introducing...Totally Rad Homepage Options</h2>
<p style="line-height: 1.6em; font-size: 1.2em; width: 75%;">You can download and install additional applications for this theme at <a href="http://graphpaperpress.com">GraphPaperPress.com</a>.  You can add things like a homepage slideshow, a Mac-style image slider, a magazine-style front page, plus much, much more.</p>
</div>
<form method="post">

<table class="form-table">

<?php foreach ($options as $value) { 
	
	switch ( $value['type'] ) {
		case 'subhead':
		?>
			</table>
			
			<h3><?php echo $value['name']; ?></h3>
			
			<table class="form-table">
		<?php
		break;
		case 'text':
		option_wrapper_header($value);
		?>
		        <input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo get_settings( $value['id'] ); } else { echo $value['std']; } ?>" />
		<?php
		option_wrapper_footer($value);
		break;
		
		case 'select':
		option_wrapper_header($value);
		?>
	            <select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
	                <?php foreach ($value['options'] as $option) { ?>
	                <option <?php if ( get_settings( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
	                <?php } ?>
	            </select>
		<?php
		option_wrapper_footer($value);
		break;
		
		case 'textarea':
		$ta_options = $value['options'];
		option_wrapper_header($value);
		?>
				<textarea name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" cols="<?php echo $ta_options['cols']; ?>" rows="<?php echo $ta_options['rows']; ?>"><?php 
				if( get_settings($value['id']) != "") {
						echo stripslashes(get_settings($value['id']));
					}else{
						echo stripslashes($value['std']);
				}?></textarea>
		<?php
		option_wrapper_footer($value);
		break;

		case "radio":
		option_wrapper_header($value);
		
 		foreach ($value['options'] as $key=>$option) { 
				$radio_setting = get_settings($value['id']);
				if($radio_setting != ''){
		    		if ($key == get_settings($value['id']) ) {
						$checked = "checked=\"checked\"";
						} else {
							$checked = "";
						}
				}else{
					if($key == $value['std']){
						$checked = "checked=\"checked\"";
					}else{
						$checked = "";
					}
				}?>
	            <input type="radio" name="<?php echo $value['id']; ?>" value="<?php echo $key; ?>" <?php echo $checked; ?> /><?php echo $option; ?><br />
		<?php 
		}
		 
		option_wrapper_footer($value);
		break;
		
		case "checkbox":
		option_wrapper_header($value);
						if(get_settings($value['id'])){
							$checked = "checked=\"checked\"";
						}else{
							$checked = "";
						}
					?>
		            <input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />
		<?php
		option_wrapper_footer($value);
		break;

		default:

		break;
	}
}
?>

</table>

<p class="submit">
<input name="save" type="submit" value="Save changes" />    
<input type="hidden" name="action" value="save" />
</p>
</form>
<form method="post">
<p class="submit">
<input name="reset" type="submit" value="Reset" />
<input type="hidden" name="action" value="reset" />
</p>
</form>
<?php
}

function option_wrapper_header($values){
	?>
	<tr valign="top"> 
	    <th scope="row"><?php echo $values['name']; ?>:</th>
	    <td>
	<?php
}
function option_wrapper_footer($values){
	?>
		<br /><br />
		<?php echo $values['desc']; ?>
	    </td>
	</tr>
	<?php 
}
add_action('admin_menu', 'mytheme_add_admin'); 
?>