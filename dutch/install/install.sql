-------------------------------------------------------------------------
-- This file is part of Dutch
-- 
-- Copyright (C) 2003-2008 Barnraiser
-- http:--www.barnraiser.org/
-- info@barnraiser.org
-- 
-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation, either version 3 of the License, or
-- (at your option) any later version.
-- 
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
-- 
-- You should have received a copy of the GNU General Public License
-- along with this program; see the file COPYING.txt.  If not, see
-- <http:--www.gnu.org/licenses/>
-------------------------------------------------------------------------


-- Table structure for table `dutch_notification`
CREATE TABLE IF NOT EXISTS `dutch_notification` (
  `notification_id` int(11) NOT NULL auto_increment,
  `notification` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `create_datetime` datetime NOT NULL,
  `child_count` int(11) NOT NULL default '0',
  `tag_name` varchar(255) NOT NULL,
  `update_datetime` datetime NOT NULL,
  `notification_type` int(1) NOT NULL,
  `bayesian_rating` float NOT NULL,
  PRIMARY KEY  (`notification_id`),
  FULLTEXT KEY `notification_ft` (`notification`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


-- Table structure for table `dutch_notification_rating`
CREATE TABLE IF NOT EXISTS `dutch_notification_rating` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(1) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- Table structure for table `dutch_relation`
CREATE TABLE IF NOT EXISTS `dutch_relation` (
  `relation_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `user_id_relation` int(11) NOT NULL,
  `relation_create_datetime` datetime NOT NULL,
  PRIMARY KEY  (`relation_id`),
  UNIQUE KEY `relation_index` (`user_id`,`user_id_relation`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- Table structure for table `dutch_tag`
CREATE TABLE IF NOT EXISTS `dutch_tag` (
  `tag_id` int(11) NOT NULL auto_increment,
  `tag_name` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tag_display_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- Table structure for table `dutch_user`
CREATE TABLE IF NOT EXISTS `dutch_user` (
  `user_id` int(11) NOT NULL auto_increment,
  `user_live` int(1) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_openid` varchar(255) default NULL,
  `user_password` varchar(255) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_location` varchar(100) NOT NULL,
  `user_dob` date NOT NULL,
  `user_create_datetime` datetime NOT NULL,
  `user_privacy` int(1) NOT NULL,
  `user_registration_key` varchar(100) default NULL,
  `user_last_login_datetime` datetime NOT NULL,
  `user_next_digest_datetime` datetime NOT NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ENDS