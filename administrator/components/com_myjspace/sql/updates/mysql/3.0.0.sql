-- install.mysql.utf8.sql
-- version 3.0.0 26/08/2019
-- author	Bernard saulme
-- package	com_myjspace

CREATE TABLE IF NOT EXISTS `#__myjspace_cfg` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`params` text NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
