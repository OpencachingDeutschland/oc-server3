-- Table replication_overwritetypes
SET NAMES 'utf8';
TRUNCATE TABLE `replication_overwritetypes`;
INSERT INTO `replication_overwritetypes` (`id`, `table`, `field`, `uuid_fieldname`, `backupfirst`) VALUES
('1', 'user', 'password', 'uuid', '0'),
('2', 'user', 'email', 'uuid', '0'),
('3', 'user', 'is_active_flag', 'uuid', '0'),
('4', 'user', 'permanent_login_flag', 'uuid', '0'),
('5', 'user', 'admin', 'uuid', '0');
