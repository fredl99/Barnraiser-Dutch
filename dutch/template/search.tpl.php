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

<div id="col_right2">
	<?php
	if (isset($similar_networks)) {
	?>
	<div class="box" id="box_notifications">
		<div class="box_header">
			<h1><?php echo _("Similar networks");?></h1>
		</div>

		<div class="box_body">
			<p>
				<?php
				$txt = _("Also look in {NETWORKS}");
				$tags = "";
				foreach($similar_networks as $key => $val):
					$tags .= "<a href=\"/network/" . $val['tag_name'] . "\">" . $val['tag_display_name'] . "</a>, ";
				endforeach;
				$tags = rtrim($tags, ', ');
				echo str_replace("{NETWORKS}", $tags, $txt);
				?>
			</p>
		</div>
	</div>
	<?php }?>
	
	<div class="box" id="box_notifications">
		<div class="box_header">
			<h1><?php echo _("Search result");?></h1>
		</div>

		<div class="box_body">
			<?php
			if (isset($search_result)) {
			?>
			<ul>
				<?php
				foreach($search_result as $key => $val):
				?>
				<li>
					<div class="avatar">
						<a href="/profile/<?php echo $val['user_id']; ?>"><img src="/get_file.php?avatar=<?php echo $val['user_id']; ?>" width="60" border="0" alt="<?php echo $val['user_name']; ?> avatar" /></a>
					</div>
					<div class="notification">
						<span class="nickname"><a href="/profile/<?php echo $val['user_id']; ?>"><?php echo $val['user_name']; ?></a></span>
						<span class="datetime"><?php echo timeDiff($val['create_datetime']); ?> in <a href="/network/<?php echo $val['tag_name'];?>"><?php echo $val['tag_name']; ?></a></span>
						<div class="body"><?php echo $this->outputBody($val['notification']); ?></div>
					</div>
					<div class="footer">
						<?php
						$txt = _("{RELEVANCE}% relevance");
						$txt = str_replace("{RELEVANCE}", intval($val['relevance_precentage']), $txt);
						echo $txt;
						?>
					</div>
				</li>
				<?php endforeach; ?>
			</ul>
			<?php
			}
			else {
			?>
			<p>
				<?php echo _("Sorry, no search items were found matching your request.");?>
			</p>
			<?php }?>
			
			<div style="clear:both;"></div>
		</div>
	</div>

	<?php
	if (isset($search_total)) {
	?>
		<div class="list_navigation">
		<hr />
		<?php
		$url = '/search/' . $uri_routing[1] . '/';
		?>
		<?php echo $this->paging($search_total, AM_MAX_LIST_ROWS, $page, $url, 'page'); ?>
	</div>
	<?php }?>
</div>