/* Please note that triggers must be installed before running this script */
/* Please note that this script drops some triggers to they MUST be reinstalled after running this script */
/* Please check that news.date_created has not been destroyed (insufficient privileges to drop trigger) */

/* this is to fool most triggers not to update dates */
set @XMLSYNC=1;


/*
delete from news;
delete from pictures;
delete from removed_objects;
*/

/* replace topics with relevant topics for this site */
delete from news_topics;
insert into news_topics ( `id`, `name` )
values ( 1, 'Opencaching' );

DROP TRIGGER newsBeforeInsert;

INSERT INTO `news` (
	`id`,
	`date_created`,
	`content`,
	`topic`,
	`display` )
select
`id`,
`date_posted` as `date_created`,
`content`,
`topic`,
`display`
from ocpl.news;


INSERT INTO `pictures` (
`id`, 
`uuid`, 
`node`, 
`date_created`, 
`last_modified`, 
`url`, 
`title`, 
`last_url_check`, 
`object_id`, 
`object_type`, 
`thumb_url`, 
`thumb_last_generated`, 
`spoiler`, 
`local`, 
`unknown_format`, 
`display`)
select
`id`,
`uuid`,
`node`,
`date_created`,
`last_modified`,
`url`,
`title`,
`last_url_check`,
`object_id`,
`object_type`,
`thumb_url`,
`thumb_last_generated`,
`spoiler`,
`local`,
`unknown_format`,
`display`
-- `description`,
-- `desc_html`,
-- `user_id`,
from ocpl.pictures;

INSERT INTO `removed_objects` (
`id`,
`localID`,
`uuid`,
`type`,
`removed_date`,
`node` )
select
`id`,
`localID`,
`uuid`,
`type`,
`removed_date`,
`node`
from ocpl.removed_objects;

set @XMLSYNC=0;
