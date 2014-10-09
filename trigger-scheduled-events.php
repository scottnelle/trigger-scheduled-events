<?php
/*
Plugin Name: Trigger Scheduled Events
Description: Trigger any scheduled event now instead of waiting until WP cron tells it to run. Handy for debugging.
Version:     1.0
Plugin URI:  http://scottnelle.com
Author:      Scott Nelle
Author URI:  http://scottnelle.com

	Copyright 2014 Scott Nellé  (email : contact@scottnelle.com)

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

/* create the menu item */
function trigger_scheduled_event_create_menu() {
	add_submenu_page( 'tools.php', 'Trigger Scheduled Events', 'Scheduled Events', 'manage_options', 'trigger_scheduled_event', 'trigger_scheduled_event_tools_page');
}
add_action('admin_menu', 'trigger_scheduled_event_create_menu', 99);

/* create the tools page */
function trigger_scheduled_event_tools_page() {
	if ( isset($_POST['submit_trigger_scheduled_event']) && current_user_can('manage_options') ) {
		check_admin_referer( 'trigger_event', '_trigger_scheduled_event_nonce' );

		if (isset($_POST['event_hook']) && $_POST['event_hook'] != '') {
			//todo: run this just before shutdown instead of inline?
			do_action($_POST['event_hook']);
			echo '<div id="message" class="updated"><p>Event Triggered: '.$_POST['event_hook'].'</p></div>';
		}
	}

	$events = get_option('cron');
	?>

	<div class="wrap" id="trigger-scheduled-event">
		<h2>Trigger Scheduled Event</h2>
	<?php
	
	if ( count($events)) {
		echo '<form method="post" action="tools.php?page=trigger_scheduled_event">';
		echo wp_nonce_field( 'trigger_event', '_trigger_scheduled_event_nonce', true, false );
		echo '	<p><select name="event_hook">';
		echo '		<option value="">Select a hook to trigger</option>';
		foreach ($events as $event) {
			if (is_array($event)) {
				echo '<option value="'.key($event).'">'.key($event).'</option>';
			}
		}
		echo '	</select>';
		echo '	<input type="submit" name="submit_trigger_scheduled_event" value="Trigger Now!" class="button button-primary" /></p>';
		echo '</form>';
	}
	else {
		echo '<p><strong>You do not appear to have any events scheduled</strong></p>';
	}

	?>
	</div>
	<?php

}