/* this must be the first script for counters to updated properly */
/* Please note that triggers must be installed before running this script */
/* Please note that this script drops some triggers to they MUST be reinstalled after running this script */
/* Please check that logentries.date_created has not been destroyed (insufficient privileges to drop trigger) */

/* this is to fool most triggers not to update dates */
set @XMLSYNC=1;

/*
delete from user;
delete from user_options;
delete from email_user;
delete from logentries;
*/

/* clear statistics counters, these will be updated by the triggers */
delete from stat_caches;
delete from stat_cache_logs;
delete from stat_user;


INSERT INTO `user` (
`user_id`, 
`uuid`, 
`node`, 
`date_created`, 
`last_modified`, 
`last_login`, 
`username`, 
`password`, 
`email`, 
`latitude`, 
`longitude`, 
`is_active_flag`, 
`last_name`, 
`first_name`, 
`country`, 
`pmr_flag`, 
`new_pw_code`, 
`new_pw_date`, 
`new_email_code`, 
`new_email_date`, 
`new_email`, 
`permanent_login_flag`, 
`watchmail_mode`, 
`watchmail_hour`, 
`watchmail_nextmail`, 
`watchmail_day`, 
`activation_code`, 
`statpic_logo`, 
`statpic_text`, 
`no_htmledit_flag`, 
`notify_radius`, 
`admin`)
select
`user_id`, 
`uuid`, 
`node`, 
`date_created`, 
`last_modified`, 
`last_login`, 
`username`, 
`password`, 
`email`, 
`latitude`, 
`longitude`, 
`is_active_flag`, 
NULL, -- `last_name`, 
NULL, -- `first_name`, 
`country`, 
`pmr_flag`, 
`new_pw_code`, 
`new_pw_date`, 
`new_email_code`, 
`new_email_date`, 
`new_email`, 
`permanent_login_flag`, 
if(`watchmail_mode`=1, 0, if(`watchmail_mode`=0, 1, `watchmail_mode`)),
`watchmail_hour`, 
`watchmail_nextmail`, 
`watchmail_day`, 
`activation_code`, 
`statpic_logo`, 
`statpic_text`, 
`no_htmledit_flag`, 
`notify_radius`, 
`admin`
--  `login_faults`
--  `login_id`
--  `was_loggedin`
--  `post_news`
--  `hidden_count`
--  `log_notes_count`
--  `founds_count`
--  `notfounds_count`
--  `cache_watches`
--  `cache_ignores`
--  `stat_ban`
--  `description`
--  `rules_confirmed`
--  `get_bulletin`
--  `ozi_filips`
--  `hide_flag`
from
ocpl.user
where user_id>=0;

INSERT INTO `user_options` (
`user_id`, 
`option_id`, 
`option_visible`, 
`option_value` )
select
`user_id`, 
1,
0,
'11'
from
ocpl.user
where user_id>=0;

INSERT INTO `user_options` (
`user_id`, 
`option_id`, 
`option_visible`, 
`option_value` )
select
`user_id`, 
2,
0,
''
from
ocpl.user
where user_id>=0;

INSERT INTO `user_options` (
`user_id`, 
`option_id`, 
`option_visible`, 
`option_value` )
select
`user_id`, 
3,
1,
`description`
from
ocpl.user
where user_id>=0;

INSERT INTO `user_options` (
`user_id`, 
`option_id`, 
`option_visible`, 
`option_value` )
select
`user_id`, 
4,
0,
''
from
ocpl.user
where user_id>=0;

INSERT INTO `user_options` (
`user_id`, 
`option_id`, 
`option_visible`, 
`option_value` )
select
`user_id`, 
5,
0,
'1'
from
ocpl.user
where user_id>=0;

/* This trigger destroys date so drop it. */

DROP TRIGGER emailUserBeforeInsert;

INSERT INTO `email_user` (
	`id`,
	`date_created`,
	`ipaddress`,
	`from_user_id`,
	`from_email`,
	`to_user_id`,
	`to_email` )
select
`id`,
`date_generated` as `date_created`,
`ipaddress`,
`from_user_id`,
`from_email`,
`to_user_id`,
`to_email`
-- `mail_subject`,
-- `mail_text`,
-- `send_emailaddress`,
-- `date_sent`
from ocpl.email_user;

/* This trigger destroys date so drop it. */

DROP TRIGGER `logentriesBeforeInsert`;

INSERT INTO `logentries` (
	`id`,
	`date_created`,
	`module`,
	`eventid`,
	`userid`,
	`objectid1`,
	`objectid2`,
	`logtext`,
	`details` )
select
`id`,
`logtime` as `date_created`,
`module`,
`eventid`,
`userid`,
`objectid1`,
`objectid2`,
`logtext`,
`details`
from ocpl.logentries;

set @XMLSYNC=0;
