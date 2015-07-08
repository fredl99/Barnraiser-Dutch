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

include_once ("../config/core.config.php");
include_once ("../inc/functions.inc.php");

// SESSION HANDLER --------------------------------------------------
session_name($core_config['php']['session_name']);
session_start();

// SETUP AROUNDMe CORE ----------------------------------------------
require_once('../class/Db.class.php');
$db = new Database($core_config['db']);

if (isset($_POST['notification_id'])) {
	$query = "
		SELECT n.create_datetime, n.notification, n.notification_id,
		u.user_id, u.user_name
		FROM " . $db->prefix . "_notification n
		INNER JOIN " . $db->prefix . "_user u
		ON n.user_id=u.user_id
		WHERE n.parent_id=" . $_POST['notification_id'] . "
		ORDER BY n.create_datetime"
	;
	
	$result = $db->Execute($query);
}

?>

<div class="replies">
	<?php
	if (!empty($result)) {
	?>
		<h3>Replies</h3>
		
		<ul>
			<?php
			foreach($result as $key => $val):
			?>
				<li>
					<?php
					if (!empty($_SESSION['user_id']) && $_SESSION['user_id'] == $val['user_id']) {
					?>
							Added by <a href="/people/<?php echo $val['user_id']; ?>">you</a> <?php echo timeDiff($val['create_datetime']); ?>
					<?php
					}
					else {
					?>
					Added by <a href="/people/<?php echo $val['user_id']; ?>"><?php echo $val['user_name']; ?></a> <?php echo timeDiff($val['create_datetime']); ?>
					<?php }?>
					<div class="reply">
						<?php echo stripslashes($val['notification']); ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>

		<div style="clear:both;"></div>
	<?php }?>
	
	<h3><label for="id_reply<?php echo $val['notification_id']; ?>">Add a reply</label></h3>
	<?php
	if (isset($_SESSION['user_id'])) {
	?>
	<form method="post" class="reply_form">
		<input type="hidden" name="notification_id" value="<?php echo $_POST['notification_id']?>" />
	
		<p>
			<textarea name="reply_notification" id="id_reply<?php echo $val['notification_id']; ?>"></textarea>
		</p>
	
		<p class="buttons">
			<input type="submit" name="post_reply" value="send" />
		</p>
	</form>
	<?php
	}
	else {
	?>
	<p>
		To reply to this contribution please <a href="/">login</a>.
	</p>
	<?php }?>
</div>