<?php 
/*
Plugin Name: Events
Plugin URI: http://meandmymac.net/plugins/events/
Description: Enables you to show a list of events with a static countdown to date. Sidebar widget and page template options. And more...
Author: Arnan de Gans
Version: 2.1.1
Author URI: http://meandmymac.net/
*/

#---------------------------------------------------
# Load other plugin files and configuration
#---------------------------------------------------
include_once(ABSPATH.'wp-content/plugins/wp-events/wp-events-setup.php');
include_once(ABSPATH.'wp-content/plugins/wp-events/wp-events-functions.php');
include_once(ABSPATH.'wp-content/plugins/wp-events/wp-events-manage.php');
include_once(ABSPATH.'wp-content/plugins/wp-events/wp-events-widget.php');

register_activation_hook(__FILE__, 'events_activate');
register_deactivation_hook(__FILE__, 'events_deactivate');
events_check_config();
events_clear_old();
setlocale(LC_ALL, get_locale());

add_action('init', 'events_textdomain'); 
add_action('widgets_init', 'events_widget_sidebar_init');
add_action('wp_dashboard_setup', 'events_widget_dashboard_init');
add_action('admin_menu', 'events_dashboard', 1);
add_shortcode('events_show', 'events_show');

if (isset($_POST['events_submit'])) 			add_action('init', 'events_insert_input');
if (isset($_POST['events_category_submit'])) 	add_action('init', 'events_insert_category');
if (isset($_POST['delete_events']) OR isset($_POST['delete_categories'])) add_action('init', 'events_request_delete');
if (isset($_POST['events_submit_general'])) 	add_action('init', 'events_general_submit');
if (isset($_POST['events_submit_templates'])) 	add_action('init', 'events_templates_submit');
if (isset($_POST['events_submit_language'])) 	add_action('init', 'events_language_submit');
if (isset($_POST['events_uninstall'])) 			add_action('init', 'events_plugin_uninstall');

$events_config = get_option('events_config');
$events_template = get_option('events_template');
$events_language = get_option('events_language');

/*-------------------------------------------------------------
 Name:      events_dashboard

 Purpose:   Add pages to admin menus
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function events_dashboard() {
	global $events_config;

	add_object_page(__('Events', 'wpevents'), __('Events', 'wpevents'), $events_config['editlevel'], 'wp-events', 'events_manage');
		add_submenu_page('wp-events', __('Events', 'wpevents').' > '.__('Manage', 'wpevents'), __('Manage Events', 'wpevents'), $events_config['editlevel'], 'wp-events', 'events_manage');
		add_submenu_page('wp-events', __('Events', 'wpevents').' > '.__('Add/Edit', 'wpevents'), __('Add|Edit Event', 'wpevents'), $events_config['editlevel'], 'wp-events2', 'events_schedule');
		add_submenu_page('wp-events', __('Events', 'wpevents').' > '.__('Categories', 'wpevents'), __('Manage Categories', 'wpevents'), $events_config['editlevel'], 'wp-events3', 'events_categories');

	add_options_page(__('Events', 'wpevents'), __('Events', 'wpevents'), 'manage_options', 'wp-events4', 'events_options');
}

/*-------------------------------------------------------------
 Name:      events_manage

 Purpose:   Admin management page
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function events_manage() {
	global $wpdb, $events_config;

	$action = $_GET['action'];
	if(isset($_POST['order'])) {
		$order = $_POST['order'];
	} else {
		$order = 'thetime DESC';
	} ?>
	<div class="wrap">
		<h2><?php _e('Manage Events', 'wpevents'); ?></h2>

		<?php if ($action == 'delete-event') { ?>
			<div id="message" class="updated fade"><p><?php _e('Event <strong>deleted</strong>', 'wpevents'); ?></p></div>
		<?php } else if ($action == 'updated') { ?>
			<div id="message" class="updated fade"><p><?php _e('Event <strong>updated</strong>', 'wpevents'); ?></p></div>
		<?php } else if ($action == 'no_access') { ?>
			<div id="message" class="updated fade"><p><?php _e('Action prohibited', 'wpevents'); ?></p></div>
		<?php } ?>

		<form name="events" id="post" method="post" action="admin.php?page=wp-events">
		<div class="tablenav">

			<div class="alignleft actions">
				<input onclick="return confirm('<?php _e('You are about to delete one or more events!', 'wpevents');?>\n<?php _e('[OK] to continue, [Cancel] to stop.', 'wpevents'); ?>')" type="submit" value="<?php _e('Delete events', 'wpevents'); ?>" name="delete_events" class="button-secondary delete" />
				<select name='order'>
			        <option value="thetime DESC" <?php if($order == "thetime DESC") { echo 'selected'; } ?>><?php _e('by date', 'wpevents'); ?> (<?php _e('descending', 'wpevents'); ?>, <?php _e('default', 'wpevents'); ?>)</option>
			        <option value="thetime ASC" <?php if($order == "thetime ASC") { echo 'selected'; } ?>><?php _e('by date', 'wpevents'); ?> (<?php _e('ascending', 'wpevents'); ?>)</option>
			        <option value="ID ASC" <?php if($order == "ID ASC") { echo 'selected'; } ?>><?php _e('in the order you made them', 'wpevents'); ?> (<?php _e('ascending', 'wpevents'); ?>)</option>
			        <option value="ID DESC" <?php if($order == "ID DESC") { echo 'selected'; } ?>><?php _e('in the order you made them', 'wpevents'); ?> (<?php _e('descending', 'wpevents'); ?>)</option>
			        <option value="title ASC" <?php if($order == "title ASC") { echo 'selected'; } ?>><?php _e('by title', 'wpevents'); ?> (A-Z)</option>
			        <option value="title DESC" <?php if($order == "title DESC") { echo 'selected'; } ?>><?php _e('by title', 'wpevents'); ?> (Z-A)</option>
			        <option value="category ASC" <?php if($order == "category ASC") { echo 'selected'; } ?>><?php _e('by category', 'wpevents'); ?> (A-Z)</option>
			        <option value="category DESC" <?php if($order == "category DESC") { echo 'selected'; } ?>><?php _e('by category', 'wpevents'); ?> (Z-A)</option>
				</select>
				<input type="submit" id="post-query-submit" value="<?php _e('Sort', 'wpevents'); ?>" class="button-secondary" />
			</div>
		</div>
		<table class="widefat">
  			<thead>
  				<tr>
					<th scope="col" class="check-column">&nbsp;</th>
					<th scope="col" width="15%"><?php _e('Date', 'wpevents'); ?></th>
					<th scope="col"><?php _e('Title', 'wpevents'); ?></th>
					<th scope="col" width="10%"><?php _e('Category', 'wpevents'); ?></th>
					<th scope="col" width="20%"><?php _e('Starts when', 'wpevents'); ?></th>
				</tr>
  			</thead>
  			<tbody>
		<?php 
		if(events_mysql_table_exists($wpdb->prefix.'events')) {
			$events = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."events` ORDER BY $order");
			if ($events) {
				foreach($events as $event) {
					$cat = $wpdb->get_row("SELECT name FROM " . $wpdb->prefix . "events_categories WHERE id = '".$event->category."'");
					$class = ('alternate' != $class) ? 'alternate' : ''; ?>
				    <tr id='event-<?php echo $event->id; ?>' class=' <?php echo $class; ?>'>
						<th scope="row" class="check-column"><input type="checkbox" name="eventcheck[]" value="<?php echo $event->id; ?>" /></th>
						<td><?php echo gmdate('d-m-Y H:i', $event->thetime);?></td>
						<td><strong><a class="row-title" href="<?php echo get_option('siteurl').'/wp-admin/admin.php?page=wp-events2&amp;edit_event='.$event->id;?>" title="<?php _e('Edit', 'wpevents'); ?>"><?php echo stripslashes(html_entity_decode($event->title));?></a></strong></td>
						<td><?php echo $cat->name; ?></td>
						<td><?php echo events_countdown($event->thetime, $event->theend, $event->post_message, $event->allday); ?></td>
					</tr>
	 			<?php } ?>
	 		<?php } else { ?>
				<tr id='no-id'><td scope="row" colspan="5"><em><?php _e('No Events yet!', 'wpevents'); ?></em></td></tr>
			<?php 
			}
		} else { ?>
			<tr id='no-id'><td scope="row" colspan="5"><span style="font-weight: bold; color: #f00;"><?php _e('There was an error locating the main database table for Events.', 'wpevents'); _e('Please deactivate and re-activate Events from the plugin page!!', 'wpevents'); ?><br /><?php echo sprintf(__('If this does not solve the issue please seek support at <a href="%s">%s</a>.', 'wpevents'), 'http://forum.at.meandmymac.net', 'http://forum.at.meandmymac.net'); ?></span></td></tr>

		<?php }	?>
			</tbody>
		</table>
		</form>

		<br class="clear" />
		<?php events_credits(); ?>

	</div>
	<?php 
}

/*-------------------------------------------------------------
 Name:      events_categories

 Purpose:   Admin categories page
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function events_categories() {
	global $wpdb, $events_config;

	$action = $_GET['action'];
	if($_GET['edit_cat']) $cat_edit_id = $_GET['edit_cat'];

	if(isset($_POST['catorder'])) {
		$catorder = $_POST['catorder'];
	} else {
		$catorder = 'id ASC';
	} ?>

	<div class="wrap">
		<h2><?php _e('Categories', 'wpevents'); ?></h2>

		<?php if ($action == 'delete-category') { ?>
			<div id="message" class="updated fade"><p><?php _e('Category <strong>deleted</strong>', 'wpevents'); ?></p></div>
		<?php } else if ($action == 'no_access') { ?>
			<div id="message" class="updated fade"><p><?php _e('Action prohibited', 'wpevents'); ?></p></div>
		<?php } else if ($action == 'category_new') { ?>
			<div id="message" class="updated fade"><p><?php _e('Category <strong>created</strong>', 'wpevents'); ?>. <a href="admin.php?page=wp-events2"><?php _e('Add events now', 'wpevents'); ?></a></p></div>
		<?php } else if ($action == 'category_edit') { ?>
			<div id="message" class="updated fade"><p><?php _e('Category <strong>updated</strong>', 'wpevents'); ?></p></div>
		<?php } else if ($action == 'category_field_error') { ?>
			<div id="message" class="updated fade"><p><?php _e('No category name filled in', 'wpevents'); ?></p></div>
		<?php } ?>

		<?php if(!$cat_edit_id) { ?>
			<form name="groups" id="post" method="post" action="admin.php?page=wp-events3">
			<div class="tablenav">
				<div class="alignleft actions">
					<input onclick="return confirm('<?php _e('You are about to delete one or more categories! Make sure there are no events in those categories or they will not show on the website', 'wpevents'); ?>\n<?php _e('[OK] to continue, [Cancel] to stop.', 'wpevents'); ?>')" type="submit" value="<?php _e('Delete category', 'wpevents'); ?>" name="delete_categories" class="button-secondary delete" />
					<select name='catorder'>
				        <option value="id ASC" <?php if($catorder == "id ASC") { echo 'selected'; } ?>><?php _e('in the order you made them', 'wpevents'); ?> (<?php _e('ascending', 'wpevents'); ?>)</option>
				        <option value="id DESC" <?php if($catorder == "id DESC") { echo 'selected'; } ?>><?php _e('in the order you made them', 'wpevents'); ?> (<?php _e('descending', 'wpevents'); ?>)</option>
				        <option value="name ASC" <?php if($catorder == "name ASC") { echo 'selected'; } ?>><?php _e('by name', 'wpevents'); ?> (A-Z)</option>
				        <option value="name DESC" <?php if($catorder == "name DESC") { echo 'selected'; } ?>><?php _e('by name', 'wpevents'); ?> (Z-A)</option>
					</select>
					<input type="submit" id="post-query-submit" value="<?php _e('Sort', 'wpevents'); ?>" class="button-secondary" />
				</div>
			</div>
	
			<table class="widefat" style="margin-top: .5em">
	  			<thead>
	  				<tr>
						<th scope="col" class="check-column">&nbsp;</th>
						<th scope="col" width="5%"><center><?php _e('ID', 'wpevents'); ?></center></th>
						<th scope="col"><?php _e('Name', 'wpevents'); ?></th>
						<th scope="col" width="10%"><center><?php _e('Events', 'wpevents'); ?></center></th>
					</tr>
	  			</thead>
	  			<tbody>
			<?php 
			if(events_mysql_table_exists($wpdb->prefix.'events_categories')) {
				$categories = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "events_categories ORDER BY $catorder");
				if ($categories) {
					foreach($categories as $category) {
						$count = $wpdb->get_var("SELECT COUNT(category) FROM " . $wpdb->prefix . "events WHERE category = '". $category->id."' GROUP BY category");
						$class = ('alternate' != $class) ? 'alternate' : ''; ?>
					    <tr id='group-<?php echo $category->id; ?>' class=' <?php echo $class; ?>'>
							<th scope="row" class="check-column"><input type="checkbox" name="categorycheck[]" value="<?php echo $category->id; ?>" /></th>
							<td><center><?php echo $category->id;?></center></td>
							<td><strong><a class="row-title" href="<?php echo get_option('siteurl').'/wp-admin/admin.php?page=wp-events3&amp;edit_cat='.$category->id;?>" title="<?php _e('Edit', 'wpevents'); ?>"><?php echo $category->name;?></a></strong></td>
							<td><center><?php echo $count;?></center></td>
						</tr>
		 			<?php } ?>
				<?php 
				}
			} else { ?>
				<tr id='no-id'><td scope="row" colspan="4"><span style="font-weight: bold; color: #f00;"><?php _e('There was an error locating the database table for the Events categories.', 'wpevents'); _e('Please deactivate and re-activate Events from the plugin page!!', 'wpevents'); ?><br /><?php echo sprintf(__('If this does not solve the issue please seek support at <a href="%s">%s</a>.', 'wpevents'), 'http://forum.at.meandmymac.net', 'http://forum.at.meandmymac.net'); ?></a></span></td></tr>
			<?php }	?>
				<tr id='category-new'>
					<th scope="row" class="check-column">&nbsp;</th>
					<td colspan="3"><input name="events_cat" type="text" class="search-input" size="40" maxlength="255" value="" /> <input type="submit" id="post-query-submit" name="events_category_submit" value="<?php _e('Add', 'wpevents'); ?>" class="button-secondary" /></td>
				</tr>
	 		</tbody>
			</table>
			</form>

			<br class="clear" />
			<?php events_credits(); ?>

		<?php } else { ?>

			<?php 
			$edit_cat = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."events_categories` WHERE `id` = '$cat_edit_id'");

			if ($message == 'field_error') { ?>
				<div id="message" class="updated fade"><p><?php _e('Please fill in a name for your category!', 'wpevents'); ?></p></div>
			<?php }

			if($cat_edit_id > 0) { ?>
			  	<form method="post" action="admin.php?page=wp-events3">
			    	<input type="hidden" name="events_id" value="<?php echo $cat_edit_id;?>" />

			    	<table class="widefat" style="margin-top: .5em">

						<thead>
						<tr valign="top">
							<th colspan="2" bgcolor="#DDD"><?php _e('You can change the name of the category here. The ID stays the same!', 'wpevents'); ?></th>
						</tr>
						</thead>

						<tbody>
				      	<tr>
					        <th scope="row" width="25%"><?php _e('ID', 'wpevents'); ?>:</th>
					        <td><?php echo $edit_cat->id;?></td>
				      	</tr>
				      	<tr>
					        <th scope="row" width="25%"><?php _e('Name', 'wpevents'); ?>:</th>
					        <td><input tabindex="1" name="events_cat" type="text" size="67" class="search-input" autocomplete="off" value="<?php echo $edit_cat->name;?>" /></td>
				      	</tr>
				      	</tbody>

					</table>

			    	<p class="submit">
						<input tabindex="2" type="submit" name="events_category_submit" class="button-primary" value="<?php _e('Save Category', 'wpevents'); ?>" />
						<a href="admin.php?page=wp-events3" class="button"><?php _e('Cancel', 'wpevents'); ?></a>
			    	</p>

			  	</form>
			<?php } else { ?>
			    <table class="widefat" style="margin-top: .5em">
			    	<thead>
					<tr valign="top">
						<th><?php _e('Error!', 'wpevents'); ?></th>
					</tr>
					</thead>

					<tbody>
			      	<tr>
				        <td><?php _e('No valid group ID specified!', 'wpevents'); ?> <a href="admin.php?page=wp-events3"><?php _e('Continue', 'wpevents'); ?></a>.</td>
			      	</tr>
			      	</tbody>
				</table>
			<?php } ?>
		<?php } ?>
	</div>
<?php 
}

/*-------------------------------------------------------------
 Name:      events_schedule

 Purpose:   Create new or edit events
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function events_schedule() {
	global $wpdb, $userdata, $events_config;
	setlocale(LC_TIME, 'fr_FR.utf8');
	$timezone = get_option('gmt_offset')*3600;
	$thetime 	= current_time('timestamp');

	$action = $_GET['action'];
	if($_GET['edit_event']) {
		$event_edit_id = $_GET['edit_event'];
	}
	if($_GET['duplicate']) {
		$event_edit_id = $_GET['duplicate'];
	}
	?>
	<div class="wrap">
		<?php if(!$event_edit_id) { ?>
		<h2><?php _e('Add event', 'wpevents'); ?></h2>
		<?php 
			list($sday, $smonth, $syear) = split(" ", gmdate("d m Y", $thetime));
		} else { ?>
		<h2><?php _e('Edit event', 'wpevents'); ?></h2>
		<?php 
			$edit_event = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."events` WHERE `id` = $event_edit_id");
			list($sday, $smonth, $syear, $shour, $sminute) = split(" ", gmdate("d m Y H i", $edit_event->thetime));
			list($eday, $emonth, $eyear, $ehour, $eminute) = split(" ", gmdate("d m Y H i", $edit_event->theend));
		}

		if ($action == 'created') { ?>
			<div id="message" class="updated fade"><p><?php _e('Event <strong>created</strong>', 'wpevents'); ?> | <a href="admin.php?page=wp-events"><?php _e('Manage Events', 'wpevents'); ?></a></p></div>
		<?php } else if ($action == 'duplicated') { ?>
			<div id="message" class="updated fade"><p><?php _e('Event <strong>duplicated</strong>', 'wpevents'); ?></p></div>
		<?php } else if ($action == 'no_access') { ?>
			<div id="message" class="updated fade"><p><?php _e('Action prohibited', 'wpevents'); ?></p></div>
		<?php } else if ($action == 'field_error') { ?>
			<div id="message" class="updated fade"><p><?php _e('Not all fields met the requirements', 'wpevents'); ?></p></div>
		<?php }

		$SQL2 = "SELECT * FROM ".$wpdb->prefix."events_categories ORDER BY id";
		$categories = $wpdb->get_results($SQL2);
		if($categories) { ?>
		  	<form method="post" action="admin.php?page=wp-events2">
		  	   	<input type="hidden" name="events_submit" value="true" />
		    	<input type="hidden" name="events_username" value="<?php echo $userdata->display_name;?>" />
		    	<input type="hidden" name="events_event_id" value="<?php echo $event_edit_id;?>" />
				<?php if($event_edit_id) { ?>
		    	<input type="hidden" name="events_repeat_int" value="0" />
				<?php } ?>
		
		    	<table class="widefat" style="margin-top: .5em">

					<thead>
					<tr valign="top" id="quicktags">
						<td colspan="3"><?php _e('Enter your event details below.', 'wpevents'); ?></td>
					</tr>
			      	</thead>

			      	<tbody>
			      	<tr>
				        <th scope="row"><?php _e('Title', 'wpevents'); ?>:</th>
				        <td><input name="events_title" class="search-input" type="text" size="55" maxlength="<?php echo $events_config['length'];?>" value="<?php echo $edit_event->title;?>" tabindex="1" autocomplete="off" /><br /><em><?php echo sprintf(__('Maximum %s characters.', 'wpevents'), $events_config['length']); ?></em></td>
				        <td width="35%"><input type="checkbox" name="events_title_link" <?php if($edit_event->title_link == 'Y') { ?>checked="checked" <?php } ?> tabindex="2" /> <?php _e('Make title a link.', 'wpevents');?><br /><?php _e('Use the field below.', 'wpevents'); ?><br /><input type="checkbox" name="events_allday" <?php if($edit_event->allday == 'Y') { ?>checked="checked" <?php } ?> tabindex="3" /> <?php _e('All-day event.', 'wpevents'); ?></td>
					</tr>
					</tbody>

				</table>

				<br class="clear" />
				<div id="postdivrich" class="postarea">
					<?php events_editor($edit_event->pre_message, 'content', 'events_title', false, 4); ?>
				</div>

				<br class="clear" />
		    	<table class="widefat" style="margin-top: .5em">

					<thead>
					<tr valign="top" id="quicktags">
						<td colspan="4"><?php _e('Please note that the time field uses a 24 hour clock. This means that 22:00 hour is actually 10:00pm.', 'wpevents'); ?><br /><?php _e('Hint: If you\'re used to the AM/PM system and the event takes place/starts after lunch just add 12 hours.', 'wpevents'); ?></td>
					</tr>
			      	</thead>

			      	<tbody>
			      	<tr>
				        <th scope="row"><?php _e('Startdate', 'wpevents'); ?> <?php _e('Day', 'wpevents'); ?>/<?php _e('Month', 'wpevents'); ?>/<?php _e('Year', 'wpevents'); ?>:</th>
				        <td width="25%">
				        	<input id="title" name="events_sday" class="search-input" type="text" size="4" maxlength="2" value="<?php echo $sday;?>" tabindex="5" /> /
							<select name="events_smonth" tabindex="6">
								<option value="01" <?php if($smonth == "01") { echo 'selected'; } ?>><?php _e('January'); ?></option>
								<option value="02" <?php if($smonth == "02") { echo 'selected'; } ?>><?php _e('February'); ?></option>
								<option value="03" <?php if($smonth == "03") { echo 'selected'; } ?>><?php _e('March'); ?></option>
								<option value="04" <?php if($smonth == "04") { echo 'selected'; } ?>><?php _e('April'); ?></option>
								<option value="05" <?php if($smonth == "05") { echo 'selected'; } ?>><?php _e('May'); ?></option>
								<option value="06" <?php if($smonth == "06") { echo 'selected'; } ?>><?php _e('June'); ?></option>
								<option value="07" <?php if($smonth == "07") { echo 'selected'; } ?>><?php _e('July'); ?></option>
								<option value="08" <?php if($smonth == "08") { echo 'selected'; } ?>><?php _e('August'); ?></option>
								<option value="09" <?php if($smonth == "09") { echo 'selected'; } ?>><?php _e('September'); ?></option>
								<option value="10" <?php if($smonth == "10") { echo 'selected'; } ?>><?php _e('October'); ?></option>
								<option value="11" <?php if($smonth == "11") { echo 'selected'; } ?>><?php _e('November'); ?></option>
								<option value="12" <?php if($smonth == "12") { echo 'selected'; } ?>><?php _e('December'); ?></option>
							</select> /
							<input name="events_syear" class="search-input" type="text" size="4" maxlength="4" value="<?php echo $syear;?>" tabindex="6" />
						</td>
				        <th scope="row"><?php _e('Hour', 'wpevents'); ?>/<?php _e('Minutes', 'wpevents'); ?> (<?php _e('optional', 'wpevents'); ?>):</th>
				        <td width="25%"><select name="events_shour" tabindex="7">
				        <option value="00" <?php if($shour == "00") { echo 'selected'; } ?>>00</option>
				        <option value="01" <?php if($shour == "01") { echo 'selected'; } ?>>01</option>
				        <option value="02" <?php if($shour == "02") { echo 'selected'; } ?>>02</option>
				        <option value="03" <?php if($shour == "03") { echo 'selected'; } ?>>03</option>
				        <option value="04" <?php if($shour == "04") { echo 'selected'; } ?>>04</option>
				        <option value="05" <?php if($shour == "05") { echo 'selected'; } ?>>05</option>
				        <option value="06" <?php if($shour == "06") { echo 'selected'; } ?>>06</option>
				        <option value="07" <?php if($shour == "07") { echo 'selected'; } ?>>07</option>
				        <option value="08" <?php if($shour == "08") { echo 'selected'; } ?>>08</option>
				        <option value="09" <?php if($shour == "09") { echo 'selected'; } ?>>09</option>
				        <option value="10" <?php if($shour == "10") { echo 'selected'; } ?>>10</option>
				        <option value="11" <?php if($shour == "11") { echo 'selected'; } ?>>11</option>
				        <option value="12" <?php if($shour == "12") { echo 'selected'; } ?>>12</option>
				        <option value="13" <?php if($shour == "13") { echo 'selected'; } ?>>13</option>
				        <option value="14" <?php if($shour == "14") { echo 'selected'; } ?>>14</option>
				        <option value="15" <?php if($shour == "15") { echo 'selected'; } ?>>15</option>
				        <option value="16" <?php if($shour == "16") { echo 'selected'; } ?>>16</option>
				        <option value="17" <?php if($shour == "17") { echo 'selected'; } ?>>17</option>
				        <option value="18" <?php if($shour == "18") { echo 'selected'; } ?>>18</option>
				        <option value="19" <?php if($shour == "19") { echo 'selected'; } ?>>19</option>
				        <option value="20" <?php if($shour == "20") { echo 'selected'; } ?>>20</option>
				        <option value="21" <?php if($shour == "21") { echo 'selected'; } ?>>21</option>
				        <option value="22" <?php if($shour == "22") { echo 'selected'; } ?>>22</option>
				        <option value="23" <?php if($shour == "23") { echo 'selected'; } ?>>23</option>
					</select> / <select name="events_sminute" tabindex="8">
				        <option value="00" <?php if($sminute == "00") { echo 'selected'; } ?>>00</option>
				        <option value="01" <?php if($sminute == "01") { echo 'selected'; } ?>>01</option>
				        <option value="02" <?php if($sminute == "02") { echo 'selected'; } ?>>02</option>
				        <option value="03" <?php if($sminute == "03") { echo 'selected'; } ?>>03</option>
				        <option value="04" <?php if($sminute == "04") { echo 'selected'; } ?>>04</option>
				        <option value="05" <?php if($sminute == "05") { echo 'selected'; } ?>>05</option>
				        <option value="06" <?php if($sminute == "06") { echo 'selected'; } ?>>06</option>
				        <option value="07" <?php if($sminute == "07") { echo 'selected'; } ?>>07</option>
				        <option value="08" <?php if($sminute == "08") { echo 'selected'; } ?>>08</option>
				        <option value="09" <?php if($sminute == "09") { echo 'selected'; } ?>>09</option>
				        <option value="10" <?php if($sminute == "10") { echo 'selected'; } ?>>10</option>
				        <option value="11" <?php if($sminute == "11") { echo 'selected'; } ?>>11</option>
				        <option value="12" <?php if($sminute == "12") { echo 'selected'; } ?>>12</option>
				        <option value="13" <?php if($sminute == "13") { echo 'selected'; } ?>>13</option>
				        <option value="14" <?php if($sminute == "14") { echo 'selected'; } ?>>14</option>
				        <option value="15" <?php if($sminute == "15") { echo 'selected'; } ?>>15</option>
				        <option value="16" <?php if($sminute == "16") { echo 'selected'; } ?>>16</option>
				        <option value="17" <?php if($sminute == "17") { echo 'selected'; } ?>>17</option>
				        <option value="18" <?php if($sminute == "18") { echo 'selected'; } ?>>18</option>
				        <option value="19" <?php if($sminute == "19") { echo 'selected'; } ?>>19</option>
				        <option value="20" <?php if($sminute == "20") { echo 'selected'; } ?>>20</option>
				        <option value="21" <?php if($sminute == "21") { echo 'selected'; } ?>>21</option>
				        <option value="22" <?php if($sminute == "22") { echo 'selected'; } ?>>22</option>
				        <option value="23" <?php if($sminute == "23") { echo 'selected'; } ?>>23</option>
				        <option value="24" <?php if($sminute == "24") { echo 'selected'; } ?>>24</option>
				        <option value="25" <?php if($sminute == "25") { echo 'selected'; } ?>>25</option>
				        <option value="26" <?php if($sminute == "26") { echo 'selected'; } ?>>26</option>
				        <option value="27" <?php if($sminute == "27") { echo 'selected'; } ?>>27</option>
				        <option value="28" <?php if($sminute == "28") { echo 'selected'; } ?>>28</option>
				        <option value="29" <?php if($sminute == "29") { echo 'selected'; } ?>>29</option>
				        <option value="30" <?php if($sminute == "30") { echo 'selected'; } ?>>30</option>
				        <option value="31" <?php if($sminute == "31") { echo 'selected'; } ?>>31</option>
				        <option value="32" <?php if($sminute == "32") { echo 'selected'; } ?>>32</option>
				        <option value="33" <?php if($sminute == "33") { echo 'selected'; } ?>>33</option>
				        <option value="34" <?php if($sminute == "34") { echo 'selected'; } ?>>34</option>
				        <option value="35" <?php if($sminute == "35") { echo 'selected'; } ?>>35</option>
				        <option value="36" <?php if($sminute == "36") { echo 'selected'; } ?>>36</option>
				        <option value="37" <?php if($sminute == "37") { echo 'selected'; } ?>>37</option>
				        <option value="38" <?php if($sminute == "38") { echo 'selected'; } ?>>38</option>
				        <option value="39" <?php if($sminute == "39") { echo 'selected'; } ?>>39</option>
				        <option value="40" <?php if($sminute == "40") { echo 'selected'; } ?>>40</option>
				        <option value="41" <?php if($sminute == "41") { echo 'selected'; } ?>>41</option>
				        <option value="42" <?php if($sminute == "42") { echo 'selected'; } ?>>42</option>
				        <option value="43" <?php if($sminute == "43") { echo 'selected'; } ?>>43</option>
				        <option value="44" <?php if($sminute == "44") { echo 'selected'; } ?>>44</option>
				        <option value="45" <?php if($sminute == "45") { echo 'selected'; } ?>>45</option>
				        <option value="46" <?php if($sminute == "46") { echo 'selected'; } ?>>46</option>
				        <option value="47" <?php if($sminute == "47") { echo 'selected'; } ?>>47</option>
				        <option value="48" <?php if($sminute == "48") { echo 'selected'; } ?>>48</option>
				        <option value="49" <?php if($sminute == "49") { echo 'selected'; } ?>>49</option>
				        <option value="50" <?php if($sminute == "50") { echo 'selected'; } ?>>50</option>
				        <option value="51" <?php if($sminute == "51") { echo 'selected'; } ?>>51</option>
				        <option value="52" <?php if($sminute == "52") { echo 'selected'; } ?>>52</option>
				        <option value="53" <?php if($sminute == "53") { echo 'selected'; } ?>>53</option>
				        <option value="54" <?php if($sminute == "54") { echo 'selected'; } ?>>54</option>
				        <option value="55" <?php if($sminute == "55") { echo 'selected'; } ?>>55</option>
				        <option value="56" <?php if($sminute == "56") { echo 'selected'; } ?>>56</option>
				        <option value="57" <?php if($sminute == "57") { echo 'selected'; } ?>>57</option>
				        <option value="58" <?php if($sminute == "58") { echo 'selected'; } ?>>58</option>
				        <option value="59" <?php if($sminute == "59") { echo 'selected'; } ?>>59</option>
				        <option value="60" <?php if($sminute == "60") { echo 'selected'; } ?>>60</option>
					</select></td>
			      	</tr>
			      	<tr>
				        <th scope="row"><?php _e('Enddate', 'wpevents'); ?> <?php _e('Day', 'wpevents'); ?>/<?php _e('Month', 'wpevents'); ?>/<?php _e('Year', 'wpevents'); ?> (<?php _e('optional', 'wpevents'); ?>):</th>
				        <td width="25%">
				        	<input id="title" name="events_eday" class="search-input" type="text" size="4" maxlength="2" value="<?php echo $eday;?>" tabindex="9" /> /
							<select name="events_emonth" tabindex="10">
								<option value="" <?php if($emonth == "") { echo 'selected'; } ?>>--</option>
								<option value="01" <?php if($emonth == "01") { echo 'selected'; } ?>><?php _e('January', 'wpevents'); ?></option>
								<option value="02" <?php if($emonth == "02") { echo 'selected'; } ?>><?php _e('February', 'wpevents'); ?></option>
								<option value="03" <?php if($emonth == "03") { echo 'selected'; } ?>><?php _e('March', 'wpevents'); ?></option>
								<option value="04" <?php if($emonth == "04") { echo 'selected'; } ?>><?php _e('April', 'wpevents'); ?></option>
								<option value="05" <?php if($emonth == "05") { echo 'selected'; } ?>><?php _e('May', 'wpevents'); ?></option>
								<option value="06" <?php if($emonth == "06") { echo 'selected'; } ?>><?php _e('June', 'wpevents'); ?></option>
								<option value="07" <?php if($emonth == "07") { echo 'selected'; } ?>><?php _e('July', 'wpevents'); ?></option>
								<option value="08" <?php if($emonth == "08") { echo 'selected'; } ?>><?php _e('August', 'wpevents'); ?></option>
								<option value="09" <?php if($emonth == "09") { echo 'selected'; } ?>><?php _e('September', 'wpevents'); ?></option>
								<option value="10" <?php if($emonth == "10") { echo 'selected'; } ?>><?php _e('October', 'wpevents'); ?></option>
								<option value="11" <?php if($emonth == "11") { echo 'selected'; } ?>><?php _e('November', 'wpevents'); ?></option>
								<option value="12" <?php if($emonth == "12") { echo 'selected'; } ?>><?php _e('December', 'wpevents'); ?></option>
							</select> /
							<input name="events_eyear" class="search-input" type="text" size="4" maxlength="4" value="<?php echo $eyear;?>" tabindex="11"/></td>
				        <th scope="row"><?php _e('Hour', 'wpevents'); ?>/<?php _e('Minutes', 'wpevents'); ?> (<?php _e('optional', 'wpevents'); ?>):</th>
				        <td width="25%"><select name="events_ehour" tabindex="12">
				        <option value="" <?php if($ehour == "") { echo 'selected'; } ?>>--</option>
				        <option value="00" <?php if($ehour == "00") { echo 'selected'; } ?>>00</option>
				        <option value="01" <?php if($ehour == "01") { echo 'selected'; } ?>>01</option>
				        <option value="02" <?php if($ehour == "02") { echo 'selected'; } ?>>02</option>
				        <option value="03" <?php if($ehour == "03") { echo 'selected'; } ?>>03</option>
				        <option value="04" <?php if($ehour == "04") { echo 'selected'; } ?>>04</option>
				        <option value="05" <?php if($ehour == "05") { echo 'selected'; } ?>>05</option>
				        <option value="06" <?php if($ehour == "06") { echo 'selected'; } ?>>06</option>
				        <option value="07" <?php if($ehour == "07") { echo 'selected'; } ?>>07</option>
				        <option value="08" <?php if($ehour == "08") { echo 'selected'; } ?>>08</option>
				        <option value="09" <?php if($ehour == "09") { echo 'selected'; } ?>>09</option>
				        <option value="10" <?php if($ehour == "10") { echo 'selected'; } ?>>10</option>
				        <option value="11" <?php if($ehour == "11") { echo 'selected'; } ?>>11</option>
				        <option value="12" <?php if($ehour == "12") { echo 'selected'; } ?>>12</option>
				        <option value="13" <?php if($ehour == "13") { echo 'selected'; } ?>>13</option>
				        <option value="14" <?php if($ehour == "14") { echo 'selected'; } ?>>14</option>
				        <option value="15" <?php if($ehour == "15") { echo 'selected'; } ?>>15</option>
				        <option value="16" <?php if($ehour == "16") { echo 'selected'; } ?>>16</option>
				        <option value="17" <?php if($ehour == "17") { echo 'selected'; } ?>>17</option>
				        <option value="18" <?php if($ehour == "18") { echo 'selected'; } ?>>18</option>
				        <option value="19" <?php if($ehour == "19") { echo 'selected'; } ?>>19</option>
				        <option value="20" <?php if($ehour == "20") { echo 'selected'; } ?>>20</option>
				        <option value="21" <?php if($ehour == "21") { echo 'selected'; } ?>>21</option>
				        <option value="22" <?php if($ehour == "22") { echo 'selected'; } ?>>22</option>
				        <option value="23" <?php if($ehour == "23") { echo 'selected'; } ?>>23</option>
					</select> / <select name="events_eminute" tabindex="13">
				        <option value="" <?php if($eminute == "") { echo 'selected'; } ?>>--</option>
				        <option value="00" <?php if($eminute == "00") { echo 'selected'; } ?>>00</option>
				        <option value="01" <?php if($eminute == "01") { echo 'selected'; } ?>>01</option>
				        <option value="02" <?php if($eminute == "02") { echo 'selected'; } ?>>02</option>
				        <option value="03" <?php if($eminute == "03") { echo 'selected'; } ?>>03</option>
				        <option value="04" <?php if($eminute == "04") { echo 'selected'; } ?>>04</option>
				        <option value="05" <?php if($eminute == "05") { echo 'selected'; } ?>>05</option>
				        <option value="06" <?php if($eminute == "06") { echo 'selected'; } ?>>06</option>
				        <option value="07" <?php if($eminute == "07") { echo 'selected'; } ?>>07</option>
				        <option value="08" <?php if($eminute == "08") { echo 'selected'; } ?>>08</option>
				        <option value="09" <?php if($eminute == "09") { echo 'selected'; } ?>>09</option>
				        <option value="10" <?php if($eminute == "10") { echo 'selected'; } ?>>10</option>
				        <option value="11" <?php if($eminute == "11") { echo 'selected'; } ?>>11</option>
				        <option value="12" <?php if($eminute == "12") { echo 'selected'; } ?>>12</option>
				        <option value="13" <?php if($eminute == "13") { echo 'selected'; } ?>>13</option>
				        <option value="14" <?php if($eminute == "14") { echo 'selected'; } ?>>14</option>
				        <option value="15" <?php if($eminute == "15") { echo 'selected'; } ?>>15</option>
				        <option value="16" <?php if($eminute == "16") { echo 'selected'; } ?>>16</option>
				        <option value="17" <?php if($eminute == "17") { echo 'selected'; } ?>>17</option>
				        <option value="18" <?php if($eminute == "18") { echo 'selected'; } ?>>18</option>
				        <option value="19" <?php if($eminute == "19") { echo 'selected'; } ?>>19</option>
				        <option value="20" <?php if($eminute == "20") { echo 'selected'; } ?>>20</option>
				        <option value="21" <?php if($eminute == "21") { echo 'selected'; } ?>>21</option>
				        <option value="22" <?php if($eminute == "22") { echo 'selected'; } ?>>22</option>
				        <option value="23" <?php if($eminute == "23") { echo 'selected'; } ?>>23</option>
				        <option value="24" <?php if($eminute == "24") { echo 'selected'; } ?>>24</option>
				        <option value="25" <?php if($eminute == "25") { echo 'selected'; } ?>>25</option>
				        <option value="26" <?php if($eminute == "26") { echo 'selected'; } ?>>26</option>
				        <option value="27" <?php if($eminute == "27") { echo 'selected'; } ?>>27</option>
				        <option value="28" <?php if($eminute == "28") { echo 'selected'; } ?>>28</option>
				        <option value="29" <?php if($eminute == "29") { echo 'selected'; } ?>>29</option>
				        <option value="30" <?php if($eminute == "30") { echo 'selected'; } ?>>30</option>
				        <option value="31" <?php if($eminute == "31") { echo 'selected'; } ?>>31</option>
				        <option value="32" <?php if($eminute == "32") { echo 'selected'; } ?>>32</option>
				        <option value="33" <?php if($eminute == "33") { echo 'selected'; } ?>>33</option>
				        <option value="34" <?php if($eminute == "34") { echo 'selected'; } ?>>34</option>
				        <option value="35" <?php if($eminute == "35") { echo 'selected'; } ?>>35</option>
				        <option value="36" <?php if($eminute == "36") { echo 'selected'; } ?>>36</option>
				        <option value="37" <?php if($eminute == "37") { echo 'selected'; } ?>>37</option>
				        <option value="38" <?php if($eminute == "38") { echo 'selected'; } ?>>38</option>
				        <option value="39" <?php if($eminute == "39") { echo 'selected'; } ?>>39</option>
				        <option value="40" <?php if($eminute == "40") { echo 'selected'; } ?>>40</option>
				        <option value="41" <?php if($eminute == "41") { echo 'selected'; } ?>>41</option>
				        <option value="42" <?php if($eminute == "42") { echo 'selected'; } ?>>42</option>
				        <option value="43" <?php if($eminute == "43") { echo 'selected'; } ?>>43</option>
				        <option value="44" <?php if($eminute == "44") { echo 'selected'; } ?>>44</option>
				        <option value="45" <?php if($eminute == "45") { echo 'selected'; } ?>>45</option>
				        <option value="46" <?php if($eminute == "46") { echo 'selected'; } ?>>46</option>
				        <option value="47" <?php if($eminute == "47") { echo 'selected'; } ?>>47</option>
				        <option value="48" <?php if($eminute == "48") { echo 'selected'; } ?>>48</option>
				        <option value="49" <?php if($eminute == "49") { echo 'selected'; } ?>>49</option>
				        <option value="50" <?php if($eminute == "50") { echo 'selected'; } ?>>50</option>
				        <option value="51" <?php if($eminute == "51") { echo 'selected'; } ?>>51</option>
				        <option value="52" <?php if($eminute == "52") { echo 'selected'; } ?>>52</option>
				        <option value="53" <?php if($eminute == "53") { echo 'selected'; } ?>>53</option>
				        <option value="54" <?php if($eminute == "54") { echo 'selected'; } ?>>54</option>
				        <option value="55" <?php if($eminute == "55") { echo 'selected'; } ?>>55</option>
				        <option value="56" <?php if($eminute == "56") { echo 'selected'; } ?>>56</option>
				        <option value="57" <?php if($eminute == "57") { echo 'selected'; } ?>>57</option>
				        <option value="58" <?php if($eminute == "58") { echo 'selected'; } ?>>58</option>
				        <option value="59" <?php if($eminute == "59") { echo 'selected'; } ?>>59</option>
				        <option value="60" <?php if($eminute == "60") { echo 'selected'; } ?>>60</option>
					</select></td>
			      	</tr>
	      			<?php if(!$event_edit_id) { ?>
			      	<tr>
				        <th scope="row"><?php _e('Repeat', 'wpevents'); ?> (<?php _e('optional', 'wpevents'); ?>):</th>
				        <td width="30%"><select name="events_repeat_every" tabindex="14">
							<option value=""><?php _e('Don\'t repeat', 'wpevents'); ?></option>
							<option value="day"><?php _e('Every day', 'wpevents'); ?></option>
							<option value="week"><?php _e('Every week', 'wpevents'); ?></option>
							<option value="4week"><?php _e('Every 4 Weeks', 'wpevents'); ?></option>
							<option value="year"><?php _e('Every year', 'wpevents'); ?></option>
						</select></td>
						<th scope="row"><?php _e('This many times', 'wpevents'); ?> (<?php _e('optional', 'wpevents'); ?>)</th>
				        <td width="30%" valign="top"><select name="events_repeat" tabindex="15">
							<option value="0">--</option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
							<option value="6">6</option>
							<option value="7">7</option>
							<option value="8">8</option>
							<option value="9">9</option>
							<option value="10">10</option>
							<option value="11">11</option>
							<option value="12">12</option>
							<option value="13">13</option>
							<option value="14">14</option>
							<option value="15">15</option>
							<option value="16">16</option>
							<option value="17">17</option>
							<option value="18">18</option>
							<option value="19">19</option>
							<option value="20">20</option>
						</select></td>
			      	</tr>
			      	<?php } ?>
			      	<tr>
				        <th scope="row"><?php _e('Location', 'wpevents'); ?> (<?php _e('optional', 'wpevents'); ?>):</th>
				        <td width="30%"><input name="events_location" class="search-input" type="text" size="25" maxlength="255" value="<?php echo $edit_event->location;?>" tabindex="16" /><br /><em><?php _e('Maximum 255 characters.', 'wpevents'); ?></em></td>
				        <th scope="row"><?php _e('Category', 'wpevents'); ?>:</th>
				        <td width="30%" valign="top"><select name='events_category' id='cat' class='postform' tabindex="17">
						<?php foreach($categories as $category) { ?>
						    <option value="<?php echo $category->id; ?>" <?php if($category->id == $edit_event->category) { echo 'selected'; } ?>><?php echo $category->name; ?></option>
				    	<?php } ?>
				    	</select></td>
			      	</tr>
			      	<tr>
				        <th scope="row"><?php _e('Show in the sidebar', 'wpevents'); ?>:</th>
				        <td width="25%"><select name="events_priority" tabindex="18">
						<?php if($edit_event->priority == "yes" OR $edit_event->priority == "") { ?>
						<option value="yes"><?php _e('Yes', 'wpevents'); ?></option>
						<option value="no"><?php _e('No', 'wpevents'); ?></option>
						<?php } else { ?>
						<option value="no"><?php _e('No', 'wpevents'); ?></option>
						<option value="yes"><?php _e('Yes', 'wpevents'); ?></option>
						<?php } ?>
						</select></td>
						<th scope="row"><?php _e('Archive this event', 'wpevents'); ?>:</th>
						<td width="25%"><select name="events_archive" tabindex="19">
						<?php if($edit_event->archive == "yes" OR $edit_event->archive == "") { ?>
						<option value="yes"><?php _e('Yes', 'wpevents'); ?></option>
						<option value="no"><?php _e('No', 'wpevents'); ?></option>
						<?php } else { ?>
						<option value="no"><?php _e('No', 'wpevents'); ?></option>
						<option value="yes"><?php _e('Yes', 'wpevents'); ?></option>
						<?php } ?>
						</select></td>
					</tr>
			      	<tr>
				        <th scope="row"><?php _e('Message when event ends', 'wpevents'); ?> (<?php _e('optional', 'wpevents'); ?>):</th>
				        <td colspan="3"><textarea name="events_post_event" class="search-input" cols="65" rows="2" tabindex="20"><?php echo $edit_event->post_message;?></textarea><br />
				        	<em><?php echo sprintf(__('Maximum %s characters.', 'wpevents'), $events_config['length']); ?> <?php _e('HTML allowed', 'wpevents'); ?>.</em></td>
			      	</tr>
			      	<tr>
				        <th scope="row"><?php _e('Link to page', 'wpevents'); ?> (<?php _e('optional', 'wpevents'); ?>):</th>
				        <td colspan="3"><input name="events_link" class="search-input" type="text" size="65 " maxlength="10000" value="<?php echo $edit_event->link;?>" tabindex="21" /><br />
				        	<em><?php _e('Include full url and http://, this can be any page.', 'wpevents'); _e('Required if checkbox above is checked!', 'wpevents'); ?></em></td>
			      	</tr>
			      	</tbody>

				</table>

				<br class="clear" />
				<?php events_credits(); ?>

		    	<p class="submit">
					<?php if($event_edit_id) { ?>
					<input type="submit" name="submit_save" class="button-primary" value="<?php _e('Edit event', 'wpevents'); ?>" tabindex="22" />
					<input type="submit" name="submit_new" class="button-primary" value="<?php _e('Duplicate event', 'wpevents'); ?>" tabindex="23" />
					<?php } else { ?>
					<input type="submit" name="submit_save" class="button-primary" value="<?php _e('Save event', 'wpevents'); ?>" tabindex="22" />
					<?php } ?>
					<a href="admin.php?page=wp-events" class="button"><?php _e('Cancel', 'wpevents'); ?></a>
		    	</p>

		  	</form>
		<?php } else { ?>
		    <table class="form-table">
				<tr valign="top">
					<td bgcolor="#DDD"><strong><?php _e('You should create at least one category before adding events!', 'wpevents'); ?> <a href="admin.php?page=wp-events3"><?php _e('Add a category now', 'wpevents'); ?></a>.</strong><br /><?php _e('Tip: If you do not want to use categories create one "uncategorized" and put all events in there. You don\'t have to show the categories on your blog.', 'wpevents'); ?></td>
				</tr>
			</table>
		<?php } ?>
	</div>
<?php }

/*-------------------------------------------------------------
 Name:      events_options

 Purpose:   Admin options page
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function events_options() {
	$events_config = get_option('events_config');
	$events_template = get_option('events_template');
	$events_language = get_option('events_language');

	$gmt_offset = (get_option('gmt_offset')*3600);
	$timezone 	= gmdate("U") + $gmt_offset;
	$view 		= $_GET['view'];
?>
	<div class="wrap">
	  	<h2><?php _e('Events options', 'wpevents'); ?></h2>
	  	<form method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>&amp;updated=true">

			<div class="tablenav">
				<div class="alignleft actions">
					<a class="row-title" href="<?php echo get_option('siteurl').'/wp-admin/options-general.php?page=wp-events4&view=main';?>"><?php _e('General', 'wpevents'); ?></a> | 
					<a class="row-title" href="<?php echo get_option('siteurl').'/wp-admin/options-general.php?page=wp-events4&view=templates';?>"><?php _e('Templates', 'wpevents'); ?></a> | 
					<a class="row-title" href="<?php echo get_option('siteurl').'/wp-admin/options-general.php?page=wp-events4&view=language';?>"><?php _e('Language', 'wpevents'); ?></a> | 
					<a class="row-title" href="<?php echo get_option('siteurl').'/wp-admin/options-general.php?page=wp-events4&view=uninstall';?>"><?php _e('Uninstall', 'wpevents'); ?></a>
				</div>
			</div>

	    	<?php if ($view == "" OR $view == "main") { ?>
	    	
	    	<h3><?php _e('Main config', 'wpevents'); ?></h3>
	    	
	    	<input type="hidden" name="events_submit_general" value="true" />
	    	<table class="form-table">
	    	
				<tr valign="top">
					<td colspan="4"><span style="font-weight: bold; text-decoration: underline; font-size: 12px;"><?php _e('Options for the sidebar and widget', 'wpevents'); ?></span></td>
				</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('Show this many events', 'wpevents'); ?></th>
			        <td colspan="3"><input name="events_amount" type="text" value="<?php echo $events_config['amount'];?>" size="6" /> (<?php _e('default', 'wpevents'); ?>: 2)</td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('Show', 'wpevents'); ?></th>
			        <td colspan="3"><select name="events_sideshow">
				        <option value="1" <?php if($events_config['sideshow'] == "1") { echo 'selected'; } ?>><?php _e('Future events including events that happen today', 'wpevents'); ?> (<?php _e('default', 'wpevents'); ?>)</option>
				        <option value="2" <?php if($events_config['sideshow'] == "2") { echo 'selected'; } ?>><?php _e('Events that didn\'t start yet', 'wpevents'); ?></option>
				        <option value="3" <?php if($events_config['sideshow'] == "3") { echo 'selected'; } ?>><?php _e('Events that didn\'t end yet', 'wpevents'); ?></option>
				        <option value="4" <?php if($events_config['sideshow'] == "4") { echo 'selected'; } ?>><?php _e('The archive', 'wpevents'); ?></option>
				        <option value="5" <?php if($events_config['sideshow'] == "5") { echo 'selected'; } ?>><?php _e('Just today\'s events', 'wpevents'); ?></option>
				        <option value="6" <?php if($events_config['sideshow'] == "6") { echo 'selected'; } ?>><?php _e('The next 7 days', 'wpevents'); ?></option>
					</select></td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('Date format', 'wpevents'); ?></th>
			        <?php if($events_config['custom_date_sidebar'] == 'no') { ?>
			        <td><select name="events_dateformat_sidebar">
				        <option disabled="disabled">-- <?php _e('Day', 'wpevents'); ?>/<?php _e('Month', 'wpevents'); ?>/<?php _e('Year', 'wpevents'); ?> --</option>
				        <option value="%d %m %Y" <?php if($events_config['dateformat_sidebar'] == "%d %m %Y") { echo 'selected'; } ?>><?php echo gmstrftime("%d %m %Y", $timezone); ?></option>
				        <option value="%d %b %Y" <?php if($events_config['dateformat_sidebar'] == "%d %b %Y") { echo 'selected'; } ?>><?php echo gmstrftime("%d %b %Y", $timezone); ?> (<?php _e('default', 'wpevents'); ?>)</option>
				        <option value="%d %B %Y" <?php if($events_config['dateformat_sidebar'] == "%d %B %Y") { echo 'selected'; } ?>><?php echo gmstrftime("%d %B %Y", $timezone); ?></option>
				        <option disabled="disabled">-- <?php _e('Month', 'wpevents'); ?>/<?php _e('Day', 'wpevents'); ?>/<?php _e('Year', 'wpevents'); ?> --</option>
				        <option value="%m %d %Y" <?php if($events_config['dateformat_sidebar'] == "%m %d %Y") { echo 'selected'; } ?>><?php echo gmstrftime("%m %d %Y", $timezone); ?></option>
				        <option value="%b %d %Y" <?php if($events_config['dateformat_sidebar'] == "%b %d %Y") { echo 'selected'; } ?>><?php echo gmstrftime("%b %d %Y", $timezone); ?></option>
				        <option value="%B %d %Y" <?php if($events_config['dateformat_sidebar'] == "%B %d %Y") { echo 'selected'; } ?>><?php echo gmstrftime("%B %d %Y", $timezone); ?></option>
				        <option disabled="disabled">-- <?php _e('Weekday', 'wpevents'); ?> <?php _e('Day', 'wpevents'); ?>/<?php _e('Month', 'wpevents'); ?>/<?php _e('Year', 'wpevents'); ?> --</option>
				        <option value="%a, %d %B %Y" <?php if($events_config['dateformat_sidebar'] == "%a, %d %B %Y") { echo 'selected'; } ?>><?php echo gmstrftime("%a, %d %B %Y", $timezone); ?></option>
				        <option value="%A, %d %B %Y" <?php if($events_config['dateformat_sidebar'] == "%A, %d %B %Y") { echo 'selected'; } ?>><?php echo gmstrftime("%A, %d %B %Y", $timezone); ?></option>
				        <option disabled="disabled">-- <?php _e('Preferred by locale', 'wpevents'); ?> --</option>
				        <option value="%x" <?php if($events_config['dateformat_sidebar'] == "%x") { echo 'selected'; } ?>><?php echo gmstrftime("%x", $timezone); ?></option>
					</select></td>
					<?php } else { ?>
 			        <td><input name="events_dateformat_sidebar" type="text" value="<?php echo $events_config['dateformat_sidebar'];?>" size="30" /><br /><?php _e('Careful what you put here!', 'wpevents'); ?> <?php _e('Learn', 'wpevents'); ?>: <a href="http://www.php.net/manual/en/function.gmstrftime.php" target="_blank"><?php _e('php manual', 'wpevents'); ?></a>.</td>
 			        <?php } ?>
			        <th scope="row"><?php _e('Date system', 'wpevents'); ?></th>
			        <td><select name="events_custom_date_sidebar">
			        	 <?php if($events_config['custom_date_sidebar'] == "no") { ?>
				        <option value="no"><?php _e('default', 'wpevents'); ?></option>
				        <option value="yes"><?php _e('advanced', 'wpevents'); ?></option>
				        <?php } else { ?>
				        <option value="yes"><?php _e('advanced', 'wpevents'); ?></option>
				        <option value="no"><?php _e('default', 'wpevents'); ?></option>
				        <?php } ?>
					</select><br /><?php _e('Save options to see the result!', 'wpevents'); ?></td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('Time format', 'wpevents'); ?></th>
			        <td colspan="3"><select name="events_timeformat_sidebar">
				        <option disabled="disabled">-- <?php _e('24-hour clock', 'wpevents'); ?> --</option>
				        <option value="%H:%M" <?php if($events_config['timeformat_sidebar'] == "%H:%M") { echo 'selected'; } ?>><?php echo gmstrftime("%H:%M", $timezone); ?> (<?php _e('default', 'wpevents'); ?>)</option>
				        <option value="%H:%M:%S" <?php if($events_config['timeformat_sidebar'] == "%H:%M:%S") { echo 'selected'; } ?>><?php echo gmstrftime("%H:%M:%S", $timezone); ?></option>
				        <option disabled="disabled">-- <?php _e('12-hour clock', 'wpevents'); ?> --</option>
				        <option value="%I:%M %p" <?php if($events_config['timeformat_sidebar'] == "%I:%M %p") { echo 'selected'; } ?>><?php echo gmstrftime("%I:%M %p", $timezone); ?></option>
				        <option value="%I:%M:%S %p" <?php if($events_config['timeformat_sidebar'] == "%I:%M:%S %p") { echo 'selected'; } ?>><?php echo gmstrftime("%I:%M:%S %p", $timezone); ?></option>
				        <option disabled="disabled">-- <?php _e('Preferred by locale', 'wpevents'); ?> --</option>
				        <option value="%X" <?php if($events_config['timeformat_sidebar'] == "%X") { echo 'selected'; } ?>><?php echo gmstrftime("%X", $timezone); ?></option>
					</select></td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('Character limit', 'wpevents'); ?></th>
			        <td colspan="3"><input name="events_sidelength" type="text" value="<?php echo $events_config['sidelength'];?>" size="6" /> (<?php _e('default', 'wpevents'); ?>: 120)</td>
		      	</tr>
				<tr valign="top">
					<td colspan="4"><span style="font-weight: bold; text-decoration: underline; font-size: 12px;"><?php _e('Options for the page lists', 'wpevents'); ?></span></td>
				</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('Date format', 'wpevents'); ?></th>
			        <?php if($events_config['custom_date_page'] == 'no') { ?>
			        <td><select name="events_dateformat">
				        <option disabled="disabled">-- <?php _e('Day', 'wpevents'); ?>/<?php _e('Month', 'wpevents'); ?>/<?php _e('Year', 'wpevents'); ?> --</option>
				        <option value="%d %m %Y" <?php if($events_config['dateformat'] == "%d %m %Y") { echo 'selected'; } ?>><?php echo gmstrftime("%d %m %Y", $timezone); ?></option>
				        <option value="%d %b %Y" <?php if($events_config['dateformat'] == "%d %b %Y") { echo 'selected'; } ?>><?php echo gmstrftime("%d %b %Y", $timezone); ?></option>
				        <option value="%d %B %Y" <?php if($events_config['dateformat'] == "%d %B %Y") { echo 'selected'; } ?>><?php echo gmstrftime("%d %B %Y", $timezone); ?> (<?php _e('default', 'wpevents'); ?>)</option>
				        <option disabled="disabled">-- <?php _e('Month', 'wpevents'); ?>/<?php _e('Day', 'wpevents'); ?>/<?php _e('Year', 'wpevents'); ?> --</option>
				        <option value="%m %d %Y" <?php if($events_config['dateformat'] == "%m %d %Y") { echo 'selected'; } ?>><?php echo gmstrftime("%m %d %Y", $timezone); ?></option>
				        <option value="%b %d %Y" <?php if($events_config['dateformat'] == "%d %b %Y") { echo 'selected'; } ?>><?php echo gmstrftime("%d %b %Y", $timezone); ?></option>
				        <option value="%B %d %Y" <?php if($events_config['dateformat'] == "%B %d %Y") { echo 'selected'; } ?>><?php echo gmstrftime("%B %d %Y", $timezone); ?></option>
				        <option disabled="disabled">-- <?php _e('Weekday', 'wpevents'); ?> <?php _e('Day', 'wpevents'); ?>/<?php _e('Month', 'wpevents'); ?>/<?php _e('Year', 'wpevents'); ?> --</option>
				        <option value="%a, %d %B %Y" <?php if($events_config['dateformat'] == "%a, %d %B %Y") { echo 'selected'; } ?>><?php echo gmstrftime("%a, %d %B %Y", $timezone); ?></option>
				        <option value="%A, %d %B %Y" <?php if($events_config['dateformat'] == "%A, %d %B %Y") { echo 'selected'; } ?>><?php echo gmstrftime("%A, %d %B %Y", $timezone); ?></option>
				        <option disabled="disabled">-- <?php _e('Preferred by locale', 'wpevents'); ?> --</option>
				        <option value="%x" <?php if($events_config['dateformat'] == "%x") { echo 'selected'; } ?>><?php echo gmstrftime("%x", $timezone); ?></option>
					</select></td>
					<?php } else { ?>
 			        <td><input name="events_dateformat" type="text" value="<?php echo $events_config['dateformat'];?>" size="30" /><br /><?php _e('Careful what you put here!', 'wpevents'); ?> <?php _e('Learn', 'wpevents'); ?>: <a href="http://www.php.net/manual/en/function.gmstrftime.php" target="_blank"><?php _e('php manual', 'wpevents'); ?></a>.</td>
 			        <?php } ?>
			        <th scope="row"><?php _e('Date system', 'wpevents'); ?></th>
			        <td><select name="events_custom_date_page">
			        	 <?php if($events_config['custom_date_page'] == "no") { ?>
				        <option value="no"><?php _e('default', 'wpevents'); ?></option>
				        <option value="yes"><?php _e('advanced', 'wpevents'); ?></option>
				        <?php } else { ?>
				        <option value="yes"><?php _e('advanced', 'wpevents'); ?></option>
				        <option value="no"><?php _e('default', 'wpevents'); ?></option>
				        <?php } ?>
					</select><br /><?php _e('Save options to see the result!', 'wpevents'); ?></td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('Time format', 'wpevents'); ?></th>
			        <td colspan="3"><select name="events_timeformat">
				        <option disabled="disabled">-- <?php _e('24-hour clock', 'wpevents'); ?> --</option>
				        <option value="%H:%M" <?php if($events_config['timeformat'] == "%H:%M") { echo 'selected'; } ?>><?php echo gmstrftime("%H:%M", $timezone); ?> (<?php _e('default', 'wpevents'); ?>)</option>
				        <option value="%H:%M:%S" <?php if($events_config['timeformat'] == "%H:%M:%S") { echo 'selected'; } ?>><?php echo gmstrftime("%H:%M:%S", $timezone); ?></option>
				        <option disabled="disabled">-- <?php _e('12-hour clock', 'wpevents'); ?> --</option>
				        <option value="%I:%M %p" <?php if($events_config['timeformat'] == "%I:%M %p") { echo 'selected'; } ?>><?php echo gmstrftime("%I:%M %p", $timezone); ?></option>
				        <option value="%I:%M:%S %p" <?php if($events_config['timeformat'] == "%I:%M:%S %p") { echo 'selected'; } ?>><?php echo gmstrftime("%I:%M:%S %p", $timezone); ?></option>
				        <option disabled="disabled">-- <?php _e('Preferred by locale', 'wpevents'); ?> --</option>
				        <option value="%X" <?php if($events_config['timeformat'] == "%X") { echo 'selected'; } ?>><?php echo gmstrftime("%X", $timezone); ?></option>
					</select></td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('Character limit', 'wpevents'); ?> </th>
			        <td colspan="3"><input name="events_length" type="text" value="<?php echo $events_config['length'];?>" size="6" /> (<?php _e('default', 'wpevents'); ?> : 1000)</td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('Date parsing', 'wpevents'); ?> </th>
			        <td colspan="3"><select name="events_hideend">
				        <option value="hide" <?php if($events_config['hideend'] == "hide") { echo 'selected'; } ?>><?php _e('Hide the ending date if it\'s the same as the starting date', 'wpevents'); ?> </option>
				        <option value="show" <?php if($events_config['hideend'] == "show") { echo 'selected'; } ?>><?php _e('Show the ending date even if it\'s the same as the starting date', 'wpevents'); ?> </option>
					</select></td>
		      	</tr>
				<tr valign="top">
					<td colspan="4"><span style="font-weight: bold; text-decoration: underline; font-size: 12px;"><?php _e('Global or other options', 'wpevents'); ?> </span></td>
				</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('Order events', 'wpevents'); ?> </th>
			        <td colspan="3"><select name="events_order">
				        <option value="thetime ASC" <?php if($events_config['order'] == "thetime ASC") { echo 'selected'; } ?>><?php _e('by date', 'wpevents'); ?> (<?php _e('ascending', 'wpevents'); ?>)</option>
				        <option value="thetime DESC" <?php if($events_config['order'] == "thetime DESC") { echo 'selected'; } ?>><?php _e('by date', 'wpevents'); ?> (<?php _e('descending', 'wpevents'); ?>)</option>
				        <option value="ID ASC" <?php if($events_config['order'] == "ID ASC") { echo 'selected'; } ?>><?php _e('in the order you made them', 'wpevents'); ?> (<?php _e('ascending', 'wpevents'); ?>)</option>
				        <option value="ID DESC" <?php if($events_config['order'] == "ID DESC") { echo 'selected'; } ?>><?php _e('in the order you made them', 'wpevents'); ?> (<?php _e('descending', 'wpevents'); ?>)</option>
				        <option value="author ASC" <?php if($events_config['order'] == "author ASC") { echo 'selected'; } ?>><?php _e('by author', 'wpevents'); ?> (A-Z)</option>
				        <option value="author DESC" <?php if($events_config['order'] == "author DESC") { echo 'selected'; } ?>><?php _e('by author', 'wpevents'); ?> (Z-A)</option>
				        <option value="title ASC" <?php if($events_config['order'] == "title ASC") { echo 'selected'; } ?>><?php _e('by title', 'wpevents'); ?> (A-Z)</option>
				        <option value="title DESC" <?php if($events_config['order'] == "title DESC") { echo 'selected'; } ?>><?php _e('by title', 'wpevents'); ?> (Z-A)</option>
				        <option value="pre_message ASC" <?php if($events_config['order'] == "pre_message ASC") { echo 'selected'; } ?>><?php _e('by description', 'wpevents'); ?> (A-Z)</option>
				        <option value="pre_message DESC" <?php if($events_config['order'] == "pre_message DESC") { echo 'selected'; } ?>><?php _e('by description', 'wpevents'); ?> (Z-A)</option>
					</select></td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('Order archive', 'wpevents'); ?></th>
			        <td colspan="3"><select name="events_order_archive">
				        <option value="thetime ASC" <?php if($events_config['order_archive'] == "thetime ASC") { echo 'selected'; } ?>><?php _e('by date', 'wpevents'); ?> (<?php _e('ascending', 'wpevents'); ?>)</option>
				        <option value="thetime DESC" <?php if($events_config['order_archive'] == "thetime DESC") { echo 'selected'; } ?>><?php _e('by date', 'wpevents'); ?> (<?php _e('descending', 'wpevents'); ?>)</option>
				        <option value="ID ASC" <?php if($events_config['order_archive'] == "ID ASC") { echo 'selected'; } ?>><?php _e('in the order you made them', 'wpevents'); ?> (<?php _e('ascending', 'wpevents'); ?>)</option>
				        <option value="ID DESC" <?php if($events_config['order_archive'] == "ID DESC") { echo 'selected'; } ?>><?php _e('in the order you made them', 'wpevents'); ?> (<?php _e('descending', 'wpevents'); ?>)</option>
				        <option value="author ASC" <?php if($events_config['order_archive'] == "author ASC") { echo 'selected'; } ?>><?php _e('by author', 'wpevents'); ?> (A-Z)</option>
				        <option value="author DESC" <?php if($events_config['order_archive'] == "author DESC") { echo 'selected'; } ?>><?php _e('by author', 'wpevents'); ?> (Z-A)</option>
				        <option value="title ASC" <?php if($events_config['order_archive'] == "title ASC") { echo 'selected'; } ?>><?php _e('by title', 'wpevents'); ?> (A-Z)</option>
				        <option value="title DESC" <?php if($events_config['order_archive'] == "title DESC") { echo 'selected'; } ?>><?php _e('by title', 'wpevents'); ?> (Z-A)</option>
				        <option value="pre_message ASC" <?php if($events_config['order_archive'] == "pre_message ASC") { echo 'selected'; } ?>><?php _e('by description', 'wpevents'); ?> (A-Z)</option>
				        <option value="pre_message DESC" <?php if($events_config['order_archive'] == "pre_message DESC") { echo 'selected'; } ?>><?php _e('by description', 'wpevents'); ?> (Z-A)</option>
					</select></td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('Links open in', 'wpevents'); ?></th>
			        <td colspan="3"><select name="events_linktarget">
				        <option value="_blank" <?php if($events_config['linktarget'] == "_target") { echo 'selected'; } ?>><?php _e('new window', 'wpevents'); ?></option>
				        <option value="_self" <?php if($events_config['linktarget'] == "_self") { echo 'selected'; } ?>><?php _e('same window', 'wpevents'); ?></option>
				        <option value="_parent" <?php if($events_config['linktarget'] == "_parent") { echo 'selected'; } ?>><?php _e('parent window', 'wpevents'); ?></option>
					</select></td>
		      	</tr>
				<tr valign="top">
					<td colspan="4"><span style="font-weight: bold; text-decoration: underline; font-size: 12px;"><?php _e('User Access', 'wpevents'); ?></span></td>
				</tr>
				<tr valign="top">
					<td colspan="4"><?php _e('Set these options to prevent certain userlevels from editing, creating or deleting events. The options panel user level cannot be changed.', 'wpevents'); ?> <?php echo sprintf(__('For more information on user roles go to <a href="%s">the codex</a>.', 'wpevents'), 'http://codex.wordpress.org/Roles_and_Capabilities#Summary_of_Roles" target="_blank'); ?></td>
				</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('Manage Events', 'wpevents'); ?></th>
			        <td colspan="3"><select name="events_editlevel">
				        <option value="manage_options" <?php if($events_config['editlevel'] == "manage_options") { echo 'selected'; } ?>><?php _e('Administrator', 'wpevents'); ?></option>
				        <option value="edit_pages" <?php if($events_config['editlevel'] == "edit_pages") { echo 'selected'; } ?>><?php _e('Editor'); ?> (<?php _e('default', 'wpevents'); ?>)</option>
				        <option value="publish_posts" <?php if($events_config['editlevel'] == "publish_posts") { echo 'selected'; } ?>><?php _e('Author', 'wpevents'); ?></option>
				        <option value="edit_posts" <?php if($events_config['editlevel'] == "edit_posts") { echo 'selected'; } ?>><?php _e('Contributor', 'wpevents'); ?></option>
				        <option value="read" <?php if($events_config['editlevel'] == "read") { echo 'selected'; } ?>><?php _e('Subscriber', 'wpevents'); ?></option>
					</select> <em><?php _e('Can add/edit/review events.', 'wpevents'); ?></em></td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('Delete Events', 'wpevents'); ?></th>
			        <td colspan="3"><select name="events_managelevel">
				        <option value="manage_options" <?php if($events_config['managelevel'] == "manage_options") { echo 'selected'; } ?>><?php _e('Administrator', 'wpevents'); ?> (<?php _e('default', 'wpevents'); ?>)</option>
				        <option value="edit_pages" <?php if($events_config['managelevel'] == "edit_pages") { echo 'selected'; } ?>><?php _e('Editor', 'wpevents'); ?></option>
				        <option value="publish_posts" <?php if($events_config['managelevel'] == "publish_posts") { echo 'selected'; } ?>><?php _e('Author', 'wpevents'); ?></option>
				        <option value="edit_posts" <?php if($events_config['managelevel'] == "edit_posts") { echo 'selected'; } ?>><?php _e('Contributor', 'wpevents'); ?></option>
				        <option value="read" <?php if($events_config['managelevel'] == "read") { echo 'selected'; } ?>><?php _e('Subscriber', 'wpevents'); ?></option>
					</select> <em><?php _e('Can review/delete events.', 'wpevents'); ?></em></td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('Manage Categories', 'wpevents'); ?></th>
			        <td colspan="3"><select name="events_catlevel">
				        <option value="manage_options" <?php if($events_config['catlevel'] == "manage_options") { echo 'selected'; } ?>><?php _e('Administrator', 'wpevents'); ?> (<?php _e('default', 'wpevents'); ?>)</option>
				        <option value="edit_pages" <?php if($events_config['catlevel'] == "edit_pages") { echo 'selected'; } ?>><?php _e('Editor', 'wpevents'); ?></option>
				        <option value="publish_posts" <?php if($events_config['catlevel'] == "publish_posts") { echo 'selected'; } ?>><?php _e('Author', 'wpevents'); ?></option>
				        <option value="edit_posts" <?php if($events_config['catlevel'] == "edit_posts") { echo 'selected'; } ?>><?php _e('Contributor', 'wpevents'); ?></option>
				        <option value="read" <?php if($events_config['catlevel'] == "read") { echo 'selected'; } ?>><?php _e('Subscriber', 'wpevents'); ?></option>
					</select> <em><?php _e('Can add/remove categories.', 'wpevents'); ?></em></td>
		      	</tr>
			</table>
		    <p class="submit">
		      	<input type="submit" name="Submit" class="button-primary" value="<?php _e('Update Options', 'wpevents'); ?> &raquo;" />
		    </p>

		   	<?php } else if($view == "templates") { ?>
		   	
		   	<h3><?php _e('Templates', 'wpevents'); ?></h3>

	    	<input type="hidden" name="events_submit_templates" value="true" />
		   	<table class="form-table">
				<tr valign="top">
					<td colspan="2"><span style="font-weight: bold; text-decoration: underline; font-size: 12px;"><?php _e('Sidebar and widget', 'wpevents'); ?></span></td>
				</tr>
		      	<tr valign="top">
			        <th scope="row" valign="top"><?php _e('Header', 'wpevents'); ?>:</th>
			        <td><textarea name="sidebar_h_template" cols="50" rows="4"><?php echo stripslashes($events_template['sidebar_h_template']); ?></textarea></td>
		      	<tr valign="top">
			        <th scope="row" valign="top"><?php _e('Body', 'wpevents'); ?>:</th>
			        <td><textarea name="sidebar_template" cols="50" rows="4"><?php echo stripslashes($events_template['sidebar_template']); ?></textarea><br /><em><?php _e('Options', 'wpevents'); ?>: %title% %event% %link% %countdown% %startdate% %starttime% %author% %location% %category%</em></td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row" valign="top"><?php _e('Footer', 'wpevents'); ?>:</th>
			        <td><textarea name="sidebar_f_template" cols="50" rows="4"><?php echo stripslashes($events_template['sidebar_f_template']); ?></textarea></td>
		      	</tr>
				<tr valign="top">
					<td colspan="2"><span style="font-weight: bold; text-decoration: underline; font-size: 12px;"><?php _e('Page', 'wpevents'); ?>, <?php _e('main list', 'wpevents'); ?>. <?php _e('Week', 'wpevents'); ?>, <?php _e('7 days ahead', 'wpevents'); ?></span></td>
				</tr>
		      	<tr valign="top">
			        <th scope="row" valign="top"><?php _e('Header', 'wpevents'); ?>:</th>
			        <td><textarea name="page_h_template" cols="50" rows="4"><?php echo stripslashes($events_template['page_h_template']); ?></textarea><br /><em><?php _e('Options', 'wpevents'); ?>: %category%</em></td>
		      	</tr>
		      	</tr>
			        <th scope="row" valign="top"><?php _e('Default title', 'wpevents'); ?> (<?php _e('optional', 'wpevents'); ?>):</th>
			        <td><input name="page_title_default" type="text" value="<?php echo $events_template['page_title_default'];?>" size="20" /><br /><em><?php _e('This only works if you use %category% in the header.', 'wpevents'); ?> <?php _e('No HTML allowed.', 'wpevents'); ?></em></td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row" valign="top"><?php _e('Body', 'wpevents'); ?>:</th>
			        <td><textarea name="page_template" cols="50" rows="4"><?php echo stripslashes($events_template['page_template']); ?></textarea><br /><em><?php _e('Options', 'wpevents'); ?>: %title% %event% %link% %startdate% %starttime% %enddate% %endtime% %duration% %countdown% %author% %location% %category%</em></td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row" valign="top"><?php _e('Footer', 'wpevents'); ?>:</th>
			        <td><textarea name="page_f_template" cols="50" rows="4"><?php echo stripslashes($events_template['page_f_template']); ?></textarea></td>
		      	</tr>
				<tr valign="top">
					<td colspan="2"><span style="font-weight: bold; text-decoration: underline; font-size: 12px;"><?php _e('Page', 'wpevents'); ?>, <?php _e('archive list', 'wpevents'); ?></span></td>
				</tr>
		      	<tr valign="top">
			        <th scope="row" valign="top"><?php _e('Header', 'wpevents'); ?>:</th>
			        <td><textarea name="archive_h_template" cols="50" rows="4"><?php echo stripslashes($events_template['archive_h_template']); ?></textarea><br /><em><?php _e('Options', 'wpevents'); ?>: %category%</em></td>
		      	</tr>
		      	</tr>
			        <th scope="row" valign="top"><?php _e('Default title', 'wpevents'); ?> (<?php _e('optional', 'wpevents'); ?>):</th>
			        <td><input name="archive_title_default" type="text" value="<?php echo $events_template['archive_title_default'];?>" size="20" /><br /><em><?php _e('This only works if you use %category% in the header.', 'wpevents'); ?> <?php _e('No HTML allowed.', 'wpevents'); ?></em></td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row" valign="top"><?php _e('Body', 'wpevents'); ?>:</th>
			        <td><textarea name="archive_template" cols="50" rows="4"><?php echo stripslashes($events_template['archive_template']); ?></textarea><br /><em><?php _e('Options', 'wpevents'); ?>: %title% %event% %after% %link% %startdate% %starttime% %enddate% %endtime% %duration% %countup% %author% %location% %category%</em></td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row" valign="top"><?php _e('Footer', 'wpevents'); ?>:</th>
			        <td><textarea name="archive_f_template" cols="50" rows="4"><?php echo stripslashes($events_template['archive_f_template']); ?></textarea></td>
		      	</tr>
				<tr valign="top">
					<td colspan="2"><span style="font-weight: bold; text-decoration: underline; font-size: 12px;"><?php _e('Page', 'wpevents'); ?>, <?php _e('today\'s list', 'wpevents'); ?></span></th>
				</tr>
		      	<tr valign="top">
			        <th scope="row" valign="top"><?php _e('Header', 'wpevents'); ?>:</th>
			        <td><textarea name="daily_h_template" cols="50" rows="4"><?php echo stripslashes($events_template['daily_h_template']); ?></textarea><br /><em><?php _e('Options', 'wpevents'); ?>: %category%</em></td>
		      	</tr>
		      	</tr>
			        <th scope="row" valign="top"><?php _e('Default title', 'wpevents'); ?> (<?php _e('optional', 'wpevents'); ?>):</th>
			        <td><input name="daily_title_default" type="text" value="<?php echo $events_template['daily_title_default'];?>" size="20" /><br /><em><?php _e('This only works if you use %category% in the header.', 'wpevents'); ?> <?php _e('No HTML allowed.', 'wpevents'); ?></em></td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row" valign="top"><?php _e('Body', 'wpevents'); ?>:</th>
			        <td><textarea name="daily_template" cols="50" rows="4"><?php echo stripslashes($events_template['daily_template']); ?></textarea><br /><em><?php _e('Options', 'wpevents'); ?>: %title% %event% %link% %startdate% %starttime% %enddate% %endtime% %duration% %countdown% %author% %location% %category%</em></td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row" valign="top"><?php _e('Footer', 'wpevents'); ?>:</th>
			        <td><textarea name="daily_f_template" cols="50" rows="4"><?php echo stripslashes($events_template['daily_f_template']); ?></textarea></td>
		      	</tr>
				<tr valign="top">
					<td colspan="2"><span style="font-weight: bold; text-decoration: underline; font-size: 12px;"><?php _e('Global template values', 'wpevents'); ?></span></th>
				</tr>
		      	<tr valign="top">
			        <th scope="row" valign="top"><?php _e('Location separator', 'wpevents'); ?>:</th>
			        <td><input name="location_seperator" type="text" value="<?php echo $events_template['location_seperator'];?>" size="6" /> (<?php _e('default', 'wpevents'); ?>: @ )<br /><em><?php _e('Can be text also.', 'wpevents'); ?> <?php _e('Ending spaces allowed.', 'wpevents'); ?></em></td>
		      	</tr>
			</table>
		    <p class="submit">
		      	<input type="submit" name="Submit" class="button-primary" value="<?php _e('Update Templates', 'wpevents'); ?> &raquo;" />
		    </p>

		   	<?php } else if($view == "language") { ?>

 		    <h3><?php _e('Language', 'wpevents'); ?></h3>

	    	<input type="hidden" name="events_submit_language" value="true" />
		    <table class="form-table">
		      	<tr valign="top">
			        <th scope="row"><?php _e('Today', 'wpevents'); ?>:</th>
			        <td><input name="events_language_today" type="text" value="<?php echo $events_language['language_today'];?>" size="45" /> (<?php _e('default', 'wpevents'); ?>: <?php _e('today', 'wpevents'); ?>)</td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('Hours', 'wpevents'); ?>:</th>
			        <td><input name="events_language_hours" type="text" value="<?php echo $events_language['language_hours'];?>" size="45" /> (<?php _e('default', 'wpevents'); ?>: <?php _e('hours', 'wpevents'); ?>)</td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('Minutes', 'wpevents'); ?>:</th>
			        <td><input name="events_language_minutes" type="text" value="<?php echo $events_language['language_minutes'];?>" size="45" /> (<?php _e('default', 'wpevents'); ?>: <?php _e('minutes', 'wpevents'); ?>)</td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('Day', 'wpevents'); ?>:</th>
			        <td><input name="events_language_day" type="text" value="<?php echo $events_language['language_day'];?>" size="45" /> (<?php _e('default', 'wpevents'); ?>: <?php _e('day', 'wpevents'); ?>)</td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('Days', 'wpevents'); ?>:</th>
			        <td><input name="events_language_days" type="text" value="<?php echo $events_language['language_days'];?>" size="45" /> (<?php _e('default', 'wpevents'); ?>: <?php _e('days', 'wpevents'); ?>)</td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('And', 'wpevents'); ?>:</th>
			        <td><input name="events_language_and" type="text" value="<?php echo $events_language['language_and'];?>" size="45" /> (<?php _e('default', 'wpevents'); ?>: <?php _e('and', 'wpevents'); ?>)</td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('On', 'wpevents'); ?>:</th>
			        <td><input name="events_language_on" type="text" value="<?php echo $events_language['language_on'];?>" size="45" /> (<?php _e('default', 'wpevents'); ?>: <?php _e('on', 'wpevents'); ?>)</td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('In', 'wpevents'); ?>:</th>
			        <td><input name="events_language_in" type="text" value="<?php echo $events_language['language_in'];?>" size="45" /> (<?php _e('default', 'wpevents'); ?>: <?php _e('in', 'wpevents'); ?>)</td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('Ago', 'wpevents'); ?>:</th>
			        <td><input name="events_language_ago" type="text" value="<?php echo $events_language['language_ago'];?>" size="45" /> (<?php _e('default', 'wpevents'); ?>: <?php _e('ago', 'wpevents'); ?>)</td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('Sidebar link', 'wpevents'); ?>:</th>
			        <td><input name="events_language_sidelink" type="text" value="<?php echo $events_language['language_sidelink'];?>" size="45" /> (<?php _e('default', 'wpevents'); ?>: <?php _e('more', 'wpevents'); ?> &raquo;)</td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('Page link', 'wpevents'); ?>:</th>
			        <td><input name="events_language_pagelink" type="text" value="<?php echo $events_language['language_pagelink'];?>" size="45" /> (<?php _e('default', 'wpevents'); ?>: <?php _e('More information', 'wpevents'); ?> &raquo;)</td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('If there are no events to show', 'wpevents'); ?>:</th>
			        <td><input name="events_language_noevents" type="text" value="<?php echo $events_language['language_noevents'];?>" size="45" /> (<?php _e('default', 'wpevents'); ?>: <?php _e('No events to show', 'wpevents'); ?>)</td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('If the event already happened', 'wpevents'); ?>:</th>
			        <td><input name="events_language_past" type="text" value="<?php echo $events_language['language_past'];?>" size="45" /> (<?php _e('default', 'wpevents'); ?>: <?php _e('Past event!', 'wpevents'); ?>)</td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('If there are no events today', 'wpevents'); ?>:</th>
			        <td><input name="events_language_nodaily" type="text" value="<?php echo $events_language['language_nodaily'];?>" size="45" /> (<?php _e('default', 'wpevents'); ?>: <?php _e('No events today', 'wpevents'); ?>)</td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('If the archive is empty', 'wpevents'); ?>:</th>
			        <td><input name="events_language_noarchive" type="text" value="<?php echo $events_language['language_noarchive'];?>" size="45" /> (<?php _e('default', 'wpevents'); ?>: <?php _e('No events in the archive', 'wpevents'); ?>)</td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('If there is an error', 'wpevents'); ?>:</th>
			        <td><input name="events_language_e_config" type="text" value="<?php echo $events_language['language_e_config'];?>" size="45" /> (<?php _e('default', 'wpevents'); ?>: <?php _e('A configuration error occurred', 'wpevents'); ?>)</td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('If no duration is set for an event', 'wpevents'); ?>:</th>
			        <td><input name="events_language_noduration" type="text" value="<?php echo $events_language['language_noduration'];?>" size="45" /> (<?php _e('default', 'wpevents'); ?>: <?php _e('No duration!', 'wpevents'); ?>)</td>
		      	</tr>
		      	<tr valign="top">
			        <th scope="row"><?php _e('If event is an all-day event', 'wpevents'); ?>:</th>
			        <td><input name="events_language_allday" type="text" value="<?php echo $events_language['language_allday'];?>" size="45" /> (<?php _e('default', 'wpevents'); ?>: <?php _e('All-day event!', 'wpevents'); ?>)</td>
		      	</tr>
	    	</table>
		    <p class="submit">
		      	<input type="submit" name="Submit" class="button-primary" value="<?php _e('Update Language', 'wpevents'); ?> &raquo;" />
		    </p>

		</form>

	   	<?php } else if($view == "uninstall") { ?>

	  	<h3><?php _e('Uninstaller', 'wpevents'); ?></h3>

    	<form method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>" name="events_uninstall">
	    	<table class="form-table">
				<tr valign="top">
					<td><?php _e('Events installs a table in MySQL. When you disable the plugin the table will not be deleted. To delete the table use the button below.', 'wpevents'); ?><br />
				</tr>
		      	<tr valign="top">
			        <th scope="row"><b style="color: #f00;"><?php _e('WARNING!', 'wpevents'); ?> <?php _e('This process is irreversible and will delete ALL scheduled events and associated options!', 'wpevents'); ?></b></td>
				</tr>
			</table>
	  		<p class="submit">
		    	<input type="hidden" name="events_uninstall" value="true" />
		    	<input onclick="return confirm('<?php _e('You are about to uninstall the events plugin', 'wpevents'); ?>\n  <?php _e('All scheduled events will be lost!', 'wpevents'); ?>\n<?php _e('[OK] to continue, [Cancel] to stop.', 'wpevents'); ?>')" type="submit" name="Submit" class="button-secondary" value="<?php _e('Uninstall Events', 'wpevents'); ?> &raquo;" />
	  		</p>
	  	</form>
	  	
	   	<?php } ?>

	</div>
<?php 
}
?>