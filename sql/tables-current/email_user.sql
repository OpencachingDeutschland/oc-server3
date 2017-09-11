SET NAMES 'utf8';
DROP TABLE IF EXISTS `email_user`;
CREATE TABLE `email_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_created` datetime NOT NULL COMMENT 'via Trigger (email_user)',
  `ipaddress` varchar(20) NOT NULL,
  `from_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `from_email` varchar(60) NOT NULL,
  `to_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `to_email` varchar(60) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `from_user_id` (`from_user_id`),
  KEY `date` (`date_created`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;
