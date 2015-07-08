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

<div id="col_left">
	<div class="box" id="box_welcome">
		<div class="box_header">
			<h1><?php echo _("Welcome");?></h1>
		</div>

		<div class="box_body">
			<p>
				<?php echo _("Welcome to Dutch; our knowledge sharing network tool. Dutch is free software from <a href='http://www.barnraiser.org/'>Barnraiser</a>. You can find out more about Dutch from the <a href='http://www.barnraiser.org/dutch'>Dutch product page</a>.");?>
			</p>
		</div>
	</div>
	
	<?php
	if (!empty($networks)) {
	?>
	<div class="box" id="box_networks">
		<div class="box_header">
			<h1><?php echo _("Popular networks");?></h1>
		</div>

		<div class="box_body">
			<p>
				<?php echo _("The size of the network reflects the number of people that have this network in their favourites list.");?>
			</p>
			<?php
			$max_qty = 0;
			$number_of_styles = 5;

			foreach($networks as $key => $t):
				if ($t['tag_total'] > $max_qty) {
					$max_qty = $t['tag_total'];
				}
			endforeach;
			?>
			<p>
			<?php
			foreach($networks as $key => $t):

			if ($t['tag_total'] > 0 && $max_qty > 0) {
				$percent = floor(($t['tag_total'] / $max_qty) * 100);

				$tag_size = ceil(($number_of_styles/100)*$percent);

			}
			else {
				$tag_size = 1;
			}
			?>
			<a href="/network/<?php echo $t['tag_name'];?>" class="tagsize<?php echo $tag_size;?>" onmouseover="javascript: showTooltip(this, 'tooltip_b<?php echo $key;?>');" onmouseout="javascript: hideTooltip(this, 'tooltip_b<?php echo $key;?>');"><?php echo $t['tag_display_name'];?></a>
			<span id="tooltip_b<?php echo $key;?>" class="tooltip">
				<?php
				$txt = _("{1} contributions from {2} people.");
				$txt = str_replace("{1}", $t['total_contributions'], $txt);
				$txt = str_replace("{2}", $t['total_contributors'], $txt);
				?>
			</span>
			<?php
			if ($key+1 < count($networks)) {
				echo " * ";
			}

			endforeach;
			?>
			</p>
		</div>
	</div>
	<?php }?>
</div>

<div id="col_right">
	<form method="post">
	<div class="box" id="box_login">
		<div class="box_header">
			<h1><?php echo _("Login");?></h1>
		</div>

		<div class="box_body">
		<form method="post">
			<p>
				<label for="id_login_email"><?php echo _("Email");?></label>
				<input type="text" id="id_login_email" value="" name="login_email" />
			</p>

			<p>
				<label for="id_login_password"><?php echo _("Password");?></label>
				<input type="password" id="id_login_password" value="" name="login_password" />
			</p>

			<p class="buttons">
				<input type="submit" value="<?php echo _("log in");?>" name="login_frontpage" />
			</p>
		</form>
		</div>

		<div class="box_footer">
			<a href="/about"><?php echo _("Lost password?");?></a>
		</div>
	</div>
	</form>
	
	<form method="post">
	<div class="box">
		<div class="box_header">
			<h1><?php echo _("OpenID login");?></h1>
		</div>

		<div class="box_body">
		<form method="post">
			<p>
				<label for="id_login_openid"><?php echo _("OpenID");?></label>
				<input type="text" id="id_login_openid" value="" name="openid_login" />
			</p>

			<p class="buttons">
				<input type="submit" value="<?php echo _("log in");?>" name="submit_openid_login" />
			</p>
		</form>
		</div>
	</div>
	</form>
</div>