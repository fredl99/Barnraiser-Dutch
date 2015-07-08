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


if (!empty($_POST['search_text'])) {
	header('location: /search/' . $_POST['search_text']);
	exit;
}

if (isset($uri_routing[1])) {
	
	$search_text = urldecode($uri_routing[1]);
	
	$from = 0;
	
	if (isset($uri_routing[2]) && substr($uri_routing[2], 0, 4) == 'page') {
		if (is_numeric(substr($uri_routing[2], 4))) {
			$from = (int) substr($uri_routing[2], 4) * AM_MAX_LIST_ROWS;
			$body->set('page', substr($uri_routing[2], 4));
		}
		else {
			$body->set('page', "0");
		}
	}
	else {
		$body->set('page', "0");
	}
	
	$query = "
		SELECT COUNT(*) AS search_total
		FROM " . $db->prefix . "_notification
		WHERE MATCH(notification) AGAINST(" . $db->qstr($search_text) . ")
		AND parent_id=0"
	;
			
	$result = $db->Execute($query);
	
	if (!empty($result[0]['search_total']) && $result[0]['search_total'] > $core_config['display']['max_list_rows']) {
		$body->set('search_total', $result[0]['search_total']);
	}
	
	$query = "
		SELECT MATCH(notification) AGAINST(" . $db->qstr($search_text) . ") AS relevance
		FROM " . $db->prefix . "_notification
		WHERE parent_id=0
		ORDER BY MATCH(notification) AGAINST(" . $db->qstr($search_text) . ") DESC
		LIMIT 1"
	;
	
	$result = $db->Execute($query);
	
	if (!empty($result[0]['relevance'])) {
		$max_relevance = $result[0]['relevance'];
	}
	else {
		$max_relevance = 0;
	}
			
	$query = "
		SELECT n.*, u.*, MATCH(n.notification) AGAINST(" . $db->qstr($search_text) . ") AS relevance
		FROM " . $db->prefix . "_notification n
		INNER JOIN " . $db->prefix . "_user u
		ON n.user_id=u.user_id
		WHERE n.parent_id=0
		AND MATCH(n.notification) AGAINST(" . $db->qstr($search_text) . ")
		ORDER BY MATCH(n.notification) AGAINST(" . $db->qstr($search_text) . ") DESC"
	;
			
	$result = $db->Execute($query, AM_MAX_LIST_ROWS, $from);
	
	if (!empty($result)) {
		foreach($result as $key => $val):
			$result[$key]['relevance_precentage'] = $val['relevance']/$max_relevance * 100;
		endforeach;
	
		$body->set('search_result', $result);
	}
	
	$query = "
		SELECT COUNT(*) AS tag_total, t.tag_display_name, t.tag_name
		FROM " . $db->prefix . "_tag t
		WHERE t.tag_name LIKE " . $db->qstr('%' . $search_text . '%') . "
		GROUP BY t.tag_name
		ORDER BY tag_total DESC"
	;

	$result = $db->Execute($query);
	
	if (!empty($result)) {
		usort($result, 'tag_cmp');
		$body->set('similar_networks', $result);
	}
}

?>