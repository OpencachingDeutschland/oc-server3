SET NAMES 'utf8';
DROP TABLE IF EXISTS `profile_options`;
CREATE TABLE `profile_options` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `trans_id` int(10) unsigned NOT NULL,
  `internal_use` tinyint(1) NOT NULL default '1',
  `default_value` tinytext,
  `check_regex` varchar(255) default NULL,
  `option_order` int(11) NOT NULL default '100',
  `option_input` varchar(20) NOT NULL default 'text',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='static content' ;
