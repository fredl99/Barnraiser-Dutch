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


function digg_parse($str) {

	$pattern = "/http\:\/\/digg\.com\/(\S+)\/(\S+)/";
	if (preg_match_all($pattern, $str, $matches, PREG_PATTERN_ORDER)) {

		foreach ($matches[1] as $key => $val) {
		
			$tmp = explode('?', $matches[2][$key]);
			
			$hostname = "http://services.digg.com/" . 'story/' . $tmp[0] . "?type=xml";
	
			$appid = urlencode("http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);	
			$host = $hostname."&appkey=".$appid;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_URL,$host);
			curl_setopt ($ch,CURLOPT_USERAGENT,"mizu.se - share stuff with your networks");
			curl_setopt ($ch,CURLOPT_CONNECTTIMEOUT,60);
			$response = curl_exec ( $ch );
			curl_close($ch);	

			$s = simplexml_load_string($response);

			$image_path = $s->story->thumbnail['src'];
			$title = $s->story->title . " (" . $s->story['diggs'] . " diggs)";
			$link = $s->story['href'];
			$desc_data = $s->story->description;

			if (isset($image_path) && !empty($image_path)) {

				$digg_data = "
						<table width=\"100%\">
						<tr>
							<td valign=\"top\"><a href=\"" . $link . "\"><img src=\"" . $image_path . "\" /></a></td>
							<td valign=\"top\"><a href=\"" . $link . "\">" . $title . "</a><br />" . $desc_data . "</td>
						</tr>
						</table>
					";
			}
			else {
				$digg_data = "
						<table width=\"100%\">
						<tr>
							<td valign=\"top\"><a href=\"" . $link . "\">" . $title . "</a><br />" . $desc_data . "</td>
						</tr>
						</table>
					";
			}

			$str = str_replace ($matches[0][$key], $digg_data, $str);		
		}
	}
	return $str;
}

?>