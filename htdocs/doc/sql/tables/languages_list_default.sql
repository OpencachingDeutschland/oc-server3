SET NAMES 'utf8';
DROP TABLE IF EXISTS `languages_list_default`;
CREATE TABLE `languages_list_default` (
  `lang` varchar(2) NOT NULL,
  `show` varchar(2) NOT NULL,
  PRIMARY KEY  (`lang`,`show`),
  KEY `show` (`show`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='static content' ;
