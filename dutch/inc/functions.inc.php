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


// URL routing into array
function routeURL () {

	$document_root = trim(dirname($_SERVER['PHP_SELF']), '/');
	$script_name = $_SERVER['PHP_SELF'];

	$request_uri = substr($_SERVER['REQUEST_URI'], strlen($document_root) + 1);
	
	$tmp = strpos($request_uri, '?');
	
	if ($tmp) {
		$request_uri = substr($request_uri, 0, $tmp);
	}
	
	$request_arr = explode('/', $request_uri);

	return $request_arr;
}

function tag_cmp($a, $b) {
	return $a['tag_name'] > $b['tag_name'];
}


function timeDiff($timestamp){

	$timestamp = strtotime($timestamp);

        $now = time();

        //If the difference is positive "ago" - negative "away"
        ($timestamp >= $now) ? $action = 'om' : $action = 'sedan';

        switch($action) {
        case 'om':
                $diff = $timestamp - $now;
                break;
        case 'sedan':
        default:
                // Determine the difference, between the time now and the timestamp
                $diff = $now - $timestamp;
                break;
        }

        // Set the periods of time
        $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");

        // Set the number of seconds per period
        $lengths = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);

        // Go from decades backwards to seconds
        for ($val = sizeof($lengths) - 1; ($val >= 0) && (($number = $diff / $lengths[$val]) <= 1); $val--);

        // Ensure the script has found a match
        if ($val < 0) $val = 0;

        // Determine the minor value, to recurse through
        $new_time = $now - ($diff % $lengths[$val]);

        // Set the current value to be floored
        $number = floor($number);

        // If required create a plural
        if($number != 1) {
        	if ($periods[$val] == 'hour') {
        		$periods[$val] = "hours";
        	}
        	elseif ($periods[$val] == 'day') {
        		$periods[$val] = "days";
        	}
        	elseif ($periods[$val] == 'week') {
        		$periods[$val] = "weeks";
        	}
        	elseif ($periods[$val] == 'year') {
        		$periods[$val] = "years";
        	}
        	else {
	        	$periods[$val].= "s";
	        }
        }

        // Return text
        $text = sprintf("%d %s ", $number, $periods[$val]);
        return $text . "ago";
}

function parse($str) {

	$str = strip_tags($str, '<object><param><embed><a><img><code><ul><ol><li>');
	$str = nl2br($str);
	
	$addons = glob('addons/*.inc.php');
	
	foreach($addons as $key => $val):
		$tmp = explode('/', $val);
		$function = substr($tmp[1], 0, -8);
		if (is_readable('addons/' . $function . '.inc.php')) {
			include_once 'addons/' . $function . '.inc.php';
			if (function_exists($function . '_parse')) {
				$function .= '_parse';
				$str = $function($str);
			}
		}
	endforeach;
	
	$str = link_parse($str); // we need to linkparse after all other links (youtube) has been parsed
	
	//

	return addslashes($str);
}

function link_parse($str) {
	
	$pattern = '#(^|[^"\'=\]>]{1})(http|HTTP|ftp)(s|S)?://([^\s<>\.]+)\.([^\s<>]+)#sm';
	
	//$pattern = '/(https?|ftp|file):\/\/[-A-Z0-9+&@#/\%?=~_|!:,.;]*[-A-Z0-9+&@#/\%=~_|]/i';
	/*$replace = '\\1<a href="\\2\\3://\\4.\\5">\\2\\3://\\4.\\5</a>';
	$str = preg_replace($pattern, $replace, $str);
	*/
	if (preg_match_all($pattern, $str, $matches, PREG_PATTERN_ORDER)) { 
		
		foreach ($matches[0] as $key => $val) {

			if (strlen($val) > 30) {
				$title = substr($val, 0, 30) . '...';
			}
			else {
				$title = $val;
			}
			
			$link = "<a href=\"" . $val . "\" target=\"_blank\" title=\"" . $val . "\">" . $title . "</a>";
			
			$str = str_replace ($val, $link, $str);
		}
	}
	return $str;
}

// used in register and account to format the DOB
function formatDate($y, $m, $d) {
	if (empty($y)) {
		$GLOBALS['script_error_log'][] = _("Please provide us with a valid year.");
		return 0;
	}
	
	if (empty($m)) {
		$GLOBALS['script_error_log'][] = _("Please provide us with a valid month.");
		return 0;
	}
	
	if (empty($d)) {
		$GLOBALS['script_error_log'][] = _("Please provide us with a valid day.");
		return 0;
	}
	
	return date('Y-m-d', mktime(0, 0, 0, $m, $d, $y));
}


// used in register and account
function checkEmail($email) {
  $result = TRUE;
  if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email)) {
    $result = FALSE;
  }
  return $result;
}

function parse_tag($tag) {
	$tag = mb_strtolower($tag, mb_detect_encoding($tag));
	$look_for = array('å', 'ä', 'ö', ' ');
	$replace_width = array('a', 'a', 'o', '_');
	return str_replace($look_for, $replace_width, $tag);
}

?>