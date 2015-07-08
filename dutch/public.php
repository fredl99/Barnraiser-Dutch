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


include_once 'inc/openid_consumer.inc.php';

// If there is a problem with a page we get redirected to this file
// If the session is there then we are logged in but looking at the login
// hence we make sure you are logged off
if (isset($_SESSION['user_id'])) { 
	session_unset();
	session_destroy();
	session_write_close();
	header("Location: /");
	exit;
}

// SELECT POPULAR NETWORKS (TAGS) ------------------------------------------------------
$query = "
	SELECT COUNT(*) AS tag_total, t.tag_name, t.tag_display_name
	FROM " . $db->prefix . "_tag t
	GROUP BY t.tag_name
	ORDER BY tag_total DESC
	LIMIT 20"
;

$result = $db->Execute($query);

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
}

$body->set('networks', $result);

?>