
/*
delete from cache_type where id = '11';
delete from cache_type where id = '12';
delete from cache_type where id = '13';
delete from cache_type where id = '14';
*/

INSERT INTO `cache_type` (`id`, `name`, `trans_id`, `ordinal`, `short`, `de`, `en`, `icon_large`) VALUES ('11', 'Podcache', '1043', '11', 'Pod', 'Podcast', 'Podcast', 'cache/podcache.png');
INSERT INTO `cache_type` (`id`, `name`, `trans_id`, `ordinal`, `short`, `de`, `en`, `icon_large`) VALUES ('12', 'Educache', '1044', '12', 'Edu', 'Educache', 'Educache', 'cache/edu.png');
INSERT INTO `cache_type` (`id`, `name`, `trans_id`, `ordinal`, `short`, `de`, `en`, `icon_large`) VALUES ('13', 'Challenge', '1045', '13', 'Chlg.', 'Challenge', 'Challenge', 'cache/challenge.png');
INSERT INTO `cache_type` (`id`, `name`, `trans_id`, `ordinal`, `short`, `de`, `en`, `icon_large`) VALUES ('14', 'Guestbook', '1046', '14', 'Guest', 'Guestbook', 'Guestbook', 'cache/guestbook.png');

INSERT INTO `cache_logtype` (`cache_type_id`, `log_type_id`) VALUES ('11', '1');
INSERT INTO `cache_logtype` (`cache_type_id`, `log_type_id`) VALUES ('11', '2');
INSERT INTO `cache_logtype` (`cache_type_id`, `log_type_id`) VALUES ('11', '3');
INSERT INTO `cache_logtype` (`cache_type_id`, `log_type_id`) VALUES ('12', '1');
INSERT INTO `cache_logtype` (`cache_type_id`, `log_type_id`) VALUES ('12', '2');
INSERT INTO `cache_logtype` (`cache_type_id`, `log_type_id`) VALUES ('12', '3');
INSERT INTO `cache_logtype` (`cache_type_id`, `log_type_id`) VALUES ('13', '1');
INSERT INTO `cache_logtype` (`cache_type_id`, `log_type_id`) VALUES ('13', '2');
INSERT INTO `cache_logtype` (`cache_type_id`, `log_type_id`) VALUES ('13', '3');
INSERT INTO `cache_logtype` (`cache_type_id`, `log_type_id`) VALUES ('14', '1');
INSERT INTO `cache_logtype` (`cache_type_id`, `log_type_id`) VALUES ('14', '2');
INSERT INTO `cache_logtype` (`cache_type_id`, `log_type_id`) VALUES ('14', '3');


INSERT INTO `sys_trans` (`id`, `text`, `last_modified`) VALUES
(1043, 'Podcache', '2011-01-15 22:00:01');
INSERT INTO `sys_trans` (`id`, `text`, `last_modified`) VALUES
(1044, 'Educache', '2011-01-15 22:00:01');
INSERT INTO `sys_trans` (`id`, `text`, `last_modified`) VALUES
(1045, 'Challengecache', '2011-01-15 22:00:01');
INSERT INTO `sys_trans` (`id`, `text`, `last_modified`) VALUES
(1046, 'Guestbookcache', '2011-01-15 22:00:01');

INSERT INTO `sys_trans_ref` (`trans_id`, `resource_name`, `line`) VALUES
(1043, './lang/de/ocstyle/search.tpl.php', 336),
(1043, 'table:cache_type;field=name', 0);
INSERT INTO `sys_trans_ref` (`trans_id`, `resource_name`, `line`) VALUES
(1044, './lang/de/ocstyle/search.tpl.php', 336),
(1044, 'table:cache_type;field=name', 0);
INSERT INTO `sys_trans_ref` (`trans_id`, `resource_name`, `line`) VALUES
(1045, './lang/de/ocstyle/search.tpl.php', 336),
(1045, 'table:cache_type;field=name', 0);
INSERT INTO `sys_trans_ref` (`trans_id`, `resource_name`, `line`) VALUES
(1046, './lang/de/ocstyle/search.tpl.php', 336),
(1046, 'table:cache_type;field=name', 0);

INSERT INTO `sys_trans_text` (`trans_id`, `lang`, `text`, `last_modified`) VALUES
(1043, 'EN', 'Podcast Cache', '2011-01-15 22:00:01');
INSERT INTO `sys_trans_text` (`trans_id`, `lang`, `text`, `last_modified`) VALUES
(1043, 'SV', 'Podcast Cache', '2011-01-15 22:00:01');
INSERT INTO `sys_trans_text` (`trans_id`, `lang`, `text`, `last_modified`) VALUES
(1044, 'EN', 'Educational Cache', '2011-01-15 22:00:01');
INSERT INTO `sys_trans_text` (`trans_id`, `lang`, `text`, `last_modified`) VALUES
(1044, 'SV', 'Utbildningscache', '2011-01-15 22:00:01');
INSERT INTO `sys_trans_text` (`trans_id`, `lang`, `text`, `last_modified`) VALUES
(1045, 'EN', 'Challenge Cache', '2011-01-15 22:00:01');
INSERT INTO `sys_trans_text` (`trans_id`, `lang`, `text`, `last_modified`) VALUES
(1045, 'SV', 'Utmaningscache', '2011-01-15 22:00:01');
INSERT INTO `sys_trans_text` (`trans_id`, `lang`, `text`, `last_modified`) VALUES
(1046, 'EN', 'Guest Book Cache', '2011-01-15 22:00:01');
INSERT INTO `sys_trans_text` (`trans_id`, `lang`, `text`, `last_modified`) VALUES
(1046, 'SV', 'Gästbokscache', '2011-01-15 22:00:01');
