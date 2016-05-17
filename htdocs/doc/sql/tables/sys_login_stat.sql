SET NAMES 'utf8';
DROP TABLE IF EXISTS `sys_login_stat`;
CREATE TABLE `sys_login_stat` (
  `day` date NOT NULL,
  `type` char(10) NOT NULL,
  `count` int(11) NOT NULL,
  UNIQUE KEY `day` (`day`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;
