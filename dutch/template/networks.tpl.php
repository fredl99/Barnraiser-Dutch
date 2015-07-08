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
	<div class="box">
		<div class="box_header">
			<h1><?php echo _("Popular networks");?></h1>
		</div>

		<div class="box_body">
			<?php
			if (!empty($networks)) {
			?>
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
				$txt = "{1} contributions from {2} people.";
				$txt = str_replace("{1}", $t['total_contributions'], $txt);
				$txt = str_replace("{2}", $t['total_contributors'], $txt);
				echo $txt;
				?>
			</span>
			<?php
			if ($key+1 < count($networks)) {
				echo " * ";
			}

			endforeach;
			?>
			</p>

		<?php
		}
		else {
		?>
			<p>
				<?php echo _("No networks have been created yet. Why not start one? Simply type the name of your network into the 'create a network' box.");?>
			</p>
		<?php }?>
		</div>
	</div>
</div>

<div id="col_right">
	<div class="box" id="box_new_network">
		<div class="box_header">
			<h1><?php echo _("Start a network");?></h1>
		</div>
		
		<div class="box_body">
			<?php
			if (isset($_SESSION['user_id'])) {
			?>
			<form method="post">
				<p>
					<label for="id_tag_name"><?php echo _("Name");?></label>
				</p>
				
				<p>
					<input type="text" name="tag" id="id_tag_name" />
				</p>

				<p class="buttons">
					<input type="submit" name="submit_add_tag" value="<?php echo _("create");?>" />
				</p>
			</form>
			<?php
			}
			else {
			?>
			<p>
				<?php echo _("Please <a href='/'>login</a> to start a network.");?>
			</p>
			<?php }?>
		</div>
	</div>
</div>