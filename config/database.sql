CREATE DATABASE IF NOT EXISTS `kartulimardikas` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `kartulimardikas`;

CREATE TABLE IF NOT EXISTS `user` (
  `uid` int(6) NOT NULL AUTO_INCREMENT COMMENT 'user id',
  `username` varchar(64) COLLATE utf8_general_ci NOT NULL,
  `email` varchar(128) COLLATE utf8_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `language` varchar(8) COLLATE utf8_general_ci NOT NULL COMMENT 'language of user interface',
  `rights` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'users rights: 0 = user, 1 = admin, 2 = superadmin',
  `date_registration` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'timestamp of registration',
  `date_lastsignin` timestamp NULL DEFAULT NULL COMMENT 'timestamp of latest signin',
  `date_deletion` timestamp NULL DEFAULT NULL COMMENT 'timestamp of deletion',
  PRIMARY KEY (`uid`),
  INDEX (`username`, `email`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `algorithm` (
  `aid` int(8) NOT NULL AUTO_INCREMENT COMMENT 'algorithm id',
  `uid` int(6) NOT NULL COMMENT 'the author''s user id',
  `name` varchar(64) COLLATE utf8_general_ci DEFAULT NULL,
  `description` varchar(1024) COLLATE utf8_general_ci DEFAULT NULL,
  `long_description` text COLLATE utf8_general_ci COMMENT 'algorithm information',
  `variables` text COLLATE utf8_general_ci COMMENT 'variable information in json format',
  `tree` mediumtext COLLATE utf8_general_ci COMMENT 'script information in json format',
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'timestamp of creation',
  `date_lastedit` timestamp NULL DEFAULT NULL COMMENT 'timestamp of last modification',
  `date_publish` timestamp NULL DEFAULT NULL COMMENT 'timestamp of publication',
  `date_deletion` timestamp NULL DEFAULT NULL COMMENT 'timestamp of deletion',
  `view_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'counts the algorithm views',
  PRIMARY KEY (`aid`),
  CONSTRAINT FOREIGN KEY (`uid`) REFERENCES `user`(`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `tag` (
  `tag` varchar(64) COLLATE utf8_general_ci NOT NULL COMMENT 'The tag''s name',
  `aid` int(8) NOT NULL COMMENT 'The algorithm''s id',
  PRIMARY KEY (`tag`,`aid`),
  CONSTRAINT FOREIGN KEY (`aid`) REFERENCES `algorithm`(`aid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE VIEW `algorithm_public` AS
  SELECT * FROM `algorithm`
  WHERE `date_deletion` IS NULL
  AND `date_publish` IS NOT NULL;

INSERT INTO `user` (`uid`, `username`, `email`, `password`, `language`, `rights`, `date_registration`, `date_lastsignin`, `date_deletion`) VALUES
  (1, 'admin', 'admin@domain.org', '$2y$10$fJHBSe6cHlE56h2v7qmWauwmznkNji1eZYxXkZh.z6rnXJ/f.4Ov2', 'en', 2, CURRENT_TIMESTAMP, NULL, NULL);