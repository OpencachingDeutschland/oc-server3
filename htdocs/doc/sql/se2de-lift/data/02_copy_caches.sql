/* Please note that triggers must be installed before running this script */

/* this is to fool triggers not to update dates */
set @XMLSYNC=1;


/*
delete from caches;
delete from caches_attributes;
delete from cache_desc;
delete from cache_visits;
delete from cache_rating;
delete from cache_ignore;
delete from cache_watches;
delete from cache_logs;
*/

DROP TRIGGER cacheAttributesAfterInsert;
DROP TRIGGER cacheAttributesAfterUpdate;
DROP TRIGGER cacheAttributesAfterDelete;

DROP TRIGGER cacheVisitsBeforeInsert;
DROP TRIGGER cacheVisitsBeforeUpdate;
DROP TRIGGER cacheLogsAfterInsert;

DROP PROCEDURE sp_notify_new_cache;

CREATE PROCEDURE sp_notify_new_cache (IN nCacheId INT(10) UNSIGNED, IN nLongitude DOUBLE, IN nLatitude DOUBLE)
BEGIN
END;

/* map
pl-type de-type description
1       1       Other
2       2       Trad
3       3       Multi
4       4       Virt
5       5       Webcam
6       6       Event
7       7       Quiz
        8       Math/Physics-Cache
8       9       Moving
        10      Drive-In
9       11      Podcast
10      12      Educache
11      13      Challenge
12      14      Guestbook
*/

create table type_conversion (
  pltype tinyint(3) unsigned NOT NULL,
  detype tinyint(3) unsigned NOT NULL );

insert into type_conversion ( pltype, detype ) values ( 1, 1 );
insert into type_conversion ( pltype, detype ) values ( 2, 2 );
insert into type_conversion ( pltype, detype ) values ( 3, 3 );
insert into type_conversion ( pltype, detype ) values ( 4, 4 );
insert into type_conversion ( pltype, detype ) values ( 5, 5 );
insert into type_conversion ( pltype, detype ) values ( 6, 6 );
insert into type_conversion ( pltype, detype ) values ( 7, 7 );
insert into type_conversion ( pltype, detype ) values ( 8, 9 );
insert into type_conversion ( pltype, detype ) values ( 9, 11 );
insert into type_conversion ( pltype, detype ) values ( 10, 12 );
insert into type_conversion ( pltype, detype ) values ( 11, 13 );
insert into type_conversion ( pltype, detype ) values ( 12, 14 );

/*
(select detype from type_conversion where pltype = `type`) as `type`
*/

INSERT INTO `caches` (
  `cache_id`,
  `uuid`,
  `node`,
  `date_created`,
  `last_modified`,
  `user_id`,
  `name`,
  `longitude`,
  `latitude`,
  `type`,
  `status`,
  `country`,
  `date_hidden`,
  `size`,
  `difficulty`,
  `terrain`,
  `logpw`,
  `search_time`,
  `way_length`,
  `wp_gc`,
  `wp_nc`,
  `wp_oc`,
  `desc_languages`,
  `default_desclang`,
  `date_activate`,
  `need_npa_recalc` )
 select
  `cache_id`,
  `uuid`,
  `node`,
  `date_created`,
  `last_modified`,
  `user_id`,
  `name`,
  `longitude`,
  `latitude`,
  (select detype from type_conversion where pltype = `type`) as `type`,
  `status`,
  `country`,
  `date_hidden`,
  `size`,
  `difficulty`,
  `terrain`,
  `logpw`,
  `search_time`,
  `way_length`,
  `wp_gc`,
  `wp_nc`,
  `wp_oc`,
  `desc_languages`,
  `default_desclang`,
  `date_activate`,
  `need_npa_recalc`
--  `founds`
--  `notfounds`
--  `notes`
--  `images`
--  `last_found`
--  `watcher`
--  `picturescount`
--  `topratings`
--  `ignorer_count`
--  `votes`
--  `score`
--  `mp3count`
--  `solution`
--  `solved`
--  `not_solved`
  from ocpl.caches;

DROP TABLE IF EXISTS type_conversion;

insert into `cache_desc` (
  `id`,
  `uuid`,
  `node`,
  `date_created`,
  `last_modified`,
  `cache_id`,
  `language`,
  `desc`,
  `desc_html`,
  `desc_htmledit`,
  `hint`,
  `short_desc` )
 select
  `id`,
  `uuid`,
  `node`,
  `date_created`,
  `last_modified`,
  `cache_id`,
  `language`,
  `desc`,
  `desc_html`,
  `desc_htmledit`,
  `hint`,
  `short_desc`
from ocpl.cache_desc;
  
/* no 60 wheelchair accessible is missing
*/
INSERT INTO `caches_attributes` (
`cache_id`, 
`attrib_id`)
select
`cache_id`, 
if(`attrib_id`=60, 63, `attrib_id`) `attrib_id`
from ocpl.caches_attributes;

INSERT INTO `cache_visits` (
`cache_id`, 
`user_id_ip`, 
`count`, 
`last_modified` )
select
`cache_id`, 
`user_id_ip`, 
`count`, 
last_visited as `last_modified`
from ocpl.cache_visits;

INSERT INTO `cache_rating` (
`cache_id`, 
`user_id` )
select
`cache_id`, 
`user_id`
from ocpl.cache_rating;

INSERT INTO `cache_ignore` (
`cache_id`, 
`user_id` )
select
`cache_id`, 
`user_id`
from ocpl.cache_ignore;

INSERT INTO `cache_watches` (
`cache_id`, 
`user_id`,
`last_executed` )
select
`cache_id`, 
`user_id`,
`last_executed`
from ocpl.cache_watches;

/*
automatic archived logs does not have a valid uuid
if(`uuid`=-1,upper(uuid()),`uuid`),
*/
INSERT INTO `cache_logs` (
`id`,
`uuid`,
`node`,
`date_created`,
`last_modified`,
`cache_id`,
`user_id`,
`type`,
`date`,
`text`,
`text_html`,
`text_htmledit`,
`owner_notified`,
`picture`) 
select
`id`,
if(`uuid`=-1,upper(uuid()),`uuid`),
`node`,
`date_created`,
`last_modified`,
`cache_id`,
`user_id`,
`type`,
`date`,
`text`,
`text_html`,
`text_htmledit`,
`owner_notified`,
`picturescount` as `picture`
-- `mp3count`
from ocpl.cache_logs
where `deleted` = 0 and `hidden` = 0;

update cache_logs
set text = replace(text, 'lib/tinymce/plugins/emotions/images/', 'resource2/tinymce/plugins/emotions/img/')

update cache_logs
set text = replace(text, 'lib/tinymce/plugins/emotions/img/', 'resource2/tinymce/plugins/emotions/img/')

INSERT INTO `cache_logs_archived` (
`id`,
`uuid`,
`node`,
`date_created`,
`last_modified`,
`cache_id`,
`user_id`,
`type`,
`date`,
`text`,
`text_html`,
`text_htmledit`,
`owner_notified`,
`picture`) 
select
`id`,
if(`uuid`=-1,upper(uuid()),`uuid`),
`node`,
`date_created`,
`last_modified`,
`cache_id`,
`user_id`,
`type`,
`date`,
`text`,
`text_html`,
`text_htmledit`,
`owner_notified`,
`picturescount` as `picture`
-- `mp3count`
from ocpl.cache_logs
where `deleted` = 1 or `hidden` = 1;

set @XMLSYNC=0;
