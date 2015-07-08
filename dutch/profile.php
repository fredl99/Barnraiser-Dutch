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


if (isset($_POST['create_relation'])) {

	$rec = array();
	$rec['user_id'] = $_SESSION['user_id'];
	$rec['user_id_relation'] = $uri_routing[1];
	$rec['user_id_relation'] = $uri_routing[1];
	$rec['relation_create_datetime'] = time();
	
	$table = $db->prefix . '_relation';
	
	$db->insertDB($rec, $table);
}
elseif (isset($_POST['delete_relation'])) {

	$query = "
		DELETE FROM " . $db->prefix . "_relation
		WHERE 
		user_id=" . $_SESSION['user_id'] . " AND 
		user_id_relation=" . $uri_routing[1]
	;

	$db->Execute($query);
}
elseif (isset($_POST['send_email'])) {
	if (empty($_POST['email_subject'])) {
		$GLOBALS['script_error_log'][] = _("Email subject empty.");
	}

	if (empty($_POST['email_message'])) {
		$GLOBALS['script_error_log'][] = _("Email message empty.");
	}

	if (empty($GLOBALS['script_error_log'])) {

		$query = "
			SELECT user_email
			FROM " . $db->prefix . "_user
			WHERE user_id=" . $uri_routing[1]
		;
		
		$result = $db->Execute($query);

		$user_email = $result[0]['user_email'];	

	
				
		require_once('class/Mail/class.phpmailer.php');
		$mail->From = $_SESSION['user_email'];
		$mail->FromName =	$_SESSION['user_name'];
		$email_subject = stripslashes(htmlspecialchars($_POST['email_subject']));
		
		$mail->Subject = $email_subject;
	
		$email_message = stripslashes(htmlspecialchars($_POST['email_message']));
	
		$email_message .= "\n\nThis mail was sent from the knowledge sharing network";
	
	
		// HTML-version of the mail
		$html  = "<HTML><HEAD><TITLE></TITLE></HEAD>";
		$html .= "<BODY>";
		$html .= utf8_decode(nl2br($email_message));
		$html .= "</BODY></HTML>";
	
		$mail->Body = $html;
		// non - HTML-version of the email
		$mail->AltBody   = utf8_decode($email_message);
	
		$mail->ClearAddresses();
		$mail->AddAddress($user_email);
	
		if($mail->Send()) {
			// sent
			$contact_msg = 1;
		}
	}
}


if (isset($uri_routing[1]) && is_numeric($uri_routing[1])) {

	$query = "
		SELECT COUNT(*) AS contributions_total
		FROM " . $db->prefix . "_notification n
		WHERE n.user_id=" . $uri_routing[1]
	;
	
	$result = $db->Execute($query);
	
	$body->set('contributions_total', $result[0]['contributions_total']);

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
		SELECT DISTINCT n.*, u.*, t.tag_display_name
		FROM " . $db->prefix . "_notification n
		INNER JOIN " . $db->prefix . "_user u
		ON u.user_id=n.user_id
		LEFT JOIN " . $db->prefix . "_tag t
		ON n.tag_name=t.tag_name
		WHERE n.user_id=" . $uri_routing[1] . "
		ORDER BY n.create_datetime DESC"
	;

	$result = $db->Execute($query, AM_MAX_LIST_ROWS, $from);
	
	if (!empty($result)) {
		$body->set('notifications', $result);
	}
}

// SELECT NETWORKS
$query = "
	SELECT COUNT(*) AS tag_total, t.tag_name, t.tag_display_name
	FROM " . $db->prefix . "_tag t
	WHERE t.user_id=" . $uri_routing[1] . "
	GROUP BY t.tag_name
	ORDER BY tag_total DESC"
;

$result = $db->Execute($query);

if (!empty($result)) {
	usort($result, 'tag_cmp');
}

$body->set('networks', $result);

if (!empty($_SESSION['user_id'])) {
	if ($uri_routing[1] != $_SESSION['user_id']) {
		$query = "
			SELECT tag_name
			FROM " . $db->prefix . "_tag
			WHERE user_id=" . $_SESSION['user_id']
		;
	
		$result = $db->Execute($query);
		$tags = "";
		foreach($result as $key => $val):
			$tags .= $db->qstr($val['tag_name']) . ', ';
		endforeach;
		$tags = rtrim($tags, ', ');
	
		if (!empty($result)) {
			$query = "
				SELECT COUNT(*) AS tag_total, t.tag_name, t.tag_display_name
				FROM " . $db->prefix . "_tag t
				WHERE t.user_id=" . $uri_routing[1] . "
				AND t.tag_name IN (" . $tags . ")
				GROUP BY t.tag_name
				ORDER BY tag_total DESC"
			;
		
			$result = $db->Execute($query);
	
			if (!empty($result)) {
				usort($result, 'tag_cmp');
			}
		
			$body->set('shared_networks', $result);
		}

		// ASCERTAIN RELATIONSHIP
		if ($uri_routing[1] != $_SESSION['user_id']) { // It is not me
			$query = "
				SELECT user_id 
				FROM " . $db->prefix . "_relation
				WHERE user_id=" . $_SESSION['user_id'] . " AND 
				user_id_relation=" . $uri_routing[1]
			;
		
			$result = $db->Execute($query);
			
			if (isset($result[0]['user_id'])) {
				$body->set('relation', 1);
			}
		}
	}
}

$query = "
	SELECT *
	FROM " . $db->prefix . "_user
	WHERE user_id=" . $uri_routing[1]
;

$result = $db->Execute($query);

if (!empty($result)) {
	$body->set('profile', $result[0]);

	// work out privacy
	if (isset($result[0]['user_privacy']) && $result[0]['user_privacy'] == 2) {

		$body->set('display_profile', 1);
	}
	elseif (isset($result[0]['user_privacy']) && $result[0]['user_privacy'] == 1 && isset($_SESSION['user_id'])) {

		$body->set('display_profile', 1);
	}
}

?>