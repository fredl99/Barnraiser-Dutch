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


$path = dirname(__FILE__) . "/";

include_once ($path . "../config/core.config.php");
include_once ($path . "../inc/functions.inc.php");

// SETUP AROUNDMe CORE ----------------------------------------------
require_once($path . '../class/Db.class.php');
$db = new Database($core_config['db']);

$query = "
	SELECT *
	FROM " . $db->prefix . "_user
	WHERE DATE(user_next_digest_datetime)=DATE(NOW())"
;

$result = $db->Execute($query);

if (!empty($result)) {
	foreach($result as $key => $val):
		
		$content = "New stuff from the knowledge network!<br /><br />";

		// number of new notifications in dutch ----------------------------------------------
		$query = "
			SELECT COUNT(*) AS total_notifications
			FROM " . $db->prefix . "_notification
			WHERE create_datetime>NOW() - INTERVAL 7 DAY"
		;
		
		$result2 = $db->Execute($query);
		
		$content .= "There were " . $result2[0]['total_notifications'] . " new notifications added in the last week.";
		// ----------------------------------------------------------------------------------
		
		// need to select user-tags ---------------------------------------------------------
		$query = "
			SELECT tag_name
			FROM " . $db->prefix . "_tag
			WHERE user_id=" . $val['user_id']
		;
		
		$result2 = $db->Execute($query);
		
		$tags = "";
		foreach($result2 as $key2 => $val2):
			$tags .= $db->qstr($val2['tag_name']) . ', ';
		endforeach;
		$tags = rtrim($tags, ', ');
		
		if (!empty($tags)) {
		
			$query = "
				SELECT COUNT(*) AS total_notifications
				FROM " . $db->prefix . "_notification
				WHERE create_datetime> NOW() - INTERVAL 7 DAY
				AND tag_name IN (" . $tags . ")"
			;
		
			$result2 = $db->Execute($query);
			
			$content .= " Of those " . $result2[0]['total_notifications'] . " are in your favourites.";
			
			if ($result2[0]['total_notifications'] != 0) {
				$content .= " They are spread as listed below:";
				$content .= "<ul>";
				$query = "
					SELECT n.tag_name, t.tag_display_name, COUNT(n.notification_id) AS total_notifications
					FROM " . $db->prefix . "_notification n, " . $db->prefix . "_tag t 
					WHERE 
					n.create_datetime>NOW() - INTERVAL 7 DAY AND 
					n.tag_name IN (" . $tags . ") AND 
					n.tag_name=t.tag_name 
					GROUP BY n.tag_name
					ORDER BY n.tag_name"
				;
				
				$result2 = $db->Execute($query);
				
				foreach($result2 as $key2 => $val2):
					$query = "
						SELECT update_datetime
						FROM " . $db->prefix . "_notification
						WHERE tag_name=" . $db->qstr($val2['tag_name']) . "
						ORDER BY update_datetime DESC
						LIMIT 1"
					;
					
					$result3 = $db->Execute($query);

					$content .= "<li>" . $val2['tag_display_name'] . ": " . $val2['total_notifications'] . " added. Last updated " . timeDiff($result3[0]['update_datetime']) . "</li>";
				endforeach;
				
				$content .= "</ul>";
			}
		}

		$query = "
			SELECT COUNT(*) AS total_users
			FROM " . $db->prefix . "_user
			WHERE user_create_datetime> NOW() - INTERVAL 7 DAY"
		;
		
		
		$result2 = $db->Execute($query);
		
		$content .= "<br />";
		$content .= $result2[0]['total_users'] . " people have registered in the last week.";
		
		$query = "
			SELECT *
			FROM " . $db->prefix . "_user
			ORDER BY user_create_datetime DESC
			LIMIT 1"
		;
		
		$result2 = $db->Execute($query);
		
		$url = $core_config['am']['core_domain'];

		$content .= " <a href=\"" . $url . "/profile/" . $result2[0]['user_id'] . "\">" . $result2[0]['user_name'] . "</a> is the newest person to register with us.";
		
		$content .= "<br /><br />This email was sent automatically from the knowledge network. To unsubscribe please visit <a href='" . $url . "'>" . $url . "</a>, login, go to your account and uncheck the newsletter digest checkbox. ";

		// setup mail
		require_once($path . '../class/Mail/class.phpmailer.php');
		$mail->From = $core_config['mail']['email_address'];
		
		// email, subject, message
		$email_subject = "Newsletter from the knowledge network!";
		
		$mail->Subject = utf8_decode($email_subject);
			
		// HTML-version of the mail
		$email_message_html = $content;
		$html_content = $email_message_html;
		
		$mail->Body = utf8_decode($html_content);

		// text version of email
		$email_message_txt = $content;
		$email_message_txt = utf8_decode($email_message_txt);
		
		$mail->AltBody = $email_message_txt;
		$mail->ClearAddresses();
		$mail->AddAddress($val['user_email']);
		
		if($mail->Send()) {
			// sent
			$query = "
				UPDATE " . $db->prefix . "_user
				SET user_next_digest_datetime=NOW() + INTERVAL 7 DAY
				WHERE user_id=" . $val['user_id']
			;
			
			$db->Execute($query);
		}
		
	endforeach;
}

?>