SET NAMES 'utf8';
DROP TABLE IF EXISTS `listing_restored`;
CREATE TABLE `listing_restored` (
  `cache_id` int(10) NOT NULL,
  `date_modified` date NOT NULL,
  `admin_id` int(10) NOT NULL,
  UNIQUE KEY `cache_id` (`cache_id`,`date_modified`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
