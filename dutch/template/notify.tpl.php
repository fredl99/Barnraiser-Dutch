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
/* <![CDATA[ */
	rate = ajax();
	bayesian_rating = [];
	
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
/* ]]> */
</script>

<div id="col_left">
	<div class="box" id="box_notifications">
		<div class="box_header">
			<h1><?php echo _("Latest notifications from your networks");?></h1>
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
						$txt = _("<a href='/profile/{USRID}'>{NAME}</a> joined <a href='/network/{NWID}'>{NETWORK}</a> at {TIME}.");
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
						$txt = _("<a href='/profile/{USRID}'>{NAME}</a> created a new network called  <a href='/network/{NWID}'>{NETWORK}</a> at {TIME}!");
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
					else {
					?>
					
					<?php
					$txt = _("Added by <a href='/profile/{USRID}'>{NAME}</a> at {TIME} into <a href='/network/{NWID}'>{NETWORK}</a>.");
					$txt = str_replace("{USRID}", $val['user_id'], $txt);
					$txt = str_replace("{NAME}", ucfirst($val['user_name']), $txt);
					$txt = str_replace("{NWID}", $val['tag_name'], $txt);
					$txt = str_replace("{NETWORK}", $val['tag_display_name'], $txt);
					$txt = str_replace("{TIME}", timeDiff($val['create_datetime']), $txt);
					echo $txt;
					?>

					<div class="body"><?php echo $this->outputBody($val['notification']); ?></div>
						
					<div id="output_node_<?php echo $val['notification_id']; ?>" style="display: none;"></div>
					<div class="footer">
							<script type="text/javascript">
								bayesian_rating[<?php echo $val['notification_id']; ?>]=<?php echo $val['bayesian_rating']; ?>;
							</script>
							<span class="rating" id="rating_<?php echo $val['notification_id']; ?>" onmouseout="draw_rating('<?php echo $val['notification_id']; ?>', bayesian_rating[<?php echo $val['notification_id']; ?>], '1');">
								<span id="id_<?php echo $val['notification_id']; ?>_rating_1" onclick="vote('<?php echo $val['notification_id']; ?>', '1', '0');" onmouseover="draw_rating('<?php echo $val['notification_id']; ?>', '1', '1');"></span>
								<span id="id_<?php echo $val['notification_id']; ?>_rating_2" onclick="vote('<?php echo $val['notification_id']; ?>', '2', '0');" onmouseover="draw_rating('<?php echo $val['notification_id']; ?>', '2', '1');"></span>
								<span id="id_<?php echo $val['notification_id']; ?>_rating_3" onclick="vote('<?php echo $val['notification_id']; ?>', '3', '0');" onmouseover="draw_rating('<?php echo $val['notification_id']; ?>', '3', '1');"></span>
								<span id="id_<?php echo $val['notification_id']; ?>_rating_4" onclick="vote('<?php echo $val['notification_id']; ?>', '4', '0');" onmouseover="draw_rating('<?php echo $val['notification_id']; ?>', '4', '1');"></span>
								<span id="id_<?php echo $val['notification_id']; ?>_rating_5" onclick="vote('<?php echo $val['notification_id']; ?>', '5', '0');" onmouseover="draw_rating('<?php echo $val['notification_id']; ?>', '5', '1');"></span>
							</span>
							<script type="text/javascript">
								draw_rating('<?php echo $val['notification_id']; ?>', '<?php echo $val['bayesian_rating']; ?>', '0');
							</script>
						</div>
					<?php }?>
				</li>

				<?php endforeach; ?>
			</ul>
			<?php 
			} 
			else {
			?>
			<p>
				<?php echo _("Notifications are updates from your favourite networks.");?>
			</p>

			<p>
				<?php echo _("Select a network from <a href='/networks'>networks</a>. If you like it then add  it to your favourites by pressing 'add to favourites'. You will now receive notifications when stuff happens inside the network.");?>
			</p>
			<?php }?>
			
			<div style="clear:both;"></div>
		</div>
	</div>
</div>

<div id="col_right">
	<div class="box" id="box_networks">
		<div class="box_header">
			<h1><?php echo _("My favourites");?></h1>
		</div>
		
		<div class="box_body">
			<?php
			if (isset($my_networks)) {
			?>
			<ul>
				<?php
				foreach($my_networks as $key => $i):
				?>
				<li><a href="/network/<?php echo $i['tag_name'];?>"><?php echo $i['tag_display_name'];?></a><sup> (<?php echo $i['tag_total'];?>)</sup></li>
				<?php
				endforeach;
				?>
			</ul>
			<?php
			}
			else {
			?>
			<p>
				<?php echo _("To place networks into your favourites list go to the network and press 'add to favourites'.");?>
			</p>
			<?php }?>
		</div>
	</div>
	
	<?php
	if (isset($similar)) {
	?>
	<div class="box" id="box_shared">
		<div class="box_header">
			<h1><?php echo _("Similar people");?></h1>
		</div>

		<?php
		foreach($similar as $key => $val):
		?>
		<div class="box_body">
			<p>
				<div class="avatar">
					<a href="/profile/<?php echo $val['user_id']; ?>">
						<img src="/get_file.php?avatar=<?php echo $val['user_id']; ?>&amp;width=60" border="0" alt="nickname avatar" />
					</a>
				</div>
				
				<?php
				$txt = _("<a href='/profile/{USRID}'>{NAME}</a> have {NETWORKS} networks in common with you.");
				$txt = str_replace("{USRID}", $val['user_id'], $txt);
				$txt = str_replace("{NAME}", ucfirst($val['user_name']), $txt);
				$txt = str_replace("{NETWORKS}", $val['total'], $txt);
				echo $txt;
				?>
			</p>
			<div style="clear:both;"></div>
		</div>
		<?php
		endforeach;
		?>
	</div>
	<?php }?>
	
	<?php
	if (isset($relations)) {
	?>
	<div class="box" id="box_shared">
		<div class="box_header">
			<h1><?php echo _("Contributors followed");?></h1>
		</div>

		<?php
		foreach($relations as $key => $val):
		?>
		<div class="box_body">
			<p>
				<div class="avatar">
					<a href="/profile/<?php echo $val['user_id']; ?>">
						<img src="/get_file.php?avatar=<?php echo $val['user_id']; ?>&amp;width=60" border="0" alt="nickname avatar" />
					</a>
				</div>
				
				<?php
				$txt = _("<a href='/profile/{USRID}'>{NAME}</a>.");
				$txt = str_replace("{USRID}", $val['user_id'], $txt);
				$txt = str_replace("{NAME}", ucfirst($val['user_name']), $txt);
				echo $txt;
				?>
			</p>
			<div style="clear:both;"></div>
		</div>
		<?php
		endforeach;
		?>
	</div>
	<?php }?>
</div>