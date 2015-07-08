<?php

// ---------------------------------------------------------------------
// This file is part of Dutch
// 
// Copyright (C) 2003-2008 Barnraiser
// http://www.barnraiser.org/
// info@barnraiser.org
// 
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; see the file COPYING.txt.  If not, see
// <http://www.gnu.org/licenses/>
// --------------------------------------------------------------------


if (empty($uri_routing[1])) {
	header('location: /networks');
	exit;
}


if (isset($uri_routing[2]) && $uri_routing[2] == 'join') {
	
	$query = "
		SELECT tag_name
		FROM " . $db->prefix . "_tag
		WHERE user_id=" . $_SESSION['user_id'] . "
		AND tag_name=" . $db->qstr(urldecode($uri_routing[1]))
	;
	
	$result = $db->Execute($query);

	if (empty($result)) {
		$query = "
			SELECT tag_display_name
			FROM " . $db->prefix . "_tag
			WHERE tag_name=" . $db->qstr(urldecode($uri_routing[1]))
		;
	
		$result = $db->Execute($query);

		$rec = array();
		$rec['tag_name'] = urldecode($uri_routing[1]);
		$rec['tag_display_name'] = $result[0]['tag_display_name'];
		$rec['user_id'] = $_SESSION['user_id'];
	
		$table = $db->prefix . '_tag';
	
		$db->insertDB($rec, $table);
	
		$query = "
			SELECT user_name
			FROM " . $db->prefix . "_user
			WHERE user_id=" . $_SESSION['user_id']
		;
		
		$result = $db->Execute($query);

		$user_name = $result[0]['user_name'];

		// insert the notification
		$rec = array();
		$rec['create_datetime'] = time();
		$rec['update_datetime'] = time();
		$rec['user_id'] = $_SESSION['user_id'];
		$rec['tag_name'] = urldecode($uri_routing[1]);
		$rec['notification_type'] = $core_config['script']['notification_type']['join_network'];

		$table = $db->prefix . '_notification';

		$db->insertDB($rec, $table);
	}
	header('location: /network/' . $uri_routing[1]);
	exit;
}
elseif (isset($uri_routing[2]) && $uri_routing[2] == 'leave') {

	$query = "
		DELETE FROM " . $db->prefix . "_tag
		WHERE tag_name=" . $db->qstr(urldecode($uri_routing[1])) . "
		AND user_id=" . $_SESSION['user_id']
	;

	$db->Execute($query);

	header('location: /network/' . $uri_routing[1]);
	exit;
}
elseif (isset($_POST['post_reply'])) {

	$reply_notification = trim($_POST['reply_notification']);

	if (empty($reply_notification)) {
		$GLOBALS['script_error_log'][] = _("No reply was given.");
	}
	else {
		$reply_notification = parse($reply_notification);
	}

	if (empty($GLOBALS['script_error_log'])) {
		$rec = array();
		$rec['notification'] = $reply_notification;
		$rec['create_datetime'] = time();
		$rec['user_id'] = $_SESSION['user_id'];
		$rec['tag_name'] = urldecode($uri_routing[1]);
		$rec['parent_id'] = $_POST['notification_id'];

		$table = $db->prefix . '_notification';

		$db->insertDB($rec, $table);

		$query = "
			UPDATE " . $db->prefix . "_notification
			SET child_count=child_count+1, update_datetime=NOW()
			WHERE notification_id=" . $_POST['notification_id']
		;

		$db->Execute($query);

		// We send the notification orignator an email --------------
		$query = "
			SELECT u.user_email 
			FROM  " . $db->prefix . "_user u, " . $db->prefix . "_notification n 
			WHERE 
			n.user_id=u.user_id AND
			n.notification_id=" . $_POST['notification_id']
		;

		$result = $db->Execute($query);

		if (!empty($result[0]['user_email'])) {
			require_once('class/Mail/class.phpmailer.php');
		
			$mail->Subject = 'You have received a reply to your notification';
			
			$url = 'http://';
				
			if (isset($_SERVER['HTTPS'])) {
				if (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == 1) {
					$url = 'https://';
				}
			}

			$url .= $core_config['script']['core_domain'] . "/" . $uri_routing[1];

			$email_message = "You have received a reply to your notification.\n\n";
			$email_message .= "<hr />" . $reply_notification . "<hr />\n";
			$email_message .= "You can see it at <a href='" . $url . "'>" . $url . "</a>";
			$email_message .= "\n\nThis mail was sent from <a href='http://www.barnraiser.org/dutch'>Dutch</a>.";
		
		
			// HTML-version of the mail
			$html  = "<HTML><HEAD><TITLE></TITLE></HEAD>";
			$html .= "<BODY>";
			$html .= utf8_decode(nl2br($email_message));
			$html .= "</BODY></HTML>";
		
			$mail->Body = $html;
			// non - HTML-version of the email
			$mail->AltBody = utf8_decode($email_message);
		
			$mail->ClearAddresses();
			$mail->AddAddress($result[0]['user_email']);
		
			if($mail->Send()) {
				// sent
				$contact_msg = 1;
			}
		}

		header('location: /network/' . $uri_routing[1]);
		exit;
	}
}
elseif (isset($_POST['submit_notification'])) {
	$notification = trim($_POST['text']);

	if (empty($notification)) {
		$GLOBALS['script_error_log'][] = _("No notification set.");
	}

	if (empty($GLOBALS['script_error_log'])) {
		$rec = array();
		$rec['notification'] = parse($notification);
		$rec['create_datetime'] = time();
		$rec['update_datetime'] = time();
		$rec['user_id'] = $_SESSION['user_id'];
		$rec['tag_name'] = urldecode($uri_routing[1]);

		$table = $db->prefix . '_notification';

		$db->insertDB($rec, $table);

		header('location: /network/' . $uri_routing[1]);
		exit;
	}
}


$network_name = urldecode($uri_routing[1]);

// get tag
$query = "
	SELECT tag_name, tag_display_name
	FROM " . $db->prefix . "_tag t
	WHERE
	tag_name=" . $db->qstr($network_name)
;

$result = $db->Execute($query);

if (!empty($result)) {

	$network = array();
	$network['network_name'] = $result[0]['tag_name'];
	$network['network_display_name'] = $result[0]['tag_display_name'];


	// get number of joined members
	$query = "
		SELECT COUNT(user_id) AS people_total
		FROM " . $db->prefix . "_tag t
		WHERE
		t.tag_name=" . $db->qstr($network_name) . "
		GROUP BY t.tag_name"
	;
	
	$result = $db->Execute($query);

	if (!empty($result)) {
		$network['people_total'] = $result[0]['people_total'];
	}

	// Get contributors
	$query = "
		SELECT COUNT(DISTINCT user_id) AS contributors_total
		FROM " . $db->prefix . "_notification
		WHERE tag_name=" . $db->qstr($network['network_name']) . "
		AND parent_id=0
		GROUP BY tag_name"
	;

	$result = $db->Execute($query);

	if (!empty($result)) {
		$network['contributors_total'] = $result[0]['contributors_total'];
	}
	
	
	// Get contributions
	$query = "
		SELECT COUNT(user_id) AS contributions_total
		FROM " . $db->prefix . "_notification
		WHERE tag_name=" . $db->qstr($network['network_name']) . "
		AND parent_id=0"
	;

	$result = $db->Execute($query);

	if (!empty($result)) {
		$network['contributions_total'] = $result[0]['contributions_total'];
	}
	
	if (!empty($_SESSION['user_id'])) {
		$query = "
			SELECT tag_id
			FROM " . $db->prefix . "_tag
			WHERE tag_name=" . $db->qstr($network['network_name']) . "
			AND user_id=" . $_SESSION['user_id']
		;

		$result = $db->Execute($query);

		if (isset($result[0]['tag_id'])) {
			$network['has_joined'] = 1;
		}
	}
	
	$body->set('network', $network);
	$body->set('contributions_total', $network['contributions_total']);


	if (isset($uri_routing[2]) && $uri_routing[2] == "contributors") {
		$query = "
			SELECT u.*, COUNT(n.notification_id) AS total_notification
			FROM " . $db->prefix . "_user u
			LEFT JOIN " . $db->prefix . "_notification n
			ON u.user_id=n.user_id
			WHERE n.tag_name=" . $db->qstr($network_name) . "
			GROUP BY u.user_id
			ORDER BY u.user_name"
		;
	
		$result = $db->Execute($query);
	
		$body->set('users', $result);
	}
	elseif (isset($uri_routing[2]) && $uri_routing[2] == "people") {
		$query = "
			SELECT u.*, COUNT(n.notification_id) AS total_notification
			FROM " . $db->prefix . "_user u
			LEFT JOIN " . $db->prefix . "_notification n
			ON u.user_id=n.user_id
			WHERE n.tag_name=" . $db->qstr($network_name) . "
			GROUP BY u.user_id
			ORDER BY u.user_name"
		;
	
		$result = $db->Execute($query);
	
		$body->set('users', $result);
	}
	else {

		// SELECT NOTIFICATIONS FOR THIS NETWORK ---------------------------------------------
		$from = 0;

		if (isset($uri_routing[2]) && substr($uri_routing[2], 0, 4) == 'page') {
			if (is_numeric(substr($uri_routing[2], 4))) {
				$from = (int) substr($uri_routing[2], 4) * AM_MAX_LIST_ROWS;
				$body->set('page', substr($uri_routing[2], 4));
			}
			else {
				$body->set('page', "0");
			}
		}
		else {
			$body->set('page', "0");
		}
			
		$query = "
			SELECT n.*, u.*
			FROM " . $db->prefix . "_notification n
			INNER JOIN " . $db->prefix . "_user u
			ON n.user_id=u.user_id
			WHERE n.tag_name=" . $db->qstr($network['network_name']) . "
			AND n.parent_id=0
			ORDER BY n.update_datetime DESC"
		;
	
		$result = $db->Execute($query, AM_MAX_LIST_ROWS, $from);
	
		if (!empty($result)) {
			$body->set('notifications', $result);
		}


	}

	if (!empty($_SESSION['user_id'])) {	
		// MY NETWORKS -----------------------------
		$query = "
			SELECT COUNT(*) AS tag_total, t.tag_name, t.tag_display_name
			FROM " . $db->prefix . "_tag t
			WHERE t.user_id=" . $_SESSION['user_id'] . "
			GROUP BY t.tag_name
			ORDER BY tag_total DESC"
		;
		
		$result = $db->Execute($query);
		
		if (!empty($result)) {
			usort($result, 'tag_cmp');
		}
		
		$body->set('my_networks', $result);
	}
}
else {
	header('location: /networks');
	exit;
}

?>