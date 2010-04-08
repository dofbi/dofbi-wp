<?php
/*-------------------------------------------------------------
 Name:      events_activate

 Purpose:   Creates database tables if they don't exist
 Receive:   -none-
 Return:	-none-
-------------------------------------------------------------*/
function events_activate() {
	global $wpdb;

	$mysql1 		= false;
	$mysql2 		= false;
	$upgrade 		= false;
	$table_name1	= $wpdb->prefix . "events";
	$table_name2 	= $wpdb->prefix . "events_categories";

	if ( $wpdb->has_cap( 'collation' ) ) {
		if ( ! empty($wpdb->charset) )
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if ( ! empty($wpdb->collate) )
			$charset_collate .= " COLLATE $wpdb->collate";
	}

	if(!events_mysql_table_exists($table_name1)) { // Add table if it's not there
		$add1 = "CREATE TABLE `".$table_name1."` (
	  		`id` mediumint(8) unsigned NOT NULL auto_increment PRIMARY KEY,
	  		`title` longtext NOT NULL,
	  		`title_link` varchar(3) NOT NULL default 'N',
	  		`location` varchar(255) NOT NULL,
	  		`category` int(11) NOT NULL default '1',
	  		`pre_message` longtext NOT NULL,
	  		`post_message` longtext NOT NULL,
	  		`link` longtext NOT NULL,
	  		`allday` varchar(3) NOT NULL default 'N',
	  		`thetime` int(15) NOT NULL default '0',
	  		`theend` int(15) NOT NULL default '0',
	  		`author` varchar(60) NOT NULL default '',
	  		`priority` varchar(4) NOT NULL default 'no',
	  		`archive` varchar(4) NOT NULL default 'no'
			) ".$charset_collate;
		if(mysql_query($add1) === true) {
			$myqsl1 = true;
		} else {
			$mysql1 = false;
		}
//	} else if(events_mysql_table_exists($table_name1)) { // Upgrade table if it is incomplete
//		if (!$result = mysql_query("SHOW COLUMNS FROM `$table_name1`")) {
//		    echo 'Could not run query: ' . mysql_error();
//		}
//		$i = 0;
//	    while ($row = mysql_fetch_assoc($result)) {
//			$field_array[] = mysql_field_name($row, $i);
//        	$i++;
//		}
//
//		if (!in_array('active', $field_array)) {
//			## REVIEW THE FUNCTION FOR USAGE ##
//			$upgrade = events_update_table($tablename1, 'active', 'INT( 5 ) NOT NULL DEFAULT \'1\'', 'archive');
//		} else {
//			$mysql1 = true;
//		}
	} else { // Or send out epic fail!
		$mysql1 = false;
	}

	if(!events_mysql_table_exists($table_name2)) {
		$add2 = "CREATE TABLE `".$table_name2."` (
			`id` mediumint(8) unsigned NOT NULL auto_increment PRIMARY KEY,
			`name` varchar(255) NOT NULL
			) ".$charset_collate;
		if(mysql_query($add2) === true) {
			$myqsl2 = true;
		} else {
			$mysql2 = false;
		}
//	} else if(events_mysql_table_exists($table_name2)) { // Upgrade table if it is incomplete
//		if (!$result = mysql_query("SHOW COLUMNS FROM `$table_name2`")) {
//		    echo 'Could not run query: ' . mysql_error();
//		}
//		$i = 0;
//	    while ($row = mysql_fetch_assoc($result)) {
//			$field_array[] = mysql_field_name($row, $i);
//        	$i++;
//		}
//
//		if (!in_array('somefield', $field_array)) {
//			## REVIEW THE FUNCTION FOR USAGE ##
//			$upgrade = events_update_table($tablename2, 'somefield', 'INT( 15 ) NOT NULL DEFAULT \'0\'', 'someotherfield');
//		} else {
//			$mysql2 = true;
//		}
	} else { // Or send out epic fail!
		$mysql2 = false;
	}
	
	if($mysql1 == false OR $mysql2 == false OR $upgrade == false) {
		events_mysql_warning();
	}
}

/*-------------------------------------------------------------
 Name:      events_deactivate

 Purpose:   Deactivate script
 Receive:   -none-
 Return:	-none-
-------------------------------------------------------------*/
function events_deactivate() {
}

/*-------------------------------------------------------------
 Name:      events_mysql_table_exists

 Purpose:   Check if the table exists in the database
 Receive:   -none-
 Return:	-none-
-------------------------------------------------------------*/
function events_mysql_table_exists($table_name) {
	global $wpdb;

	foreach ($wpdb->get_col("SHOW TABLES",0) as $table ) {
		if ($table == $table_name) {
			return true;
		}
	}
	return false;
}

/*-------------------------------------------------------------
 Name:      events_update_table

 Purpose:   Deactivate script
 Receive:   $tablename, $field_to_add, $specs, $after_field
 Return:	Boolean
-------------------------------------------------------------*/
function events_update_table($tablename, $field_to_add, $specs, $after_field) {
	if(mysql_query("ALTER TABLE `$table_name` ADD `$field_to_add` $specs AFTER `$after_field`;") === true) {
		return true;
	} else {
		events_mysql_upgrade_error();
	}
}

/*-------------------------------------------------------------
 Name:      events_mysql_warning

 Purpose:   Database errors if things go wrong
 Receive:   -none-
 Return:	-none-
-------------------------------------------------------------*/
function events_mysql_warning() {
	echo '<div class="updated"><h3>'.__('WARNING!', 'wpevents').' '.__('The MySQL table was not created! You cannot store events. See if you have the right MySQL access rights and check if you can create tables.', 'wpevents').' '.__('Contact your webhost/sysadmin if you must.', 'wpevents').' '.sprintf(__('If this brings no answers seek support at <a href="%s">%s</a>', 'wpevents'),'http://forum.at.meandmymac.net', 'http://forum.at.meandmymac.net').'. '.__('Please give as much information as you can related to your problem.', 'wpevents').'</h3></div>';
}

/*-------------------------------------------------------------
 Name:      events_mysql_upgrade_error

 Purpose:   Database errors if things go wrong
 Receive:   -none-
 Return:	-none-
-------------------------------------------------------------*/
function events_mysql_upgrade_error() {

	echo '<div class="updated"><h3>'.__('WARNING!', 'wpevents').' '.__('The MySQL table was not properly upgrade! Events cannot work properly without this upgrade. Check your MySQL permissions and see if you have ALTER rights (rights to alter existing tables).', 'wpevents').' '.__('Contact your webhost/sysadmin if you must.', 'wpevents').' '.sprintf(__('If this brings no answers seek support at <a href="%s">%s</a>', 'wpevents'),'http://forum.at.meandmymac.net', 'http://forum.at.meandmymac.net').' '.__('and mention any errors you saw/got and explain what you were doing!', 'wpevents').'</h3></div>';
	}
/*-------------------------------------------------------------
 Name:      events_plugin_uninstall

 Purpose:   Delete the entire database table and remove the options on uninstall.
 Receive:   -none-
 Return:	-none-
-------------------------------------------------------------*/
function events_plugin_uninstall() {
	global $wpdb;

	// Deactivate Plugin
	$current = get_settings('active_plugins');
    array_splice($current, array_search( "wp-events/wp-events.php", $current), 1 );
	update_option('active_plugins', $current);
	do_action('deactivate_' . trim( $_GET['plugin'] ));

	// Drop MySQL Tables
	$SQL = "DROP TABLE `".$wpdb->prefix."events`";
	mysql_query($SQL) or die(__('An unexpected error occured')."<br />".mysql_error());
	$SQL2 = "DROP TABLE `".$wpdb->prefix."events_categories`";
	mysql_query($SQL2) or die(__('An unexpected error occured')."<br />".mysql_error());

	// Delete Option
	delete_option('events_config');
	delete_option('events_template');
	delete_option('events_language');

	events_return('uninstall');
}
?>