-- Table logentries_types
SET NAMES 'utf8';
TRUNCATE TABLE `logentries_types`;
INSERT INTO `logentries_types` (`id`, `module`, `eventname`) VALUES ('1', 'watchlist', 'owner_notify');
INSERT INTO `logentries_types` (`id`, `module`, `eventname`) VALUES ('2', 'watchlist', 'sendmail');
INSERT INTO `logentries_types` (`id`, `module`, `eventname`) VALUES ('3', 'remindemail', 'sendmail');
INSERT INTO `logentries_types` (`id`, `module`, `eventname`) VALUES ('4', 'approving', 'deletecache');
INSERT INTO `logentries_types` (`id`, `module`, `eventname`) VALUES ('5', 'cache', 'changeowner');
INSERT INTO `logentries_types` (`id`, `module`, `eventname`) VALUES ('6', 'user', 'disable');
INSERT INTO `logentries_types` (`id`, `module`, `eventname`) VALUES ('7', 'user', 'delete');
INSERT INTO `logentries_types` (`id`, `module`, `eventname`) VALUES ('8', 'notification', 'sendmail');
