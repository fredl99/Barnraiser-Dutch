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



// MAIN INCLUDES ---------------------------------------------------------
include_once ("../config/core.config.php");
include_once ("../inc/functions.inc.php");


// SESSION HANDLER -------------------------------------------------------
// sets up all session and global vars 
session_name($core_config['php']['session_name']);
session_start();


// ERROR HANDLING
// this is accessed and updated with all errors thoughtout this build
// processing regularly checks if empty before continuing
$GLOBALS['script_error_log'] = array();



// SETUP TEMPLATE -------------------------------------------
require_once('../class/Template.class.php');
$tpl = new Template();




if (isset($_POST['start_install'])) {

	$tpl->set('display', 'install_form');

}
elseif (isset($_POST['perform_installation'])) {
	
	// set the session_id -----------------
	if ($core_config['php']['session_name'] == "PHPSESSIDDUTCH") {
		$php_session_name = 'PHPSESSIDDUTCH';
	
		for($i = 0; $i < 4; $i++) {
			$n = rand(0, 9);
			$php_session_name .= $n;
		}
		writeToConfig('$core_config[\'php\'][\'session_name\']', $php_session_name);
	}
	
	
	// setup domain -----------------
	$domain = $_SERVER['SERVER_NAME'];
	
	// remove trailing slash
	if (substr($domain, -1) == "/") {
		$domain = substr($domain, 0, -1);
	}

	writeToConfig('$core_config[\'script\'][\'core_domain\']', $domain);
	


	// create database --------------
	$core_config['db']['host'] = $_POST['database_host'];
	$core_config['db']['user'] = $_POST['database_user'];
	$core_config['db']['pass'] = $_POST['database_password'];
	$core_config['db']['db'] = $_POST['database_db'];
	
	$connection = @mysql_connect($core_config['db']['host'], $core_config['db']['user'] ,$core_config['db']['pass']);

	if (!is_resource($connection)) {
		$GLOBALS['script_error_log'][] = _("The database connection could not be created. Please check your database settings.");
	}
	else {
		// We write the config
		writeToConfig('$core_config[\'db\'][\'host\']', $core_config['db']['host']);
		writeToConfig('$core_config[\'db\'][\'user\']', $core_config['db']['user']);
		writeToConfig('$core_config[\'db\'][\'pass\']', $core_config['db']['pass']);
		writeToConfig('$core_config[\'db\'][\'db\']', $core_config['db']['db']);
		
		$db_selected = mysql_select_db($core_config['db']['db'], $connection);
		
		if (!$db_selected) { // we create the database

			$query = "SET NAMES 'utf8'";
	
			mysql_query($query, $connection);
	
			$query = "SET CHARACTER SET 'utf8'";
	
			mysql_query($query, $connection);
			
			$query = "CREATE DATABASE " . $core_config['db']['db'] . " DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
			
			mysql_query($query, $connection);
	
			$db_selected = mysql_select_db($core_config['db']['db'], $connection);
		}
		
		if (!$db_selected) {
			$GLOBALS['script_error_log'][] = _("The database could not be created. Check your MySql log for errors.");
		}
		else {
			// we populate the database
			$queries = file_get_contents('../install/install.sql');
	
			$pattern = "/CREATE(.*?);/s";
			
			if (preg_match_all($pattern, $queries, $matches)) {
				
				if (isset($matches[0])) {
					foreach ($matches[0] as $key => $i):
						$query = str_replace(';', '', $i);
						
						mysql_query($query, $connection);
					endforeach;
				}
			}
		}
	}


	// Create account --------------
	if (empty($GLOBALS['script_error_log'])) {
		$_POST['user_name'] = trim($_POST['user_name']);
		$_POST['user_location'] = trim($_POST['user_location']);
		$_POST['user_email'] = trim($_POST['user_email']);

		if (empty($_POST['user_name'])) {
			$GLOBALS['script_error_log'][] = _("You must provide a name.");
		}

		if (!checkEmail($_POST['user_email'])) {
			$GLOBALS['script_error_log'][] = _("Your email address does not like a valid email address.");
		}
		
		if (empty($GLOBALS['script_error_log'])) {
			require_once('../class/Db.class.php');
			$db = new Database($core_config['db']);

			$query = "
				SELECT user_id
				FROM " . $db->prefix . "_user
				WHERE user_email=" . $db->qstr($_POST['user_email'])
			;
			
			$result = $db->Execute($query);
			
			if (!empty($result[0])) {
				$user_id = $result[0]['user_id'];
			}
		}
	
		if (empty($_POST['user_location'])) {
			$GLOBALS['script_error_log'][] = _("You must provide a location.");
		}
	
		if ($_POST['user_password1'] != $_POST['user_password2']) {
			$GLOBALS['script_error_log'][] = _("Your new passwords did not match.");
		}
		
		if (strlen($_POST['user_password1']) < 2) {
			$GLOBALS['script_error_log'][] = _("Your password must be longer than 2 characters.");
		}
	
		$dob_year = (int) $_POST['dob_year'];
		$dob_month = (int) $_POST['dob_month'];
		$dob_day = (int) $_POST['dob_day'];
	
		$dob = formatDate($dob_year, $dob_month, $dob_day);
	
		if (empty($GLOBALS['script_error_log'])) {
			if (isset($user_id)) {
		
				$query = "UPDATE " . $db->prefix . "_user 
					SET 
					user_name=" . $db->qstr($_POST['user_name']) . ",
					user_location=" . $db->qstr($_POST['user_location']) . ", 
					user_password=" . $db->qstr(md5($_POST['user_password1'])) . ", 
					user_email=" . $db->qstr($_POST['user_email']) . ", 
					user_dob=" . $db->qstr($dob) . " 
					WHERE
					user_id=" . $user_id
				;
		
				$db->Execute($query);

				$user_id = $db->insertID();
			}
			else { // we insert record

				$rec = array();
				$rec['user_name'] = $_POST['user_name'];
				$rec['user_location'] = $_POST['user_location'];
				$rec['user_create_datetime'] = time();
				$rec['user_password'] = md5($_POST['user_password1']);
				$rec['user_email'] = $_POST['user_email'];
				$rec['user_dob'] = $dob;
				$rec['user_live'] = 1;
				
				$table = $db->prefix . '_user';
				
				$db->insertDB($rec, $table);
		
				$user_id = $db->insertID();
			}
		}
	}


	// configure email --------------
	$core_config['mail']['host'] = 	$_POST['email_host'];
	$core_config['mail']['email_address'] = $_POST['email_address'];
	$core_config['mail']['from_name'] = $_POST['email_from_name'];

	writeToConfig('$core_config[\'mail\'][\'host\']', $core_config['mail']['host']);
	writeToConfig('$core_config[\'mail\'][\'email_address\']', $core_config['mail']['email_address']);
	writeToConfig('$core_config[\'mail\'][\'from_name\']', $core_config['mail']['from_name']);

	if (!empty($_POST['smtp_user'])) {
		$core_config['mail']['smtp']['username'] = $_POST['smtp_user'];
		$core_config['mail']['smtp']['password'] = $_POST['smtp_password'];

		writeToConfig('$core_config[\'mail\'][\'smtp\'][\'username\']', $core_config['mail']['smtp']['username']);
		writeToConfig('$core_config[\'mail\'][\'smtp\'][\'password\']', $core_config['mail']['smtp']['password']);
	}
	
	// Send email test message
	require_once('../class/Mail/class.phpmailer.php');
	$mail->From = $core_config['mail']['email_address'];
		
	$email_subject = _("Test email from Dutch");
			
	$mail->Subject = utf8_decode($email_subject);

	$email_message_txt = "This is a test message from Dutch to confirm that your email configuration is correct.";
	$mail->Body = utf8_decode($email_message_txt);
	
	$mail->AltBody = $email_message_txt;
	$mail->AddAddress($_POST['user_email']);

	if($mail->Send()) {
		// sent
		$tpl->set('email_sent', 1);
	}
	else {
		$GLOBALS['script_error_log'][] = _("Either your email server configuration or your email address is incorrect as Dutch failed to send you a test email.");
	}


	if (empty($GLOBALS['script_error_log'])) {
		// set the installation date MM-DD-YYYY
		$date = date("m-d-Y");
		writeToConfig('$core_config[\'release\'][\'install_date\']', $date);
		
		
		// We set the installer up as a maintainer
		writeToConfig('$core_config[\'security\'][\'maintainer_userids\']', $user_id);
		
		
		// set this file to not readable
		if (@chmod ('../install/installer.php', 0000)) { // disable this installer

		header("Location: /");
		exit;
			
		}
		else {
			$tpl->set('display', 'no_chmod_allowed');
		}
	}
	else {
		$tpl->set('display', 'install_form');
	}
}
else { // pre-start checks and setup

	$system_checks = array();
	$is_error = 0;
	
	// Check for MySQL
	if (!function_exists('mysql_connect')) {
		$system_check['result'] = _("Dutch needs MySQL. Please add MySQL support to PHP.");
		$system_check['is_valid'] = 0;
		$is_error = 1;
	}
	else {
		$system_check['result'] = _("PHP MySQL exists");
		$system_check['is_valid'] = 1;
	}
	
	array_push($system_checks, $system_check);
	
	
	// Check for PHP 5
	if ( (int) phpversion() < 5) {
		$system_check['is_valid'] = 0;
		$system_check['result'] = _("Dutch needs PHP to be version 5.0 or greater. Your version is {version}.");
		$system_check['result'] = str_replace("{version}", phpversion(), $system_check[0]['note']);
		$is_error = 1;
	}
	else {
		$system_check['result'] = _("PHP version is higher than version 5.0");
		$system_check['is_valid'] = 1;
	}
	
	array_push($system_checks, $system_check);

	
	// Check for Curl
	if (!function_exists('curl_init') || !function_exists('curl_setopt') || !function_exists('curl_exec')) {
		$system_check['is_valid'] = 0;
		$system_check['result'] = _("Dutch needs Curl. Please add Curl to PHP.");
		$is_error = 1;
	}
	else {
		$system_check['result'] = _("Curl exists.");
		$system_check['is_valid'] = 1;
	}
	
	array_push($system_checks, $system_check);

	
	// Check for BCMath
	if (!extension_loaded ('bcmath')) {
		$system_check['is_valid'] = 0;
		$system_check['result'] = _("Dutch needs BCMath. Please add BCMath to PHP.");
		$is_error = 1;
	}
	else {
		$system_check['result'] = _("BCMath exists.");
		$system_check['is_valid'] = 1;
	}
	
	array_push($system_checks, $system_check);


	// Check for gettext
	if (!function_exists('gettext')) {
		$system_check['is_valid'] = 0;
		$system_check['result'] = _("Dutch needs gettext. Please add gettext to PHP.");
		$is_error = 1;
	}
	else {
		$system_check['result'] = _("Gettext exists.");
		$system_check['is_valid'] = 1;
	}

	
	// Check for GD Library
	if (function_exists('gd_info')) {
		$gd_info = gd_info();
		
		if (!isset($gd_info['GD Version'])) {
			$system_check['is_valid'] = 0;
			$system_check['result'] = _("Dutch needs GD Library. Please add GD Library support to PHP.");
			$is_error = 1;
		}
		else {
			$system_check['result'] = _("Version 2.0 or greater of GD Library exists.");
			$system_check['is_valid'] = 1;
		}
	}
	else {
		$system_check['is_valid'] = 0;
		$system_check['result'] = _("Dutch needs GD Library. Please add GD Library support to PHP.");
		$is_error = 1;
	}
	
	array_push($system_checks, $system_check);

	
	// Check for Directory integrity
	if (!is_dir('../../')) {
		$system_check['is_valid'] = 0;
		$system_check['result'] = _("The directory structure is not intact. Please upload the entire release directory structure.");
		$is_error = 1;
	}
	else {
		$system_check['result'] = _("The directory structure is intact.");
		$system_check['is_valid'] = 1;
	}
	
	array_push($system_checks, $system_check);

	
	// Check Config file is writeable
	if (!is_writable("../config/core.config.php")) {
		$system_check['is_valid'] = 0;
		$system_check['result'] = _("PHP cannot write to the config file. Please check your permissions.");
		$is_error = 1;
	}
	else {
		$system_check['result'] = _("The config file is writeable.");
		$system_check['is_valid'] = 1;
	}
	
	array_push($system_checks, $system_check);
	
	$tpl->set('system_checks', $system_checks);
	$tpl->set('is_error', $is_error);
}

$tpl->set('core_config', $core_config);
echo $tpl->fetch('installer.tpl.php');


function writeToConfig($where, $what) {
	$config = file('../config/core.config.php');
	foreach($config as $key => $val) {
		if (strstr($val, $where)) {
			$config[$key] = $where . ' = "' . $what . "\";\n";
			file_put_contents('../config/core.config.php', implode($config));
			break;
		}
	}
}

?>