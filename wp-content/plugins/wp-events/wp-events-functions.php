<?php  
/*-------------------------------------------------------------
 Name:      events_countdown

 Purpose:   Calculates countdown times
 Receive:   $time_start, $time_end, $message, $allday
 Return:	$output_countdown
-------------------------------------------------------------*/
function events_countdown($time_start, $time_end, $message, $allday) {
	global $events_config, $events_language;

  	$present = current_time('timestamp');
 	$difference = $time_start - $present;
	$daystart = floor($present / 86400) * 86400;
	$dayend = $daystart + 86400;

  	if ($difference < 0) $difference = 0;

 	$days_left = floor($difference/60/60/24);
  	$hours_left = floor(($difference - $days_left*60*60*24)/60/60);
  	$minutes_left = floor(($difference - $days_left*60*60*24 - $hours_left*60*60)/60);

	if($minutes_left < "10") $minutes_left = "0".$minutes_left;
	if($hours_left < "10") $hours_left = "0".$hours_left;

	$output_countdown = '';
  	if ( $days_left == 0 and $hours_left == 0 and $minutes_left == 0 and $present > $time_end) {
		$output_countdown .= $message;
	} else if ( $days_left == 0 ) {
		$output_countdown .= $events_language['language_in'].' '. $hours_left .':'. $minutes_left .' '.$events_language['language_hours'].'.';
	} else if ($time_end >= $daystart and $time_start <= $dayend) {
		$output_countdown .= $events_language['language_today'].'. '.$events_config['in'].' '. $minutes_left .' '.$events_language['language_minutes'].'.';
	} else {
	  	$output_countdown .= $events_language['language_in'].' ';
		if($days_left == 1) {
			$output_countdown .= $days_left .' '.$events_language['language_day'].' ';
		} else {
			$output_countdown .= $days_left .' '.$events_language['language_days'].' ';
		}
		$output_countdown .= $events_language['language_and'].' '. $hours_left .':'. $minutes_left .' '.$events_language['language_hours'].'.';
	}
	return $output_countdown;
}

/*-------------------------------------------------------------
 Name:      events_countup

 Purpose:   Calculates the time since the event
 Receive:   $time_start, $time_end, $message
 Return:	$output_archive
-------------------------------------------------------------*/
function events_countup($time_start, $time_end, $message) {
	global $events_config, $events_language;

  	$present = current_time('timestamp');
  	$difference = $present - $time_start;
	$daystart = floor($present / 86400) * 86400;
	$dayend = $daystart + 86400;

  	if ($difference < 0) $difference = 0;

 	$days_ago = floor($difference/60/60/24);
  	$hours_ago = floor(($difference - $days_ago*60*60*24)/60/60);
  	$minutes_ago = floor(($difference - $days_ago*60*60*24 - $hours_ago*60*60)/60);

	if($minutes_ago < "10") $minutes_ago = "0".$minutes_ago;
	if($hours_ago < "10") $hours_ago = "0".$hours_ago;

	$output_archive = '';
  	if ( $days_ago == 0 and $hours_ago == 0 and $minutes_ago == 0 and $present > $time_end ) {
		$output_archive .= $message;
	} else if ( $days_ago == 0 ) {
		$output_archive .= $hours_ago .':'. $minutes_ago .' '.$events_language['language_hours'].' '.$events_language['language_ago'].'.';
	} else {
		if($days_ago == 1) {
			$output_archive .= $days_ago .' '.$events_language['language_day'].' ';
		} else {
			$output_archive .= $days_ago .' '.$events_language['language_days'].' ';
		}
		$output_archive .= $events_language['language_and'].' '. $hours_ago .':'. $minutes_ago .' '.$events_language['language_hours'].' '.$events_language['language_ago'].'.';
	}
	return $output_archive;
}

/*-------------------------------------------------------------
 Name:      events_duration

 Purpose:   Calculates the duration of the event
 Receive:   $event_start, $event_end, $allday
 Return:	$output_duration
-------------------------------------------------------------*/
function events_duration($event_start, $event_end, $allday) {
	global $events_config, $events_language;

  	$difference = $event_end - $event_start;
  	if ($difference < 0) $difference = 0;

 	$days_duration = floor($difference/60/60/24);
  	$hours_duration = floor(($difference - $days_duration*60*60*24)/60/60);
  	$minutes_duration = floor(($difference - $days_duration*60*60*24 - $hours_duration*60*60)/60);

	if($minutes_duration < "10") $minutes_duration = "0".$minutes_duration;
	if($hours_duration < "10") $hours_duration = "0".$hours_duration;

	$output_duration = '';
  	if ($allday == 'Y') {
		$output_duration .= $events_language['language_allday'];
	} else if (($days_duration == 0 and $hours_duration == 0 and $minutes_duration == 0) or ($event_start == $event_end)) {
		$output_duration .= $events_language['language_noduration'];
	} else if ($days_duration == 0) {
		$output_duration .= $hours_duration .':'. $minutes_duration .' '.$events_language['language_hours'].'.';
	} else if ($days_duration == 0 and $hours_duration == 0) {
		$output_duration .= $minutes_duration .' '.$events_language['language_minutes'].'.';
	} else {
		if($days_duration == 1) {
			$output_duration .= $days_duration .' '.$events_language['language_day'].' ';
		} else {
			$output_duration .= $days_duration .' '.$events_language['language_days'].' ';
		}
		$output_duration .= $events_language['language_and'].' '. $hours_duration .':'. $minutes_duration .' '.$events_language['language_hours'].'.';
	}

	return $output_duration;
}

/*-------------------------------------------------------------
 Name:      events_sidebar

 Purpose:   Show events in the sidebar, also used for widget
 Receive:   $lim, $cat
 Return:	$output_sidebar
-------------------------------------------------------------*/
function events_sidebar($lim = 0, $cat = 0) {
	global $wpdb, $events_config, $events_language, $events_template;

	$present 		= current_time('timestamp');
	$daystart 		= floor($present / 86400) * 86400;
	$dayend 		= $daystart + 86400;
	$nextsevendays	= $present + 604800;

	if($cat == 0 or strlen($cat) < 1) $category = '';
		else $category = "AND `category` = '$cat'";

	if($lim == 0 or strlen($lim) < 1) $limit = $events_config['amount'];
		else $limit = $lim;

	if($events_config['sideshow'] == "2") {
		$sideshow = "AND `thetime` >= '$present'";
	} else if($events_config['sideshow'] == "3") {
		$sideshow = "AND `theend` >= '$present'";
	} else if($events_config['sideshow'] == "4") {
		$sideshow = "AND `thetime` <= '$present'";
	} else if($events_config['sideshow'] == "5") {
		$sideshow = "AND (`thetime` >= $daystart AND `theend` <= $dayend) OR ($present >= `thetime` AND $present <= `theend`)";
	} else if($events_config['sideshow'] == "6") {
		$sideshow = "AND (`thetime` >= '$present' AND `thetime` <= '$nextsevendays') OR ($present >= `thetime` AND $present <= `theend`)";
	} else {
		$sideshow = "AND `theend` >= '$daystart'"; // default behaviour (1)
	}

	$sidebar_header = $events_template['sidebar_h_template'];
	$sidebar_footer = $events_template['sidebar_f_template'];
	$output_sidebar = stripslashes(html_entity_decode($sidebar_header));
	if($events_config['order']){
		/* Fetch events */
		$events = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."events` WHERE `priority` = 'yes' ".$sideshow." ".$category." ORDER BY ".$events_config['order']." LIMIT ".$limit);
		/* Start processing data */
		if(count($events) == 0) {
			$output_sidebar .= '<em>'.$events_language['language_noevents'].'</em>';
		} else {
			foreach($events as $event) {
				$get_category = $wpdb->get_row("SELECT name FROM `".$wpdb->prefix."events_categories` WHERE `id` = $event->category");

				$template = $events_template['sidebar_template'];

				$event->title = substr($event->title, 0 , $events_config['sidelength']);
				if($event->title_link == 'Y') { $event->title = '<a href="'.$event->link.'" target="'.$events_config['linktarget'].'">'.$event->title.'</a>'; }
				$template = str_replace('%title%', $event->title, $template);
				$template = str_replace('%event%', substr($event->pre_message, 0 , $events_config['sidelength']), $template);

				if(strlen($event->link) > 0) { $template = str_replace('%link%', '<a href="'.$event->link.'" target="'.$events_config['linktarget'].'">'.$events_language['language_sidelink'].'</a>', $template); }
				if(strlen($event->link) == 0) { $template = str_replace('%link%', '', $template); }

				$template = str_replace('%countdown%', events_countdown($event->thetime, $event->theend, substr($event->post_message, 0 , $events_config['sidelength']), $event->allday), $template);
				if($event->allday == "Y") {
					$template = str_replace('%starttime%', '', $template);
				} else {
					$template = str_replace('%starttime%', str_replace('00:00', '', gmstrftime($events_config['timeformat_sidebar'], $event->thetime)), $template);
				}
				$template = str_replace('%startdate%', gmstrftime($events_config['dateformat_sidebar'], $event->thetime), $template);

				$template = str_replace('%author%', $event->author, $template);
				$template = str_replace('%category%', $get_category->name, $template);

				if(strlen($event->location) == 0) {
					$template = str_replace('%location%', '', $template);
				} else {
					$template = str_replace('%location%', $events_template['location_seperator'].$event->location, $template);
				}

				$output_sidebar .= stripslashes(html_entity_decode($template));
			}
		}
	} else {
		/* Configuration is fuxored, output an error */
		$output_sidebar .= '<em style="color:#f00;">'.$events_language['language_e_config'].'.</em>';
	}
	$output_sidebar .= stripslashes(html_entity_decode($sidebar_footer));

	return $output_sidebar;
}

/*-------------------------------------------------------------
 Name:      events_show

 Purpose:   Create list of events for the template using [events_show]
 Receive:   $atts, $content
 Return:	$output
-------------------------------------------------------------*/
function events_show($atts, $content = null) {
	global $wpdb, $events_config, $events_language, $events_template;

	$present = current_time('timestamp');
	$daystart = floor($present / 86400) * 86400;
	$dayend = $daystart + 86400;
	$nextsevendays	= $present + 604800;

	if(empty($atts['type'])) $type = "default";
		else $type = $atts['type'];

	if(empty($atts['amount'])) $amount = "";
		else $amount = " LIMIT $atts[amount]";

	if(empty($atts['event'])) $one_event = '';
		else $one_event = " AND `id` = '$atts[event]'";

	if(empty($atts['order'])) {
		if($type == "archive") {
			$order = $events_config['order_archive'];
		} else {
			$order = $events_config['order'];
		}
	} else {
		$amount = $atts['order'];
	}

	if(empty($atts['category'])) {
		$category = '';
	} else {
		$category = " AND `category` = '$atts[category]'";
		$category2 = $atts[category];
	}

	if($events_config AND $events_language AND $events_template AND isset($type)){
		if(!empty($atts['category'])) {
			$get_category = $wpdb->get_row("SELECT `name` FROM `".$wpdb->prefix."events_categories` WHERE `id` = $category2");
		}

		if($type == 'default') {

			$events = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."events` WHERE `thetime` >= $present$category$one_event ORDER BY $order$amount");
			$header = $events_template['page_h_template'];
			$titledefault = $events_template['page_title_default'];
			$footer = $events_template['page_f_template'];

		} else if ($type == 'archive') {

			$events = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."events` WHERE `archive` = 'yes' AND `thetime` <= $present$category$one_event ORDER BY $order$amount");
			$header = $events_template['archive_h_template'];
			$titledefault = $events_template['archive_title_default'];
			$footer = $events_template['archive_f_template'];

		} else if ($type == 'today') {

			$events = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."events` WHERE (`thetime` >= $daystart AND `theend` <= $dayend) OR ($present >= `thetime` AND $present <= `theend`) $category$one_event ORDER BY $order$amount");
			$header = $events_template['daily_h_template'];
			$titledefault = $events_template['daily_title_default'];
			$footer = $events_template['daily_f_template'];

		} else if ($type == 'week') {

			$events = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."events` WHERE (`thetime` >= '$present' AND `thetime` <= '$nextsevendays') OR ($present >= `thetime` AND $present <= `theend`) $category$one_event ORDER BY $order$amount");
			$header = $events_template['page_h_template'];
			$titledefault = $events_template['page_title_default'];
			$footer = $events_template['page_f_template'];

		}

		if(!empty($atts['category'])) {
			$header = str_replace('%category%', $get_category->name, $header);
		} else {
			$header = str_replace('%category%', $titledefault, $header);			
		}
		$output = stripslashes(html_entity_decode($header));
		if (count($events) == 0) {
			$output .= '<em>'.$events_language['language_noevents'].'</em>';
		} else {
			foreach($events as $event) {
				$category_name = $wpdb->get_row("SELECT name FROM `".$wpdb->prefix."events_categories` WHERE `id` = $event->category");
				$template = events_build_output($type, $category_name->name, $event->link, $event->title, $event->title_link, $event->pre_message, $event->post_message, $event->thetime, $event->theend, $event->allday, $event->author, $event->location);
				$output .= stripslashes(html_entity_decode($template));
			}
		}
		$output .= stripslashes(html_entity_decode($footer));
	} else {
		/* Configuration is fuxored, output an error */
		$output = '<em style="color:#f00;">'.$events_language['language_e_config'].'.</em>';
	}

	return $output;
}

/*-------------------------------------------------------------
 Name:      events_build_output

 Purpose:   Build output for page, archive and today's listings
 Receive:   $type, $category, $link, $title, $title_link, $pre_message, $post_message, $thetime, $theend, $allday, $author, $location
 Return:	$template
-------------------------------------------------------------*/
function events_build_output($type, $category, $link, $title, $title_link, $pre_message, $post_message, $thetime, $theend, $allday, $author, $location) {
	global $wpdb, $events_config, $events_language, $events_template;

	if($type == 'default') $template = $events_template['page_template'];
	if($type == 'archive') $template = $events_template['archive_template'];
	if($type == 'today') $template = $events_template['daily_template'];
	if($type == 'week') $template = $events_template['page_template'];

	if($title_link == 'Y') { $title = '<a href="'.$link.'" target="'.$events_config['linktarget'].'">'.$title.'</a>'; }
	$template = str_replace('%title%', $title, $template);
	$template = str_replace('%event%', $pre_message, $template);

	if(current_time('timestamp') <= $theend) { 
		$template = str_replace('%after%', '', $template);
	} else {
		$template = str_replace('%after%', $post_message, $template);
	}

	if(strlen($link) > 0) { $template = str_replace('%link%', '<a href="'.$link.'" target="'.$events_config['linktarget'].'">'.$events_language['language_pagelink'].'</a>', $template); }
	if(strlen($link) == 0) { $template = str_replace('%link%', '', $template); }

	$template = str_replace('%countdown%', events_countdown($thetime, $theend, $post_message, $allday), $template);
	$template = str_replace('%countup%', events_countup($thetime, $theend, $post_message), $template);
	$template = str_replace('%duration%', events_duration($thetime, $theend, $allday), $template);

	$template = str_replace('%startdate%', gmstrftime($events_config['dateformat'], $thetime), $template);

	if($thetime == $theend AND $events_config['hideend'] == 'hide') {
		$template = str_replace('%endtime%', '', $template);
		$template = str_replace('%enddate%', '', $template);
	} else {
		if($allday == "Y") {
			$template = str_replace('%endtime%', '', $template);
		} else {
			$template = str_replace('%endtime%', str_replace('00:00', '', gmstrftime($events_config['timeformat'], $theend)), $template);
		}
		$template = str_replace('%enddate%', gmstrftime($events_config['dateformat'], $theend), $template);
	}
	if($allday == "Y") {
		$template = str_replace('%starttime%', '', $template);
	} else {
		$template = str_replace('%starttime%', str_replace('00:00', '', gmstrftime($events_config['timeformat'], $thetime)), $template);
	}

	$template = str_replace('%author%', $author, $template);
	$template = str_replace('%category%', $category, $template);

	if(strlen($location) == 0) {
		$template = str_replace('%location%', '', $template);
	} else {
		$template = str_replace('%location%', $events_template['location_seperator'].$location, $template);
	}

	return $template;
}

/*-------------------------------------------------------------
 Name:      events_textdomain

 Purpose:   Initiates Translation files
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function events_textdomain() {
	load_plugin_textdomain( 'wpevents', WP_PLUGIN_DIR."/".basename(dirname(__FILE__)), basename(dirname(__FILE__)) );
}

/*-------------------------------------------------------------
 Name:      events_clear_old

 Purpose:   Removes old non archived events
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function events_clear_old() {
	global $wpdb;

	$removeme = current_time('timestamp') - 86400;
	$wpdb->query("DELETE FROM `".$wpdb->prefix."events` WHERE `thetime` <= ".$removeme." AND `archive` = 'no'");
}

/*-------------------------------------------------------------
 Name:      events_credits

 Purpose:   Credits
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function events_credits() {
	echo '<table class="widefat" style="margin-top: .5em">';
	
	echo '<thead>';
	echo '<tr valign="top">';
	echo '	<th>'.__('Events for Excellent!','wpevents').'</th>';
	echo '</tr>';
	echo '</thead>';

	echo '<tbody>';
	echo '<tr>';
	echo '<td>';
	echo    sprintf(__('Find me on <a href="%s">%s</a>.', 'wpevents'),'http://meandmymac.net" target="_blank', 'meandmymac.net').'<br />';
	echo    sprintf(__('The plugin page at <a href="%s">%s</a>. Getting started, manuals and more...', 'wpevents'),'http://meandmymac.net/plugins/events/" target="_blank','meandmymac.net/plugins/events/').' ';
	echo   sprintf(__('<a href="%s">%s</a> for updates and notes about Events!', 'wpevents'),'http://meandmymac.net/tag/events/" target="_blank', 'meandmymac.net/tag/events/').'<br />';
	echo   sprintf(__('Need help? <a href="%s">%s</a>. Now with a search function!', 'wpevents'),'http://forum.at.meandmymac.net" target="_blank','forum.at.meandmymac.net').' ';
	echo   sprintf(__('Do you require more help than the forum can offer? <a href="%s">Premium Support</a> is available!', 'wpevents'),'http://meandmymac.net/contact-and-support/premium-support/" target="_blank').'<br />';
	echo   sprintf(__('Like my software? <a href="%s">Show your appreciation</a>. Thanks!', 'wpevents'),'http://meandmymac.net/donate/" target="_blank');
	if (get_locale() != "en_US") echo "<br />".__('Translation: ---', 'wpevents');
	echo '</td>';
	echo '</tr>';
	echo '</tbody>';
	
	echo '</table';
}

/*-------------------------------------------------------------
 Name:      meandmymac_rss

 Purpose:   A very simple RSS parser for Meandmymac.net
 Receive:   $rss, $count
 Return:    -none-
-------------------------------------------------------------*/
if(!function_exists('meandmymac_rss')) {
	function meandmymac_rss($rss, $count = 10) {
		if ( is_string( $rss ) ) {
			require_once(ABSPATH . WPINC . '/rss.php');
			if ( !$rss = fetch_rss($rss) ) {
				echo '<div class="text-wrap"><span class="rsserror">The feed could not be fetched, try again later!</span></div>';
				return;
			}
		}

		if ( is_array( $rss->items ) && !empty( $rss->items ) ) {
			$rss->items = array_slice($rss->items, 0, $count);
			foreach ( (array) $rss->items as $item ) {
				while ( strstr($item['link'], 'http') != $item['link'] ) {
					$item['link'] = substr($item['link'], 1);
				}

				$link = clean_url(strip_tags($item['link']));
				$desc = attribute_escape(strip_tags( $item['description']));
				$title = attribute_escape(strip_tags($item['title']));
				if ( empty($title) ) {
					$title = __('Untitled');
				}
				
				if ( empty($link) ) {
					$link = "#";
				}

				if (isset($item['pubdate'])) {
					$date = $item['pubdate'];
				} elseif (isset($item['published'])) {
					$date = $item['published'];
				}

				if ($date) {
					if ($date_stamp = strtotime($date)) {
						$date = date_i18n( get_option('date_format'), $date_stamp);
					} else {
						$date = '';
					}
				}
				echo '<div class="text-wrap"><a href="'.$link.'" target="_blank">'.$title.'</a> '.__('on','wpevents').$date.'</div>';
			}
		} else {
			echo '<div class="text-wrap"><span class="rsserror">The feed appears to be invalid or corrupt!</span></div>';
		}
		return;
	}
}
?>