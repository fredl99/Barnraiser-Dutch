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

<?php
if (isset($display_profile)) {
?>
<div id="col_left">
	<div class="box" id="box_profile">
		<div class="box_header">
			<h1><?php echo _("About me");?></h1>
		</div>

		<div class="box_body">
			<p>
				<?php echo $profile['user_name']; ?>,
				<?php echo $profile['user_location']; ?>
			</p>

			<div class="avatar">
				<img src="/get_file.php?avatar=<?php echo $profile['user_id']?>&amp;width=200" />
			</div>

			<?php
			if (!empty($profile['profile_status'])) {
			?>
			<p>
				<?php echo $profile['profile_status'];?>
			</p>
			<?php }?>
		</div>

		<div class="box_footer">
			<a href="#" onclick="javascript:objShowHide('box_contact');"><?php echo _("Contact me");?></a>
		</div>
	</div>

	<?php
	if (isset($shared_networks)) {
	?>
		<div class="box" id="box_shared_tags">
			<div class="box_header">
				<h1><?php echo _("Shared networks");?></h1>
			</div>

			<div class="box_body">
				<p>
					<?php
					$tags = "";
					foreach($shared_networks as $key => $val):
						$tags .= "<a href=\"/network/" . $val['tag_name'] . "\">" . $val['tag_display_name'] . "</a><sup>(" . $val['tag_total'] . ")</sup>, ";
					endforeach; 
					echo rtrim($tags, ', ');
					?>
				</p>
			</div>
		</div>
	<?php }?>

	<div class="box" id="box_tags">
		<div class="box_header">
			<h1><?php echo _("Networks");?></h1>
		</div>

		<div class="box_body">
			<?php
			if (isset($networks)) {
			?>
				<p>
					<?php
					$tags = "";
					foreach($networks as $key => $val):
						$tags .= "<a href=\"/network/" . $val['tag_name'] . "\">" . $val['tag_display_name'] . "</a><sup>(" . $val['tag_total'] . ")</sup>, ";
					endforeach; 
					echo rtrim($tags, ', ');
					?>
				</p>
			<?php }?>
		</div>
	</div>

	<?php
	if (isset($_SESSION['user_id'])) {
	?>
	<form method="post">
	<div class="box" id="box_relation">
		<div class="box_header">
			<h1><?php echo _("Relationship");?></h1>
		</div>
		
		<?php
		if (isset($relation)) {
		?>
		<div class="box_body">
			<p>
				<?php echo _("You are following this contributor meaning that their contributions will appear in your notifications");?>
			</p>
		</div>

		<div class="box_footer">
			<input type="submit" name="delete_relation" value="<?php echo _("Remove relationship");?>" />
		</div>
		<?php
		}
		else {
		?>
		<div class="box_body">
			<p>
				<?php echo _("You can create a relationship to this contributor and receive notifications when they contribute.");?>
			</p>
		</div>

		<div class="box_footer">
			<input type="submit" name="create_relation" value="<?php echo _("Create relationship");?>" />
		</div>
		<?php }?>
	</div>
	</form>
	<?php }?>
	
</div>

<div id="col_right">
	<div class="box" id="box_contact">
		<div class="box_header">
			<h1><?php echo _("Email me");?></h1>
		</div>

		<form method="post" action="/profile/<?php echo $uri_routing[1]; ?>">
		<div class="box_body">

			<p>
				<label for="id_email_subject"><?php echo _("Subject");?></label>
				<input type="text" name="email_subject" id="id_email_subject" />
			</p>
			
			<p>
				<label for="id_email_message"><?php echo _("Message");?></label></br />
				<textarea name="email_message" id="id_email_message"></textarea>
			</p>

			<p class="hint">
				<?php echo _("Your email address will be sent to me to allow me to reply.");?>
			</p>

			<p class="buttons">
				<input type="submit" name="send_email" value="<?php echo _("send");?>" />
			</p>
		</div>
		</form>
	</div>
	
	<div class="box" id="box_notifications">
		<div class="box_header">
			<h1><?php echo _("Notifications added");?></h1>
		</div>
		
		<div class="box_body">
			<?php
			if (isset($notifications)) {
			?>
			<ul>
				<?php
				foreach($notifications as $key => $val):
				?>

				<li>
					<?php
					if ($val['notification_type'] == $notification_types['join_network']) {
					?>
					<div class="avatar">
						<a href="/profile/<?php echo $val['user_id']; ?>"><img src="/get_file.php?avatar=<?php echo $val['user_id']; ?>" width="60" border="0" alt="<?php echo $val['user_name']; ?> avatar" /></a>
					</div>

					<div class="notification">
						<?php
						$txt = _("<a href='/profile/{USRID}'>{NAME}</a> joined <a href='/network/{NWID}'>{NETWORK}</a> {TIME}.");
						$txt = str_replace("{USRID}", $val['user_id'], $txt);
						$txt = str_replace("{NAME}", ucfirst($val['user_name']), $txt);
						$txt = str_replace("{NWID}", $val['tag_name'], $txt);
						$txt = str_replace("{NETWORK}", $val['tag_display_name'], $txt);
						$txt = str_replace("{TIME}", timeDiff($val['create_datetime']), $txt);
						echo $txt;
						?>
					</div>

					<?php
					}
					elseif ($val['notification_type'] == $notification_types['create_network']) {
					?>
					<div class="avatar">
						<a href="/profile/<?php echo $val['user_id']; ?>"><img src="/get_file.php?avatar=<?php echo $val['user_id']; ?>" width="60" border="0" alt="<?php echo $val['user_name']; ?> avatar" /></a>
					</div>

					<div class="notification">
						<?php
						$txt = _("<a href='/profile/{USRID}'>{NAME}</a> created a new network called <a href='{NWID}'>{NETWORK}</a>!");
						$txt = str_replace("{USRID}", $val['user_id'], $txt);
						$txt = str_replace("{NAME}", ucfirst($val['user_name']), $txt);
						$txt = str_replace("{NWID}", $val['tag_name'], $txt);
						$txt = str_replace("{NETWORK}", $val['tag_display_name'], $txt);
						echo $txt;
						?>
					</div>

					<?php
					}
					else {
					?>

					<?php
					$txt = _("Added by <a href='/profile/{USRID}'>{NAME}</a> at {TIME} into <a href='{NWID}'>{NETWORK}</a>.");
					$txt = str_replace("{USRID}", $val['user_id'], $txt);
					$txt = str_replace("{NAME}", ucfirst($val['user_name']), $txt);
					$txt = str_replace("{NWID}", $val['tag_name'], $txt);
					$txt = str_replace("{NETWORK}", $val['tag_display_name'], $txt);
					$txt = str_replace("{TIME}", timeDiff($val['create_datetime']), $txt);
					echo $txt;
					?>
						
					<div class="body"><?php echo $this->outputBody($val['notification']); ?></div>
						
					<div id="output_node_<?php echo $val['notification_id']; ?>" style="display: none;"></div>
					
					<?php }?>
				</li>
				<?php endforeach; ?>
			</ul>
			<?php 
			} 
			else {
			?>
			<p>
				<?php echo _("No notifications from this person");?>
			</p>
			<?php }?>
			<div style="clear:both;"></div>
			<?php
			if (isset($contributions_total)) {
			?>
			<div class="list_navigation">
				<?php
				$url = '/profile/' . $uri_routing[1] . '/';
				?>
				<?php echo $this->paging($contributions_total, AM_MAX_LIST_ROWS, $page, $url, 'page'); ?>
			</div>
			<?php }?>
		</div>
	</div>
</div>
<?php
}
elseif ($profile['user_privacy'] == 1) {
?>
<div class="box">
	<div class="box_body">
		<p>
			<?php echo _("This profile is hidden. You can <a href='/'>login</a> to see this profile.");?>
		</p>
	</div>
</div>
<?php
}
else {
?>
<div class="box">
	<div class="box_body">
		<p>
			<?php echo _("This profile is hidden.");?>
		</p>
	</div>
</div>
<?php }?>