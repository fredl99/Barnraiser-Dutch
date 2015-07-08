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


if (!isset($_SESSION['user_id'])) { 
	header('location: /');
	exit;
}


// MY RELATIONS -----------------------------
$query = "
	SELECT u.user_id, u.user_name  
	FROM " . $db->prefix . "_relation r, " . $db->prefix . "_user u
	WHERE r.user_id=" . $_SESSION['user_id'] . " 
	AND r.user_id_relation=u.user_id 
	ORDER BY u.user_name"
;

$result = $db->Execute($query);

if (!empty($result)) {
	$body->set('relations', $result);

	$relation_ids = "";
	
	foreach($result as $key => $i):
		$relation_ids .= $i['user_id'];

		if ($key < count($result)) {
			$relation_ids .= ",";
		}
	endforeach;
	$relation_ids = rtrim($relation_ids, ',');
}


// LATEST NOTIFICATIONS IN MY NETWORKS ------------------------------------

$query = "
	SELECT DISTINCT t.tag_name
	FROM " . $db->prefix . "_tag t
	WHERE user_id=" . $_SESSION['user_id']
;

$result = $db->Execute($query);

if (!empty($result)) {
	$tags = "";
	foreach($result as $key => $val):
		$tags .= $db->qstr($val['tag_name']) . ', ';
	endforeach;
	$tags = rtrim($tags, ', ');

	$query = "
		SELECT DISTINCT n.*, u.*, t.tag_display_name, n.create_datetime AS create_datetime_1
		FROM " . $db->prefix . "_notification n
		INNER JOIN " . $db->prefix . "_user u
		ON n.user_id=u.user_id
		LEFT JOIN " . $db->prefix . "_tag t
		ON n.tag_name=t.tag_name
		WHERE n.tag_name IN(" . $tags . ")
		AND n.parent_id=0
		AND n.user_id !=" . $_SESSION['user_id'] . "
		UNION
		SELECT DISTINCT n2.*, u2.*, t2.tag_display_name, n2.create_datetime AS create_datetime_1
		FROM " . $db->prefix . "_notification n2
		INNER JOIN " . $db->prefix . "_user u2
		ON n2.user_id=u2.user_id
		LEFT JOIN " . $db->prefix . "_tag t2
		ON n2.tag_name=t2.tag_name
		WHERE n2.notification_type=" . $core_config['script']['notification_type']['create_network'] . "
		AND n2.user_id != " . $_SESSION['user_id']
	;

	if (!empty($relation_ids)) {
		$query .= "
			UNION
			SELECT DISTINCT n3.*, u3.*, t3.tag_display_name, n3.create_datetime AS create_datetime_1
			FROM " . $db->prefix . "_notification n3
			INNER JOIN " . $db->prefix . "_user u3
			ON n3.user_id=u3.user_id
			LEFT JOIN " . $db->prefix . "_tag t3
			ON n3.tag_name=t3.tag_name
			WHERE 
			n3.user_id IN(" . $relation_ids . ")"
		;
	}

	$query .= "
		ORDER BY create_datetime DESC
		LIMIT 10"
	;

	$result = $db->Execute($query);

	if (!empty($result)) {
		$body->set('notifications', $result);
	}
}


// MY NETWORKS -----------------------------
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
	
	$body->set('my_networks', $result);
}



// SIMILAR NETWORKS --------------------------
$query = "
	SELECT *
	FROM " . $db->prefix . "_tag
	WHERE user_id=" . $_SESSION['user_id']
;

$result = $db->Execute($query);

$networks = "";
foreach($result as $key => $val):
	$networks .= $db->qstr($val['tag_name']) . ', ';
endforeach;
$networks = rtrim($networks, ', ');

if (!empty($networks)) {

	$query = "
		SELECT u.*, COUNT(t.user_id) AS total
		FROM " . $db->prefix . "_tag t
		INNER JOIN " . $db->prefix . "_user u
		ON u.user_id=t.user_id
		WHERE t.tag_name IN (" . $networks . ")
		AND t.user_id != " . $_SESSION['user_id'] . "
		GROUP BY t.user_id
		ORDER BY total DESC, RAND()
		LIMIT 3"
	;
	
	$result = $db->Execute($query);
	
	if (!empty($result)) {
		$body->set('similar', $result);
	}
}

?>