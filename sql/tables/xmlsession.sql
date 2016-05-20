SET NAMES 'utf8';
DROP TABLE IF EXISTS `xmlsession`;
CREATE TABLE `xmlsession` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'via trigger (xmlsession)',
  `last_use` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `users` int(10) unsigned NOT NULL DEFAULT '0',
  `caches` int(10) unsigned NOT NULL DEFAULT '0',
  `cachedescs` int(10) unsigned NOT NULL DEFAULT '0',
  `cachelogs` int(10) unsigned NOT NULL DEFAULT '0',
  `pictures` int(10) unsigned NOT NULL DEFAULT '0',
  `removedobjects` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_since` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cleaned` tinyint(1) NOT NULL DEFAULT '0',
  `agent` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;
