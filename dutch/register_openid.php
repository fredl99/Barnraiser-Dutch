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


if (isset($_POST['submit_openid_registration']) && isset($_SESSION['user_authenticated_openid'])) {

	$_POST['user_name'] = trim($_POST['user_name']);
	$_POST['user_location'] = trim($_POST['user_location']);
	$_POST['user_email'] = trim($_POST['user_email']);

	if (empty($_POST['user_name'])) {
		$GLOBALS['script_error_log'][] = _("You must provide a name");
	}

	if (!checkEmail($_POST['user_email'])) {
		$GLOBALS['script_error_log'][] = _("Your email address does not like a valid email address");
	}

	if (!empty($core_config['am']['email_domains'])) {
		$email_domain =  substr(strrchr($_POST['user_email'], "@"), 1 );

		if (!in_array($email_domain, $core_config['registration']['email_domains'])) {
			$error_domains = implode(", ", $core_config['registration']['email_domains']);
			$error_txt = _("You must provide us with a valid email address. This has to be within the domains: {error_domains}.");
			$error_txt = str_replace("{error_domains}", $error_domains, $error_txt);
			$GLOBALS['script_error_log'][] = _($error_txt);
		}
	}

	if (empty($_POST['user_location'])) {
		$GLOBALS['script_error_log'][] = _("You must provide a location");
	}

	if (empty($_POST['tos'])) {
		$GLOBALS['script_error_log'][] = _("You must agree to our terms of service");
	}
	
	if (!match_maptcha($_POST['maptcha_text'])) {
		$GLOBALS['script_error_log'][] = _("You failed the math test dismally. Please try again");
	}
	
	if (empty($GLOBALS['script_error_log'])) {
		// insert into db here
		
		$rec = array();
		$rec['user_name'] = $_POST['user_name'];
		$rec['user_openid'] = $_SESSION['user_authenticated_openid'];
		$rec['user_location'] = $_POST['user_location'];
		$rec['user_create_datetime'] = time();
		$rec['user_email'] = $_POST['user_email'];
		$rec['user_next_digest_datetime'] = time();
		$rec['user_privacy'] = 1;
		$rec['user_live'] = 1;
		$rec['user_last_login_datetime'] = time();

		$table = $db->prefix . '_user';
		
		$db->insertDB($rec, $table);

		$user_id = $db->insertID();

		$query = "
			SELECT user_id, user_email, user_name, user_dob, user_location,
			user_privacy, user_openid 
			FROM " . $db->prefix . "_user 
			WHERE 
			user_id=" . $user_id
		;

		$result = $db->Execute($query, 1);

		if (isset($result[0]['user_id'])) { // We log them in
			$_SESSION['user_id'] = $result[0]['user_id'];
			$_SESSION['user_email'] = $result[0]['user_email'];
			$_SESSION['user_name'] = $result[0]['user_name'];
			$_SESSION['user_dob'] = $result[0]['user_dob'];
			$_SESSION['user_location'] = $result[0]['user_location'];
			$_SESSION['user_privacy'] = $result[0]['user_privacy'];
			$_SESSION['user_openid'] = $result[0]['user_privacy'];
			
			$query = "
				UPDATE " . $db->prefix . "_user
				SET user_last_login_datetime=" . $db->qstr(time()) . "
				WHERE user_id=" . $_SESSION['user_id']
			;
			$db->Execute($query);
	
			header('location: /notify');
			exit;
			
		}
	}
}


$maptcha = gen_maptcha();
$body->set('maptcha', $maptcha);

//$body->set('domain_name', $core_config['script']['core_domain']);

if (!empty($core_config['registration']['email_domains'])) {
	$body->set('email_domains', $core_config['registration']['email_domains']);
}


function gen_maptcha() {
	$numbers = array();
	$numbers['ascii'] = array(0,1,2,3,4,5,6,7,8,9,10);
	$numbers['words'] = array('zero','one','two','three','four','five','six','seven','eight','nine','ten');

	$operators = array();
	$operators['ascii'] = array('+','-','*');
	$operators['words'] = array('plus','minus','times');
	
	$_SESSION['maptcha'] = "";
	
	$m = 'ascii';
	if (rand(0,1)) {
		$m = 'words';
	}
	$n1 = rand(0, count($numbers[$m])-1);
	
	$x = $numbers[$m][$n1];
	
	$m = 'ascii';
	if (rand(0,1)) {
		$m = 'words';
	}
	$n2 = rand(0, count($numbers[$m])-1);
	
	$y = $numbers[$m][$n2];
	
	$m = 'ascii';
	if (rand(0,1)) {
		$m = 'words';
	}
	$n3 = rand(0, count($operators[$m])-1);
	
	$o = $operators[$m][$n3];
	eval('$_SESSION[\'maptcha\']=' . intval($numbers['ascii'][$n1]) . $operators['ascii'][$n3] . intval($numbers['ascii'][$n2]) . ';');
	
	if (rand(0,1)) {
		return 'Calculate this: ' . $x . ' ' . $o . ' ' .$y;
	}
	elseif (rand(0,1)) {
		return 'Solve this equation: ' . $x . ' ' . $o . ' ' .$y;
	}
	elseif (rand(0,1)) {
		return 'Work this out: ' . $x . ' ' . $o . ' ' .$y . " ?";
	}
	else {
		return 'Solve this little puzzle: ' . $x . ' ' . $o . ' ' .$y;
	}
}

function match_maptcha($answer) {
	return intval($answer) == intval($_SESSION['maptcha']);
}

?>