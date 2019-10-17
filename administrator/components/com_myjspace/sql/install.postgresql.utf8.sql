-- install.postgresql.utf8.sql
-- version 3.0.0 26/08/2019
-- author  Bernard saulme
-- package com_myjspace

CREATE TABLE IF NOT EXISTS #__myjspace (
	"id" serial NOT NULL,
	"title" varchar(100) NOT NULL,
	"pagename" varchar(100) NOT NULL,
	"foldername" varchar(150) NOT NULL,
	"userid" integer DEFAULT 0 NOT NULL,
	"modified_by" integer DEFAULT 0 NOT NULL,
	"access" integer DEFAULT 0 NOT NULL,
	"content" text NOT NULL,
	"blockedit" integer DEFAULT 0 NOT NULL,
	"blockview" integer DEFAULT 0 NOT NULL,
	"userread" varchar(100) DEFAULT '' NOT NULL,
	"create_date" timestamp DEFAULT CURRENT_TIMESTAMP,
	"last_update_date" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
	"last_access_date" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
	"last_access_ip" varchar(8) DEFAULT '0' NOT NULL,
	"hits" bigint DEFAULT 0 NOT NULL,
	"publish_up" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
	"publish_down" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
	"metakey" text NOT NULL,
	"template" varchar(50) DEFAULT '' NOT NULL,
	"catid" bigint DEFAULT 0 NOT NULL,
	"language" varchar(7) DEFAULT '*' NOT NULL,
	PRIMARY KEY ("id"),
	CONSTRAINT "#le5lz_myjspace_idx_pagename" UNIQUE ("pagename")
);
CREATE INDEX IF NOT EXISTS "#__myjspace_idx_userid" ON "#__myjspace" ("userid");
CREATE INDEX IF NOT EXISTS "#__myjspace_idx_access" ON "#__myjspace" ("access");

CREATE TABLE IF NOT EXISTS #__myjspace_cfg (
	"id" serial NOT NULL,
	"params" text NOT NULL,
	PRIMARY KEY ("id")
);
