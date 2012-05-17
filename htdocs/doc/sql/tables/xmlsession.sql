SET NAMES 'utf8';
DROP TABLE IF EXISTS `xmlsession`;
CREATE TABLE `xmlsession` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `date_created` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'via trigger (xmlsession)',
  `last_use` datetime NOT NULL default '0000-00-00 00:00:00',
  `remote_addr` char(15) default NULL,
  `users` int(10) unsigned NOT NULL default '0',
  `caches` int(10) unsigned NOT NULL default '0',
  `cachedescs` int(10) unsigned NOT NULL default '0',
  `cachelogs` int(10) unsigned NOT NULL default '0',
  `pictures` int(10) unsigned NOT NULL default '0',
  `removedobjects` int(10) unsigned NOT NULL default '0',
  `modified_since` datetime NOT NULL default '0000-00-00 00:00:00',
  `cleaned` tinyint(1) NOT NULL default '0',
  `agent` varchar(60) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `remote_addr` (`remote_addr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
