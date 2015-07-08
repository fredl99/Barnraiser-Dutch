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


include_once ("../config/core.config.php");
include_once ("../inc/functions.inc.php");

// SESSION HANDLER --------------------------------------------------
session_name($core_config['php']['session_name']);
session_start();

// SETUP AROUNDMe CORE ----------------------------------------------
require_once('../class/Db.class.php');
$db = new Database($core_config['db']);

if (isset($_POST['notification_id']) && !empty($_POST['rating']) && !empty($_SESSION['user_id'])) {
	// have I already rated this item?
	$query = "
		SELECT user_id
		FROM " . $db->prefix . "_notification_rating
		WHERE user_id=" . $_SESSION['user_id'] . "
		AND notification_id=" . $_POST['notification_id']
	;
	
	$result = $db->Execute($query);
	
	if (empty($result)) {
		// insert rating
		$rec = array();
		$rec['notification_id'] = $_POST['notification_id'];
		$rec['user_id'] = $_SESSION['user_id'];
		$rec['rating'] = $_POST['rating'];
		
		$table = $db->prefix . '_notification_rating';
		
		$db->insertDB($rec, $table);
		
		// calculate new rating for item.		
		$query = "
			SELECT COUNT(notification_id)/(SELECT COUNT(DISTINCT notification_id))
			AS avg_num_votes, AVG(rating) AS avg_rating
			FROM " . $db->prefix . "_notification_rating"
		;
		
		$result = $db->Execute($query);
		
		$avg_num_votes = $result[0]['avg_num_votes'];
		$avg_rating = $result[0]['avg_rating'];
		
		$query = "
			SELECT COUNT( notification_id ) / (
			SELECT COUNT( DISTINCT notification_id ) ) AS this_num_votes, AVG( rating ) AS this_rating
			FROM am_notification_rating
			WHERE notification_id=" . $_POST['notification_id']
		;
		
		$result = $db->Execute($query);
		
		$this_num_votes = $result[0]['this_num_votes'];
		$this_rating = $result[0]['this_rating'];
		
		$bayesian_rating = (($avg_num_votes * $avg_rating) + ($this_num_votes * $this_rating)) / ($avg_num_votes + $this_num_votes);
		
		$query = "
			UPDATE " . $db->prefix . "_notification
			SET bayesian_rating=" . $bayesian_rating . "
			WHERE notification_id=" . $_POST['notification_id']
		;
		
		$db->Execute($query);
		
		//output new rating
		echo $bayesian_rating;
	}
}

?>