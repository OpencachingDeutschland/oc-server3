SET NAMES 'utf8';
DROP TABLE IF EXISTS `helppages`;
CREATE TABLE `helppages` (
  `ocpage` varchar(60) NOT NULL,
  `language` char(2) NOT NULL,
  `helppage` varchar(120) NOT NULL,
  UNIQUE KEY `ocpage` (`ocpage`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;
