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


if (!isset($_SESSION['user_id'])) {
	header('location: /');
	exit;
}

if (isset($_POST['save_profile_information'])) {

	$dob_year = (int) $_POST['dob_year'];
	$dob_month = (int) $_POST['dob_month'];
	$dob_day = (int) $_POST['dob_day'];

	$dob = formatDate($dob_year, $dob_month, $dob_day);

	if (empty($_POST['user_name'])) {
		$GLOBALS['script_error_log'][] = _("You must provide a name.");
	}
	
	if (empty($GLOBALS['script_error_log'])) {
		
		$query = "UPDATE " . $db->prefix . "_user 
			SET 
			user_name=" . $db->qstr($_POST['user_name']) . ",
			user_dob=" . $db->qstr($dob) . ",
			user_location=" . $db->qstr($_POST['user_location']) . " 
			WHERE
			user_id=" . $_SESSION['user_id']
		;

		$db->Execute($query);
		
		if (empty($GLOBALS['script_error_log'])) {
			$_SESSION['user_name'] = $_POST['user_name'];
			$_SESSION['user_dob'] = $dob;
			$_SESSION['user_location'] = $_POST['user_location'];

			// success message
			$GLOBALS['am_message_log'][] = _("Your profile information was updated");
		}
	}
}
elseif (isset($_POST['save_profile_privacy'])) {

	$query = "UPDATE " . $db->prefix . "_user
		SET
		user_privacy=" . $_POST['user_privacy'] . "
		WHERE
		user_id=" . $_SESSION['user_id']
	;

	$db->Execute($query);

	if (empty($GLOBALS['script_error_log'])) {
		$_SESSION['user_privacy'] = $_POST['user_privacy'];

		// success message
		$GLOBALS['am_message_log'][] = _("Your profile privacy information was updated");
	}	
}
elseif (isset($_POST['change_user_email'])) {
	$_POST['user_email1'] = trim($_POST['user_email1']);
	$_POST['user_email2'] = trim($_POST['user_email2']);

	// We check to see if there is a list of valid email addresses
	if (!empty($core_config['am']['email_domains'])) {
		$email_domain =  substr(strrchr($_POST['user_email1'], "@"), 1 );

		if (!in_array($email_domain, $core_config['am']['email_domains'])) {
			$error_domains = implode(", ", $core_config['am']['email_domains']);
			$error_txt = _("You must provide us with a valid email address. This has to be within the domains: {error_domains}.");
			$error_txt = str_replace("{error_domains}", $error_domains, $error_txt);
			$GLOBALS['script_error_log'][] = $error_txt;
		}
	}

	if (empty($GLOBALS['script_error_log'])) {
		if (empty($_POST['user_email1'])) {
			$GLOBALS['script_error_log'][] = _("You must provide us with an email address");
		}
	
		if ($_POST['user_email1'] != $_POST['user_email2']) {
			$GLOBALS['script_error_log'][] = _("Your email addresses did not match");
		}
		
		if (empty($GLOBALS['script_error_log'])) {
			if (!checkEmail($_POST['user_email1'])) {
				$GLOBALS['script_error_log'][] = _("Your email address does not like a valid email address");
			}
		}
	}
	
	if (empty($GLOBALS['script_error_log'])) {

		$query = "
			UPDATE " . $db->prefix . "_user
			SET user_email=" . $db->qstr(trim($_POST['user_email1'])) . " 
			WHERE user_id=" . $_SESSION['user_id']
		;

		$db->Execute($query);

		header('location: /disconnect');
		exit;
	}
}
elseif (isset($_POST['save_email_digest'])) {
	if (!empty($_POST['email_digest'])) {
		$query = "
			UPDATE " . $db->prefix . "_user
			SET user_next_digest_datetime=NOW() + INTERVAL 7 DAY
			WHERE user_id=" . $_SESSION['user_id']
		;
		
		$db->Execute($query);
		$body->set('email_digest', 1);
	}
	else {
		$query = "
			UPDATE " . $db->prefix . "_user
			SET user_next_digest_datetime='0000-00-00 00:00:00'
			WHERE user_id=" . $_SESSION['user_id']
		;
		
		$db->Execute($query);
	}
}
elseif (isset($_POST['change_user_password'])) {
	if (empty($_POST['user_password_old'])) {
		$GLOBALS['script_error_log'][] = _("You did not give us the correct current password");
	}
	
	if ($_POST['user_password1'] != $_POST['user_password2']) {
		$GLOBALS['script_error_log'][] = _("Your new passwords did not match");
	}
	
	if (strlen($_POST['user_password1']) < 2) {
		$GLOBALS['script_error_log'][] = _("Your password must be longer than 2 characters");
	}
	
	if (empty($GLOBALS['script_error_log'])) {
		$query = "
			SELECT user_id
			FROM " . $db->prefix . "_user
			WHERE user_id=" . $_SESSION['user_id'] . "
			AND user_password=" . $db->qstr(md5($_POST['user_password_old']))
		;
		
		$result = $db->Execute($query);
		
		if (!isset($result[0]['user_id'])) {
			$GLOBALS['script_error_log'][] = _("You did not give us the correct current password");
		}
	}
	
	if (empty($GLOBALS['script_error_log'])) {
		$query = "
			UPDATE " . $db->prefix . "_user
			SET user_password=" . $db->qstr(md5($_POST['user_password1'])) . "
			WHERE
			user_id=" . $_SESSION['user_id'] . " AND
			user_password=" . $db->qstr(md5($_POST['user_password_old']))
		;
		
		$db->Execute($query);
		// success message
		$GLOBALS['am_message_log'][] = _("Your password has been changed");
	}
}




// CHECK TO DISPLAY AVATAR DELETE BUTTON ------
$av = glob($core_config['file']['dir'] . "avatars/" . $_SESSION['user_id'] . "/100*");

if (isset($av[0])) {
	$body->set('display_avatar_delete_button', 1);
}

$query = "
	SELECT user_next_digest_datetime
	FROM " . $db->prefix . "_user
	WHERE user_id=" . $_SESSION['user_id']
;

$result = $db->Execute($query);
if ($result[0]['user_next_digest_datetime'] != '0000-00-00 00:00:00') {
	$body->set('email_digest', 1);
}

?>