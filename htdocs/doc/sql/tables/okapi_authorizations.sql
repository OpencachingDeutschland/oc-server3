SET NAMES 'utf8';
DROP TABLE IF EXISTS `okapi_authorizations`;
CREATE TABLE `okapi_authorizations` (
  `consumer_key` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `user_id` int(11) NOT NULL,
  `last_access_token` datetime DEFAULT NULL,
  PRIMARY KEY (`consumer_key`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
