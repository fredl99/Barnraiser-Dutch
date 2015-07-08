
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

<script type="text/javascript">
	
	rate = ajax();
	ajax = ajax();
	bayesian_rating = [];
	
	function view_replies (id)  {
		
		ajax.onreadystatechange = function() {
			if(ajax.readyState == 4) {
				get('output_node_' + id).innerHTML = ajax.responseText;
				objShowHide('output_node_' + id);
			}
		}

		ajax.open("POST","/ajax/get_replies.php",true);
		ajax.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		ajax.send('notification_id=' + id);
		
	}
	
	function vote (id, rating) {
		rate.onreadystatechange = function() {
			if(rate.readyState == 4) {
				if(rate.responseText) {
					bayesian_rating[id] = rate.responseText;
				}
			}
		}

		rate.open("POST","/ajax/bayesian_rating.php",true);
		rate.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		rate.send('notification_id=' + id + '&rating=' + rating);
	}
	
	function draw_rating(id, rating, complete_redraw) {
	
		if (complete_redraw==1) {
			for(i=1; i<=5; i++) {
				get('id_' + id + '_rating_' + i).className = 'off';
			}
		}
	
		counter = 1;
		for(i=1; i<=rating; i++) {
			get('id_' + id + '_rating_' + i).className = 'on';
			counter++;
		}
		
		decimal = rating - (counter-1);
		
		if (decimal > 0.75) {
			get('id_' + id + '_rating_' + counter).className = 'on';
		}
		else if (decimal > 0.25) {
			get('id_' + id + '_rating_' + counter).className = 'half';
		}

	}

</script>

<div id="col_left">
	<?php
	if (isset($uri_routing[2]) && $uri_routing[2] == "contributors") {
	?>
	<div class="box" id="box_people">
		<div class="box_header">
			<?php
			$txt = _("Contributors to {1}");
			$txt = str_replace("{1}", ucfirst($network['network_display_name']), $txt);
			?>
			<h1><?php echo $txt;?></h1>
		</div>
	
		<div class="box_body">
		<p>
			<?php
				$max_qty = 0;
				$number_of_styles = 5;

				foreach($users as $key => $t):
					if ($t['total_notification'] > $max_qty) {
						$max_qty = $t['total_notification'];
					}
				endforeach;
				?>
				<p>
				<?php
				foreach($users as $key => $t):

				if ($t['total_notification'] > 0 && $max_qty > 0) {
					$percent = floor(($t['total_notification'] / $max_qty) * 100);

					$tag_size = ceil(($number_of_styles/100)*$percent);

				}
				else {
					$tag_size = 1;
				}
				?>	
				<a href="/profile/<?php echo $t['user_id'];?>" class="tagsize<?php echo $tag_size;?>" onmouseover="javascript: showTooltip(this, 'tooltip<?php echo $key;?>');" onmouseout="javascript: hideTooltip(this, 'tooltip<?php echo $key;?>');"><?php echo $t['user_name'];?></a>
				
				<?php
				if ($key+1 < count($users)) {
					echo " * ";
				}
				
				endforeach;
				?>
				
				<?php
				foreach($users as $key => $t):
				?>
					<div id="tooltip<?php echo $key; ?>" class="tooltip">
					<img src="/get_file.php?avatar=<?php echo $t['user_id']; ?>&amp;width=60" width="60" border="0" />
					<span class="nickname"><?php echo $t['user_name']; ?></span><br />
					<?php echo $t['user_location']; ?><br />
					<?php 
					$txt = ngettext("{1} contribution.", "{1} contributions.", $t['total_notification']);
					$txt = str_replace("{1}", $t['total_notification'], $txt);
					echo $txt;
					?>
				</div>
				<?php endforeach; ?>
		</p>
		</div>
	</div>
	<?php
	}
	elseif (isset($uri_routing[2]) && $uri_routing[2] == "people") {
	?>
	<div class="box" id="box_people">
		<div class="box_header">
			<?php
			$txt = _("Fans for {1}");
			$txt = str_replace("{1}", ucfirst($network['network_display_name']), $txt);
			?>
			<h1><?php echo $txt;?></h1>
		</div>
	
		<div class="box_body">
		<p>
			<?php
				$max_qty = 0;
				$number_of_styles = 5;

				foreach($users as $key => $t):
					if ($t['total_notification'] > $max_qty) {
						$max_qty = $t['total_notification'];
					}
				endforeach;
				?>
				<p>
				<?php
				foreach($users as $key => $t):

				if ($t['total_notification'] > 0 && $max_qty > 0) {
					$percent = floor(($t['total_notification'] / $max_qty) * 100);

					$tag_size = ceil(($number_of_styles/100)*$percent);

				}
				else {
					$tag_size = 1;
				}
				?>	
				<a href="/profile/<?php echo $t['user_id'];?>" class="tagsize<?php echo $tag_size;?>" onmouseover="javascript: showTooltip(this, 'tooltip<?php echo $key;?>');" onmouseout="javascript: hideTooltip(this, 'tooltip<?php echo $key;?>');"><?php echo $t['user_name'];?></a>
				
				<?php
				if ($key+1 < count($users)) {
					echo " * ";
				}
				
				endforeach;
				?>
				
				<?php
				foreach($users as $key => $t):
				?>
					<div id="tooltip<?php echo $key; ?>" class="tooltip">
					<img src="/get_file.php?avatar=<?php echo $t['user_id']; ?>&amp;width=60" width="60" border="0" />
					<span class="nickname"><?php echo $t['user_name']; ?></span><br />
					<?php echo $t['user_location']; ?><br />
					<?php 
					$txt = ngettext("{1} contribution.", "{1} contributions.", $t['total_notification']);
					$txt = str_replace("{1}", $t['total_notification'], $txt);
					echo $txt;
					?>
				</div>
				<?php endforeach; ?>
		</p>
		</div>
	</div>
	<?php
	}
	else {
	?>
	<div class="box" id="box_notifications">
		<div class="box_header">
			<?php
			$txt = _("Notifications for {network_name}");
			$txt = str_replace("{network_name}", ucfirst($network['network_display_name']), $txt);
			?>
			<h1><?php echo $txt;?></h1>
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
							if (!empty($_SESSION['user_id']) && $_SESSION['user_id'] == $val['user_id']) {
							
								$txt = _("<a href='/profile/{1}'>You</a> became a fan of this network {2}.");
								$txt = str_replace("{1}", $val['user_id'], $txt);
								$txt = str_replace("{2}", timeDiff($val['create_datetime']), $txt);
								echo $txt;
							}
							else {
							
								$txt = _("<a href='/profile/{1}'>{2}</a> became a fan of this network {3}.");
								$txt = str_replace("{1}", $val['user_id'], $txt);
								$txt = str_replace("{2}", $val['user_name'], $txt);
								$txt = str_replace("{3}", timeDiff($val['create_datetime']), $txt);
								echo $txt;
							}
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
							if (!empty($_SESSION['user_id']) && $_SESSION['user_id'] == $val['user_id']) {
							
								$txt = _("<a href='/profile/{1}'>You</a> started this network!");
								$txt = str_replace("{1}", $val['user_id'], $txt);
								echo $txt;
							}
							else {
							
								$txt = _("<a href='/profile/{1}'>{2}</a> started this network!");
								$txt = str_replace("{1}", $val['user_id'], $txt);
								$txt = str_replace("{2}", $val['user_name'], $txt);
								echo $txt;
							}
							?>
						</div>	
	
						<?php
						}
						else {
						?>
						
						<?php
						if (!empty($_SESSION['user_id']) && $_SESSION['user_id'] == $val['user_id']) {
							
								$txt = _("Added by <a href='/profile/{user_id}'>You</a> {datetime}.");
								$txt = str_replace("{user_id}", $val['user_id'], $txt);
								$txt = str_replace("{datetime}", timeDiff($val['create_datetime']), $txt);
								echo $txt;
						}
						else {
							
								$txt = _("Added by <a href='/profile/{user_id}'>{user_name}</a> {datetime}.");
								$txt = str_replace("{user_id}", $val['user_id'], $txt);
								$txt = str_replace("{user_name}", $val['user_name'], $txt);
								$txt = str_replace("{datetime}", timeDiff($val['create_datetime']), $txt);
								echo $txt;
						}
						?>
							
						<div class="body"><?php echo $this->outputBody($val['notification']); ?></div>
						
						<div id="output_node_<?php echo $val['notification_id']; ?>" style="display: none;"></div>

						<div class="footer">
							<script type="text/javascript">
								bayesian_rating[<?php echo $val['notification_id']; ?>]=<?php echo $val['bayesian_rating']; ?>;
							</script>

							<span class="rating" id="rating_<?php echo $val['notification_id']; ?>" onmouseOut="draw_rating('<?php echo $val['notification_id']; ?>', bayesian_rating[<?php echo $val['notification_id']; ?>], '1');">
								<span id="id_<?php echo $val['notification_id']; ?>_rating_1" onclick="vote('<?php echo $val['notification_id']; ?>', '1', '0');" onmouseover="draw_rating('<?php echo $val['notification_id']; ?>', '1', '1');"></span>
								<span id="id_<?php echo $val['notification_id']; ?>_rating_2" onclick="vote('<?php echo $val['notification_id']; ?>', '2', '0');" onmouseover="draw_rating('<?php echo $val['notification_id']; ?>', '2', '1');"></span>
								<span id="id_<?php echo $val['notification_id']; ?>_rating_3" onclick="vote('<?php echo $val['notification_id']; ?>', '3', '0');" onmouseover="draw_rating('<?php echo $val['notification_id']; ?>', '3', '1');"></span>
								<span id="id_<?php echo $val['notification_id']; ?>_rating_4" onclick="vote('<?php echo $val['notification_id']; ?>', '4', '0');" onmouseover="draw_rating('<?php echo $val['notification_id']; ?>', '4', '1');"></span>
								<span id="id_<?php echo $val['notification_id']; ?>_rating_5" onclick="vote('<?php echo $val['notification_id']; ?>', '5', '0');" onmouseover="draw_rating('<?php echo $val['notification_id']; ?>', '5', '1');"></span>
							</span>

							<span onclick="view_replies('<?php echo $val['notification_id']; ?>')" class="span_link" id="reply_<?php echo $val['notification_id']; ?>">
								<?php
								$txt = _("reply ({1})");
								$txt = str_replace("{1}", $val['child_count'], $txt);
								echo $txt;
								?>
							</span>

							<?php
							if ($val['child_count'] > 0) {
							
								$txt = _("last reply posted {1}");
								$txt = str_replace("{1}", timeDiff($val['update_datetime']), $txt);
								echo $txt;
							}
							?>
							<script type="text/javascript">
								draw_rating('<?php echo $val['notification_id']; ?>', '<?php echo $val['bayesian_rating']; ?>', '0');
							</script>
						</div>
						
						<?php }?>
					</li>

				<?php 
endforeach; ?>
				</ul>
			<?php
			}
			else {
			?>
				<p><?php echo _("No notifications for this network.");?></p>
			<?php }?>

			<div style="clear:both;"></div>
			
			<?php
			if (isset($search_total)) {
			?>
			<div class="list_navigation">
				<?php
				$url = '/network/' . $uri_routing[1] . '/search/' . $uri_routing[3] . '/';
				?>
				<?php echo $this->paging($search_total, AM_MAX_LIST_ROWS, $page, $url, 'page'); ?>
			</div>
			<?php
			}
			elseif (isset($contributions_total)) {
			?>
			<div class="list_navigation">
				<?php
				$url = '/network/' . $uri_routing[1] . '/';
				?>
				<?php echo $this->paging($contributions_total, AM_MAX_LIST_ROWS, $page, $url, 'page'); ?>
			</div>
			<?php }?>
		</div>
	</div>
	<?php }?>
</div>

	
<div id="col_right">
	<div class="box" id="box_submit_contribution">
		<div class="box_header">
			<h1><?php echo _("Contribute");?></h1>
		</div>

		<div class="box_body" id="box_network">
			<?php
			if (isset($_SESSION['user_id'])) {
			?>
			<form method="post" action="/network/<?php echo $uri_routing[1]; ?>" id="box_network_add">
			<p>
				<?php echo _("Please add short, concise text along with any links to sources.");?>
			</p>

			<p>
				<textarea name="text" id="id_notification_text"></textarea>
			</p>

			<p class="buttons">
				<input type="submit" name="submit_notification" value="<?php echo _("add");?>" />
			</p>
			</form>
			<?php
			}
			else {
			?>
			<p>
				<?php echo _("To contribute to this network please <a href='/'>login</a>.");?>
			</p>
			<?php }?>
		</div>
	</div>


	<div class="box" id="box_options">
		<div class="box_header">
			<h1><?php echo _("Options");?></h1>
		</div>

		<div class="box_body">
			<ul class="tag_menu">
				<?php
				if (!empty($_SESSION['user_id'])) {
				?>
					<?php
					if (isset($network['has_joined'])) {
					?>
						<li><a href="/network/<?php echo $uri_routing[1]; ?>/leave"><?php echo _("Remove favourite");?></a></li>
					<?php
					}
					else {
					?>
						<li><a href="/network/<?php echo $uri_routing[1]; ?>/join"><?php echo _("Add as favourite");?></a></li>
					<?php }?>
				<?php
				}
				else {
				?>
					<li><?php echo _("<a href='/'>Login</a> to add as favourite");?></a></li>
				<?php }?>

				<?php
				if (isset($uri_routing[2]) && $uri_routing[2] == "contributors") {
				?>
				<li><a href="/network/<?php echo $uri_routing[1]; ?>"><?php echo _("Notifications");?></a><sup>(<?php echo $network['contributions_total']; ?>)</sup></li>
				<li><?php echo _("Contributors");?><sup>(<?php echo $network['contributors_total'];?>)</sup></li>
				<li><a href="/network/<?php echo $uri_routing[1]; ?>/people"><?php echo _("Fans");?></a><sup>(<?php echo $network['people_total']; ?>)</sup></li>
				<?php
				}
				elseif (isset($uri_routing[2]) && $uri_routing[2] == "people") {
				?>
				<li><a href="/network/<?php echo $uri_routing[1]; ?>"><?php echo _("Notifications");?></a><sup>(<?php echo $network['contributions_total']; ?>)</sup></li>
				<li><a href="/network/<?php echo $uri_routing[1]; ?>/contributors"><?php echo _("Contributors");?></a><sup>(<?php echo $network['contributors_total']; ?>)</sup></li>
				<li><?php echo _("Fans");?><sup>(<?php echo $network['people_total']; ?>)</sup></li>
				<?php
				}
				else {
				?>
				<li><?php echo _("Notifications");?><sup>(<?php echo $network['contributions_total']; ?>)</sup></li>
				<li><a href="/network/<?php echo $uri_routing[1]; ?>/contributors"><?php echo _("Contributors");?></a><sup>(<?php echo $network['contributors_total']; ?>)</sup></li>
				<li><a href="/network/<?php echo $uri_routing[1]; ?>/people"><?php echo _("Fans");?></a><sup>(<?php echo $network['people_total']; ?>)</sup></li>
				<?php }?>
			</ul>
		</div>
	</div>


	<?php
	if (isset($my_networks)) {
	?>
	<div class="box" id="box_networks">
		<div class="box_header">
			<h1><?php echo _("My favourites");?></h1>
		</div>

		<div class="box_body">
			<ul>
				<?php
				foreach($my_networks as $key => $i):
				?>
				<li><a href="/network/<?php echo $i['tag_name'];?>"><?php echo $i['tag_display_name'];?></a><sup> (<?php echo $i['tag_total'];?>)</sup></li>
				<?php
				endforeach;
				?>
			</ul>
		</div>
	</div>
	<?php }?>
</div>