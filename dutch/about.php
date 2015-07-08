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


if (isset($_POST['submit_new_password'])) {

	$dob_year = (int) $_POST['dob_year'];
	$dob_month = (int) $_POST['dob_month'];
	$dob_day = (int) $_POST['dob_day'];

	$dob = formatDate($dob_year, $dob_month, $dob_day);

	if (empty($_POST['new_password_email'])) {
		$GLOBALS['script_error_log'][] = _("You must provide a valid email address.");
	}
	
	if (empty($GLOBALS['script_error_log'])) {
		
		$query = "
			SELECT user_id
			FROM " . $db->prefix . "_user
			WHERE
			user_email=" . $db->qstr($_POST['new_password_email']) . " AND 
			user_dob=" . $db->qstr($dob)
		;

		$result = $db->Execute($query, 1);
		
		if (!empty($result[0]['user_id'])) {
			// we reset the password
			$new_password  = $new_password = substr(md5(time()), 0, 5);
			
			// we send a message
			$query = "
				UPDATE " . $db->prefix . "_user
				SET user_password=" . $db->qstr(md5($new_password)) . "
				WHERE user_id=" . $result[0]['user_id']
			;
			
			$db->Execute($query);
			
			require_once('class/Mail/class.phpmailer.php');
	
			// email, subject, message
			$email_subject = "Here is your new password";
		
			$mail->Subject = $email_subject;
		
			$email_message = "Hi!\nThis is your new password: " . $new_password;
		
			// HTML-version of the mail
			$html  = "<HTML><HEAD><TITLE></TITLE></HEAD>";
			$html .= "<BODY>";
			$html .= utf8_decode(nl2br($email_message));
			$html .= "</BODY></HTML>";
	
			$mail->Body = $html;
			// non - HTML-version of the email
			$mail->AltBody   = utf8_decode(strip_tags($email_message));
			$mail->AddAddress($_POST['new_password_email']);
		
			if($mail->Send()) {
				// sent
				$body->set('new_password', 1);
			}
			// success message
			$GLOBALS['am_message_log'][] = _("Your profile information was updated");
		}
		else {
			$GLOBALS['script_error_log'][] = _("We could not find a match to your email and memorable date. Please use the 'contact us' form to inform us if you are unable to login.");
		}
	}
}
elseif (isset($_POST['send_email'])) {
	if (empty($_POST['email'])) {
		$GLOBALS['am_message_log'][] = _("You have not given us your email");
	}
	
	if (empty($_POST['message'])) {
		$GLOBALS['am_message_log'][] = _("You have not given us a message");		
	}
	
	if (empty($GLOBALS['script_error_log'])) {
		require_once('class/Mail/class.phpmailer.php');
	
		// email, subject, message
		$email_subject = "Email from Dutch";
		
		$mail->Subject = $email_subject;
		
		$email_message = $_POST['message'];
		
		// HTML-version of the mail
		$html  = "<HTML><HEAD><TITLE></TITLE></HEAD>";
		$html .= "<BODY>";
		$html .= utf8_decode(nl2br($email_message));
		$html .= "</BODY></HTML>";
	
		$mail->Body = $html;
		// non - HTML-version of the email
		$mail->AltBody   = utf8_decode(strip_tags($email_message));
		$mail->AddAddress($core_config['mail']['email_address']);
		$mail->From = $_POST['email'];
		
		if($mail->Send()) {
			// sent
			$body->set('contact_email_sent', 1);
		}
	}
}

?>