SET NAMES 'utf8';
DROP TABLE IF EXISTS `search_ignore`;
CREATE TABLE `search_ignore` (
  `word` varchar(30) NOT NULL,
  PRIMARY KEY  (`word`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='static content' ;
