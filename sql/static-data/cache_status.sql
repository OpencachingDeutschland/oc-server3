-- Table cache_status
SET NAMES 'utf8';
TRUNCATE TABLE `cache_status`;
INSERT INTO `cache_status` (`id`, `name`, `trans_id`, `de`, `en`, `allow_user_view`, `allow_owner_edit_status`, `allow_user_log`) VALUES ('1', 'Available', '531', 'Kann gesucht werden', 'Available', '1', '1', '1');
INSERT INTO `cache_status` (`id`, `name`, `trans_id`, `de`, `en`, `allow_user_view`, `allow_owner_edit_status`, `allow_user_log`) VALUES ('2', 'Temporarily not available', '532', 'Momentan nicht verfügbar', 'Temporarily not available', '1', '1', '1');
INSERT INTO `cache_status` (`id`, `name`, `trans_id`, `de`, `en`, `allow_user_view`, `allow_owner_edit_status`, `allow_user_log`) VALUES ('3', 'Archived', '496', 'Archiviert', 'Archived', '1', '1', '1');
INSERT INTO `cache_status` (`id`, `name`, `trans_id`, `de`, `en`, `allow_user_view`, `allow_owner_edit_status`, `allow_user_log`) VALUES ('4', 'Hidden by approvers to check', '533', 'Von den Approvern entfernt, um geürpft zu werden', 'Hidden by approvers to check', '0', '1', '0');
INSERT INTO `cache_status` (`id`, `name`, `trans_id`, `de`, `en`, `allow_user_view`, `allow_owner_edit_status`, `allow_user_log`) VALUES ('5', 'Not yet published', '534', 'Noch nicht veröffentlicht', 'Not yet published', '0', '1', '0');
INSERT INTO `cache_status` (`id`, `name`, `trans_id`, `de`, `en`, `allow_user_view`, `allow_owner_edit_status`, `allow_user_log`) VALUES ('6', 'Locked, visible', '821', '', '', '1', '1', '0');
INSERT INTO `cache_status` (`id`, `name`, `trans_id`, `de`, `en`, `allow_user_view`, `allow_owner_edit_status`, `allow_user_log`) VALUES ('7', 'Locked, invisible', '822', '', '', '0', '0', '0');
