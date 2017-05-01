-- Table watches_waitingtypes
SET NAMES 'utf8';
TRUNCATE TABLE `watches_waitingtypes`;
INSERT INTO `watches_waitingtypes` (`id`, `watchtype`) VALUES ('1', 'ownerlog');
INSERT INTO `watches_waitingtypes` (`id`, `watchtype`) VALUES ('2', 'cache_watches');

INSERT INTO `sysconfig` (`name`, `value`) VALUES ('datasql_checksum', '416acec531ee1886f827e8615482e263') ON DUPLICATE KEY UPDATE `value`='416acec531ee1886f827e8615482e263';
