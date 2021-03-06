<?php
if (!defined('IN_FINECMS')) exit('No permission resources');

return array(
    "DROP TABLE IF EXISTS `{prefix}myblock`;",
	"CREATE TABLE IF NOT EXISTS `{prefix}myblock` (
	`id` smallint(5) NOT NULL AUTO_INCREMENT,
	`adname` varchar(20) NOT NULL,
	`width` smallint(5) NOT NULL,
	`height` smallint(5) NOT NULL,
	`showtype` tinyint(1) NOT NULL,
	PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;",
	"DROP TABLE IF EXISTS `{prefix}myblock_data`;",
	"CREATE TABLE IF NOT EXISTS `{prefix}myblock_data` (
	`id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
	`aid` smallint(5) NOT NULL,
	`typeid` tinyint(3) NOT NULL,
	`name` char(50) NOT NULL,
	`sub_name` char(50) NOT NULL,
	`default_pic` varchar(150) NOT NULL,
	`hover_pic` varchar(150) NOT NULL,
	`addtime` int(10) NOT NULL,
	`clicks` int(10) NOT NULL,
	`disabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`listorder` tinyint(3) NOT NULL,
	`catid` smallint(5) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `disabled` (`disabled`),
	KEY `aid` (`aid`),
	KEY `listorder` (`listorder`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
);