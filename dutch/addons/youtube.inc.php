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



// needs fix. http://youtube.com works, http://www.youtube.com does not.
function youtube_parse($str) {

	// ^http\://[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(/\S*)?$
		//$pattern = "/youtube\.com\/watch\?v=([a-zA-z\d]+)/";
		$pattern = "/[www\.]?youtube\.com\/watch\?v=([a-zA-z\d]+)/";
		$pattern = "/[www\.]?youtube\.com\/watch\?v=([a-zA-z\d]+)/";
		if (preg_match_all($pattern, $str, $matches, PREG_PATTERN_ORDER)) { 
			foreach ($matches[1] as $key => $val) { 
				$tmp = explode('&', $val);
				$val = $tmp[0];
			
				$image_path = "http://i.ytimg.com/vi/" . $val . "/default.jpg";
				$title = "No title could be found for this video";
				$desc_data = "No description could be found for this video";
				$link = 'http://youtube.com/watch?v=' . $matches[1][$key];
				$content = file_get_contents($link);
				
				if (preg_match("/<title>(.*?)<\/title>/s", $content, $_matches)) {
					$title = $_matches[1];
				}
				
				if (preg_match_all("/<div  class=\"watch-video-desc\">(.*?)<\/div>/s", $content, $_matches2)) {
 					$desc_data = $_matches2[1][1];
 				}
				
				$youtube_data = "
					<table width=\"100%\">
					<tr>
						<td valign=\"top\"><a href=\"http://youtube.com/watch?p=1&v=" .  $matches[1][$key] . "\" target=\"_blank\"><img src=\"" . $image_path . "\" /></a></td>
						<td valign=\"top\"><a href=\"http://youtube.com/watch?p=1&v=" .  $matches[1][$key] . "\" target=\"_blank\">" . $title . "</a><br />" . $desc_data . "</td>
					</tr>
					</table>
				";

				$str = str_replace ($link, $youtube_data, $str);
			}
		}
		return $str;
}

?>