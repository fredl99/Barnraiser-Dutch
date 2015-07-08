<?php

// -----------------------------------------------------------------------
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
// -----------------------------------------------------------------------


// CHECK INSTALLED
if (is_readable("install/installer.php")) {
	header("Location: install/installer.php");
	exit;
}


include_once ("config/core.config.php");
include_once ("inc/functions.inc.php");


// SESSION HANDLER --------------------------------------------------
session_name($core_config['php']['session_name']);
session_start();


// SETUP URL ROUTING ------------------------------------------------
$uri_routing = routeURL();


if (isset($uri_routing[0]) && $uri_routing[0] == "disconnect") {
	session_unset();
	session_destroy();
	session_write_close();
	header("Location: /");
	exit;
}


// SET LOCALE ----------------------------------------------------------
define ('LOCALE', $core_config['language']['server_locale']);

if (isset($core_config['language']['standard_locale'])) {
	define ('STND_LOCALE', $core_config['language']['standard_locale']);
}
else {
	define ('STND_LOCALE', $core_config['language']['server_locale']);
}

putenv("LANGUAGE=".LOCALE);
setlocale(LC_ALL, LOCALE);

$domain = 'dutch';
bindtextdomain($domain, dirname(__FILE__) . "/language"); 
textdomain($domain);


// SETUP Dutch CORE ----------------------------------------------
require_once('class/Db.class.php');
$db = new Database($core_config['db']);


if (isset($_POST['login_frontpage'])) {
	$query = "
		SELECT user_id, user_email, user_name, user_dob, user_location,
		user_privacy 
		FROM " . $db->prefix . "_user
		WHERE
		user_email=" . $db->qstr($_POST['login_email']) . " AND
		user_password=" . $db->qstr(md5($_POST['login_password'])) . " AND
		user_live=1"
	;
	
	$result = $db->Execute($query, 1);

	if (isset($result[0]['user_id'])) {
		$_SESSION['user_id'] = $result[0]['user_id'];
		$_SESSION['user_email'] = $result[0]['user_email'];
		$_SESSION['user_name'] = $result[0]['user_name'];
		$_SESSION['user_dob'] = $result[0]['user_dob'];
		$_SESSION['user_location'] = $result[0]['user_location'];
		$_SESSION['user_privacy'] = $result[0]['user_privacy'];

		$maintainer_userids = array();
		
		if (!empty($core_config['security']['maintainer_userids'])) {
			$maintainer_userids = explode(",", $core_config['security']['maintainer_userids']);
	
			foreach ($maintainer_userids as $key => $i):
				$maintainer_userids[$key] = trim($i);
			endforeach;
		}

		if (in_array($result[0]['user_id'], $maintainer_userids)) {
			$_SESSION['user_is_maintainer'] = 1;
		}
		
		$query = "
			UPDATE " . $db->prefix . "_user
			SET user_last_login_datetime=NOW()
			WHERE user_id=" . $_SESSION['user_id']
		;
		$db->Execute($query);

		header('location: /notify');
		exit;
	}
	else {
		$GLOBALS['script_error_log'][] = _("Your email or password is incorrect.");
	}
}


// SETUP TEMPLATES --------------------------------------------------
define("AM_TEMPLATE_PATH", "template/");

require_once('class/Template.class.php');
$tpl = new Template(); // outer template
$body = new Template(); // inner template

define('AM_MAX_LIST_ROWS', $core_config['display']['max_list_rows']);



// SELECT SCRIPT AND TEMPLATE --------------------------------------------
if (isset($uri_routing[0]) && is_readable(AM_TEMPLATE_PATH . $uri_routing[0] . '.tpl.php')) {
	define("AM_SCRIPT_NAME", $uri_routing[0]);
}
else {
	define("AM_SCRIPT_NAME", 'public');
}

if (defined('AM_SCRIPT_NAME') && is_readable(AM_SCRIPT_NAME . '.php')) {
	require_once(AM_SCRIPT_NAME . '.php');
	$inner_template_body = file_get_contents(AM_TEMPLATE_PATH . AM_SCRIPT_NAME . '.tpl.php');
}
else {
	header('location: /disconnect');
	exit;
}


// SETUP THEME ---------------------------------------------------------
define("SCRIPT_THEME_NAME", $core_config['script']['theme_name']);
define("SCRIPT_THEME_PATH", "theme/" . SCRIPT_THEME_NAME . "/");


// SET TEMPLATE VARS -----------------------------------------------------
$body->set('uri_routing', $uri_routing);
$tpl->set('uri_routing', $uri_routing);

$body->set('notification_types', $core_config['script']['notification_type']);


$tpl->set('content', $body->parse($inner_template_body));

$outer_tpl = AM_TEMPLATE_PATH . 'wrapper.tpl.php';

echo $tpl->fetch($outer_tpl);

?>