SET NAMES 'utf8';
DROP TABLE IF EXISTS `pw_dict`;
CREATE TABLE `pw_dict` (
  `pw` varchar(40) NOT NULL,
  UNIQUE KEY `pw` (`pw`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='static content' ;
