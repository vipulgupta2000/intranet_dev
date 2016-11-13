--
-- Install SQL Script
--
-- @version		$Id: install.sql 362 2010-02-20 06:50:23Z dr_drsh $
-- @package		Joomla
-- @subpackage	JForms
-- @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
-- @license		GNU/GPL
--

CREATE TABLE IF NOT EXISTS `#__jforms_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `type` int(11) NOT NULL,
  `plugins` varchar(100) NOT NULL,
  `theme` varchar(100) NOT NULL DEFAULT 'default',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) unsigned NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `groups` varchar(255) NOT NULL,
  `maximum` int(11) unsigned NOT NULL DEFAULT '0',
  `redirections` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) TYPE=InnoDB ;

CREATE TABLE IF NOT EXISTS `#__jforms_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `position` int(12) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) TYPE=InnoDB ;

CREATE TABLE IF NOT EXISTS `#__jforms_parameters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `plugin_name` varchar(100) NOT NULL,
  `plugin_type` tinyint(4) NOT NULL,
  `parameter_name` varchar(100) NOT NULL,
  `parameter_value` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) TYPE=InnoDB ;

CREATE TABLE IF NOT EXISTS `#__jforms_tparameters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `plugin_name` varchar(100) NOT NULL,
  `plugin_type` tinyint(4) NOT NULL,
  `parameter_name` varchar(100) NOT NULL,
  `parameter_value` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) TYPE=InnoDB ;