--
-- Table structure for table `table_users`
--

CREATE TABLE IF NOT EXISTS `table_users` (
  `uid` SMALLINT(4) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(32) NOT NULL,
  `password` VARCHAR(64) NOT NULL,
  UNIQUE KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Table structure for table `table_options`
--

CREATE TABLE IF NOT EXISTS `table_options` (
  `name` VARCHAR(16) NOT NULL,
  `value` TEXT NOT NULL,
  `for` SMALLINT(8) NOT NULL DEFAULT 0,
  UNIQUE `option` (`name`, `for`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `table_articles`
--

CREATE TABLE IF NOT EXISTS `table_articles` (
  `pid` SMALLINT(8) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `title` VARCHAR(64) NOT NULL,
  `author` VARCHAR(32) DEFAULT NULL,
  `contents` TEXT DEFAULT NULL,
  `parent` SMALLINT(4) UNSIGNED NULL DEFAULT 1,
  UNIQUE KEY (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Empty Data
--

TRUNCATE TABLE `table_users`;
TRUNCATE TABLE `table_options`;
TRUNCATE TABLE `table_articles`;

--
-- Insert Data
--

INSERT INTO `test`.`table_users` (`name`, `password`) VALUES ('John', 'JohnPassword'), ('Jack', 'JackPassword');

INSERT INTO `test`.`table_options` (`name`, `value`, `for`) VALUES ('JohnOptions', 'JohnOptionsValue', '1'), ('JackOptions', 'JackOptionsValue', '2');

INSERT INTO `test`.`table_articles` (`pid`, `title`, `author`, `contents`, `parent`) VALUES (NULL, 'John Article', 'John', 'John Article Contents', '1'), (NULL, 'Jack Article', 'Jack', 'Jack Article Contents', '2');

