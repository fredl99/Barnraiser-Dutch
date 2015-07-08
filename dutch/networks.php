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


if (isset($_POST['submit_add_tag'])) {
	
	$display_tag = trim($_POST['tag']);
	$tag = strtolower(trim($_POST['tag']));
	$tag = parse_tag($tag);
	
	if (empty($tag)) {
		$GLOBALS['script_error_log'][] = _("Please enter the name of your network.");
	}
	
	if (empty($GLOBALS['script_error_log'])) {
	
		$query = "
			SELECT tag_id, tag_name, tag_display_name
			FROM " . $db->prefix . "_tag
			WHERE tag_name=" . $db->qstr($tag)
		;
		
		$result = $db->Execute($query);
		
		if (!empty($result)) {
			// network already exists
			header('location: /network/' . $tag . '/join');
			exit;
		}
		else {
			$query = "
				SELECT tag_id
				FROM " . $db->prefix . "_tag
				WHERE tag_name=" . $db->qstr($tag)
			;
		
			$result = $db->Execute($query);

			if (!empty($result)) {
				// network already exists
				header('location: /network/' . $tag . '/join');
				exit;
			}
		}
		
		if (empty($GLOBALS['script_error_log']) && empty($result)) {
			$rec = array();
			$rec['tag_name'] = $tag;
			$rec['tag_display_name'] = $display_tag;
			$rec['user_id'] = $_SESSION['user_id'];
			
			$table = $db->prefix . '_tag';
			
			$db->insertDB($rec, $table);
			
			$query = "
				SELECT *
				FROM " . $db->prefix . "_user
				WHERE user_id=" . $_SESSION['user_id']
			;
			
			$result = $db->Execute($query);
			
			// insert the notification
			$rec = array();
			$rec['create_datetime'] = time();
			$rec['user_id'] = $_SESSION['user_id'];
			$rec['tag_name'] = $tag;
			$rec['notification_type'] = $core_config['script']['notification_type']['create_network'];
		
			$table = $db->prefix . '_notification';
		
			$db->insertDB($rec, $table);
		
			header('location: /network/' . $tag);
			exit;
		}
	}

}
else {
	// SELECT POPULAR NETWORKS (TAGS) ------------------------------------------------------
	$query = "
		SELECT COUNT(*) AS tag_total, t.tag_name, t.tag_display_name
		FROM " . $db->prefix . "_tag t
		GROUP BY t.tag_name
		ORDER BY tag_total DESC
		LIMIT 20"
	;

	$result = $db->Execute($query);
}

if (!empty($result)) {
	usort($result, 'tag_cmp');
	
	foreach($result as $key => $val):
		$query = "
			SELECT COUNT(*) AS total_contributions
			FROM " . $db->prefix . "_notification
			WHERE tag_name=" . $db->qstr($val['tag_name']) . "
			AND parent_id=0
			AND notification_type=0"
		;
		
		$result2 = $db->Execute($query);
		
		$result[$key]['total_contributions'] = $result2[0]['total_contributions'];
		
		$query = "
			SELECT COUNT(DISTINCT user_id) AS total_contributors
			FROM " . $db->prefix . "_notification
			WHERE tag_name=" . $db->qstr($val['tag_name']) . "
			AND notification_type=0"
		;
		
		$result2 = $db->Execute($query);
		
		$result[$key]['total_contributors'] = $result2[0]['total_contributors'];
	
	endforeach;

	$body->set('networks', $result);
}


// SELECT MY NETWORKS
if (!empty($_SESSION['user_id'])) {
	$query = "
		SELECT COUNT(*) AS tag_total, t.tag_name, t.tag_display_name
		FROM " . $db->prefix . "_tag t
		WHERE t.user_id=" . $_SESSION['user_id'] . "
		GROUP BY t.tag_name
		ORDER BY tag_total DESC"
	;

	$result = $db->Execute($query);

	if (!empty($result)) {
		usort($result, 'tag_cmp');
	}

	$body->set('my_networks', $result);
}

?>