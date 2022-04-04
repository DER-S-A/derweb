

CREATE TABLE `cal_accounts` (
	`id` int NOT NULL AUTO_INCREMENT,
	`user` varchar(80) NULL,
	`pass` varchar(32) NULL,
	`first_name` varchar(50) NOT NULL DEFAULT '',
	`last_name` varchar(50) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	UNIQUE KEY `user` (`user`),
	KEY `user_2` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;


LOCK TABLES `cal_accounts` WRITE;
/*!40000 ALTER TABLE `cal_accounts` DISABLE KEYS */;
INSERT INTO `cal_accounts` (`id`, `user`, `pass`, `first_name`, `last_name`) VALUES (1, 'marcosc', 'd76a6638530f100bd1ff9d35e88c477f', '', '');
INSERT INTO `cal_accounts` (`id`, `user`, `pass`, `first_name`, `last_name`) VALUES (2, '', '', '', '');
/*!40000 ALTER TABLE `cal_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cal_eventtypes`
--

CREATE TABLE `cal_eventtypes` (
	`id` int NOT NULL AUTO_INCREMENT,
	`typename` varchar(100) NOT NULL DEFAULT '',
	`typedesc` varchar(100) NOT NULL,
	`typecolor` varchar(6) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Dumping data for table `cal_eventtypes`
--

LOCK TABLES `cal_eventtypes` WRITE;
/*!40000 ALTER TABLE `cal_eventtypes` DISABLE KEYS */;
INSERT INTO `cal_eventtypes` (`id`, `typename`, `typedesc`, `typecolor`) VALUES (2, 'Importante', 'evento que no se posterga', 'FFAAAA');
INSERT INTO `cal_eventtypes` (`id`, `typename`, `typedesc`, `typecolor`) VALUES (3, 'Reunión', 'Junta', '999999');
INSERT INTO `cal_eventtypes` (`id`, `typename`, `typedesc`, `typecolor`) VALUES (4, 'Viaje', 'De trabajo', 'A4CAE6');
/*!40000 ALTER TABLE `cal_eventtypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cal_events`
--

CREATE TABLE `cal_events` (
	`id` int NOT NULL AUTO_INCREMENT,
	`username` varchar(255) NULL,
	`user_id` int NOT NULL DEFAULT '0',
	`mod_id` int NULL,
	`mod_username` varchar(50) NULL,
	`mod_stamp` datetime NULL,
	`stamp` datetime NULL,
	`duration` datetime NULL,
	`eventtype` int NULL,
	`subject` varchar(255) NULL,
	`description` text NULL,
	`alias` varchar(20) NULL,
	`private` char(1) NOT NULL DEFAULT '0',
	`repeat_end` date NULL,
	`repeat_num` mediumint NOT NULL DEFAULT '0',
	`repeat_d` smallint NOT NULL DEFAULT '0',
	`repeat_m` smallint NOT NULL DEFAULT '0',
	`repeat_y` smallint NOT NULL DEFAULT '0',
	`repeat_h` smallint NOT NULL DEFAULT '0',
	`type_id` int NOT NULL DEFAULT '0',
	`special_id` int NOT NULL DEFAULT '0',
	`deleted` TINYINT(3) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	FULLTEXT KEY `subject` (`subject`, `description`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;


--
-- Table structure for table `cal_options`
--
CREATE TABLE `cal_options` (
	`opname` varchar(30) NOT NULL DEFAULT '',
	`opvalue` varchar(50) NOT NULL DEFAULT '',
	PRIMARY KEY (`opname`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Dumping data for table `cal_options`
--

LOCK TABLES `cal_options` WRITE;
/*!40000 ALTER TABLE `cal_options` DISABLE KEYS */;
INSERT INTO `cal_options` (`opname`, `opvalue`) VALUES ('skin', 'default');
INSERT INTO `cal_options` (`opname`, `opvalue`) VALUES ('language', 'spanish');
INSERT INTO `cal_options` (`opname`, `opvalue`) VALUES ('whole_day', 'n');
INSERT INTO `cal_options` (`opname`, `opvalue`) VALUES ('anon_naming', 'n');
INSERT INTO `cal_options` (`opname`, `opvalue`) VALUES ('show_times', 'n');
INSERT INTO `cal_options` (`opname`, `opvalue`) VALUES ('hours_24', 'y');
INSERT INTO `cal_options` (`opname`, `opvalue`) VALUES ('start_monday', 'n');
INSERT INTO `cal_options` (`opname`, `opvalue`) VALUES ('timeout', '5');
/*!40000 ALTER TABLE `cal_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cal_permissions`
--

CREATE TABLE `cal_permissions` (
	`user_id` int NOT NULL DEFAULT '0',
	`pname` varchar(30) NOT NULL DEFAULT '',
	`pvalue` char(1) NOT NULL DEFAULT 'n',
	PRIMARY KEY (`user_id`, `pname`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Dumping data for table `cal_permissions`
--

LOCK TABLES `cal_permissions` WRITE;
/*!40000 ALTER TABLE `cal_permissions` DISABLE KEYS */;
INSERT INTO `cal_permissions` (`user_id`, `pname`, `pvalue`) VALUES (1, 'read', 'y');
INSERT INTO `cal_permissions` (`user_id`, `pname`, `pvalue`) VALUES (1, 'edit', 'y');
INSERT INTO `cal_permissions` (`user_id`, `pname`, `pvalue`) VALUES (1, 'editothers', 'n');
INSERT INTO `cal_permissions` (`user_id`, `pname`, `pvalue`) VALUES (1, 'readothers', 'y');
INSERT INTO `cal_permissions` (`user_id`, `pname`, `pvalue`) VALUES (1, 'remind_set', 'y');
INSERT INTO `cal_permissions` (`user_id`, `pname`, `pvalue`) VALUES (1, 'write', 'y');
INSERT INTO `cal_permissions` (`user_id`, `pname`, `pvalue`) VALUES (2, '', '');
INSERT INTO `cal_permissions` (`user_id`, `pname`, `pvalue`) VALUES (1, 'remind_get', 'y');
INSERT INTO `cal_permissions` (`user_id`, `pname`, `pvalue`) VALUES (1, 'needapproval', 'y');
INSERT INTO `cal_permissions` (`user_id`, `pname`, `pvalue`) VALUES (1, 'admin', 'y');
/*!40000 ALTER TABLE `cal_permissions` ENABLE KEYS */;
UNLOCK TABLES;


