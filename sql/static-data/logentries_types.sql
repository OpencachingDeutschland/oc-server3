-- Table logentries_types
SET NAMES 'utf8';
TRUNCATE TABLE `logentries_types`;
INSERT INTO `logentries_types` (`id`, `module`, `eventname`) VALUES
('1', 'watchlist', 'owner_notify'),
('2', 'watchlist', 'sendmail'),
('3', 'remindemail', 'sendmail'),
('4', 'approving', 'deletecache'),
('5', 'cache', 'changeowner'),
('6', 'user', 'disable'),
('7', 'user', 'delete'),
('8', 'notification', 'sendmail');
