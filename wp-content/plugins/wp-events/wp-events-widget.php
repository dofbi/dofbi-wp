<?php 
/*-------------------------------------------------------------
 Name:      events_widget_sidebar_init

 Purpose:   Events widget for the sidebar
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function events_widget_sidebar_init() {
	events_textdomain();
	
	if ( !function_exists('register_sidebar_widget') )
		return;
	if ( !function_exists('events_sidebar') )
		return;

	/*-------------------------------------------------------------
	 Name:      events_widget_list
	
	 Purpose:   Show the sidebar listing
	 Receive:   $args
	 Return:    -none-
	-------------------------------------------------------------*/
	function events_widget_list($args) {
		extract($args);

		echo $before_widget;
		$url_parts = parse_url(get_bloginfo('home'));
		echo events_sidebar();
		echo $after_widget;
	}

	/*-------------------------------------------------------------
	 Name:      events_widget_list_control
	
	 Purpose:   Allow settings for the list widget
	 Receive:   -none-
	 Return:    -none-
	-------------------------------------------------------------*/
	function events_widget_list_control() {
	echo '<p>'.sprintf(__('Options are found <a href="%s">here</a>', 'wpevents'), 'options-general.php?page=wp-events4').'<br /><small>'.__('Save your other widget settings first!', 'wpevents').'</small></p>';
	}

	$widget_list_ops = array('classname' => 'events_widget_list', 'description' => __('Add a list of Events to your Sidebar', 'wpevents') );
	wp_register_sidebar_widget('Events-List', __('Events List', 'wpevents'), 'events_widget_list', $widget_list_ops);
	wp_register_widget_control('Events-List', __('Events List', 'wpevents'), 'events_widget_list_control' );
}

/*-------------------------------------------------------------
 Name:      events_widget_dashboard_init

 Purpose:   Add a WordPress dashboard widget
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function events_widget_dashboard_init() {
	wp_add_dashboard_widget( 'events_schedule_widget', __('Events', 'wpevents'), 'events_widget_dashboard' );
	wp_add_dashboard_widget( 'meandmymac_rss_widget', __('Meandmymac.net RSS Feed', 'wpevents'), 'meandmymac_rss_widget' );
}

/*-------------------------------------------------------------
 Name:      events_widget_dashboard

 Purpose:   Create new or edit events from the dashboard
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function events_widget_dashboard() {
	global $wpdb, $userdata, $events_config;
	events_textdomain();

	$timezone = get_option('gmt_offset')*3600;
	$url = get_option('siteurl');
	?>
		<style type="text/css" media="screen">
		#events_schedule_widget h4 {
			font-family: "Lucida Grande", Verdana, Arial, "Bitstream Vera Sans", sans-serif;
			float: left;
			width: 5.5em;
			clear: both;
			font-weight: normal;
			text-align: right;
			padding-top: 5px;
			font-size: 12px;
		}
		
		#events_schedule_widget h4 label {
			margin-right: 10px;
		}
		
		#events_schedule_widget .options-wrap,
		#events_schedule_widget .input-text-wrap,
		#events_schedule_widget .textarea-wrap {
			margin: 0 0 1em 5em;
		}
		</style>
	<?php 

	$SQL2 = "SELECT * FROM ".$wpdb->prefix."events_categories ORDER BY id";
	$categories = $wpdb->get_results($SQL2);
	if($categories) { ?>
		<form method="post" action="index.php" name="events">
	  	   	<input type="hidden" name="events_submit" value="true" />
	    	<input type="hidden" name="events_username" value="<?php echo $userdata->display_name;?>" />
	    	<input type="hidden" name="events_event_id" value="<?php echo $event_edit_id;?>" />

			<h4 id="quick-post-title"><label for="events_title"><?php _e('Title', 'wpevents'); ?></label></h4>
			<div class="input-text-wrap">
				<input type="text" name="events_title" id="title" tabindex="130" autocomplete="off" value="" maxlength="<?php echo $events_config['length'];?>" />
			</div>

			<h4 id="content-label"><label for="events_pre_event"><?php _e('Event', 'wpevents'); ?></label></h4>
			<div class="textarea-wrap">
				<textarea name="events_pre_event" id="content" class="mceEditor" rows="3" cols="15" tabindex="131"></textarea>
			</div>

		    <h4 id="quick-post-title" class="options"><label for="events_sday"><?php _e('When', 'wpevents'); ?></label></h4>
		    <div class="options-wrap">
				<input id="title" name="events_sday" class="search-input" type="text" size="4" maxlength="2" tabindex="132" /> /
				<select name="events_smonth" tabindex="133">
					<option value="01"><?php _e('January', 'wpevents'); ?></option>
					<option value="02"><?php _e('February', 'wpevents'); ?></option>
					<option value="03"><?php _e('March', 'wpevents'); ?></option>
					<option value="04"><?php _e('April', 'wpevents'); ?></option>
					<option value="05"><?php _e('May', 'wpevents'); ?></option>
					<option value="06"><?php _e('June', 'wpevents'); ?></option>
					<option value="07"><?php _e('July', 'wpevents'); ?></option>
					<option value="08"><?php _e('August', 'wpevents'); ?></option>
					<option value="09"><?php _e('September', 'wpevents'); ?></option>
					<option value="10"><?php _e('October', 'wpevents'); ?></option>
					<option value="11"><?php _e('November', 'wpevents'); ?></option>
					<option value="12"><?php _e('December', 'wpevents'); ?></option>
				</select> /
				<input name="events_syear" class="search-input" type="text" size="4" maxlength="4" value="" tabindex="134" />
			</div>

			<h4 id="quick-post-title" class="options"><label for="events_category"><?php _e('Category', 'wpevents'); ?></label></h4>
		    <div class="options-wrap">
				<select name='events_category' tabindex="135">
				<?php foreach($categories as $category) { ?>
				    <option value="<?php echo $category->id; ?>" <?php if($category->id == $edit_event->category) { echo 'selected'; } ?>><?php echo $category->name; ?></option>
			    <?php } ?>
			    </select>
			</div>

			<h4 id="quick-post-title" class="options"><label for="events_priority"><?php _e('Sidebar', 'wpevents'); ?></label></h4>
		    <div class="options-wrap">
				<select name="events_priority" tabindex="136">
				<?php if($edit_event->priority == "yes" OR $edit_event->priority == "") { ?>
					<option value="yes"><?php _e('Yes, show in the sidebar', 'wpevents'); ?></option>
					<option value="no"><?php _e('No, on the event page only', 'wpevents'); ?></option>
				<?php } else { ?>
					<option value="no"><?php _e('No, on the event page only', 'wpevents'); ?></option>
					<option value="yes"><?php _e('Yes, show in the sidebar', 'wpevents'); ?></option>
				<?php } ?>
				</select>
			</div>

			<h4 id="quick-post-title" class="options"><label for="events_archive"><?php _e('Archive', 'wpevents'); ?></label></h4>
		    <div class="options-wrap">
				<select name="events_archive" tabindex="137">
					<?php if($edit_event->archive == "no" OR $edit_event->archive == "") { ?>
					<option value="no"><?php _e('No, delete one day after the event ends', 'wpevents'); ?></option>
					<option value="yes"><?php _e('Yes, save event for the archive', 'wpevents'); ?></option>
					<?php } else { ?>
					<option value="yes"><?php _e('Yes, save event for the archive', 'wpevents'); ?></option>
					<option value="no"><?php _e('No, delete one day after the event ends', 'wpevents'); ?></option>
					<?php } ?>
				</select>
			</div>

	    	<p class="submit">
				<input type="submit" name="submit_save" class="button-primary" value="<?php _e('Save event', 'wpevents'); ?>" tabindex="138" /> <span style="padding-left: 10px;"><a href="admin.php?page=wp-events3"><?php _e('Add event', 'wpevents'); ?> (<?php _e('advanced', 'wpevents'); ?>)</a> | <a href="edit.php?page=wp-events"><?php _e('Manage Events', 'wpevents'); ?></a></span>
	    	</p>
		</form>
	<?php } else { ?>
		<span style="font-style: italic;"><?php _e('You should create at least one category before adding events!', 'wpevents'); ?> <a href="admin.php?page=wp-events3"><?php _e('Add a category now', 'wpevents'); ?></a>.</span>
	<?php } ?>
<?php }

/*-------------------------------------------------------------
 Name:      meandmymac_rss_widget

 Purpose:   Shows the Meandmymac RSS feed on the dashboard
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
if(!function_exists('meandmymac_rss_widget')) {
	function meandmymac_rss_widget() {
		?>
			<style type="text/css" media="screen">
			#meandmymac_rss_widget .text-wrap {
				padding-top: 5px;
				margin: 0.5em;
				display: block;
			}
			#meandmymac_rss_widget .text-wrap .rsserror {
				color: #f00;
				border: none;
			}
			</style>
		<?php meandmymac_rss('http://meandmymac.net/feed/');
	}
}
?>