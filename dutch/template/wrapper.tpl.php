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

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo STND_LOCALE;?>" lang="<?php echo STND_LOCALE;?>">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="<?php echo STND_LOCALE;?>" />

	<title><?php echo _("Dutch");?></title>
	
	<style type="text/css">
	<!--
	@import url(/<?php echo SCRIPT_THEME_PATH;?>css/common.css);
	@import url(/<?php echo SCRIPT_THEME_PATH;?>css/<?php echo AM_SCRIPT_NAME;?>.css);
	-->
	</style>

	<?php
	//we reload an image in this template just before the session times out to
	//make sure that the session does not time out
	$session_maxlifetime = ini_get('session.gc_maxlifetime'); // in seconds
	
	// we need to warn 2 minutes before
	$session_warning_time = 120; // seconds
	if ($session_maxlifetime > $session_warning_time) {
		$session_maxlifetime = $session_maxlifetime-$session_warning_time;
	}
	$session_maxlifetime_ms = $session_maxlifetime*1000; // in milliseconds
	?>

	<script type="text/javascript" src="/<?php echo AM_TEMPLATE_PATH;?>js/functions.js"></script>

	<script type="text/javascript">
	//<![CDATA[
		var session_maxlifetime_ms = <?php echo $session_maxlifetime_ms;?>;

		function ShowTimeoutWarning () {
			// we append the time to the string to avoid caching
			var urldate = new Date()
			var urltime = urldate.getTime()
			document.session_reload_image.src = '/get_file.php?reloadsession=start&now=' + urltime;
			setTimeout( 'ShowTimeoutWarning();', session_maxlifetime_ms );
		}
	//]]>
	</script>
</head>

<body onload="setTimeout( 'ShowTimeoutWarning();', session_maxlifetime_ms );">
	<div id="content_container">
		<div id="header_container">
			<ul>
				<?php
				if (!empty($_SESSION['user_id'])) {
				$link_css = "";
				if (defined('AM_SCRIPT_NAME') && AM_SCRIPT_NAME == "notify") {
					$link_css = " class=\"current\"";
				}
				?>
				<li><a href="/notify"<?php echo $link_css;?>><?php echo _("Notifications");?></a></li>
				<?php }?>
				
				<?php
				$link_css = "";
				if (defined('AM_SCRIPT_NAME') && AM_SCRIPT_NAME == "networks") {
					$link_css = " class=\"current\"";
				}
				?>
				<li><a href="/networks"<?php echo $link_css;?>><?php echo _("Networks");?></a></li>
				
				<?php
				if (!empty($_SESSION['user_id'])) {
				$link_css = "";
				if (defined('AM_SCRIPT_NAME') && AM_SCRIPT_NAME == "profile") {
					$link_css = " class=\"current\"";
				}
				$txt = _("Logged in as {USRNAME}");
				$txt = str_replace("{USRNAME}", $_SESSION['user_name'], $txt);
				?>
				<li><a href="/profile/<?php echo $_SESSION['user_id'];?>"<?php echo $link_css;?> title="<?php echo $txt;?>"><?php echo _("Profile");?></a></li>
				
				<?php
				$link_css = "";
				if (defined('AM_SCRIPT_NAME') && AM_SCRIPT_NAME == "account") {
					$link_css = " class=\"current\"";
				}
				?>
				<li><a href="/account"<?php echo $link_css;?>><?php echo _("Account");?></a></li>
				
				

				<?php
				if (isset($_SESSION['user_is_maintainer'])) {
				$link_css = "";
				if (defined('SCRIPT_NAME') && SCRIPT_NAME == "maintain") {
					$link_css = " class=\"current\"";
				}
				?>
				<li><a href="/maintain"<?php echo $link_css;?>><?php echo _("Maintain");?></a></li>
				<?php }?>

				<li><a href="/disconnect"><?php echo _("Log off");?></a></li>
				<?php
				}
				else {
				if (defined('AM_SCRIPT_NAME') && AM_SCRIPT_NAME != "register") {
				if (defined('AM_SCRIPT_NAME') && AM_SCRIPT_NAME != "public") {
				?>
				<li><a href="/public"><?php echo _("Login");?></a></li>
				<?php }?>
				<li><a href="/register"><?php echo _("Register");?></a></li>
				<?php }?>
				<?php }?>
				<li>
					<form action="/search" method="post">
					<p style="display: inline;">
					<label for="id_search_text_box"><?php echo _("Search");?></label>
					<input type="text" id="id_search_text_box" name="search_text" />
					<input type="submit" name="submit_search" value="go" />
					</p>
					</form>
				</li>
			</ul>
			
			<div style="clear:both;"></div>
		</div>

		<?php
		if (!empty($GLOBALS['script_error_log'])) {
		?>
		<div id="system_error_container">
			<?php
			foreach($GLOBALS['script_error_log'] as $key => $val):
				echo $val . "<br />";
			endforeach;
			?>
		</div>
		<?php }?>
		
		<div id="body_container">
			<?php echo $content;?>
		</div>
		
		<div style="clear:both;"></div>
		
		<div id="footer_container">
		
			<ul>
				<li><a href="/about"><?php echo _("About us");?></a></li>
				<li><a href="/about/policy"><?php echo _("Our policy");?></a></li>
				<li><a href="/about"><?php echo _("Contact us");?></a></li>
			</ul>
		</div>
		
		<div style="clear:both;"></div>
	</div>
		
	<div id="id_session_reload_image">
		<img name="session_reload_image" src="/get_file.php?reloadsession=1" alt="" />
	</div>
</body>
</html>