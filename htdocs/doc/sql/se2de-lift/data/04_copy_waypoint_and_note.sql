/* this is to fool most triggers not to update dates */
set @XMLSYNC=1;


/*
delete from `coordinates` where `type`=1;
delete from `coordinates` where `type`=2;
*/

/*
old:
`type`,
4	Reference point
5 	Parking

new:
type=1 coordinate
subtype
1	Parking
2	Reference point

6-old_type,

*/

INSERT INTO `coordinates` (
	`type`,
	`subtype`,
	`latitude`,
	`longitude`,
	`cache_id`,
	`user_id`,
	`log_id`,
	`description` )
SELECT
1,
6-`type`,
`latitude`,
`longitude`,
`cache_id`,
null,
null,
`desc`
FROM
ocpl.waypoints;
/*
`status`,
`stage`,
*/

/*
new:
type=2 note
subtype=1
*/

INSERT INTO `coordinates` (
	`type`,
	`subtype`,
	`latitude`,
	`longitude`,
	`cache_id`,
	`user_id`,
	`log_id`,
	`description` )
SELECT
2,
1,
0,
0,
`cache_id`,
`user_id`,
null,
`desc`
FROM
ocpl.cache_notes;
/*
`date`
*/

set @XMLSYNC=0;
