-- install.mysql.utf8.sql
-- version 3.0.0 26/08/2019
-- author  Bernard saulme
-- package scom_myjspace

CREATE TABLE IF NOT EXISTS `#__myjspace` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`title` varchar(100) NOT NULL DEFAULT '',
	`pagename` varchar(100) NOT NULL,
	`foldername` varchar(150) NOT NULL DEFAULT 'media/myjsp',
	`userid` int(11) NOT NULL DEFAULT 0,
	`modified_by` int(10) unsigned NOT NULL DEFAULT 0,
	`access` int(10) unsigned NOT NULL DEFAULT 0,
	`content` text NOT NULL,
	`blockedit` int(10) unsigned NOT NULL DEFAULT 0,
	`blockview` int(10) unsigned NOT NULL DEFAULT 1,
	`userread` varchar(100) NOT NULL DEFAULT '',
	`create_date` timestamp DEFAULT CURRENT_TIMESTAMP,
	`last_update_date` timestamp,
	`last_access_date` timestamp,
	`last_access_ip` varchar(8) NOT NULL DEFAULT '0',
	`hits` bigint NOT NULL DEFAULT 0,
	`publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`metakey` text NOT NULL,
	`template` varchar(50) NOT NULL DEFAULT '',
	`catid` int(11) NOT NULL DEFAULT 0,
	`language` char(7) NOT NULL DEFAULT '*',
	PRIMARY KEY (`id`),
	UNIQUE `idx_pagename` (`pagename`),
	KEY `idx_userid` (`userid`),
	KEY `idx_access` (`access`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__myjspace_cfg` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`params` text NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
