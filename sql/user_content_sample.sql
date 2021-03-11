UPDATE `user` SET
 `last_modified` = '2020-12-16 21:00:10',
 `last_login` = '2021-02-19',
 `domain` = 'docker.team-opencaching.de',
 `description` = '<p>\n</p><hr style=\"height:5px;width:100%;\" size=\"5\" /><p><span style=\"font-size:medium;\">You can find information and login data for other users here:</span></p>\n<p><span style=\"font-size:medium;\">..tbd..</span></p>\n<p><span style=\"font-size:medium;\"><br /></span></p>\n<p>\n</p><hr style=\"height:5px;width:100%;\" size=\"5\" />'
WHERE `user_id` = 107469
;
INSERT INTO `user` (`user_id`, `uuid`, `node`, `date_created`, `last_modified`, `last_login`, `username`, `password`, `admin_password`, `roles`, `email`, `email_problems`, `first_email_problem`, `last_email_problem`, `mailing_problems`, `accept_mailing`, `usermail_send_addr`, `latitude`, `longitude`, `is_active_flag`, `last_name`, `first_name`, `country`, `pmr_flag`, `new_pw_code`, `new_pw_date`, `new_email_code`, `new_email_date`, `new_email`, `permanent_login_flag`, `watchmail_mode`, `watchmail_hour`, `watchmail_nextmail`, `watchmail_day`, `activation_code`, `statpic_logo`, `statpic_text`, `no_htmledit_flag`, `notify_radius`, `notify_oconly`, `language`, `language_guessed`, `domain`, `admin`, `data_license`, `description`, `desc_htmledit`)
VALUES 
 (170288,'1405189a-3f86-11eb-96df-0242ac120002',5,'2020-12-16 19:00:48','2020-12-16 19:00:48','2020-12-16','SupportUser','5f4dcc3b5aa765d61d8327deb882cf99',NULL,NULL,'support@example.com',0,NULL,NULL,0,1,0,0,0,1,'Sauer','Susi','DE',0,NULL,NULL,NULL,NULL,NULL,1,1,0,'2020-12-16 20:00:48',0,'',0,'Opencaching',0,0,1,'DE',0,'docker.team-opencaching.de',0,2,'',1),
 (170289,'39d92454-3f8a-11eb-96df-0242ac120002',5,'2020-12-16 19:30:30','2020-12-16 20:02:45','2020-12-19','Gustav_0815','5f4dcc3b5aa765d61d8327deb882cf99',NULL,NULL,'Gustav_0815@example.com',0,NULL,NULL,0,1,0,51.16055,10.4425,1,'Gans','Gustav','DE',0,NULL,NULL,NULL,NULL,NULL,1,1,0,'2020-12-16 20:30:30',0,'',0,'Opencaching',0,150,0,'EN',0,'docker.team-opencaching.de',0,2,'<p><span style=\"font-size:large;\">Hallo, ich bin der Gustav.</span></p>',1),
 (170290,'817091ed-3f8a-11eb-96df-0242ac120002',5,'2020-12-16 19:32:30','2020-12-16 19:32:30','2020-12-18','Müßiggang','5f4dcc3b5aa765d61d8327deb882cf99',NULL,NULL,'Muessiggang@example.com',0,NULL,NULL,0,1,0,47.8,13.033333333333,1,'Antonius','Anton','AT',0,NULL,NULL,NULL,NULL,NULL,1,1,0,'2020-12-16 20:32:30',0,'',0,'Opencaching',0,0,1,'EN',0,'docker.team-opencaching.de',0,2,'',1),
 (170291,'a945320e-3f8a-11eb-96df-0242ac120002',5,'2020-12-16 19:33:37','2020-12-16 19:33:37','2020-12-18','cacheteam Brügge','5f4dcc3b5aa765d61d8327deb882cf99',NULL,NULL,'cacheteam_Bruegge@example.com',0,NULL,NULL,0,1,0,51.209444,3.22,1,'Blau','Berta','BE',0,NULL,NULL,NULL,NULL,NULL,1,1,0,'2020-12-16 20:33:37',0,'',0,'Opencaching',0,0,1,'EN',0,'docker.team-opencaching.de',0,2,'',1),
 (170292,'38dc9a9c-3f8b-11eb-96df-0242ac120002',5,'2020-12-16 19:37:38','2020-12-16 19:37:38','2020-12-18','king cach0r &&&///###','5f4dcc3b5aa765d61d8327deb882cf99',NULL,NULL,'king_cach0r!$%&/=?@example.com',0,NULL,NULL,0,1,0,30.663611,104.066667,1,'Claus','Cäsar','CN',0,NULL,NULL,NULL,NULL,NULL,1,1,0,'2020-12-16 20:37:38',0,'',0,'Opencaching',0,0,1,'EN',0,'docker.team-opencaching.de',0,2,'',1),
 (170293,'5edc7f41-3f8b-11eb-96df-0242ac120002',5,'2020-12-16 19:38:41','2020-12-16 20:22:19','2020-12-18','lol','5f4dcc3b5aa765d61d8327deb882cf99',NULL,NULL,'user05@example.com',0,NULL,NULL,0,1,0,51.22555,6.7827833333333,1,'Dörfler','Dora','DE',1,NULL,NULL,NULL,NULL,NULL,1,1,0,'2020-12-16 20:38:41',0,'',0,'Opencaching',0,0,1,'EN',0,'docker.team-opencaching.de',0,2,'',1),
 (170294,'c6e9746f-3f8b-11eb-96df-0242ac120002',5,'2020-12-16 19:41:36','2020-12-16 20:34:34','2020-12-18','Zar Peter','5f4dcc3b5aa765d61d8327deb882cf99',NULL,NULL,'PeterDerGrosse@example.com',0,NULL,NULL,0,1,0,55.75,37.616666666667,1,'Der Große','Peter','RU',0,NULL,NULL,NULL,NULL,NULL,1,1,0,'2020-12-16 20:41:36',0,'',0,'Opencaching',0,10,0,'EN',0,'docker.team-opencaching.de',0,2,'<p><span style=\"font-size:medium;\"><a title=\"Zar Peter\" rel=\"noreferrer noopener\" href=\"https://ru.wikipedia.org/wiki/%D0%9F%D1%91%D1%82%D1%80_I\" target=\"_blank\"><strong>Пётр I Алексе́евич</strong></a>, прозванный Вели́ким (30 мая [9 июня] 1672 года — 28 января [8 февраля] 1725 года) — последний царь всея Руси (с 1682 года) и первый Император Всероссийский (с 1721 года).</span></p>\n<p><span style=\"font-size:medium;\"><img style=\"vertical-align:middle;border:1px solid #000000;margin-top:10px;margin-bottom:10px;margin-left:20px;margin-right:20px;\" src=\"https://upload.wikimedia.org/wikipedia/commons/thumb/7/72/Peter_der-Grosse_1838.jpg/274px-Peter_der-Grosse_1838.jpg\" alt=\"Pjotr\" width=\"274\" height=\"368\" /></span></p>\n<p><span style=\"font-size:medium;\"><span style=\"font-size:xx-large;\"><span style=\"font-size:x-small;\">(Quelle: ru.wikipedia.org)</span></span></span></p>',1),
 (170295,'01bfece2-3f8c-11eb-96df-0242ac120002',5,'2020-12-16 19:43:15','2020-12-16 20:33:33','2020-12-18','Beat Takeshi','5f4dcc3b5aa765d61d8327deb882cf99',NULL,NULL,'takeshi@example.com',0,NULL,NULL,0,1,0,44.302616666667,142.63515,1,'Takeshi','Kitano','JP',0,NULL,NULL,NULL,NULL,NULL,1,1,0,'2020-12-16 20:43:15',0,'',0,'Opencaching',0,0,1,'EN',0,'docker.team-opencaching.de',0,2,'<p><span style=\"font-size:xx-large;\">蛙の子は蛙。</span></p>\n<p> </p>\n<p><span style=\"font-size:xx-large;\"><strong>Takeshi Kitano</strong> (<a title=\"Japanische Schrift\" href=\"https://de.wikipedia.org/wiki/Japanische_Schrift\">jap.</a> <span class=\"Hani\" lang=\"ja-hani\" xml:lang=\"ja-hani\">北野 武</span> <em>Kitano Takeshi</em>; * <a title=\"18. Januar\" href=\"https://de.wikipedia.org/wiki/18._Januar\">18. Januar</a> <a title=\"1947\" href=\"https://de.wikipedia.org/wiki/1947\">1947</a> in <a title=\"Adachi\" href=\"https://de.wikipedia.org/wiki/Adachi\">Adachi</a>, <a title=\"Tokio\" href=\"https://de.wikipedia.org/wiki/Tokio\">Tokio</a>) ist ein <a title=\"Japan\" href=\"https://de.wikipedia.org/wiki/Japan\">japanischer</a> <a title=\"Regisseur\" href=\"https://de.wikipedia.org/wiki/Regisseur\">Regisseur</a>, <a title=\"Schauspieler\" href=\"https://de.wikipedia.org/wiki/Schauspieler\">Schauspieler</a>, Dichter, Autor, TV- und Radiomoderator, Maler und populärer Comedian. In Deutschland ist er vorrangig dank der Filme <a class=\"mw-redirect\" title=\"Hana-Bi\" href=\"https://de.wikipedia.org/wiki/Hana-Bi\">Hana-Bi</a>, <a title=\"Battle Royale\" href=\"https://de.wikipedia.org/wiki/Battle_Royale\">Battle Royale</a>, <a title=\"Zatoichi – Der blinde Samurai\" href=\"https://de.wikipedia.org/wiki/Zatoichi_%E2%80%93_Der_blinde_Samurai\">Zatoichi – Der blinde Samurai</a>, <a title=\"Kikujiros Sommer\" href=\"https://de.wikipedia.org/wiki/Kikujiros_Sommer\">Kikujiros Sommer</a>, aber auch der Gameshow <a title=\"Takeshi’s Castle\" href=\"https://de.wikipedia.org/wiki/Takeshi%E2%80%99s_Castle\">Takeshi’s Castle</a> bekannt geworden. Seit April 2005 ist er außerdem Dozent an der <a class=\"mw-redirect\" title=\"Tokyo National University of Fine Arts and Music\" href=\"https://de.wikipedia.org/wiki/Tokyo_National_University_of_Fine_Arts_and_Music\">Tokyo National University of Fine Arts and Music</a>. In Japan ist er auch unter dem Pseudonym <em>Beat Takeshi</em> bekannt.</span></p>\n<p><span style=\"font-size:xx-large;\"><img src=\"https://upload.wikimedia.org/wikipedia/commons/thumb/f/f5/TakesiKitano.jpg/170px-TakesiKitano.jpg\" alt=\"\" /></span></p>\n<p><span style=\"font-size:xx-large;\"><span style=\"font-size:x-small;\">(Quelle: de.wikipedia.org)</span><br /></span></p>',1),
 (170296,'4c916985-3f8c-11eb-96df-0242ac120002',5,'2020-12-16 19:45:20','2020-12-16 19:45:20','2020-12-18','bruker','5f4dcc3b5aa765d61d8327deb882cf99',NULL,NULL,'bruker@example.com',0,NULL,NULL,0,1,0,68.327466666667,16.77805,1,'Lyngstad','Anni-Frid','NO',0,NULL,NULL,NULL,NULL,NULL,1,1,0,'2020-12-16 20:45:20',0,'',0,'Opencaching',0,0,0,'EN',0,'docker.team-opencaching.de',0,2,'',1),
 (170297,'53ded00b-3f8d-11eb-96df-0242ac120002',5,'2020-12-16 19:52:42','2020-12-16 19:52:42',NULL,'Nichtaktivierter Nutzer','5f4dcc3b5aa765d61d8327deb882cf99',NULL,NULL,'nichtaktiviert@example.com',0,NULL,NULL,0,1,0,0,0,0,'Pan','Peter','DE',0,NULL,NULL,NULL,NULL,NULL,1,1,0,'2020-12-16 20:52:42',0,'27E0972C60D31',0,'Opencaching',0,0,1,NULL,0,NULL,0,2,'',1),
 (170298,'865f5a39-3fd6-11eb-96df-0242ac120002',5,'2020-12-17 13:23:34','2020-12-17 13:23:34','2020-12-18','Gesperrter Nutzer',NULL,NULL,NULL,NULL,0,NULL,NULL,0,1,0,0,0,0,'','',NULL,0,NULL,NULL,NULL,NULL,NULL,1,1,0,'2020-12-17 14:23:34',0,'',0,'Opencaching',0,0,1,'EN',0,'docker.team-opencaching.de',0,2,'',1),
 (170299,'8c992f35-4052-11eb-96df-0242ac120002',5,'2020-12-18 11:19:42','2020-12-18 11:19:42','2020-12-18','DSGVO gelöschter Nutzer','5f4dcc3b5aa765d61d8327deb882cf99',NULL,NULL,'noDSGVO@example.com',0,NULL,NULL,0,1,0,0,0,1,'','',NULL,0,NULL,NULL,NULL,NULL,NULL,1,1,0,'2020-12-18 12:19:42',0,'',0,'Opencaching',0,0,1,'EN',0,'docker.team-opencaching.de',0,2,'',1)
;

UPDATE `stat_user` SET
 `found` = 0,
 `notfound` = 0,
 `note` = 0,
 `hidden` = 0,
 `will_attend` = 0,
 `maintenance` = 0
WHERE `user_id` = 107469
;
INSERT INTO `stat_user` (`user_id`, `found`, `notfound`, `note`, `hidden`, `will_attend`, `maintenance`)
VALUES
 (170289,0,0,0,3,0,0),
 (170290,0,0,0,3,0,2),
 (170291,0,0,1,2,0,0),
 (170292,0,0,0,2,1,0),
 (170293,1,0,0,2,0,0),
 (170294,1,0,0,2,0,0),
 (170295,0,0,0,2,0,0),
 (170296,0,0,0,2,0,0),
 (170298,1,0,0,1,0,0),
 (170299,1,0,0,1,0,0)
;

INSERT INTO `user_options` (`user_id`, `option_id`, `option_visible`, `option_value`)
VALUES
 (170289,1,0,'11'),
 (170289,2,1,'Niederdorla'),
 (170289,3,0,''),
 (170289,4,1,'99'),
 (170289,5,0,'1'),
 (170289,6,0,'1'),
 (170289,7,0,'0'),
 (170289,8,0,'0'),
 (170289,9,0,'1'),
 (170289,10,0,'7'),
 (170289,11,0,'1'),
 (170289,13,0,''),
 (170289,14,0,'1'),
 (170290,1,0,'11'),
 (170290,2,0,'Salzburg'),
 (170290,3,0,''),
 (170290,4,0,'3'),
 (170290,5,0,'1'),
 (170290,6,0,'1'),
 (170290,7,0,'0'),
 (170290,8,0,'0'),
 (170290,9,0,'1'),
 (170290,10,0,'7'),
 (170290,11,0,'1'),
 (170290,13,0,''),
 (170290,14,0,'1'),
 (170291,1,0,'11'),
 (170291,2,0,'Brügge'),
 (170291,3,0,''),
 (170291,4,0,''),
 (170291,5,0,'1'),
 (170291,6,0,'1'),
 (170291,7,0,'0'),
 (170291,8,0,'0'),
 (170291,9,0,'1'),
 (170291,10,0,'7'),
 (170291,11,0,'1'),
 (170291,13,0,''),
 (170291,14,0,'1'),
 (170292,1,0,'11'),
 (170292,2,1,'Chengdu'),
 (170292,3,0,''),
 (170292,4,1,'35'),
 (170292,5,0,'1'),
 (170292,6,0,'1'),
 (170292,7,0,'0'),
 (170292,8,0,'0'),
 (170292,9,0,'1'),
 (170292,10,0,'7'),
 (170292,11,0,'1'),
 (170292,13,0,''),
 (170292,14,0,'1'),
 (170293,1,0,'11'),
 (170293,2,1,'Düsseldorf'),
 (170293,3,0,''),
 (170293,4,1,''),
 (170293,5,0,'1'),
 (170293,6,0,'1'),
 (170293,7,0,'0'),
 (170293,8,0,'0'),
 (170293,9,0,'1'),
 (170293,10,0,'7'),
 (170293,11,0,'1'),
 (170293,13,0,''),
 (170293,14,0,'1'),
 (170294,1,0,'11'),
 (170294,2,1,'Москва́'),
 (170294,3,0,''),
 (170294,4,1,'111'),
 (170294,5,0,'1'),
 (170294,6,0,'1'),
 (170294,7,0,'0'),
 (170294,8,0,'0'),
 (170294,9,0,'1'),
 (170294,10,0,'7'),
 (170294,11,0,'1'),
 (170294,13,0,''),
 (170294,14,0,'1'),
 (170295,1,0,'11'),
 (170295,2,1,'足立区'),
 (170295,3,0,''),
 (170295,4,1,'73'),
 (170295,5,0,'1'),
 (170295,6,0,'1'),
 (170295,7,0,'0'),
 (170295,8,0,'0'),
 (170295,9,0,'1'),
 (170295,10,0,'7'),
 (170295,11,0,'1'),
 (170295,13,0,''),
 (170295,14,0,'1'),
 (170296,1,0,'11'),
 (170296,2,1,'Bjørkåsen'),
 (170296,3,0,''),
 (170296,4,1,'75'),
 (170296,5,0,'1'),
 (170296,6,0,'1'),
 (170296,7,0,'0'),
 (170296,8,0,'0'),
 (170296,9,0,'1'),
 (170296,10,0,'7'),
 (170296,11,0,'1'),
 (170296,13,0,''),
 (170296,14,0,'1')
;

INSERT INTO `caches` (`cache_id`, `uuid`, `node`, `date_created`, `is_publishdate`, `last_modified`, `okapi_syncbase`, `listing_last_modified`, `meta_last_modified`, `user_id`, `name`, `longitude`, `latitude`, `type`, `status`, `country`, `date_hidden`, `size`, `difficulty`, `terrain`, `logpw`, `search_time`, `way_length`, `wp_gc`, `wp_gc_maintained`, `wp_nc`, `wp_oc`, `desc_languages`, `default_desclang`, `date_activate`, `need_npa_recalc`, `show_cachelists`, `protect_old_coords`, `needs_maintenance`, `listing_outdated`, `flags_last_modified`)
VALUES
 (1,'8d2d2b91-3fd8-11eb-96df-0242ac120002',4,'2020-12-17 13:38:05',1,'2020-12-18 13:26:31','2020-12-18 13:26:31','2020-12-18 13:26:31','0000-00-00 00:00:00',107469,'WANTED - Der Osterhase',6.9038833333333,51.965583333333,2,1,'DE','2015-03-30',8,2,10,'',0,0,'GC5QCNX','GC5QCNX','','OC1001','DE','DE',NULL,1,1,0,0,0,'0000-00-00 00:00:00'),
 (3,'a242b35c-3fd9-11eb-96df-0242ac120002',4,'2020-12-17 13:46:34',1,'2020-12-17 13:46:34','2020-12-18 11:23:18','2020-12-17 13:46:34','2020-12-18 11:23:18',107469,'Imkerei Taufkirchen (Projekt 12)',11.593333333333,48.046383333333,10,1,'DE','2015-02-14',2,3,9,'',0,0,'','','','OC1002','DE','DE',NULL,1,1,0,0,0,'0000-00-00 00:00:00'),
 (4,'30b28668-3fde-11eb-96df-0242ac120002',4,'2020-12-17 14:18:27',1,'2020-12-18 13:29:20','2020-12-18 13:29:20','2020-12-18 13:29:20','0000-00-00 00:00:00',170289,'Töpferstadt Stadtlohn (ö,ä,ü,ß,Ö,Ä,Ü)',6.9160833333333,51.99065,3,1,'DE','2014-08-08',3,4,8,'',24,25,'GC57KTC','GC57KTC','','OC1003','DE','DE',NULL,1,1,0,0,0,'0000-00-00 00:00:00'),
 (5,'e640f512-3fde-11eb-96df-0242ac120002',4,'2020-12-17 14:23:31',1,'2020-12-17 14:24:01','2020-12-17 14:32:49','2020-12-17 14:32:49','2020-12-17 14:32:49',170289,'\"Perspektivwechsel\"',6.9104,51.988283333333,7,1,'DE','2013-12-04',4,5,7,'',1.51667,0,'GC4V0YC','GC4V0YC','','OC1004','DE','DE',NULL,1,1,0,0,0,'0000-00-00 00:00:00'),
 (6,'81221e56-3fdf-11eb-96df-0242ac120002',4,'2020-12-17 14:27:51',1,'2020-12-17 14:27:51','2020-12-17 14:30:56','2020-12-17 14:30:56','2020-12-17 14:30:56',170289,'Der verschwundene Opencache',13.014133333333,47.648083333333,8,1,'DE','2018-10-02',5,6,6,'',0,0,'','','','OC1005','DE','DE',NULL,1,1,0,0,0,'0000-00-00 00:00:00'),
 (7,'f8cbd174-4039-11eb-96df-0242ac120002',4,'2020-12-18 08:23:47',1,'2020-12-18 08:23:47','2020-12-18 08:23:47','2020-12-18 08:23:47','0000-00-00 00:00:00',170290,'GaswerkAugsburg on Tour',10.865333333333,48.3865,9,1,'DE','2018-06-19',6,7,5,'',0,0,'','','','OC1006','DE','DE',NULL,1,1,0,0,0,'0000-00-00 00:00:00'),
 (8,'87986547-403a-11eb-96df-0242ac120002',4,'2020-12-18 08:27:46',1,'2020-12-18 08:27:46','2020-12-18 08:27:46','2020-12-18 08:27:46','0000-00-00 00:00:00',170290,'#Safari: Freizeitpark Junkies vs. Spass',12.906383333333,54.921533333333,4,1,'DE','2015-07-15',7,8,4,'',0,0,'','','','OC1007','DE','DE',NULL,1,1,0,0,0,'0000-00-00 00:00:00'),
 (9,'2b6ac7f2-403b-11eb-96df-0242ac120002',4,'2020-12-18 08:32:21',1,'2020-12-18 08:55:40','2020-12-18 08:55:40','2020-12-18 08:55:40','2020-12-18 08:47:00',170290,'Seukendorfer Waschstraßenwebcamcache',10.867633333333,49.480583333333,5,3,'DE','2017-04-08',7,9,3,'',0.333333,0,'','','','OC1008','DE,EN','DE',NULL,1,1,0,0,0,'2020-12-18 08:55:40'),
 (10,'121511b6-4040-11eb-96df-0242ac120002',4,'2020-12-18 09:07:26',1,'2020-12-18 13:10:43','2020-12-18 13:10:43','2020-12-18 13:10:43','2020-12-18 10:45:18',170291,'4. Weihnachtsmarktevent beim Kurhaus (GC&OC gemeinsam)',2.9120166666667,51.231766666667,6,1,'BE','2525-06-01',7,10,2,'Kurhaus',3.01667,0,'GC8CFY1','GC8CFY1','','OC1009','EN','EN',NULL,1,1,0,0,0,'0000-00-00 00:00:00'),
 (11,'57ee0b4c-4041-11eb-96df-0242ac120002',4,'2020-12-18 09:16:33',1,'2020-12-18 09:16:33','2020-12-18 09:16:33','2020-12-18 09:16:33','0000-00-00 00:00:00',170291,'WiG - unterm Zaun durch!',6.7019,51.622033333333,1,1,'DE','2012-03-06',3,2,2,'',0,3.45,'GC3MTAC','GC3MTAC','','OC100A','DE','DE',NULL,1,1,0,0,0,'0000-00-00 00:00:00'),
 (12,'3bc93fc3-4042-11eb-96df-0242ac120002',4,'2020-12-18 09:22:55',1,'2020-12-18 09:23:37','2020-12-18 09:23:37','2020-12-18 09:23:37','0000-00-00 00:00:00',170293,'Geocaching Award Kreis Borken 2013 (TB-Hotel)',6.8514166666667,51.930683333333,2,1,'DE','2014-02-08',8,10,10,'',0,10,'GC6AMMK','GC6AMMK','','OC100B','DE','DE',NULL,1,1,0,0,0,'0000-00-00 00:00:00'),
 (13,'1779a814-4043-11eb-96df-0242ac120002',4,'2020-12-18 09:29:03',1,'2020-12-18 09:29:04','2020-12-18 09:29:04','2020-12-18 09:29:04','0000-00-00 00:00:00',170293,'Shrike',28.071833333333,-26.536933333333,10,1,'ZA','2017-08-23',2,10,2,'',0,0,'','','','OC100C','EN','EN',NULL,1,1,0,0,0,'0000-00-00 00:00:00'),
 (14,'5f1fcb67-4045-11eb-96df-0242ac120002',4,'2020-12-18 09:45:23',1,'2020-12-18 09:45:25','2020-12-18 09:45:25','2020-12-18 09:45:25','0000-00-00 00:00:00',170294,'Trubel am Alaskaer Weihnachtshimmel (NC)',-168.08986666667,65.612866666667,3,1,'US','2016-12-14',4,9,3,'ALASKA',0,0,'','','','OC100D','EN','EN',NULL,1,1,0,0,0,'0000-00-00 00:00:00'),
 (15,'63a7590f-4047-11eb-96df-0242ac120002',4,'2020-12-18 09:59:49',1,'2020-12-18 10:05:24','2020-12-18 10:05:24','2020-12-18 10:05:24','2020-12-18 10:00:51',170294,'Voerde wo bin ich - 04 Sternzeichen/ Ð³Ð´Ðµ Ñ - Ð·Ð½Ð°Ðº Ð·Ð¾Ð´Ð¸Ð°ÐºÐ° 04',33.572216666667,59.35555,7,1,'RU','2012-10-24',5,8,4,'',0,0.2,'','','','OC100E','RU','RU',NULL,1,1,0,0,0,'0000-00-00 00:00:00'),
 (16,'3c7e5c12-4049-11eb-96df-0242ac120002',4,'2020-12-18 10:13:03',1,'2020-12-18 10:13:03','2020-12-18 10:13:03','2020-12-18 10:13:03','0000-00-00 00:00:00',170295,'Den Sternen so nah...',9.527,54.838733333333,8,1,'DE','2018-11-13',6,7,5,'',999.983,0,'GC71NED','GC71NED','','OC100F','DE','DE',NULL,1,1,0,0,0,'0000-00-00 00:00:00'),
 (17,'bf785581-4049-11eb-96df-0242ac120002',4,'2020-12-18 10:16:42',1,'2020-12-18 10:20:00','2020-12-18 10:20:00','2020-12-18 10:20:00','0000-00-00 00:00:00',170295,'Die Wanderdose',11.628583333333,48.213733333333,9,1,'JP','2016-05-03',3,6,6,'',0,0,'','','','OC1010','DE','DE',NULL,1,1,1,0,0,'0000-00-00 00:00:00'),
 (18,'ab3e4b6c-404a-11eb-96df-0242ac120002',4,'2020-12-18 10:23:18',1,'2020-12-18 10:24:35','2020-12-18 10:24:35','2020-12-18 10:24:35','0000-00-00 00:00:00',170296,'Aus Tradition - Gestern noch ein König!',6.7346166666667,51.476033333333,4,1,'DE','2013-08-04',7,5,7,'KÖNIG',0,0,'','','','OC1011','DE','DE',NULL,1,1,0,0,0,'0000-00-00 00:00:00'),
 (19,'ae8b629f-404b-11eb-96df-0242ac120002',4,'2020-12-18 10:30:33',1,'2020-12-18 10:32:46','2020-12-18 10:33:26','2020-12-18 10:33:26','2020-12-18 10:33:26',170296,'Vorsicht Kamera!',9.5247666666667,54.8373,5,1,'DE','2019-07-31',7,4,8,'',0,0,'','','','OC1012','DE','DE',NULL,1,1,0,0,0,'0000-00-00 00:00:00'),
 (20,'fcff3d23-404c-11eb-96df-0242ac120002',4,'2020-12-18 10:39:54',1,'2020-12-18 10:39:54','2020-12-18 10:39:54','2020-12-18 10:39:54','0000-00-00 00:00:00',170292,'1. Trierer OC-Stammtisch',6.63895,49.754683333333,6,1,'DE','2025-12-09',1,3,9,'',1.5,0,'','','','OC1013','DE','DE',NULL,1,1,0,0,0,'0000-00-00 00:00:00'),
 (21,'adf5497a-404d-11eb-96df-0242ac120002',4,'2020-12-18 10:44:51',1,'2020-12-18 10:44:51','2020-12-18 10:44:51','2020-12-18 10:44:51','0000-00-00 00:00:00',170292,'Der Laputische Gruß',-77,38.8976,1,1,'US','2005-12-02',8,2,10,'',0,0,'','','','OC1014','EN','EN',NULL,1,1,0,0,0,'0000-00-00 00:00:00'),
 (22,'5dd76437-4051-11eb-96df-0242ac120002',4,'2020-12-18 11:11:15',1,'2020-12-18 11:15:32','2020-12-18 11:15:32','2020-12-18 11:15:32','2020-12-18 11:15:32',170298,'A cache by Gesperrter Nutzer',11.185183333333,44.740733333333,2,6,'DE','2020-12-18',5,3,4,'',0,0,'','','','OC1015','DE','DE',NULL,1,1,0,0,0,'0000-00-00 00:00:00'),
 (23,'ee616df5-4052-11eb-96df-0242ac120002',4,'2020-12-18 11:22:26',1,'2021-01-08 09:25:58','2021-01-08 09:25:58','2021-01-08 09:25:58','0000-00-00 00:00:00',170299,'\"Mein Cache\" by DSGVO gelöschter Nutzer (Test OC1001)',11.185183333333,55.925916666667,2,1,'DE','2020-12-18',3,5,7,'',0,0,'','','','OC1016','DE','DE',NULL,1,1,0,0,0,'0000-00-00 00:00:00'),
 (24,'e6ddf99e-63c3-11eb-9f28-0242ac120003',4,'2021-02-01 13:50:43',0,'2021-02-19 07:37:13','2021-02-19 07:37:13','2021-02-19 07:37:13','0000-00-00 00:00:00',107469,'my new, yet unpublished, cache',1.1870333333333,1.3685166666667,2,5,'DE','2021-02-01',2,3,4,'',0,0,'','','','OC1017','EN','EN',NULL,1,1,0,0,0,'0000-00-00 00:00:00')
;


INSERT INTO `cache_adoption` (`cache_id`, `user_id`, `date_created`)
VALUES
 (17,170294,'2020-12-18 10:18:35')
;

-- INSERT INTO `cache_coordinates` (`id`, `date_created`, `cache_id`, `longitude`, `latitude`, `restored_by`)
-- VALUES
--  (1,'2020-12-17 13:38:05',1,6.9038833333333,51.965583333333,0),
--  (2,'2020-12-17 13:45:50',3,11.593333333333,48.046383333333,0),
--  (3,'2020-12-17 14:18:27',4,6.9160833333333,51.99065,0),
--  (4,'2020-12-17 14:23:31',5,6.9104,51.988283333333,0),
--  (5,'2020-12-17 14:27:51',6,13.014133333333,47.648083333333,0),
--  (6,'2020-12-18 08:23:47',7,10.865333333333,48.3865,0),
--  (7,'2020-12-18 08:27:46',8,12.906383333333,54.921533333333,0),
--  (8,'2020-12-18 08:32:21',9,10.867633333333,49.480583333333,0),
--  (9,'2020-12-18 09:07:26',10,2.9120166666667,51.231766666667,0),
--  (10,'2020-12-18 09:16:33',11,6.7019,51.622033333333,0),
--  (11,'2020-12-18 09:22:55',12,6.8514166666667,51.930683333333,0),
--  (12,'2020-12-18 09:29:03',13,28.071833333333,-26.536933333333,0),
--  (13,'2020-12-18 09:45:23',14,-168.08986666667,65.612866666667,0),
--  (14,'2020-12-18 09:59:49',15,33.572216666667,59.35555,0),
--  (15,'2020-12-18 10:13:03',16,9.527,54.838733333333,0),
--  (16,'2020-12-18 10:16:42',17,11.692316666667,47.90275,0),
--  (17,'2020-12-18 10:17:12',17,11.6923,47.90275,0),
--  (18,'2020-12-18 10:20:00',17,11.628583333333,48.213733333333,0),
--  (19,'2020-12-18 10:23:18',18,6.7346166666667,51.476033333333,0),
--  (20,'2020-12-18 10:30:33',19,9.5247666666667,54.8373,0),
--  (21,'2020-12-18 10:39:54',20,6.63895,49.754683333333,0),
--  (22,'2020-12-18 10:44:51',21,-77,38.8976,0),
--  (23,'2020-12-18 11:11:15',22,11.185183333333,44.740733333333,0),
--  (24,'2020-12-18 11:22:26',23,11.185183333333,55.925916666667,0),
--  (25,'2021-02-01 13:50:43',24,1.1870333333333,1.3685166666667,0)
-- ;

-- INSERT INTO `cache_countries` (`id`, `date_created`, `cache_id`, `country`, `restored_by`)
-- VALUES
--  (1,'2020-12-17 13:38:05',1,'DE',0),
--  (2,'2020-12-17 13:45:50',3,'DE',0),
--  (3,'2020-12-17 14:18:27',4,'DE',0),
--  (4,'2020-12-17 14:23:31',5,'DE',0),
--  (5,'2020-12-17 14:27:51',6,'DE',0),
--  (6,'2020-12-18 08:23:47',7,'DE',0),
--  (7,'2020-12-18 08:27:46',8,'DE',0),
--  (8,'2020-12-18 08:32:21',9,'DE',0),
--  (9,'2020-12-18 09:07:26',10,'BE',0),
--  (10,'2020-12-18 09:16:33',11,'DE',0),
--  (11,'2020-12-18 09:22:55',12,'DE',0),
--  (12,'2020-12-18 09:29:03',13,'ZA',0),
--  (13,'2020-12-18 09:45:23',14,'US',0),
--  (14,'2020-12-18 09:59:49',15,'RU',0),
--  (15,'2020-12-18 10:13:03',16,'DE',0),
--  (16,'2020-12-18 10:16:42',17,'JP',0),
--  (17,'2020-12-18 10:23:18',18,'DE',0),
--  (18,'2020-12-18 10:30:33',19,'DE',0),
--  (19,'2020-12-18 10:39:54',20,'DE',0),
--  (20,'2020-12-18 10:44:51',21,'US',0),
--  (21,'2020-12-18 11:11:15',22,'DE',0),
--  (22,'2020-12-18 11:22:26',23,'DE',0),
--  (23,'2021-02-01 13:50:43',24,'DE',0)
-- ;

INSERT INTO `cache_desc` (`id`, `uuid`, `node`, `date_created`, `last_modified`, `cache_id`, `language`, `desc`, `desc_html`, `desc_htmledit`, `hint`, `short_desc`)
VALUES
 (1,'8d335293-3fd8-11eb-96df-0242ac120002',4,'2020-12-17 13:38:05','2020-12-17 13:39:00',1,'DE','<p style=\"text-align:center;\"><em><strong>### Dieser Testcache basiert auf OC1239F ###</strong></em><br /><img src=\"https://d1u1p2xjjiahg3.cloudfront.net/b6f0f586-f5a6-439e-8210-1738cadb2c2d_l.jpg\" alt=\"Wanted - Der Osterhase\" /></p>\n<p style=\"text-align:center;\"><br /><br /><strong>Liebe Cachercommunitiy,</strong><br /><br />die CSW* benötigt eure <strong>HILFE</strong>!<br />Wie euch bei <a href=\"http://coord.info/GC5GXMV\">Weihnachtsbaum@night</a> aufgefallen sein sollte, gibt es im Stadtlohner Raum einen \"Verstecker\", genannt \"Osterhase\", der den Weihnachtsmann/Weichnachten gar nicht mag. Er hat unseren Cache in Gewahrsam genommen und am ehemaligen Ort ein Bekennerschreiben hinterlassen (<a href=\"http://coord.info/GLGMC6P1\">Quelle</a>).<br /><br />Der CSW* ist es nun gelungen, den Aufenthaltsort des \"Osterhasen\", wie sich der Verstecker nennte, zu finden. An oben genannter Position soll sich der \"Osterhase\" zuletzt aufgehalten haben. Neben dem Weihnachtsbaum lagert er offenbar auch viele Eier, die aus den nahliegenden Ställen \"bezieht\".<br />Um diesem Treiben ein Ende zu bereiten, bitten wir um eure <strong>HILFE</strong>. Begebt euch zu den oben genannten Koordinaten und schaut nach, ob unsere Vermutungen stimmen.<br /><br /><br /><strong>Zur Cachelocation:</strong><br />Der Cache liegt auf dem Grundstück des Restaurant \"Hundewicker Bahnhof\". Der Restaurantbesitzer (hoffentlich auch das Personal) ist über den Cache informiert. An Wochenenden oder anderen Tagen, wenn der Saal des Restaurant belegt ist, kann es sein, dass ihr bei der Suche/beim Loggen von ein \"paar\" Leuten beobachtet werden könnt. Das ist dann eben so.<br /><br /></p>\n<h1 style=\"text-align:center;\"><span style=\"color:#04b404;\">Die CSW* wünscht frohe Ostern</span></h1>',1,1,'Zum Öffnen: finde die 3 Eier<br />\r\n(Edit: ein Ei fehlt zur Zeit.-&gt; B = 3)','Finde den Osterhasen, der die CSW* bestohlen hat.'),
 (2,'a25f2b53-3fd9-11eb-96df-0242ac120002',4,'2020-12-17 13:45:50','2020-12-17 13:46:34',3,'DE','<p style=\"text-align:center;\"><em><strong>### Dieser Testcache basiert auf OC11AE0 ###</strong></em></p>\n<p>Â </p>\n<p>Im Rahmen des Projektes 12 für <a rel=\"noreferrer noopener\" href=\"http://blog.opencaching.de/2015/01/projekt-12/\" target=\"_blank\">http://blog.opencaching.de/2015/01/projekt-12/</a> wurde der nachfolgende Cache angelegt.<br /><br />\nDieser Cache zeigt die örtliche Imkerei und Schäferei. Für mich ist der Platz ein kleines Idyl. Auch der angrenzende Friedhof zeigt für mich ein Besonderheit, die ich eigentlich nur aus südlichen Ländern kenne. Regalurnengräber. Seltsam. Das hätte ich hier nicht erwartet. <br /><br />\nNutzt das â€žTorâ€œ in den Perlacher. Entdeckt selbst die Geheimnisse des schönen Mischwaldes.</p>',1,1,'Der Cache ist vom Weg aus nicht sichtbar. Orangefarbener Deckel.','Imkerei und Schäferei Taufkirchen (Projekt 12)'),
 (3,'30b42c64-3fde-11eb-96df-0242ac120002',4,'2020-12-17 14:18:27','2020-12-17 14:18:27',4,'DE','<p style=\"text-align:center;\"><em><strong>### Dieser Testcache basiert auf OC1239E ###</strong></em></p>\n<p>Â </p>\nWillkommen in der<br /><br /><h3>Töpferstadt Stadtlohn</h3>\n<br /><br /><img src=\"http://imgcdn.geocaching.com/cache/large/c4e962d2-4985-460b-bd8a-fc46996573cb.jpg\" alt=\"c4e962d2-4985-460b-bd8a-fc46996573cb.jpg\" /><p>\n<br />\nDiese Letterbox dauert 20 - 30 Minuten und soll euch das Stadtlohner Traditionshandwerk, das Töpfern, etwas näher bringen.<br /><br /><strong>Die Geschichte des Stadtlohner Töpferhandwerks:</strong><br />\nDas Töpferhandwerk hat eine lange Tradition in Stadtlohn. So wird Stadtlohn noch heute oft als â€žTöpferstadtâ€œ tituliert. Das Töpferhandwerk kam aus dem Rheinland, wahrscheinlich aus dem Raum Frechen zu Anfang des 17. Jahrhunderts nach Stadtlohn.<br /><br />\nBereits 1630 wird in einem Verzeichnis der Bürgermeister ein Heinrich von Frechen, genannt Krukenbäcker, aufgeführt. In Frechen stand die Krugfabrikation schon im 15. Jahrhundert in hoher Blüte, so dass man vermuten kann, dass dieser Heinrich von Frechen nach Stadtlohn übersiedelte und die in seiner Heimat erlernte Krukenbäckerei fortsetzte. Die Töpferei entwickelte sich zu einem florierenden Kunsthandwerk, dem die Tonvorkommen in der näheren Umgebung zugute kamen. 1812 gab es bereits vier Töpfereien mit 12 Arbeitern. Die Ware wurde auf den Märkten im Land verkauft. Außerdem gab es eine Fabrik von irdenen Tabakspfeifen mit sechs Arbeitern.<br /><br />\nAus dem Westerwald kam die Familie Gertz, die hauptsächlich Puppen- und Tierfiguren, Kruzifixe, Flöten und Nachtigallen herstellte. An den Wallfahrtstagen stellte sie ihre Ware in der Nähe der Kirche aus, um sie zu verkaufen. Beliebte Mitbringsel der Pilger zur wundertätigen Madonna waren vor allem die Nachtigallen, die mit Wasser gefüllt, Trillertöne abgaben. Mit dem Tod der Gebrüder Gertz hörte der Betrieb ihrer Öfen auf. An gleicher Stelle stand später die Töpferstube Brockhoff KG.<br /><br /></p> <img style=\"margin-right:10px;\" src=\"http://imgcdn.geocaching.com/cache/large/ca58596d-d85e-405c-855b-228930dff29a.jpg\" alt=\"ca58596d-d85e-405c-855b-228930dff29a.jpg\" width=\"300\" height=\"220\" /><img style=\"margin-right:10px;\" src=\"http://imgcdn.geocaching.com/cache/large/1038a728-863e-436f-b766-6836bb5d885f.jpg\" alt=\"1038a728-863e-436f-b766-6836bb5d885f.jpg\" width=\"300\" height=\"220\" /><p>\n<br />\nNachdem 1886 die Madonnenstatue aus der Wallfahrtskirche gestohlen wurde und damit die Prozessionen auswärtiger Wallfahrer nach Stadtlohn ausblieben, gingen die Zahlen der Töpfereien weiter zurück. Die Regierung setzte sich 1887 für die Hebung des Töpfergewerbes ein, was von den Töpfermeistern Stadtlohns begrüßt wurde. Die Förderung der Industrie bezog sich dabei auf das Anlernen neuer Formen und Verzierungen in besserer Arbeitsart und noch besserer Vorbereitung des Stoffes, auf Teilung hinsichtlich des Erzeugens der Ware und auf das Ausbilden von Besonderheiten für die einzelnen Töpfer und auf die Entwicklung eines geregelten Absatzes zu besseren Preisen.<br /><br />\n1924 gab es in Stadtlohn die Töpfereien Arnold Brockhoff, B. Erning, B.W. Erning, J.W. Erning und die Töpferei Thiry, deren Pächter G. Tenbrink war. Alle hatten ihren Standort am Eschtor. Die Ware konnte mittlerweile auch mittels der Eisenbahn transportiert werden und fand im Münsterland, dem rheinisch- westfälischen Industriebezirk, dem benachbarten Hannover und Holland guten Absatz. Die Tonerde wurde aus Lünten geholt und durch Mischung mit fettem Westerwälder Ton zum Gebrauch geeigneter gemacht. Den früher in Wenningfeld betriebenen Tonabbau hatte man aufgegeben, weil die Beseitigung des Wassers, das sich in den Gruben ansammelte, zu kostspielig wurde. Das Material wurde meist einige Monate zum Trocknen gelagert, dann mit der Potthacke zerkleinert, in Gruben mit Wasser eingeweicht und dann in der Tonmühle gemahlen und gewallt, bevor es zur Verarbeitung auf der Töpferscheibe geeignet war.<br /><br />\nDie Zahl der Töpfereien nahm im 20. Jahrhundert kontinuierlich ab. Heute gibt es in Stadtlohn nur noch die Töpferei B. Erning Söhne.Die Töpferei Erning blickt auf eine über 200 Jahre alte Geschichte zurück und führt die Tradition bis zum heutigen Tage, als einer der wenigen Töpfereien Deutschlands, weiter. Vieles wird heute noch so gemacht wie vor 200 Jahren. Die alten Töpfertechniken und Fertigkeiten von früher sind der Töpferei Erning bis heute erhalten geblieben. So fertigen sie bis zum heutigen Tage Einzelstücke mit liebevollen Details und maßgefertige Serien für Kunden in der ganzen Welt.<br /><br /></p> <img style=\"margin-right:10px;\" src=\"http://imgcdn.geocaching.com/cache/large/7325cbe2-83e7-435f-a232-889bd2e6245f.jpg\" alt=\"7325cbe2-83e7-435f-a232-889bd2e6245f.jpg\" width=\"300\" height=\"219\" /><img style=\"margin-right:10px;\" src=\"http://imgcdn.geocaching.com/cache/large/24096024-fafd-4ac9-8adc-1378125887d2.jpg\" alt=\"24096024-fafd-4ac9-8adc-1378125887d2.jpg\" width=\"300\" height=\"219\" /><p>\n<br /><strong>Zur Letterbox:</strong><br />\nAn den Startkords lernt ihr \"Jänsken te Küte\" kennen, ein Stadtlohner Original (er war übrigens exakt so groß, wie ihr ihn an Station 1 vorfindet). Den bürgerlichen Namen Johann Niehues kannte wohl kaum einer. Hier eine kleine Anekdote zu ihm:<br /><br /></p>\n<table><tbody><tr><td width=\"30\">      </td>\n<td><em>Kaum zu zählen sind die Döhnkes, die man sich von Jänsken erzählt: Einmal hatte er bei Erich Jansen in der Adler-Apotheke Kohlen hereingeschaufelt. Es war ein heißer Tag und Jänsken hatte wohl einen Schnaps verdient. Nun brauchen die Apotheker häufig lateinische Ausdrücke und er sagte zu Marianne Holtz: â€˜â€˜Geben Sie Jänsken mal einen ordentlichen Schnaps, aber mit wenig Aqua.\" Jänsken, der natürlich kein Lateiner ist, aber sprach zu Marianne: â€˜â€˜Do Du men düffdig Aqua inn, ick kannâ€™t wall verdrägenâ€™â€™</em></td>\n</tr></tbody></table><p><br />\nNun zur Aufgabe bei \"Jänsken te Küte\":<br />\nDas Final ist durch ein dreistelliges Schloss (welchen ein wenig klemmt) gesichert (ABC).<br /><br />\nA= Wieviel Knöpfe hat Jänskens Jacke?<br /><br />\nB=Welchen Bart hat Jänsken? Vollbart = 5, Schnäuzer = 6, Ziegenbart =3<br /><br />\nC=Was hat Jänsken links von sich stehen? Eimer = 2, Korb = 9, Schubkarre = 3<br /><br />\nDann macht folgenden Peilung:<br /><br />\nMesst den Stiel von Jänskens Besen und geht um die 70 fache Verlängerung in(wie alt wurde Jänsken*3-4)Grad.<br />\nDafür wäre es gut ein <strong>Maßband</strong> dabei zu haben.<br /><br />\nNun solltet ihr vor einer Kachelwand stehen, wo zwei Jahreszahlen abgebildet sind. Bildet die Quersumme beider Jahreszahlen, addiert 2 dazu,und ihr habt \"D\".<br /><br />\nDie obere Jahreszahl ist durch eine Fuge getrennt. Nehmt die dreistellige Zahl auf der einen Kachel und addiert zu dieser dreistelligen Zahl 14 und ihr habt \"E\". Peilt nun von hier \"D\" Meter in \"E\" Grad und ihr seid am Final.<br /><br />\nDa uns aufgefallen ist, dass leider einige ohne Stempel loggen ... hier nochmal der Hinweis:<br />\nIhr braucht keinen eigenen Stempel zu besitzten um den Cache zu loggen. Im Final gibt es eine Box, in der sich <strong>Materialien für euren do-it-youself-3-Minuten-Stempel</strong> für diesen Cache befinden. So könnt ihr euch euren eigenen Stempel in nullkommanichts für den Cache basteln und müsst nicht mit Figerabdruck (Figerabdruck ist immer etwas einfallslos) loggen!<br /><br /></p>\n<hr /><table style=\"table-layout:auto;\"><tbody><tr><td style=\"width:inherit;\"><img class=\"InsideTable\" src=\"http://www.geocaching.com/images/WptTypes/letter_72.gif\" alt=\"letter_72.gif\" /></td>\n<td style=\"vertical-align:middle;width:inherit;\"><em>Dieser Geocache ist ein <strong>Letterbox Hybrid</strong>.<br />\nBitte beachtet die folgenden Regeln:</em></td>\n</tr></tbody></table><ul><li>Ihr stempelt mit dem in der Letterbox liegenden Stempel in euer eigenes Stempelbuch.<br /><strong>Dieser Stempel verbleibt in der Letterbox!</strong></li>\n<li>Ihr könnt ganz normal im Logbuch loggen. Cacher, die keinen eigenen Stempel besizten, können einen minimalistischen 1-mal Stempel beim Cache selbst basteln. Die Materialien findet ihr im Cache. Finger-Stempelabdrücke finden wir recht unkreativ - also bastelt doch lieber :-).</li>\n<li>Die Dose bietet Platz für Travelbugs, Geokretys oder Coins.</li>\n</ul><p>Â </p>\n<hr /><p><br /><br />\nThanx an Hr Söbbing, vom Stadtarchiv Stadtlohn für die Hintergrundinfos, Hr. Erning, für das erstellen des Finals, Berkel_83, Team4Münsterland und Team4??? für die beiden Betatests, Emma und Finja für die farblichen Glanzpunkte im Logbuch und Marillos fürs Freimachen der Stelle.<br /><br />\nZuletzt noch die Bitte keine Bilder vom Final hochzulanden, damit die Überraschung für alle erhalten bleibt <img src=\"http://www.geocaching.com/images/icons/icon_smile.gif\" border=\"0\" alt=\"icon_smile.gif\" align=\"middle\" />.<br /><br />\nViel Spaß mit dieser Letterbox wünscht die CSW*.<br /><br /><br /></p>\n<p><a href=\"http://2014gakb.slini11.de/\"><img style=\"border:0px;\" title=\"BRONZE (Multi) - Geocaching Award Kreis Borken 2014\" src=\"http://2014gakb.slini11.de/pic/2014multibronze.png\" alt=\"BRONZE (Multi) - Geocaching Award Kreis Borken 2014\" /></a></p>\nDanke für eure Stimmen <img src=\"resource2/tinymce/plugins/emotions/img/smiley-smile.gif\" border=\"0\" alt=\":)\" width=\"18\" height=\"18\" /> !\n<p>\n<br /><a href=\"http://s08.flagcounter.com/more/wu\"><img style=\"display:none;\" src=\"http://s08.flagcounter.com/count/wu/bg_FFFFFF/txt_000000/border_CCCCCC/columns_2/maxflags_12/viewers_0/labels_0/pageviews_0/flags_0/\" border=\"0\" alt=\"Flag Counter\" /></a><br /><br /></p>',1,1,'Das Schloss am Final klemmt manchmal ein wenig...','Letterbox mit 2 Station + Final am Rande der Innennstadt'),
 (4,'e65d6151-3fde-11eb-96df-0242ac120002',4,'2020-12-17 14:23:31','2020-12-17 14:23:31',5,'DE','<p style=\"text-align:center;\"><em><strong>### Dieser Testcache basiert auf OC104DA ###</strong></em></p>\n<p>Â </p>\n<p><img src=\"https://www.slini11.de/pic/block1.png\" alt=\"block1.png\" width=\"700\" height=\"125\" /><br /><br /><img src=\"https://www.slini11.de/pic/block2.png\" alt=\"block2.png\" width=\"700\" height=\"125\" /><br /><br /><img src=\"https://www.slini11.de/pic/block3.png\" alt=\"block3.png\" width=\"700\" height=\"125\" /><br /><br /><img src=\"https://www.slini11.de/pic/block4.png\" alt=\"block4.png\" width=\"700\" height=\"125\" /><br /><br /><img src=\"https://www.slini11.de/pic/block5.png\" alt=\"block5.png\" width=\"700\" height=\"125\" /><br /><br /><a href=\"https://geocheck.org/geo_inputchkcoord.php?gid=61017514fd82b19-934f-47f2-885f-2f82d5e25e8b\"><img style=\"border-width:0px;border-style:solid;height:40px;width:150px;\" title=\"Prüfe Deine Lösung\" src=\"https://geocheck.org/geocheck_small.php?gid=61017514fd82b19-934f-47f2-885f-2f82d5e25e8b\" alt=\"GeoCheck.org\" /></a></p>',1,1,'',''),
 (5,'812391a4-3fdf-11eb-96df-0242ac120002',4,'2020-12-17 14:27:51','2020-12-17 14:27:51',6,'DE','<p style=\"text-align:center;\"><em><strong>### Dieser Testcache basiert auf OC14C8E ###</strong></em></p>\n<p>Â </p>\n<p>In der Stadt Logika der Cacheindianer, bei denen Opencaching ein Volkssport ist, gibt es einen Ur-Cache. Doch plötzlich ist er verschwunden. Die Verwirrung ist groß. Wer könnte ihn weggenommen haben? Wo könnte er sein? Welches Unglück könnte geschehen, wenn uns der Cachegott nun nicht mehr wohlgesonnen ist? Die Suche läuft in der ganzen Stadt auf Hochtouren, doch er bleibt verschwunden. Da kann nur noch die Hellseherin helfen. Diese weiß alles, was innerhalb der Stadt passiert und in der Vergangenheit passiert ist. Aber sie hat zwei Schwächen:<br /><br />\na) An manchen Tagen sagt sie konsequent immer die Wahrheit und an anderen Tagen lügt sie wider besseren Wissens immer â€“ je nachdem, mit welchem Fuß sie zuerst aufgestanden ist.<br /><br />\nb) Auf Fragen antwortet sie nur mit ja oder nein. <br /><br /><br />\nUm den Cache wiederzufinden, begibt sich ein tapferer Held zur Hellseherin und stellt ihr folgende Fragen:<br /><br />\n1. Befindet sich der Cache noch in der Stadt?<br /><br />\n2. Hast du ihn gefunden? <br /><br /><br />\nNun war dem Held sofort klar, ob der Cache noch in der Stadt ist und er macht sich auf die Suche. Weißt du, was sie ihm geantwortet hat? <br /><br />\nCache ist in der Stadt: A = 5<br /><br />\nCache ist nicht in der Stadt: A = 4<br /><br />\nHellseherin hat ihn gesehen: B = 15<br /><br />\nHellseherin hat ihn nicht gesehen: B = 25<br /><br /><br />\nSuper! Dann kannst die Koordinaten des aktuellen Lageorts mit folgender Formel ermitteln: N 47 3(3+A).(3+A)B E013 B-A*A.B*B+(A-1)*A<br /><br /><br />\nAn den angegebenen Koordinaten gibt\'s nichts zu finden. Der Ort eignet sich aber als Ausgangspunkt für die Suche.<br /><br />\nAm Final erwartet euch eine tolle Aussicht. Der Cache ist insoweit für Kinder geeignet, als der Anstieg nicht allzuweit ist und es in der Nähe des Finals etwas gibt, das Kindern gefallen könnte.<br /><br />\nViel Spaß!</p>',1,1,'Stein, s. Spoilerbild','Ein einfaches Logik-Rätsel'),
 (6,'f8d120d8-4039-11eb-96df-0242ac120002',4,'2020-12-18 08:23:47','2020-12-18 08:23:47',7,'DE','<div class=\"content2-container cachedesc\" style=\"text-align:center;\"><em><strong>### Dieser Testcache basiert auf OC1491A ###</strong></em></div>\n<div class=\"content2-container cachedesc\"></div>\n<div class=\"content2-container cachedesc\"></div>\n<div class=\"content2-container cachedesc\"><span style=\"font-size:medium;\">GaswerkAugsburg on Tour</span></div>\n<div class=\"content2-container cachedesc\"></div>\n<div class=\"content2-container cachedesc\"><span style=\"font-size:medium;\">Ich wollte nun mal einen beweglichen Cache machen.</span></div>\n<div class=\"content2-container cachedesc\"></div>\n<div class=\"content2-container cachedesc\">\n<div class=\"content2-container cachedesc\"><span style=\"font-size:medium;\"><strong>Das Passwort um den Cache zu loggen ist das Nummernschild von meinem grauen Skoda Kodiaq Cachemobil, </strong></span></div>\n<div class=\"content2-container cachedesc\"><span style=\"font-size:medium;\"><strong>wenn ihr mich trifft und mich danach fragt sag ich es euch auch gerne.</strong></span></div>\n<div class=\"content2-container cachedesc\"><span style=\"font-size:medium;\"><strong>Bitte kein Foto von meinem Auto worauf das Kennzeichen erkennbar ist.</strong></span></div>\n<div class=\"content2-container cachedesc\"><span style=\"font-size:medium;\"><strong><br /></strong></span></div>\n<div class=\"content2-container cachedesc\"><span style=\"font-size:medium;\"><strong>Schreibt bitte ins Log, wo und wann ihr das Auto oder mich getroffen habt.</strong></span></div>\n<div class=\"content2-container cachedesc\"><span style=\"font-size:medium;\"><strong><br /></strong></span></div>\n<div class=\"content2-container cachedesc\"><span style=\"font-size:medium;\">Entweder trefft ihr mich beim Cachen oder auf einer der Events rund um Augsburg, bei denen ich immer wieder anzutreffen bin.</span></div>\n<div class=\"content2-container cachedesc\"><span style=\"font-size:medium;\">Oder ihr legt eine Dose in Augsburg und wartet, bis ich mit dem Auto komme ;-)</span></div>\n<p>Â </p>\n<p><span style=\"font-size:medium;\"><strong>Pro Cacher bitte nur ein Log</strong></span></p>\n</div>',1,1,'Wie auf dem Nummernschild ohne &quot;-&quot; und alles zusammengeschrieben und ohne Leerzeichen.<br />\r\nBuchstaben alle GROSS',''),
 (7,'879a1830-403a-11eb-96df-0242ac120002',4,'2020-12-18 08:27:46','2020-12-18 08:27:46',8,'DE','<p style=\"text-align:center;\"><em><strong>### Dieser Testcache basiert auf OC122AD ###</strong></em></p>\n<p>Â </p>\n<div style=\"border:solid 2px #5f90bb;background:#dbe6f1;color:#000000;padding:5px;text-align:justify;\"> <img style=\"float:left;\" src=\"resource2/ocstyle/images/attributes/safari.png\" alt=\"safari.png\" /><p style=\"margin-left:45px;\">Dies ist ein virtueller <a href=\"http://wiki.opencaching.de/index.php/Safari-Cache\">Safari-Cache</a>. Es ist nicht an einen festen Ort gebunden, sondern kann an verschiedenen Orten gelöst werden. Die oben angegebenen Koordinaten dienen nur als Beispiel.<br /> Weitere Caches mit dem Attribut \"Safari-Cache\" findet man mit dieser <a href=\"search.php?searchto=searchbyname&amp;showresult=1&amp;output=HTML&amp;utf8=1&amp;sort=byname&amp;cache_attribs=61\">Suche</a>.<br /> Safari-Caches und die zugehörigen Logs werden auf der <a href=\"http://www.flopp.net/safari/\">Safari-Cache-Karte</a> angezeigt.<br /> Der Cache und seine Logs werden <a href=\"http://www.flopp.net/safari/OCF858\">hier</a> angezeigt. </p>\n</div>\n<p style=\"text-align:center;\"><img style=\"vertical-align:middle;border:0;\" src=\"http://gc.clanfamily.de/oc122ad/titel.jpg\" alt=\"OC122AD Titelbild\" width=\"770\" height=\"300\" /></p>\n<p><span style=\"font-family:\'arial black\', \'avant garde\';font-size:medium;\">Freizeitpark-Safari: Junkie vs. Spass</span></p>\n<p>Urlaubszeit ist doch die schönste Zeit, auch ein langes Wochenende erfreut einen gern.</p>\n<p>Als echter Freizeit-Junkie kann keine Achterbahn hoch genug, keine Abfahrt senkrecht genug und keine Geschwindigkeit schnell genug sein. Es gibt aber auch \"Spass\"-(Bremsen), die sich lieber alles von unten anschauen und auf das Gepack aufpassen wollen.</p>\n<p>In diesem Safari-Cache geht es darum, Freizeitparks zu entdecken. Damit die \"wenigen\" Parks nicht schnell zum Ende des Caches führen, erweitern wir die Anforderungen an diesen Cache. Ihr könnt nicht nur einen Park angeben, sondern wir bitten euch - nennt uns eurer Lieblings-Fahrgeschäft oder Show oder was auch immer in diesem Freizeitpark zu finden ist. Ein Tierpark ist in diesem Sinne auch ein Freizeit-Park. Es muss natürlich kein Park betreten werden. Ein Foto mit dem Park oder Fahrgeschäft im Hintergrund ist auch ok.</p>\n<p>Macht bitte ein Foto mit Euch oder einem persönlichen Gegenstand wie GPS, Handy (gerne mit GPS Anzeige oder ähnlichem) und ladet es hoch. Bilder, die man so bei Google findet zählen nicht! Die finden wir auch <img src=\"resource2/tinymce/plugins/emotions/img/smiley-wink.gif\" border=\"0\" alt=\";)\" width=\"18\" height=\"18\" /> Damit das ganze in der Safari-Ansicht noch verknüpft werden kann, schreibt einfach die Koordinaten mit in eurer Log.</p>\n<p>Viel Spass beim Coastern!</p>\n<div style=\"border:solid 2px #5f90bb;background:#dbe6f1;color:#000000;padding:5px;text-align:justify;\">\n<p>Bitte die in der Logbedingung geforderten Koordinaten im Format \"N/S DD MM.MMM E/W DDD MM.MMM\" (z.B. \"N 48 00.000 E 008 00.000\") ins Log eintragen, damit die Positionen von der <a href=\"http://www.flopp.net/safari/\">Safari-Cache-Karte</a> korrekt erkannt und angezeigt werden können!</p>\n<p style=\"text-align:center;\"><strong><span style=\"font-size:medium;\">Der Cache und seine Logs werden <a href=\"http://www.flopp.net/safari/OC122AD\">hier</a> angezeigt.</span></strong> </p>\n</div>',1,1,'','Zeige uns Deinen Lieblingsfreizeitpark oder das beste Fahrgeschäft'),
 (8,'2b6c6d6f-403b-11eb-96df-0242ac120002',4,'2020-12-18 08:32:21','2020-12-18 08:55:40',9,'DE','<p style=\"text-align:center;\"><em><strong>### Dieser Testcache basiert auf OC138F5 ###</strong></em></p>\n<p>Â </p>\n<p><span style=\"font-size:small;font-family:\'comic sans ms\', sans-serif;\">Na, ist das Cachemobil dreckig von der letzten Cachetour? Und kann man die ursprüngliche Lackfarbe schon nicht mehr erkennen?</span><br /><br /><span style=\"font-size:small;font-family:\'comic sans ms\', sans-serif;\">\nDann wird es wohl wieder einmal Zeit für eine Wäsche, die ihr hier erledigen könnt! ;-) </span><br /><br /></p>\n<hr style=\"width:350px;\" /><p><br /><span style=\"font-size:small;font-family:\'comic sans ms\', sans-serif;\">Um\n diesen Cache loggen zu können, müsst ihr ein Webcamfoto von euch vor \nOrt, mit dem GPS-Gerät in der Hand, machen und es dem Log hinzufügen. Ob\n ihr dabei mit oder ohne Cachemobil anreist und das Foto macht, bleibt \neuch überlassen. Den Link zur Webcam findet ihr hier: </span></p>\n<p style=\"text-align:center;\"><span style=\"font-size:small;font-family:\'comic sans ms\', sans-serif;\"><a href=\"http://www.autowaschen24.de/#webcam\">http://www.autowaschen24.de/#webcam</a></span></p>\n<p style=\"text-align:center;\">Â </p>\n<p style=\"text-align:left;\"><span style=\"font-size:small;font-family:\'comic sans ms\', sans-serif;\">Viel Spaß!<br /></span></p>',1,1,'',''),
 (9,'43d578ac-403c-11eb-96df-0242ac120002',4,'2020-12-18 08:40:11','2020-12-18 08:55:40',9,'EN','<p style=\"text-align:center;\"><em><strong>### Dieser Testcache basiert auf OC138F5 ###</strong></em></p>\n<p>Â </p>\n<p><span style=\"font-size:small;font-family:\'comic sans ms\', sans-serif;\">Na, is ze Cachemobil dirty from ze last Cachetour? And can man ze ursprüngliche lack color yet not more see?</span><br /><br /><span style=\"font-size:small;font-family:\'comic sans ms\', sans-serif;\">Then be it well again time for a wash, that you here execute can! ;-) </span><br /><br /></p>\n<hr style=\"width:350px;\" /><p><br /><span style=\"font-size:small;font-family:\'comic sans ms\', sans-serif;\">At this Cache logging can, must you a Webcam photo from you before place, with ze GPS-Gerät in ze Hand, making and it hanging on ze Log. If you thereby with or without Cachemobil arrive and ze photo make, stays you to deliver. Ze Link to ze Webcam find you here: </span></p>\n<p style=\"text-align:center;\"><span style=\"font-size:small;font-family:\'comic sans ms\', sans-serif;\"><a href=\"http://www.autowaschen24.de/#webcam\">http://www.autowaschen24.de/#webcam</a></span></p>\n<p style=\"text-align:center;\">Â </p>\n<p style=\"text-align:left;\"><span style=\"font-size:small;font-family:\'comic sans ms\', sans-serif;\">Much fun!<br /></span></p>',1,1,'',''),
 (10,'12169814-4040-11eb-96df-0242ac120002',4,'2020-12-18 09:07:26','2020-12-18 09:10:23',10,'EN','<p style=\"text-align:center;\"><em><strong>### Dieser Testcache basiert auf OC15BA5 ###</strong></em></p>\n<p>Â </p>\n<p><span class=\"VIiyi\" style=\"font-size:medium;\" lang=\"en\" xml:lang=\"en\"><span class=\"JLqJ4b\">This year there is again a Christmas market event at the Kurhaus.</span></span></p>\n<p><span class=\"VIiyi\" style=\"font-size:medium;\" lang=\"en\" xml:lang=\"en\"><span class=\"JLqJ4b\">In Oostende, the Christmas market is taking place in the courtyard of the Kurhaus for the 18th time this year. With this event I am happy to introduce you to this pearl among the Oostende\'s Christmas markets.</span></span></p>\n<p><span class=\"VIiyi\" style=\"font-size:medium;\" lang=\"en\" xml:lang=\"en\"><span class=\"JLqJ4b\"><span class=\"VIiyi\" style=\"font-size:medium;\" lang=\"en\" xml:lang=\"en\"><span class=\"JLqJ4b\">Oostende</span></span> merchants have a small selection from their range ready. This gives you a good overview of what you can buy in their shops (the \"Let the click in your city\" campaign can still be really lived here). In addition, craftsmen and artists offer e.g. Decorative items and nativity figurines. So you can take care of Christmas gifts at an early stage and at the same time collect an event point. Of course, there is sufficient provision for your physical well-being.</span></span></p>\n<p><span class=\"VIiyi\" style=\"font-size:medium;\" lang=\"en\" xml:lang=\"en\"><span class=\"JLqJ4b\">Since the market has long ceased to be an insider tip, the rush is correspondingly large. Therefore, the event takes place outside at the Red Fountain. The official event time is from 1:30 p.m. to 7:00 p.m., but it usually lasts longer. :)</span></span></p>\n<p>Â </p>\n<p><span class=\"VIiyi\" style=\"font-size:medium;\" lang=\"en\" xml:lang=\"en\"><span class=\"JLqJ4b\">I look forward to your numerous appearances!</span></span></p>\n<p><span class=\"VIiyi\" style=\"font-size:medium;\" lang=\"en\" xml:lang=\"en\"><span class=\"JLqJ4b\">p.s. the log password will be handed out during the event in the <strong><span style=\"text-decoration:underline;\">Kurhaus</span></strong></span></span></p>',1,1,'[Travel by public transport!]','meet, swim, eat, have fun'),
 (11,'57f00a4f-4041-11eb-96df-0242ac120002',4,'2020-12-18 09:16:33','2020-12-18 09:16:33',11,'DE','<p style=\"text-align:center;\"><em><strong>### Dieser Testcache basiert auf OCEB44 ###</strong></em><br /><br /></p>\n<div style=\"width:670px;font-family:Arial, Helvetica, sans-serif;font-size:10pt;\">\n<div style=\"height:400px;width:670px;float:left;background:url(&quot;http://oc.clanfamily.de/oceb44/kopf.jpg&quot;) no-repeat;\"> </div>\n<div style=\"width:610px;float:left;background:url(&quot;http://oc.clanfamily.de/oceb44/content.jpg&quot;) repeat;padding:0px 30px;text-align:justify;\">\n<h1 style=\"font-size:14pt;color:#b79b4c;\">\"Unterm Zaun durch\" - ein Tornado-Jet auf abwegen</h1>\nDieser Werbeslogan des Flugzeugherstellers â€žPANAVIAâ€œ (aus den 1980er Jahren), wurde vor 25Jahren in Hünxe-Bucholtwelmen leider Realität.<br /><br />\nAm Morgen des 30.03.1987, stürzte ein Jagdbomber des Typs â€žTornadoâ€œ, der Royal Airforce, bei der Siedlung am Waldheideweg ab. Wie durch ein Wunder wurde bei diesem Flugzeugabsturz â€žnurâ€œ der Pilot und Copilot leicht verletzt.\nHeute sieht man nichts mehr von den Schäden. Bei einem Wäldchen, an der Weseler Straße kann man erkennen, dass hier â€“zwischen alten Bäumen- auch jüngere Bäume stehen. Hier ist das Flugzeug durchgepflügt. \nAm Tag des Absturzes war ich selber vor Ort, da Verwandte dort wohnen. Es sah schlimm aus. Überall Flugzeugteile, beschädigte Häuser, Soldaten der britischen Armee und Bundeswehr. Solche Bilder kannte man bisher nur aus der Presse. Nun waren diese Bilder Realität! <br /><br />\nDieser Cache ist ohne große Rätselei oder Gehirnakrobatik lösbar.<br />\nIch möchte Euch vielmehr einladen, an den einzelnen Stationen den Verlauf des Absturzes und die Ausmaße des Trümmerfeldes zeigen. Dieses Ereignis ist 25 Jahre her, manche von Euch waren damals noch nicht geboren oder noch ein Kind. Die Fotos zeigen nur einen kleinen Teil der vielen Schäden, die hier entstanden sind.<br /><br /><hr style=\"width:600px;border:2px dashed #CCB982;\" /><h1 style=\"font-size:14pt;color:#b79b4c;\">Download:</h1>\nDu brauchst natürlich die WiG Datei. Diese kannst Du direkt [<a href=\"http://oc.clanfamily.de/oceb44/wig_unterm_zaun.gwc\">hier</a>] herunterladen.\n<br /><hr style=\"width:600px;border:2px dashed #CCB982;\" /><h1 style=\"font-size:14pt;color:#b79b4c;\">Hilfe zu diesem Modus:</h1>\nEin \"WiG\" ist für viele ein unbekanntes und fremdes Gebiet - eine noch nicht erforschte Cache-Variante.\nDamit alles funktioniert versuchen wir hier kurz das Wichtistgste aufzuzählen.<br /><br /><strong>Was braucht man: einen \"Player\"</strong><br />\nEin Player ist das \"Abspielgerät\" in der man die \"Cartrigde\" läd um den WiG zu spielen... Natürlich macht das ganze nur dann Sinn wenn auch ein GPS Signal dabei verwendet werden kann.<br />\nDie uns zur Zeit bekannten Player sind:<br /><ul><li style=\"padding-left:10px;list-style-type:square;\">Garmin Oregon-Serien</li>\n<li style=\"padding-left:10px;list-style-type:square;\">Garmin Colorado-Serien</li>\n<li style=\"padding-left:10px;list-style-type:square;\">Android-Smartphones mit dem App \"Where you go\" [<a href=\"https://play.google.com/store/apps/details?id=menion.android.whereyougo&amp;hl=de\">Download</a>]</li>\n<li style=\"padding-left:10px;list-style-type:square;\">iPhone\'s mit dem App \"Pi-Go\" [<a href=\"http://itunes.apple.com/de/app/wherigo/id385035547?mt=8\">Download</a>]</li>\n<li style=\"padding-left:10px;list-style-type:square;\">Windows Pocket-PCs mit Windows Mobile und dem passenden App [<a href=\"http://www.wherigo.com/player/download.aspx\">Download</a>]</li>\n</ul><br />\nUm diesen Cache nun begehen zu können, solltest Du einen Player haben und die WiG-Datei (Cartdridge) vorher runterladen und auf Deinem Gerät gespeichert haben. Als nächstes fährtst Du das angegebene Zielgebiet an. Starte dann die WiG-Datei. Folge dem Spielverlauf. Am Ende steht ein \"Freischalt-Code\" zur Verfügung, mit dem Du \"loggen\" kannst.<br />\nDie von Clanfamily programmierten WiG Caches sind immer so konzepiert, dass bei Abschluss eines wichtigen Schritts gespeichert wird. Stürzt Dein Player ab oder geben die Akkus auf, kein Problem. Starte am letzten Speicherpunkt.<br />\nFür Fragen und Anregungen triggert uns doch einfach an: gc@clanfamily.de<br />\nJetzt aber VIEL SPASS!</div>\n<div style=\"height:200px;width:670px;background:url(&quot;http://oc.clanfamily.de/oceb44/footer.jpg&quot;) no-repeat;float:left;padding-left:30px;padding-top:60px;font-size:8pt;\">\nDesign Â© Clanfamily.de<br />\nDie Verantwortung für die Ausführung des GeoCache übernimmt der Cacher selbst.<br />\nWir bitten euch: schont die Natur, wie auch wir dies beim legen unseres Cache berücksichtigt haben.</div>\n</div>',1,1,'Lade zu erst die Cartridge herunter!!<br />\r\nFinale - Spoilerfoto beachten. Links davon. Achte auf &quot;Killerameisen&quot;.','Where I Go mit kleiner Zeitgeschichte.'),
 (12,'3bcb07c7-4042-11eb-96df-0242ac120002',4,'2020-12-18 09:22:55','2020-12-18 09:22:55',12,'DE','<p style=\"text-align:center;\"><em><strong>### Dieser Testcache basiert auf OCF1D7 ###</strong></em><br /><br /></p>\n<div class=\"UserSuppliedContent\">\n<div style=\"width:670px;height:inherit;\">\n<div style=\"width:650px;font-family:\'Comic Sans MS\', Verdana, Tahoma, Arial;text-align:left;\"><span><img style=\"border:0px solid #333;float:right;margin:45px 0 10px 10px;\" title=\"Borken Logo\" src=\"http://imgcdn.geocaching.com/cache/large/9269f843-69a9-4d96-9c94-d1e657d3caca.png?rnd=0.4573786\" alt=\"Steg am Aasee\" width=\"207\" height=\"234\" /></span>\n<p><span><span style=\"font-size:20px;\"><strong>Hallo Geocacher,</strong></span><br /><br /><span style=\"font-size:16px;\">es ist wieder soweit: Zum zweiten mal findet der Geocaching Award nun schon im Kreis Borken statt und besitzt jetzt erstmals auch einen eigenen Cache - ein verkehrsgünstig gelegenes TB-Hotel in der Mitte des Kreises.<br /><br />\nDaher sollen auch wieder die besten Caches des vergangenen Jahres 2013 in unserer Homezone mit dem â€žGeocache Award Kreis Borkenâ€œ prämiert werden. Wie im vergangenen Jahr gibt es getrennte Kategorien: Traditional Caches, Multi Caches und Mystery Caches. Abgestimmt werden kann vom 1. Februar - 31. März. Es kommt also wieder auf eure Stimme an!</span></span></p>\n<br /><p><span style=\"font-size:24px;\"><strong>Gewählt wird unter <a href=\"http://2013gakb.slini11.de\">http://2013gakb.slini11.de</a></strong></span></p>\n<span style=\"font-size:16px;\">Zum Cache:<br />\nBitte lauft via. WP 1 zum Cache und NICHT querfeldein. Sonst entsteht dort Zudem behandelt den Cache bitte sorgfältig und rüttelt NICHT an ihm herum ! Beachtet bitte auch, dass das Schloss manchmal ein wenig klemmt.<br /><br />Ich habe das Listing(GC4D2H1) an sich bei GCcom archiviert aber der gleiche Cache wird nun unter einem neuen Listing unter GC6AMMK ( GAKB - The Lost Hotel ) weitergeführt. Daher kann man hier gerne loggen, wenn GC6AMMK gefunden wurde.<br /><br /></span>\n<p style=\"text-align:center;\"><span style=\"font-size:16px;\">Viel Spaß beim Wählen &amp; Finden wünschen euch die</span></p>\n<p style=\"text-align:center;font-size:20px;\"><span style=\"font-size:16px;\"><strong>Geocacher des Kreies Borken <img src=\"http://www.geocaching.com/images/icons/icon_smile_big.gif\" border=\"0\" alt=\"icon_smile_big.gif\" align=\"middle\" /></strong></span></p>\n</div>\n</div>\n<hr /><div style=\"text-align:center;width:670px;height:inherit;\"><span style=\"font-size:16px;\"><span style=\"font-size:16px;\"><span style=\"font-size:16px;\"><strong><img src=\"http://imgcdn.geocaching.com/cache/large/f2aa3e34-b0e8-45ea-b702-d8ade1d218e3.png?rnd=0.540646\" alt=\"f2aa3e34-b0e8-45ea-b702-d8ade1d218e3.png\" /></strong></span></span></span></div>\n</div>',1,1,'',''),
 (13,'177b3f31-4043-11eb-96df-0242ac120002',4,'2020-12-18 09:29:04','2020-12-18 09:29:04',13,'EN','<p style=\"text-align:center;\"><strong><em>### Dieser Testcache basiert auf OC13E39 ###</em></strong><br /><br /></p>\n<div class=\"content2-container cachedesc\">\n<p><span style=\"font-size:medium;font-family:arial, helvetica, sans-serif;\">In\n the Garden of Rememberance is a plaque remembering Ted Prior who was a \nscoutmaster at the Henley scout troop in the 1980\'s. In Scouting the \nadults take on a \"Scout name\" which is used to reduce the formality for \nthe scouts. Ted Prior\'s scout name was \"<strong>Shrike</strong>\"<br /></span></p>\n<p>Â </p>\n<p><span style=\"font-size:medium;font-family:arial, helvetica, sans-serif;\">There is onle little mistake on the plaque...<br />The name of Ted\'s house was \"Shrikes Loft\" (from his Scouting name) and not Squire\'s loft!</span></p>\n<p><span style=\"font-size:medium;font-family:arial, helvetica, sans-serif;\"><br /></span></p>\n<p><span style=\"font-family:arial, helvetica, sans-serif;\"><strong><span style=\"font-size:large;\">Scouting</span></strong></span></p>\n<p><span style=\"font-size:medium;font-family:arial, helvetica, sans-serif;\">Scouts\n is an organisation for the development of the youth and geocaching is a\n hobby well suited to cubs and scouts because it involves some important\n life skills:</span></p>\n<ul><li><span style=\"font-size:medium;font-family:arial, helvetica, sans-serif;\"><strong>Mapwork</strong> - developing your geospacial skills and sense of direction - still needed even in the days of GPS,</span></li>\n<li><span style=\"font-size:medium;font-family:arial, helvetica, sans-serif;\"><strong>Observation</strong> - good geocachers have keen observation skills and are able to spotsomething that is a little out of order,</span></li>\n<li><span style=\"font-size:medium;font-family:arial, helvetica, sans-serif;\"><strong>Outdoors</strong> - Getting up and out! There is nothing better to do to prevent boredom... get outdoors!</span></li>\n<li><span style=\"font-size:medium;font-family:arial, helvetica, sans-serif;\"><strong>Challenge</strong> - You learn best when we are forced to think.</span></li>\n</ul><p>Â </p>\n<p><span style=\"font-size:medium;font-family:arial, helvetica, sans-serif;\">To record this cache you need to find a password on the plaque.The password is the <strong><em>year that Ted and June were married</em></strong>?</span></p>\n<p><span style=\"font-size:medium;font-family:arial, helvetica, sans-serif;\">Take some time to walk around the spiral and read of the people who have lived and built Henley.</span></p>\n</div>',1,1,'Cache is magnetic at waist height.','Remembering Shrike - a scoutmaster'),
 (14,'5f3d6ae3-4045-11eb-96df-0242ac120002',4,'2020-12-18 09:45:23','2020-12-18 09:45:23',14,'EN','<p style=\"text-align:center;\"><em><strong>### Dieser Testcachebasiert auf OC135D1 ###</strong></em><br /><br /></p>\n<table><tbody><tr><td valign=\"middle\"><img src=\"https://s3.amazonaws.com/gs-geo-images/ef199d9b-4af6-4378-8089-190f3d1cd6e3_l.png\" alt=\"ef199d9b-4af6-4378-8089-190f3d1cd6e3_l.p\" width=\"80\" /></td>\n<td>  </td>\n<td>Als alljährliches CSW*-Weihnachtsprojekt ist dieser Multi in diesem Jahr für das <strong><a href=\"https://opencaching.de/OC1346E\">8-te Ad(E)vent</a> (Samstag, 10. Dezember ab 16 Uhr)</strong> entstanden. Es bietet sich also an den Cache während des Ad(E)vents zu suchen, da dieser an der diesjährigen Event-Location startet <img src=\"images/icons/icon_smile.gif\" border=\"0\" alt=\"icon_smile.gif\" align=\"middle\" />. Daher können für den 10. Dezember keine Termine gebucht werden!</td>\n</tr></tbody></table><p>Â </p>\n<p><span style=\"color:#ff0000;\">Bitte tragt euch in den Kalender ein. Das ist mit dem Grundstückseigentümer &amp; Jagdpächter so vereinbart. Und die wohnen an der Runde! Also haltet euch bitte an die Startzeiten, damit der Cache lang leben kann! Leuchtet bitte außerdem nicht in die Häuser/Fenster (bei einem anderen NC haben sich Anwohner schonmal beschwert)!<br /><br /><a rel=\"noreferrer noopener\" href=\"http://www.gocaching.de/calendar.php?book=60E\" target=\"_blank\"><img src=\"https://www.gocaching.de/calendar.php?cid=60E\" border=\"0\" alt=\"calendar.php?cid=60E\" /></a></span></p>\n<p>\n<strong>Story:</strong></p>\n<p>â€žPengâ€œ â€“ laut war der Knall über der Kleinstadt Gescher im westlichen Münsterland zu hören. Doch was war das? Ein Jäger im Wald? Oder doch ein Autounfall? Aber welcher Autounfall war denn so laut? Die Bewohner der Kleinstadt mögen an diesem Winterlichen Abend vielleicht unterschiedliche Vermutung gehabt haben. Ein Autounfall wäre gut möglich gewesen, konnte man doch durch den starken Schneefall fast gar nichts sehen. Doch keine der Vermutungen kam auch nur annähernd an die Realität heran. Im Gegensatz zu ihnen wusste der Flugkontrollwichtel auf dem Grundstück des Weihnachtsmanns besser Bescheid. Die kleine Weihnachtsbaumkugel neben dem Modellschlitten am Weihnachtsbaum leuchtete rot auf! Der Weihnachtsmann musste unterwegs mit dem Schlitten einen Unfall gehabt haben!</p>\n<p><img src=\"https://s3.amazonaws.com/gs-geo-images/95baab49-3635-44d8-9973-8ad9fe44d0f9_l.jpg\" alt=\"95baab49-3635-44d8-9973-8ad9fe44d0f9_l.j\" /></p>\n<p>Und genau so war es auch. Im Moment nach dem lauten Knall schlitterte der Schlitten in der Luft herum und der Weihnachtsmann hatte Mühe die Rentiere wieder in die richtige Bahn zu lenken um nicht abzustürzen. Doch was war passiert? Durch das stürmische Schneewetter über Gescher war der Weihnachtsmann bei geringer Sichtweite in eine zu schnell Wolke geflogen und als er diese gerade wieder verließ, stieß der Schlitten mit dem Stern GelbusSpekulatius Alpha (dem Bruder von GelbusSpekulatius Beta) zusammen. Der Stern fluchte im ersten Moment des Schocks, fing dann aber an zu weinen und stürzte den Himmel hinunter Richtung Gescher. Der Weihnachtsmann, sichtlich geschockt, war mit den Rentieren und seinem Schlitten durchgeschüttelt worden und hatte tatsächlich einen Stern gerammt und Geschenke verloren! Geschenke, auf die die Kinder so sehnlich warteten waren einfach weg â€“ ein Albtraum für den Weihnachtsmann. Außerdem kannte er den Bruder des Sternes sehr gut. Das würde doppelten Ärger geben!</p>\n<p><img src=\"https://s3.amazonaws.com/gs-geo-images/18c1262f-261f-4026-b513-cbc8711ec77e_l.jpg\" alt=\"18c1262f-261f-4026-b513-cbc8711ec77e_l.j\" /></p>\n<p>Bereits im letzten Jahr habt ihr dem Weihnachtsmann als Weihnachtsaushilfswichtel tatkräftig unter die Arme gegriffen. Auch in diesem Jahr benötigen die Wichtel und der Weihnachtsmann dringend eure Hilfe. Sie selbst sind viel zu sehr mit den Vorbereitungen für Weihnachten beschäftigt und haben daher keine Zeit nach den verloren gegangenen Geschenken zu suchen. Würdet ihr die Aufgabe nochmal übernehmen? Der erste Anhaltpunkt sollte die Absturzstelle von GelbusSpekulatius Alpha sein. Er muss irgendwo nord-westlich von Gescher gelandet sein. Wenn ihr ihn trefft, seid doch bitte so lieb, tröstet ihn und bittet im Namen des Weihnachtsmanns um Entschuldigung. Es sei keine Absicht gewesen und er habe es sehr eilig gehabt. Außerdem würde er den Stern gerne zu einem Versöhnungskakao einladen. So, nun aber los!</p>\n<p>Wenn Ihr GelbusSpekulatius Alpha getroffen habt, folgt weiter den Sternen am Himmelszelt. Die unterschiedlichen Farben der Sterne zeigen euch wohin es geht und wo ihr die Geschenke findet:</p>\n<table align=\"center\"><tbody><tr><td colspan=\"2\" align=\"center\"><img src=\"https://s3.amazonaws.com/gs-geo-images/77f56c49-4d46-485f-a5b4-f15d98046b21_l.jpg\" alt=\"77f56c49-4d46-485f-a5b4-f15d98046b21_l.j\" width=\"100\" height=\"100\" /></td>\n<td valign=\"middle\">Die weißen Sterne weisen den Weg.</td>\n</tr><tr><td colspan=\"2\" align=\"center\"><img src=\"https://s3.amazonaws.com/gs-geo-images/c5597442-5102-46fd-8d7a-f9f7707cbbf0_l.png\" alt=\"c5597442-5102-46fd-8d7a-f9f7707cbbf0_l.p\" width=\"100\" height=\"100\" /></td>\n<td valign=\"middle\">Bei einem roten Stern solltet ihr nach noch einem roten oder einem grünen Stern umsehen.</td>\n</tr><tr><td><img src=\"https://s3.amazonaws.com/gs-geo-images/c5597442-5102-46fd-8d7a-f9f7707cbbf0_l.png\" alt=\"c5597442-5102-46fd-8d7a-f9f7707cbbf0_l.p\" width=\"100\" height=\"100\" /></td>\n<td><img src=\"https://s3.amazonaws.com/gs-geo-images/c5597442-5102-46fd-8d7a-f9f7707cbbf0_l.png\" alt=\"c5597442-5102-46fd-8d7a-f9f7707cbbf0_l.p\" width=\"100\" height=\"100\" /></td>\n<td valign=\"middle\">Zwei rote Sterne signalisieren, dass ihr hier ein Geschenk oder alte Bekannte trefft, die euch womöglich weiter helfen.</td>\n</tr></tbody></table><p>Bei diesem Multi handelt es sich um einen Nachtcache mit 6 Stationen + Final, der vor allem für Kinder geeignet ist und etwa 60 Minuten beansprucht (ohne Kinder vielleicht auch etwas kürzer). Habt ihr alle Geschenke gefunden, begebt euch zum Schlitten des Weihnachtsmannes und gebt die Geschenke bei ihm ab. Eure Ausrüstung sollte eine Taschenlampe, einen (starken) Magneten und eine UV-Lampe (wenn Station 3 nicht will) umfassen. Beachtet bitte die Beschreibung zu den Webpunkten unten im Listing, da dort oder an der Station erklärt wird, was zu tun ist.</p>\n<p>Vielen Dank auch an Christina (Slinis Schwester) für die Bilder im Listing und Finja(Pathfinders Tochter) für Station 1.</p>\n<p>\n<br /><a rel=\"noreferrer noopener\" href=\"http://www.gocaching.de/calendar.php?book=60E\" target=\"_blank\"><img src=\"https://www.gocaching.de/calendar.php?cid=60E\" border=\"0\" alt=\"calendar.php?cid=60E\" /></a></p>\n<p>Die CSW* wünscht allen Cachern viel Erfolg bei der Suche eine schöne <a href=\"http://opencaching.de/OC1346E\">Ad(E)vent</a>szeit, frohe Weihnachten und einen guten Rutsch in\'s neue Jahr!</p>\n<p><img src=\"https://s3.amazonaws.com/gs-geo-images/5884a226-5c34-4c85-8c63-407cff39a745_l.png\" alt=\"5884a226-5c34-4c85-8c63-407cff39a745_l.p\" width=\"100\" /></p>\n<p>\n<br /><br /></p>\n<table><tbody><tr><td><strong>S1: Absturzstelle GelbusSpekulatius Alpha (N 65Â° 36.772 E 168Â° 05.392)</strong></td>\n</tr><tr><td>Hier findet ihr GelbusSpekulatius Alpha. Er wird euch sicherlich gegen eine Entschuldigung für den Unfall einen Hinweis (eine Ziffer) für \"S1\" geben. Mehr wird hier nicht benötigt und es kann auch schon weiter gehen.<br /><br /></td>\n<td>Â </td>\n</tr><tr><td><strong>S2: Das erste Geschenk</strong></td>\n</tr><tr><td>Hier muss irgendwo das erste Geschenk gelandet sein. Ganz wichtig: Geschenk Nicht! öffnen, sondern schütteln und hören. Beachtet dabei bitte die beigelegte Stationsbeschreibung.<br /><br /></td>\n</tr><tr><td><strong>S3: Außenstation der Wichtel</strong></td>\n</tr><tr><td>Die Wichtel haben überall Außenstationen im ganzen Land um die Weihnachtsmann bei seiner Aufgabe zu unterstützen. Sie können dir sicherlich auch einen Hinweis (eine Ziffer) für \"S3\" geben (wenn es nicht funktioniert: UV).<br /><br /></td>\n</tr><tr><td><strong>S4: Das zweite Geschenk</strong></td>\n</tr><tr><td>In unmittelbarer Nähe muss Geschenk zwei gelandet sein. Auch hier gilt wieder: Geschenk nicht öffnen und nur hören! Die Anweisungen gab es ja bereits an Station 2. <br /><br /></td>\n</tr><tr><td><strong>S5: Das dritte Geschenk</strong></td>\n</tr><tr><td>Ah, dort muss das letzte Geschenk gelandet sein. Als guter Aushilfswichtel sollte das Erhören des Inhalts mittlerweile kein Problem sein. Nun aber auf weiter zur letzten Station. Achtet bitte darauf NICHT auf den Hof des Bauern zu laufen sondern an der Y-Kreuzung links anzubiegen! <br /><br /></td>\n</tr><tr><td><strong>S6: Zu Besuch auf Olis Wiese</strong></td>\n</tr><tr><td>Hier trefft ihr einen alten Bekannten wieder: Oli, das Schaf. Allerdings ist er bei der Futterwahl etwas verunsichert. Vielleicht könnt ihr Ihm helfen um so den Hinweis (eine Ziffer) für \"S6\" zu erhalten. Außerdem findet ihr in seinem Keller die Formel fürs Final. <br /><br /></td>\n</tr><tr><td><strong>Final: Geschenkabgabe beim Weihnachtsmann</strong></td>\n</tr><tr><td>Gebt hier beim Weihnachtsmann die Geschenke ab und tragt euch als Dankeschön in Weihnachtsbuch ein. </td>\n</tr></tbody></table><p>Das Logpasswort lautet: <strong>ALASKA</strong></p>',1,1,'Station 1: Notiert die blaue Zahl. Mehr müsst ihr hier nicht machen.<br />\r\nStation 6: In Olis Keller findet ihr Anweisungen für die Station sowie die Formel fürs Final<br />\r\nFinalformel: Quersumme der letzten VIER Ziffern Nord: 20, Ost: 18',''),
 (15,'63a95de6-4047-11eb-96df-0242ac120002',4,'2020-12-18 09:59:49','2020-12-18 10:04:06',15,'RU','<p style=\"text-align:center;\"><span style=\"font-size:small;\"><em><strong>### Dieser Testcache basiert auf OCEE9C ###</strong></em></span></p>\n<p>Â </p>\n<p>In diesem WO BIN ICH dreht sich alles um dieses Objekt hier:<br /><img src=\"http://www.clanfamily.de/gc/GC3A735/wobinich.jpg\" alt=\"wobinich.jpg\" /><br /><br />\nDamit wir nicht einfach eine Dose im Vorgarten vergraben müssen, machen \nwir daraus einen Kurz-Multi. Um das Logbuch zu finden - findet zunächst \ndas gesuchte Objekt. Stellt euch am Zaun davor und Peilt auf 350Â° - \n230m.<br /><br />\nNun haben wir selber mal bei anderen Caches dieser Art festgestellt, \ndass man als Ortsunkundiger schnell ein Problem hat, gerade was solche \nObjekte angeht. Wir haben uns deshalb überlegt, dass wir ein \nYouTube-Video als Spoiler zur Verfügung stellen. Doch einfach so dahin \nklatschen wollen wir Euch das Video nun auch nicht. Also... Wissen ist \nMacht - nichts Wissen; macht nichts... und wer nichts Weiß muss manchmal\n einen etwas längeren Weg zum Erfolg gehen.<br /><br />\nUpdates:\n</p>\n<ul><li>18.05.2013 Neue Final-KO</li>\n</ul><p>\n</p><hr style=\"width:100%;\" /><p><em>\"kaputte Zeichenkodierungen sind doof..\"</em></p>\n<p>Â </p>\n<p><span class=\"VIiyi\" style=\"font-size:small;\" lang=\"ru\" xml:lang=\"ru\"><span class=\"JLqJ4b ChMk0b\">Ð’ ÑÑ‚Ð¾Ð¼ WHERE AM I Ð²ÑÐµ Ð²Ñ€Ð°Ñ‰Ð°ÐµÑ‚ÑÑ Ð²Ð¾ÐºÑ€ÑƒÐ³ ÑÑ‚Ð¾Ð³Ð¾ Ð¾Ð±ÑŠÐµÐºÑ‚Ð°:</span><span class=\"JLqJ4b\">\n</span><span class=\"JLqJ4b ChMk0b\">wobinich.jpg</span><span class=\"JLqJ4b\"><br /><br /></span><span class=\"JLqJ4b ChMk0b\">Ð§Ñ‚Ð¾Ð±Ñ‹ Ð±Ð°Ð½ÐºÑƒ Ð¿Ñ€Ð¾ÑÑ‚Ð¾ Ð½Ðµ Ð·Ð°ÐºÐ°Ð¿Ñ‹Ð²Ð°Ñ‚ÑŒ Ð²Ð¾ Ð´Ð²Ð¾Ñ€Ðµ, Ð¼Ñ‹ Ð´ÐµÐ»Ð°ÐµÐ¼ Ð¸Ð· Ð½ÐµÐµ ÐºÐ¾Ñ€Ð¾Ñ‚ÐµÐ½ÑŒÐºÑƒÑŽ Ð¼ÑƒÐ»ÑŒÑ‚Ð¸Ð²Ð°Ñ€ÐºÑƒ.</span> <span class=\"JLqJ4b ChMk0b\">Ð§Ñ‚Ð¾Ð±Ñ‹ Ð½Ð°Ð¹Ñ‚Ð¸ Ð±Ð¾Ñ€Ñ‚Ð¾Ð²Ð¾Ð¹ Ð¶ÑƒÑ€Ð½Ð°Ð» - ÑÐ½Ð°Ñ‡Ð°Ð»Ð° Ð½Ð°Ð¹Ð´Ð¸Ñ‚Ðµ Ð¾Ð±ÑŠÐµÐºÑ‚, ÐºÐ¾Ñ‚Ð¾Ñ€Ñ‹Ð¹ Ð¸Ñ‰ÐµÑ‚Ðµ.</span> <span class=\"JLqJ4b ChMk0b\">Ð’ÑÑ‚Ð°Ð½ÑŒÑ‚Ðµ Ñƒ Ð·Ð°Ð±Ð¾Ñ€Ð° Ð¿ÐµÑ€ÐµÐ´ Ð½Ð¸Ð¼ Ð¸ Ð¿Ñ€Ð¸Ñ†ÐµÐ»Ð¸Ñ‚ÐµÑÑŒ Ð½Ð° ÑƒÐ³Ð¾Ð» 350 Â° - 230Ð¼.</span><br /><br /><span class=\"JLqJ4b\">\n</span><span class=\"JLqJ4b ChMk0b\">Ð¢ÐµÐ¿ÐµÑ€ÑŒ Ð¼Ñ‹ Ð¾Ð±Ð½Ð°Ñ€ÑƒÐ¶Ð¸Ð»Ð¸, Ñ‡Ñ‚Ð¾ Ñƒ Ð½Ð°Ñ ÐµÑÑ‚ÑŒ Ð´Ñ€ÑƒÐ³Ð¸Ðµ ÐºÐµÑˆÐ¸ ÑÑ‚Ð¾Ð³Ð¾ Ñ‚Ð¸Ð¿Ð°, Ð¸ ÐµÑÐ»Ð¸ Ð²Ñ‹ Ð½Ðµ Ð·Ð½Ð°ÐµÑ‚Ðµ Ð¸Ñ… Ð¼ÐµÑÑ‚Ð¾Ð¿Ð¾Ð»Ð¾Ð¶ÐµÐ½Ð¸Ðµ, Ñƒ Ð²Ð°Ñ Ð±Ñ‹ÑÑ‚Ñ€Ð¾ Ð²Ð¾Ð·Ð½Ð¸ÐºÐ°ÐµÑ‚ Ð¿Ñ€Ð¾Ð±Ð»ÐµÐ¼Ð°, Ð¾ÑÐ¾Ð±ÐµÐ½Ð½Ð¾ ÐºÐ¾Ð³Ð´Ð° Ð´ÐµÐ»Ð¾ ÐºÐ°ÑÐ°ÐµÑ‚ÑÑ Ñ‚Ð°ÐºÐ¸Ñ… Ð¾Ð±ÑŠÐµÐºÑ‚Ð¾Ð².</span> <span class=\"JLqJ4b ChMk0b\">ÐŸÐ¾ÑÑ‚Ð¾Ð¼Ñƒ Ð¼Ñ‹ Ñ€ÐµÑˆÐ¸Ð»Ð¸ ÑÐ´ÐµÐ»Ð°Ñ‚ÑŒ Ð²Ð¸Ð´ÐµÐ¾ Ñ YouTube Ð² ÐºÐ°Ñ‡ÐµÑÑ‚Ð²Ðµ ÑÐ¿Ð¾Ð¹Ð»ÐµÑ€Ð°.</span> <span class=\"JLqJ4b ChMk0b\">ÐÐ¾ Ð¼Ñ‹ Ð½Ðµ Ñ…Ð¾Ñ‚Ð¸Ð¼ Ð¸ Ð²Ð°Ð¼ Ð°Ð¿Ð»Ð¾Ð´Ð¸Ñ€Ð¾Ð²Ð°Ñ‚ÑŒ Ð²Ð¸Ð´ÐµÐ¾.</span> <span class=\"JLqJ4b ChMk0b\">Ð˜Ñ‚Ð°Ðº ... Ð·Ð½Ð°Ð½Ð¸Ðµ - ÑÐ¸Ð»Ð° - Ð½Ð¸Ñ‡ÐµÐ³Ð¾ Ð½Ðµ Ð·Ð½Ð°Ð½Ð¸Ðµ;</span> <span class=\"JLqJ4b ChMk0b\">Ð½Ðµ Ð¸Ð¼ÐµÐµÑ‚ Ð·Ð½Ð°Ñ‡ÐµÐ½Ð¸Ñ ... Ð° ÐµÑÐ»Ð¸ Ð²Ñ‹ Ð½Ð¸Ñ‡ÐµÐ³Ð¾ Ð½Ðµ Ð·Ð½Ð°ÐµÑ‚Ðµ, Ð²Ð°Ð¼ Ð¸Ð½Ð¾Ð³Ð´Ð° Ð¿Ñ€Ð¸Ð´ÐµÑ‚ÑÑ Ð¿Ñ€Ð¾Ð¹Ñ‚Ð¸ Ð±Ð¾Ð»ÐµÐµ Ð´Ð»Ð¸Ð½Ð½Ñ‹Ð¹ Ð¿ÑƒÑ‚ÑŒ Ðº ÑƒÑÐ¿ÐµÑ…Ñƒ.</span><span class=\"JLqJ4b\"><br /><br /></span><span class=\"JLqJ4b ChMk0b\">ÐžÐ±Ð½Ð¾Ð²Ð»ÐµÐ½Ð¸Ñ:</span><span class=\"JLqJ4b\">Â </span></span></p>\n<ul><li><span class=\"VIiyi\" style=\"font-size:small;\" lang=\"ru\" xml:lang=\"ru\"><span class=\"JLqJ4b ChMk0b\">18 Ð¼Ð°Ñ 2013 Ð³. ÐÐ¾Ð²Ñ‹Ð¹ Ð½Ð¾ÐºÐ°ÑƒÑ‚-Ñ„Ð¸Ð½Ð°Ð»</span></span></li>\n</ul>',1,1,'YouTube Link: http://youtu.be/lxzMGmK-CrM<br />\r\nÐ¡ÑÑ‹Ð»ÐºÐ° Ð½Ð° YouTube: http://youtu.be/lxzMGmK-CrM',''),
 (16,'3c80a890-4049-11eb-96df-0242ac120002',4,'2020-12-18 10:13:03','2020-12-18 10:13:03',16,'DE','<p style=\"text-align:center;\"><em><strong>### Dieser Testcache basiert auf OC14E3B ###</strong></em><br /><br /></p>\n<p style=\"text-align:justify;\">Dieser Cache ist auf einer anderen Plattform im Zuge des Fördesteigs gepublisht, aber ich möchte ihn der Opencaching-Community nicht vorenthalten - gerade weil das Fördesteigprojekt hier nicht unbedingt so begeistert aufgenommen wurde. Aber das soll nur am Rande erwähnt bleiben und uns nicht länger aufhalten. Hier kommt nun ein Physik-Cache hin!</p>\n<p style=\"text-align:justify;\">Â </p>\n<p style=\"text-align:center;\"><img src=\"http://www.gchn.de/Daten/allgemein/GCHN_Logos/GCHN_Logo_120.jpg\" alt=\"\" width=\"120\" height=\"141\" /></p>\n<p style=\"text-align:center;\">Â </p>\n<p style=\"text-align:justify;\">Der Cache an sich steht so an den angegebenen Koordinaten und ist so vom Grundstückseigentümer abgesegnet. Aber um ans Logbuch zu gelangen müsst ihr hier die astronomisch schweren Aufgaben lösen!</p>\n<p style=\"text-align:justify;\">Â </p>\n<p style=\"text-align:justify;\">1. Wie viele Planeten gibt es nach alter Zählweise in unserem Sonnensystem?</p>\n<p style=\"text-align:justify;\">2. Wie viele Apollomissionen landeten auf dem Mond? - 1</p>\n<p style=\"text-align:justify;\">3. Wie viele AE (Astronomische Einheiten) ist die Erde im Mittel von der Sonne entfernt?</p>\n<p style=\"text-align:justify;\">Â </p>\n<p style=\"text-align:justify;\">Mit diesem Wissen werdet ihr vor Ort dann auch ans Logbuch gelangen! Viel Spaß!</p>\n<p>Â </p>\n<p><img style=\"display:block;margin-left:auto;margin-right:auto;\" src=\"https://upload.wikimedia.org/wikipedia/commons/thumb/0/0b/Moon_Sketch_vector.svg/2000px-Moon_Sketch_vector.svg.png\" alt=\"\" width=\"200\" height=\"200\" /></p>',1,1,'','Ein kleiner interstellarer Cache in Glücksburg...'),
 (17,'bf7a6b55-4049-11eb-96df-0242ac120002',4,'2020-12-18 10:16:42','2020-12-18 10:16:42',17,'DE','<p style=\"text-align:center;\"><em><strong>### Dieser Testcache basiert auf OC12E53 ###</strong></em><br /><br /></p>\n<table style=\"background-color:#b8e4e9;\" border=\"1\" cellpadding=\"1\" align=\"center\"><tbody><tr><td>\n<span style=\"font-size:small;\"><img style=\"float:left;\" src=\"resource2/ocstyle/images/attributes/moving.png\" border=\"0\" alt=\"\" width=\"52\" height=\"49\" /> Bewegliche Caches gibt es nur bei Opencaching. Sie können nicht nur \ngesucht und geloggt, sondern danach auch mitgenommen und an einem \nanderen Ort wieder versteckt werden. (Natürlich kann man sie aber auch \neinfach nur finden, loggen und am gleichen Ort wiederverstecken.)\n</span>\n</td>\n</tr></tbody></table><p>Â </p>\n<p><span style=\"font-size:small;\"><strong>Die Wanderdose ist ein beweglicher Cache, der schöne Spazierwege im Münchner Umland (S-Bahn-Bereich) zeigen soll. </strong></span></p>\n<p><span style=\"font-size:small;\"><strong>Damit die Wanderdose möglichst weit herum kommt wäre es schön, wenn sie ein Stück mit Dir reisen dürfte:</strong></span></p>\n<ul><li><span style=\"font-size:small;\">Suche den Cache an den angegebenen Koordinaten und logge ihn online als Fund. <em><br />(Eine kleine Anmerkung wann es weitergehen soll wäre super.) </em></span></li>\n<li><span style=\"font-size:small;\">Suche ein neues Versteck für die Dose in der Nähe eines Spazierweges im Münchner Umland und deponiere sie dort. <em><br />(Vergiss nicht, Dir die Koordinaten zu notieren!) </em></span></li>\n<li><span style=\"font-size:small;\">Schreibe online eine Notiz mit den neuen Koordinaten. <em><br />(Ein Hinweis zum neuen Versteck oder falls sich die D-/T-Wertung geändert hat wäre ggf. auch praktisch.)   <br /></em></span></li>\n</ul><p>Â </p>\n<p><span style=\"font-size:small;\"><img style=\"vertical-align:middle;display:block;margin-left:auto;margin-right:auto;\" src=\"images/uploads/B7071215-110D-11E6-9D14-0A81D9C03CF3.jpg\" alt=\"Otterfing\" width=\"439\" height=\"246\" /></span></p>\n<p style=\"text-align:center;\"><span style=\"font-size:small;\"><strong>Reiseverlauf:</strong></span><br /><span style=\"font-size:small;\">\nStart - Otterfing: N 47Â° 54.165  E 011Â° 41.539 </span><br /><span style=\"font-size:small;\">\n1. Station - Markt Schwaben: N 48Â° 11.524  E 011Â° 53.232 <em>(Danke an Biman!)</em></span><br /><span style=\"font-size:small;\">\n2. Station - Unterschleißheim: N 48Â° 16.002 E 011Â° 33.963 <em>(Danke an habined1!)</em></span><br /><span style=\"font-size:small;\">\n3. Station - Siegertsbrunn: N 48Â° 00.679 E 011Â° 44.005 <em>(Danke an Eddiemuc!)</em></span><br /><span style=\"font-size:small;\">\n4. Station - Grünwalder Forst: N 48Â° 01.418 E 011Â° 31.471 <em>(Danke an Schatzforscher!)</em></span><br /><span style=\"font-size:small;\">\n5. Station - Gröbenzell: N 48Â° 12.228 E 011Â° 21.802 <em style=\"letter-spacing:0px;\">(Danke an bear2006!)</em></span><span style=\"font-size:small;\"><br />\n6. Station - Langwieder See: N 48Â° 11.776 E 011Â° 25.040<em> (Danke an mape180!)</em></span><span style=\"font-size:small;\"><br />\n7. Station - Gierlinger Park: N 48Â° 05.853  E 011Â° 32.532<em> (Danke an Fredymaus!)</em></span><span style=\"font-size:small;\"><br />\n8. Station - Weltwald Freising: N 48Â° 24.872 E 011Â° 40.481<em> (Danke an rkschlotte!)</em><span style=\"font-size:small;\"><br />\n9. Station - Walderlebnispfad Kuhfluchtwasserfälle: N 47Â° 31.815 E 011Â° 07.242<em> (Danke an Yarkos!)</em><span style=\"font-size:small;\"><br />\n10. Station - Icking: N 47Â° 57.750 E 011Â° 26.217<em> (Danke an unertl!)</em><span style=\"font-size:small;\"><br />\n11. Station - Frötmaninger Berg: N 48Â° 12.824 E 011Â° 37.715 <em>(Danke an Puttenchor!)</em><span style=\"font-size:small;\"> </span></span></span></span></span></p>\n<p style=\"text-align:center;\"><span style=\"font-size:small;\"><strong>aktuell: =&gt;   <strong><span style=\"font-size:small;\"><span style=\"font-size:small;\"> <span>N 48Â° 12.824</span><span> E 011Â° 37.715</span></span></span></strong></strong></span></p>\n<p style=\"text-align:center;\"><span style=\"font-size:small;\"><strong><strong><span style=\"font-size:small;\"><span style=\"font-size:small;\"><span><img style=\"vertical-align:middle;\" src=\"images/uploads/C21659EA-5C00-11EA-8DF9-D516ED642EB6.jpg\" alt=\"Karte\" /></span></span></span></strong></strong></span></p>\n<p style=\"text-align:center;\"><span style=\"font-size:small;\"><span style=\"font-size:small;\"><span style=\"font-size:small;\"><span style=\"font-size:xx-small;\"><em>(Karte: OSM/flopp-caching.de)</em></span></span></span></span></p>',1,1,'','...unterwegs im Münchner Umland...'),
 (18,'ab40370a-404a-11eb-96df-0242ac120002',4,'2020-12-18 10:23:18','2020-12-18 10:24:30',18,'DE','<p style=\"text-align:center;\"><em><strong>### Dieser Testcache basiert auf OCFD54 ###</strong></em><br /><br /></p>\n<div style=\"width:670px;font-family:Arial, Helvetica, sans-serif;font-size:10pt;\">\n<div style=\"height:400px;width:670px;float:left;background:url(&quot;http://gc.clanfamily.de/ocfd54/kopf.jpg&quot;) no-repeat;\"> </div>\n<div style=\"width:610px;float:left;background:url(&quot;http://gc.clanfamily.de/ocfd54/content.jpg&quot;) repeat;padding:0px 30px;text-align:justify;\">\n	\nDie Duisburger Traditionsbrauerei kurz \"KöPi\" ist bereits über 100 Jahre bekannt. Sie bildet das Bild vom Stadtteil Beek wie kein anderes Unternehmen. Bevor ich jetzt seitenweise Wikipedia zitiere [<a href=\"http://de.wikipedia.org/wiki/K%C3%B6nig-Brauerei\">nachlesen</a>] komme ich direkt zur Aufgabe des Caches.<br /><br /><hr style=\"width:600px;border:2px dashed #99cc33;\" /><strong>So solltet ihr diesen Cache loggen...</strong><br />\n- besucht die Brauerei vor Ort und nehmt euer GPS/Smartphone/Karte/Kompass mit<br />\n- Macht ein Foto von/mit euch vor dem Kupferkessel<br />\n- Loggt mit Foto auf opencaching.de<br /><br />\nBeispiel:<br /><img src=\"http://gc.clanfamily.de/ocfd54/spoiler.jpg\" alt=\"spoiler.jpg\" /><br /><br />\nWer als Tourist nach Duisburg kommt - die Brauerei bietet auch Besichtigungstermine an!<br />\nAllerdings ohne anschließender Verköstigung :)\n</div>\n<div style=\"height:200px;width:670px;background:url(&quot;http://gc.clanfamily.de/ocfd54/footer.jpg&quot;) no-repeat;float:left;padding-left:30px;padding-top:60px;font-size:8pt;\">\n<p>\nDesign Â© Clanfamily.de<br />\nDie Verantwortung für jeden GeoCache übernimmt der Cacher selbst.<br />\nWir bitten euch: schont die Natur, wie auch wir dies beim legen unseres Cache berücksichtigt haben.</p>\n<p>Â </p>\n<p><em><strong>---</strong></em></p>\n<p><em><strong>Logpasswort: KÖNIG</strong></em></p>\n</div>\n</div>',1,1,'','Das Duisburger Traditionsbrauhaus.'),
 (19,'ae8d494e-404b-11eb-96df-0242ac120002',4,'2020-12-18 10:30:33','2020-12-18 10:31:42',19,'DE','<p style=\"text-align:center;\"><em><strong>### Dieser Testcache basiert auf OC15A42 ###</strong></em></p>\n<p>Â </p>\n<p style=\"text-align:center;\"><br /><span style=\"font-size:medium;\"><strong>Kein Cache ist es wert, sich oder andere in Gefahr zu bringen!</strong></span></p>\n<hr size=\"4\" /><p><br /><a rel=\"noreferrer noopener\" href=\"https://www.opencaching.de/viewcache.php?cacheid=173943\" target=\"_blank\"><img style=\"display:none;\" title=\"Der Fahrplan\" src=\"https://www.4shared.com/img/LICRRB7nfi/s25/169d2f8ce78/Werbebanner_deutsch_\" border=\"0\" alt=\"Werbebanner_deutsch_\" width=\"700\" height=\"168\" /></a><br /><br />\nMein Opa hat mir einmal die Geschichte über Drohnen erzählt<br /><br />\nDie fliegen hoch über unseren Köpfen und machen fleißig Photos. Manchmal lassen sie auch \"Dinge\" fallen, die Menschen sehr weh tun.<br />\nIch kann das gar nicht glauben, wenn ich in den Himmel schaue. Da ist doch nichts zu sehen außer Engeln?<br />\nOpa bewies mir das Gegenteil. Er stellte mit seinem \"Walkie-Talkie\" eine Funk-Verbindung zu so einem Ding her und steuerte damit die Kamera für ein Photo von der Erde.<br />\nPotzblitz! Da ist Opa zu sehen, mit seinem Spezialfahrrad. Und neben ihm, hinter der Hecke: <strong>ich</strong> <img title=\"Cool\" src=\"resource2/tinymce/plugins/emotions/img/smiley-cool.gif\" border=\"0\" alt=\"Cool\" />.<br /><a rel=\"noreferrer noopener\" href=\"https://www.opencaching.de/images/uploads/6D73B57A-C34B-11E9-9ECD-0AAA1AAB5A07.jpg\" target=\"_blank\"><img style=\"vertical-align:middle;\" title=\"Logproof\" src=\"images/uploads/6D73B57A-C34B-11E9-9ECD-0AAA1AAB5A07.jpg\" alt=\"Logproof\" width=\"200\" /></a>\n<br /><br /><br /><em>Was tun? Begebt Euch zum Flensburger Segel-Club an den Startkoordinaten. Verbindet Euch mit der <a title=\"FSC Webcam2\" rel=\"noreferrer noopener\" href=\"http://62.214.4.38/view/view.shtml?id=5544&amp;imagepath=%2Fmjpg%2Fvideo.mjpg&amp;size=1&amp;streamprofile=Bandwidth\" target=\"_blank\">Webcam2 des FSC</a>. Macht ein Photo <strong>damit</strong> von Euch oder einem markanten persönlichem Gegenstand. Ladet das Photo hoch.<br /><span>Sollte die Webcam ausgefallen oder kein Internet verfügbar sein oder Ihr anonym bleiben wollen, dürft <strong>Ihr</strong> alternativ/umgekehrt <strong>die Webcam</strong> knipsen - mit einem persönlichen Gegenstand vor der Linse!</span>\n</em><br /><br /></p>\n<hr size=\"4\" /><p>Â </p>\n<div> <span style=\"font-size:large;\">Bus: Linie <strong>21</strong>, Haltestelle \"Quellental\".</span></div>\n<p><br /><a rel=\"noreferrer noopener\" href=\"http://www.handicaching.com/show.php?waypoint=OC15A42\" target=\"_blank\"><img title=\"Handicap-Wertung\" src=\"https://www.opencaching.de/images/uploads/F1D8F644-2DA9-11E5-9CA8-525400E33611.jpg\" border=\"0\" alt=\"Handicap-Wertung\" /> <img src=\"https://www.opencaching.de/images/uploads/403BE2B2-946F-11E7-BEE1-E297132BC419.jpg\" alt=\"Für Rollstuhl\" hspace=\"5\" width=\"130\" height=\"130\" /> <img src=\"https://www.opencaching.de/images/uploads/70BD83BA-946F-11E7-BEE1-E297132BC419.jpg\" alt=\"Bus hält\" hspace=\"5\" width=\"130\" height=\"130\" /></a></p>',1,1,'Brötchenfahrradständer am Restaurant-Zuweg aufsuchen und Position &quot;kleine Halle&quot; aus dem Menü oben auf der Website wählen.','Opa erzählt: 1000und1 Nacht #86'),
 (20,'fd1b9afe-404c-11eb-96df-0242ac120002',4,'2020-12-18 10:39:54','2020-12-18 10:39:54',20,'DE','<p style=\"text-align:center;\"><em><strong>### Dieser Testcache basiert auf OC1284B ###</strong></em><br /><br /></p>\n<p style=\"text-align:center;\"><span style=\"font-size:small;\">Nachdem in diesem Jahr die Anzahl der OC Dosen in dieser Region in 2015 um einige angestiegen ist, </span></p>\n<p style=\"text-align:center;\"><span style=\"font-size:small;\">ist es nun am Ende des Jahres Zeit für ein OC-Only Event in Trier.</span></p>\n<p style=\"text-align:center;\"><span style=\"font-size:small;\"><br /></span></p>\n<p style=\"text-align:center;\"><span style=\"font-size:medium;\">Wir treffen uns</span></p>\n<p style=\"text-align:center;\"><span style=\"font-size:medium;\"> am <strong>Mittwoch</strong>,<strong> 09.12.2015</strong> um <strong>20:30</strong> Uhr </span></p>\n<p style=\"text-align:center;\"><span style=\"font-size:medium;\">im <strong>Bitburger Wirtshaus </strong>am <strong>Kornmarkt</strong> in <strong>Trier</strong>.</span></p>\n<p style=\"text-align:center;\">Â </p>\n<p style=\"text-align:justify;\"><span style=\"font-size:small;\">Das Event ist  zu einen zum Austauch von Erfahrungen und Ideen gedacht und zu anderen, um einfach mal die Leute kennenzulernen, die Dosen bei OC suchen und finden. Die Location ist nicht reserviert oder gebucht. Ich denke, das geht auch so. Haltet im Wirtshaus einfach Ausschau nach einem Logbuch mit OC-Logo (oder so...wir werden uns schon irgendwie kenntlich machen <img title=\"Lachend\" src=\"resource2/tinymce/plugins/emotions/img/smiley-laughing.gif\" border=\"0\" alt=\"Lachend\" />).</span></p>\n<p style=\"text-align:center;\"><span style=\"font-size:small;\">Freue mich über jeden, der vorbeischaut. <img title=\"Lächelnd\" src=\"resource2/tinymce/plugins/emotions/img/smiley-smile.gif\" border=\"0\" alt=\"Lächelnd\" />.</span></p>',1,1,'','Gemütlicher Plausch im Bitburger Wirtshaus'),
 (21,'adf744cb-404d-11eb-96df-0242ac120002',4,'2020-12-18 10:44:51','2020-12-18 10:44:51',21,'EN','<p style=\"text-align:center;\"><strong><em>### Dieser Testcache basiert auf OC107A ###</em></strong><br /><br /><br />\nIt\'s not very simple, but it\'s a simple locationless cache: You have to show the Laputian Salute in a certain way:\n</p>\n<ul><li> Move from your home two miles and fivehundred meters towards the last salute-position. If you don\'t have a home start at the local bakery.</li>\n<li> Turn towards your predecessor and show him or her the Laputian Salute.</li>\n<li> Take a picture of yourself and your GPSr.</li>\n<li> Take the coordinates so the next person can show you the Laputian Salute.</li>\n<li> If you like to, you can include a short explanation why you are showing the salute.</li>\n</ul><p>\n\nSince we have to start somewhere, the first cacher can show the salute to the occupant of this building.\n</p>\n<p>Â </p>\n<p> <img src=\"https://www.opencaching.de/images/uploads/E21242A8-0FEB-5772-6C86-3E480C8C5A34.jpg\" border=\"0\" alt=\"E21242A8-0FEB-5772-6C86-3E480C8C5A34.jpg\" /></p>\n<p>Â </p>\n<p>\n<strong>Long live Laputa!</strong></p>\n<p>Â </p>\n<p>\nFurther information about The Free Republic of Laputa might be found <a rel=\"noreferrer noopener\" href=\"http://laputa.de/\" target=\"_blank\">here</a></p>',1,1,'','Show the Laputian Salute'),
 (22,'5dd94d09-4051-11eb-96df-0242ac120002',4,'2020-12-18 11:11:15','2020-12-18 11:15:32',22,'DE','<p>Das ist ein Listing..</p>',1,1,'',''),
 (23,'ee63495c-4052-11eb-96df-0242ac120002',4,'2020-12-18 11:22:27','2020-12-18 11:22:27',23,'DE','<p>[insert text here]</p>',1,1,'',''),
 (24,'e6ea87de-63c3-11eb-9f28-0242ac120003',4,'2021-02-01 13:50:43','2021-02-01 13:50:43',24,'EN','',1,1,'','')
;

INSERT INTO `cache_logs` (`id`, `uuid`, `node`, `date_created`, `entry_last_modified`, `last_modified`, `okapi_syncbase`, `log_last_modified`, `cache_id`, `user_id`, `type`, `oc_team_comment`, `date`, `order_date`, `needs_maintenance`, `listing_outdated`, `text`, `text_html`, `text_htmledit`, `owner_notified`, `picture`)
VALUES
 (1,'3722f4ce-403d-11eb-96df-0242ac120002',5,'2020-12-18 08:47:00','2020-12-18 08:47:00','2020-12-18 08:55:40','2020-12-18 08:55:40','2020-12-18 08:55:40',9,170291,3,0,'2020-12-18 00:00:00','2020-12-18 08:47:00',2,2,'<p><span style=\"font-size:large;\">Oh, wie schade, gerade eben wurde der Cache gepublisht und schon funktioniert der Webcamlink nicht mehr. </span></p>\n<p><span style=\"font-size:large;\"><img title=\"Frown\" src=\"resource2/tinymce/plugins/emotions/img/smiley-frown.gif\" border=\"0\" alt=\"Frown\" /></span></p>',1,1,0,0),
 (2,'72ac8550-403d-11eb-96df-0242ac120002',5,'2020-12-18 08:48:40','2020-12-18 08:48:53','2020-12-18 08:55:40','2020-12-18 08:55:40','2020-12-18 08:55:40',9,170290,11,0,'2020-12-18 00:00:00','2020-12-18 08:48:40',2,0,'<p>Oh, da brat mir doch einer \'nen Storch.. der Link funtioniert ja tatsächlich nicht. Ich schaue mir das an.</p>',1,1,0,0),
 (3,'6d7e6a68-403e-11eb-96df-0242ac120002',5,'2020-12-18 08:55:40','2020-12-18 08:57:37','2020-12-18 08:57:37','2020-12-18 08:57:37','2020-12-18 08:57:37',9,170290,9,0,'2020-12-18 00:00:00','2020-12-18 08:55:40',0,0,'<p>Oh, die Webcam samt Waschstraße und Storch sind nicht mehr existent. Ich muss den Cache daher archivieren. #Publish\'n\'Archive</p>',1,1,0,0),
 (4,'8cb94bda-4042-11eb-96df-0242ac120002',5,'2020-12-18 09:25:11','2020-12-18 09:25:11','2020-12-18 09:25:11','2020-12-18 09:25:11','2020-12-18 09:25:11',3,170293,1,0,'2020-12-18 00:00:00','2020-12-18 09:25:11',1,0,'<p><span style=\"font-size:small;\">Juhu! Erster! Danke  für den Cache!</span> <img title=\"Laughing\" src=\"resource2/tinymce/plugins/emotions/img/smiley-laughing.gif\" border=\"0\" alt=\"Laughing\" /></p>',1,1,0,0),
 (5,'888f4dab-4047-11eb-96df-0242ac120002',5,'2020-12-18 10:00:51','2020-12-18 10:01:41','2020-12-18 10:01:41','2020-12-18 10:01:41','2020-12-18 10:01:41',15,170294,1,0,'2020-12-18 00:00:00','2020-12-18 10:00:51',0,0,'<p>Lösch mich..!</p>',1,1,0,0),
 (6,'be6104a2-404d-11eb-96df-0242ac120002',5,'2020-12-18 10:45:18','2020-12-18 10:45:18','2020-12-18 10:45:18','2020-12-18 10:45:18','2020-12-18 10:45:18',10,170292,8,0,'2020-12-18 00:00:00','2020-12-18 10:45:18',0,0,'<p>Da bin ich gerne dabei!</p>',1,1,0,0),
 (7,'9a7c5f12-4051-11eb-96df-0242ac120002',5,'2020-12-18 11:12:56','2020-12-18 11:12:56','2020-12-18 11:12:56','2020-12-18 11:12:56','2020-12-18 11:12:56',3,170298,1,0,'2020-12-18 00:00:00','2020-12-18 11:12:56',0,0,'<p>FOUND! LOL! rofl!</p>',1,1,0,0),
 (8,'f77bf871-4051-11eb-96df-0242ac120002',5,'2020-12-18 11:15:32','2020-12-18 11:15:32','2020-12-18 11:15:32','2020-12-18 11:15:32','2020-12-18 11:15:32',22,107469,13,1,'2020-12-18 00:00:00','2020-12-18 11:15:32',0,0,'Der Benutzeraccount wurde deaktiviert.',0,1,0,0),
 (9,'0d5d4831-4053-11eb-96df-0242ac120002',5,'2020-12-18 11:23:18','2020-12-18 11:23:18','2020-12-18 11:23:18','2020-12-18 11:23:18','2020-12-18 11:23:18',3,170299,1,0,'2020-12-18 00:00:00','2020-12-18 11:23:18',0,0,'<p>auch gfunden!</p>',1,1,0,0)
;

INSERT INTO `cache_reports` (`id`, `date_created`, `cacheid`, `userid`, `reason`, `note`, `status`, `adminid`, `lastmodified`, `comment`)
VALUES
 (1,'2020-12-18 11:27:53',9,170299,3,'Der Webcamlink funktioniert ja überhaupt nicht!!!1!11!',1,NULL,'2020-12-18 11:27:53','')
;

INSERT INTO `cache_status_modified` (`cache_id`, `date_modified`, `old_state`, `new_state`, `user_id`)
VALUES
 (3,'2020-12-17 13:46:34',5,1,107469),
 (9,'2020-12-18 08:48:40',1,2,170290),
 (9,'2020-12-18 08:55:40',2,3,170290),
 (22,'2020-12-18 11:15:32',1,6,107469)
;

INSERT INTO `cache_visits` (`cache_id`, `user_id_ip`, `count`, `last_modified`)
VALUES
 (1,'107469',1,'2021-02-01 14:08:30'),
 (3,'0',3,'2020-12-18 11:22:54'),
 (3,'107469',1,'2020-12-18 11:16:08'),
 (3,'170293',3,'2020-12-18 09:25:19'),
 (3,'170298',2,'2020-12-18 11:12:58'),
 (3,'170299',2,'2020-12-18 11:23:21'),
 (4,'0',1,'2020-12-18 13:27:21'),
 (4,'107469',1,'2020-12-18 13:27:21'),
 (4,'170289',3,'2020-12-18 13:29:22'),
 (5,'170289',3,'2020-12-17 14:31:25'),
 (6,'170289',3,'2020-12-17 14:44:14'),
 (8,'0',1,'2020-12-18 08:45:02'),
 (8,'170291',2,'2020-12-18 09:11:19'),
 (9,'0',3,'2021-02-16 09:34:04'),
 (9,'107469',1,'2021-02-16 09:34:04'),
 (10,'0',2,'2020-12-18 13:09:38'),
 (10,'107469',2,'2020-12-18 13:10:48'),
 (10,'170291',4,'2020-12-18 09:10:50'),
 (10,'170292',2,'2020-12-18 10:45:20'),
 (12,'0',1,'2020-12-18 11:26:29'),
 (12,'170293',2,'2020-12-18 09:23:41'),
 (12,'170299',1,'2020-12-18 11:26:29'),
 (14,'170294',1,'2020-12-18 09:45:28'),
 (15,'170294',8,'2020-12-18 10:04:20'),
 (17,'0',1,'2021-01-21 18:35:27'),
 (17,'107469',1,'2021-01-21 18:35:27'),
 (18,'170296',2,'2020-12-18 10:24:47'),
 (19,'0',1,'2020-12-18 11:12:12'),
 (19,'170296',3,'2020-12-18 10:33:37'),
 (19,'170298',1,'2020-12-18 11:12:12'),
 (22,'0',1,'2020-12-18 11:15:54'),
 (22,'107469',1,'2020-12-18 11:15:54'),
 (22,'170298',1,'2020-12-18 11:11:17'),
 (23,'170299',1,'2020-12-18 11:22:28'),
 (24,'107469',1,'2021-02-19 07:36:50')
;

INSERT INTO `caches_attributes` (`cache_id`, `attrib_id`)
VALUES
 (1,24),
 (1,40),
 (4,9),
 (4,10),
 (4,11),
 (4,12),
 (4,13),
 (4,14),
 (4,15),
 (4,16),
 (4,17),
 (10,9),
 (10,17),
 (12,18),
 (14,9),
 (14,10),
 (14,11),
 (14,12),
 (14,13),
 (14,14),
 (14,15),
 (14,16),
 (14,17),
 (22,9),
 (22,11),
 (22,13),
 (22,15),
 (22,17),
 (23,10),
 (23,12),
 (23,14),
 (23,16),
 (23,18)
;

INSERT INTO `caches_attributes_modified` (`cache_id`, `attrib_id`, `date_modified`, `was_set`, `restored_by`)
VALUES
 (1,24,'2020-12-18',0,0),
 (1,40,'2020-12-18',0,0),
 (4,9,'2020-12-18',0,0),
 (4,10,'2020-12-18',0,0),
 (4,11,'2020-12-18',0,0),
 (4,12,'2020-12-18',0,0),
 (4,13,'2020-12-18',0,0),
 (4,14,'2020-12-18',0,0),
 (4,15,'2020-12-18',0,0),
 (4,16,'2020-12-18',0,0),
 (4,17,'2020-12-18',0,0)
;

INSERT INTO `caches_modified` (`cache_id`, `date_modified`, `name`, `type`, `date_hidden`, `size`, `difficulty`, `terrain`, `search_time`, `way_length`, `wp_gc`, `wp_nc`, `restored_by`)
VALUES
 (4,'2020-12-18','Töpferstadt Stadtlohn - öäüß',3,'2014-08-08',3,4,8,24,25,'GC57KTC','',0),
 (23,'2021-01-08','\"Mein Cache\" by DSGVO gelöschter Nutzer',2,'2020-12-18',3,5,7,0,0,'','',0)
;

INSERT INTO `coordinates` (`id`, `date_created`, `last_modified`, `type`, `subtype`, `latitude`, `longitude`, `cache_id`, `user_id`, `log_id`, `description`)
VALUES
 (1,'2020-12-17 13:39:39','2020-12-17 13:39:39',1,1,51.96605,6.90405,1,NULL,NULL,'Vor dem Restaurant kann man optimal parken.'),
 (2,'2020-12-17 14:19:16','2020-12-17 14:19:16',1,1,51.98955,6.9158833333333,4,NULL,NULL,'Parkplatz 1: Hier kann man kostenlos parken.'),
 (3,'2020-12-17 14:19:38','2020-12-17 14:19:38',1,1,51.99055,6.91665,4,NULL,NULL,'Parkplatz 2: Hier gibt\'s noch einen kleinen Parkplatz.'),
 (4,'2020-12-17 14:19:54','2020-12-17 14:19:54',1,2,51.99065,6.9160833333333,4,NULL,NULL,'Station 1: Der \"Fegemeister\"'),
 (5,'2020-12-18 09:23:19','2020-12-18 09:23:19',1,1,51.9318,6.8514666666667,12,NULL,NULL,'Bitte NICHT auf dem Parkplatz des Maislabyrinth parken sonden in der \"Parkbucht\" vor dem Zaun.'),
 (6,'2020-12-18 09:23:37','2020-12-18 09:23:37',1,3,51.93075,6.8526,12,NULL,NULL,'Hier könnt ihr den Weg verlassen und durch den Wald Richtung Cache laufen.'),
 (7,'2020-12-18 10:32:03','2020-12-18 10:32:03',1,4,54.84165,9.5311333333333,19,NULL,NULL,'Schiffsanleger und Hauptveranstaltung des 6. OC-HQ-Events.\r\n\r\nShip\'s jetty and the very 6th OC-HQ-event.'),
 (8,'2020-12-18 10:32:26','2020-12-18 10:32:26',1,1,54.835883333333,9.5249833333333,19,NULL,NULL,'Unbegrenzt, kostenlos parken.\r\n\r\nUnlimited, free parking.'),
 (9,'2020-12-18 10:32:46','2020-12-18 10:32:46',1,5,54.832033333333,9.5435333333333,19,NULL,NULL,'Renaissance-Wasserschloß.\r\n\r\nRenaissance-water-castle.')
;

INSERT INTO `email_user` (`id`, `date_created`, `ipaddress`, `from_user_id`, `from_email`, `to_user_id`, `to_email`)
VALUES
 (23765,'2020-12-17 22:06:47','172.18.0.1',170289,'Gustav_0815@example.com',170294,'PeterDerGrosse@example.com'),
 (23766,'2021-01-20 16:41:31','172.18.0.1',107469,'root@localhost',107469,'root@localhost'),
 (23767,'2021-01-20 17:18:29','172.18.0.1',107469,'root@localhost',107469,'root@localhost'),
 (23768,'2021-01-20 17:21:11','172.18.0.1',107469,'root@localhost',170296,'bruker@example.com'),
 (23769,'2021-01-20 17:24:06','172.18.0.1',107469,'root@localhost',170296,'bruker@example.com'),
 (23770,'2021-01-20 17:39:03','172.18.0.1',107469,'root@localhost',170296,'bruker@example.com'),
 (23771,'2021-01-20 17:57:46','172.18.0.1',107469,'root@localhost',170296,'bruker@example.com'),
 (23772,'2021-01-20 18:01:36','172.18.0.1',107469,'root@localhost',170296,'bruker@example.com'),
 (23773,'2021-01-21 18:35:34','172.18.0.1',107469,'root@localhost',170295,'takeshi@example.com'),
 (23774,'2021-01-21 18:38:26','172.18.0.1',107469,'root@localhost',170295,'takeshi@example.com'),
 (23775,'2021-01-21 18:39:29','172.18.0.1',107469,'root@localhost',170295,'takeshi@example.com'),
 (23776,'2021-01-21 18:41:50','172.18.0.1',107469,'root@localhost',170295,'takeshi@example.com'),
 (23777,'2021-01-21 18:44:36','172.18.0.1',107469,'root@localhost',170295,'takeshi@example.com')
;

INSERT INTO `logentries` (`id`, `date_created`, `module`, `eventid`, `userid`, `objectid1`, `objectid2`, `logtext`, `details`)
VALUES
 (1,'2020-12-18 11:15:32','user',6,107469,170298,0,'User Gesperrter Nutzer disabled',_binary 'a:7:{s:8:\"username\";s:17:\"Gesperrter Nutzer\";s:5:\"email\";s:20:\"gesperrt@example.com\";s:9:\"last_name\";s:0:\"\";s:10:\"first_name\";s:0:\"\";s:7:\"country\";s:2:\"DE\";s:8:\"latitude\";i:0;s:9:\"longitude\";i:0;}')
;

INSERT INTO `map2_result` (`result_id`, `slave_id`, `sqlchecksum`, `sqlquery`, `shared_counter`, `request_counter`, `date_created`, `date_lastqueried`)
VALUES
 (1,-1,2437006912,'\nSELECT `caches`.`cache_id` `cache_id` FROM `caches` INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id` WHERE `caches`.`status` NOT IN (3,6,7) AND `caches`.`status`<>2 AND (`cache_status`.`allow_user_view`=1 OR `caches`.`user_id`=170289 OR (`caches`.`status`<>5 AND 0))',0,10,'2020-12-17 14:33:00','2020-12-17 14:33:31'),
 (2,-1,2713476729,'\nSELECT `caches`.`cache_id` `cache_id` FROM `caches` INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id` WHERE `caches`.`status` NOT IN (3,6,7) AND `caches`.`status`<>2 AND (`cache_status`.`allow_user_view`=1 OR `caches`.`user_id`=170291 OR (`caches`.`status`<>5 AND 0))',0,4,'2020-12-18 09:10:33','2020-12-18 09:10:46'),
 (3,-1,2713476729,'\nSELECT `caches`.`cache_id` `cache_id` FROM `caches` INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id` WHERE `caches`.`status` NOT IN (3,6,7) AND `caches`.`status`<>2 AND (`cache_status`.`allow_user_view`=1 OR `caches`.`user_id`=170291 OR (`caches`.`status`<>5 AND 0))',0,7,'2020-12-18 09:11:25','2020-12-18 09:11:32'),
 (4,-1,867506849,'\nSELECT `caches`.`cache_id` `cache_id` FROM `caches` INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id` WHERE `caches`.`status` NOT IN (3,6,7) AND `caches`.`status`<>2 AND (`cache_status`.`allow_user_view`=1 OR `caches`.`user_id`=170295 OR (`caches`.`status`<>5 AND 0))',0,3,'2020-12-18 10:08:52','2020-12-18 10:08:55')
;

INSERT INTO `pictures` (`id`, `uuid`, `node`, `date_created`, `last_modified`, `url`, `title`, `last_url_check`, `object_id`, `object_type`, `thumb_url`, `thumb_last_generated`, `spoiler`, `local`, `unknown_format`, `display`, `mappreview`, `seq`)
VALUES
 (1,'EF2CFA7A-3FDF-11EB-96DF-0242AC120002',5,'2020-12-17 14:30:56','2020-12-17 14:30:56','http://docker.team-opencaching.de/images/uploads/EF2CFA7A-3FDF-11EB-96DF-0242AC120002.jpg','nicht angucken.. Spoilerbild!','1970-01-01 01:00:00',6,2,'','1970-01-01 01:00:00',1,1,0,1,0,1),
 (2,'32BE89C5-3FE0-11EB-96DF-0242AC120002',5,'2020-12-17 14:32:49','2020-12-17 14:32:49','http://docker.team-opencaching.de/images/uploads/32BE89C5-3FE0-11EB-96DF-0242AC120002.png','Perspektive','1970-01-01 01:00:00',5,2,'','1970-01-01 01:00:00',0,1,0,1,1,1),
 (3,'157995B3-404C-11EB-96DF-0242AC120002',5,'2020-12-18 10:33:26','2020-12-18 10:33:26','http://docker.team-opencaching.de/images/uploads/157995B3-404C-11EB-96DF-0242AC120002.jpg','Spoiler','1970-01-01 01:00:00',19,2,'','1970-01-01 01:00:00',1,1,0,1,0,1)
;

INSERT INTO `queries` (`id`, `user_id`, `name`, `options`, `last_queried`)
VALUES
 (28798959,0,'',_binary 'a:28:{s:11:\"f_userowner\";s:1:\"0\";s:11:\"f_userfound\";s:1:\"0\";s:13:\"f_unpublished\";i:0;s:10:\"f_disabled\";s:1:\"0\";s:10:\"f_inactive\";s:1:\"1\";s:9:\"f_ignored\";s:1:\"1\";s:16:\"f_otherPlatforms\";s:1:\"0\";s:10:\"f_geokrets\";s:1:\"0\";s:6:\"expert\";s:1:\"0\";s:10:\"showresult\";s:1:\"1\";s:6:\"output\";s:4:\"HTML\";s:4:\"bbox\";b:0;s:13:\"cache_attribs\";a:0:{}s:17:\"cache_attribs_not\";a:1:{i:24;i:24;}s:4:\"unit\";s:2:\"km\";s:10:\"searchtype\";s:3:\"all\";s:4:\"sort\";s:10:\"bydistance\";s:7:\"country\";s:0:\"\";s:8:\"language\";s:0:\"\";s:4:\"adm2\";s:0:\"\";s:9:\"cachetype\";s:20:\"1;2;3;4;5;6;7;8;9;10\";s:9:\"cachesize\";s:15:\"1;2;3;4;5;6;7;8\";s:13:\"difficultymin\";i:0;s:13:\"difficultymax\";i:0;s:10:\"terrainmin\";i:0;s:10:\"terrainmax\";i:0;s:17:\"recommendationmin\";i:0;s:7:\"queryid\";i:0;}','2021-02-01 13:54:37')
;

-- INSERT INTO `search_index_times` (`object_type`, `object_id`, `last_refresh`) !!!
-- VALUES
--  (2,1,'2020-12-17 13:38:05'),
--  (3,1,'2020-12-17 13:39:00'),
--  (2,3,'2020-12-17 13:45:50'),
--  (3,3,'2020-12-17 13:45:50'),
--  (3,4,'2020-12-17 14:18:27'),
--  (3,5,'2020-12-17 14:23:31'),
--  (2,5,'2020-12-17 14:24:01'),
--  (2,6,'2020-12-17 14:27:51'),
--  (3,6,'2020-12-17 14:27:51'),
--  (6,6,'2020-12-17 14:30:56'),
--  (6,5,'2020-12-17 14:32:49'),
--  (2,7,'2020-12-18 08:23:47'),
--  (3,7,'2020-12-18 08:23:47'),
--  (2,8,'2020-12-18 08:27:46'),
--  (3,8,'2020-12-18 08:27:46'),
--  (2,9,'2020-12-18 08:32:21'),
--  (3,9,'2020-12-18 08:40:11'),
--  (1,9,'2020-12-18 08:57:37'),
--  (2,10,'2020-12-18 09:07:26'),
--  (3,10,'2020-12-18 09:10:23'),
--  (2,11,'2020-12-18 09:16:33'),
--  (3,11,'2020-12-18 09:16:33'),
--  (2,12,'2020-12-18 09:22:55'),
--  (3,12,'2020-12-18 09:22:55'),
--  (2,13,'2020-12-18 09:29:03'),
--  (3,13,'2020-12-18 09:29:04'),
--  (2,14,'2020-12-18 09:45:23'),
--  (3,14,'2020-12-18 09:45:23'),
--  (1,15,'2020-12-18 10:01:41'),
--  (3,15,'2020-12-18 10:04:06'),
--  (2,15,'2020-12-18 10:05:24'),
--  (2,16,'2020-12-18 10:13:03'),
--  (3,16,'2020-12-18 10:13:03'),
--  (2,17,'2020-12-18 10:16:42'),
--  (3,17,'2020-12-18 10:16:42'),
--  (2,18,'2020-12-18 10:23:18'),
--  (3,18,'2020-12-18 10:24:30'),
--  (2,19,'2020-12-18 10:30:33'),
--  (3,19,'2020-12-18 10:30:33'),
--  (6,19,'2020-12-18 10:33:26'),
--  (2,20,'2020-12-18 10:39:54'),
--  (3,20,'2020-12-18 10:39:54'),
--  (2,21,'2020-12-18 10:44:51'),
--  (3,21,'2020-12-18 10:44:51'),
--  (1,10,'2020-12-18 10:45:18'),
--  (2,22,'2020-12-18 11:11:15'),
--  (3,22,'2020-12-18 11:11:15'),
--  (1,22,'2020-12-18 11:15:32'),
--  (3,23,'2020-12-18 11:22:27'),
--  (1,3,'2020-12-18 11:23:18'),
--  (2,4,'2020-12-18 13:29:20'),
--  (2,23,'2021-01-08 09:25:58'),
--  (3,24,'2021-02-01 13:50:43'),
--  (2,24,'2021-02-19 07:37:13')
-- ;

-- INSERT INTO `stat_cache_logs` (`cache_id`, `user_id`, `found`, `notfound`, `note`, `will_attend`, `maintenance`) !!!!
-- VALUES
--  (3,170293,1,0,0,0,0),
--  (3,170298,1,0,0,0,0),
--  (3,170299,1,0,0,0,0),
--  (9,170290,0,0,0,0,2),
--  (9,170291,0,0,1,0,0),
--  (10,170292,0,0,0,1,0),
--  (15,170294,1,0,0,0,0),
--  (22,107469,0,0,0,0,1)
-- ;

-- INSERT INTO `stat_caches` (`cache_id`, `found`, `notfound`, `note`, `will_attend`, `maintenance`, `last_found`, `watch`, `ignore`, `toprating`, `picture`) !!!!
-- VALUES
--  (3,3,0,0,0,0,'2020-12-18',1,0,0,0),
--  (5,0,0,0,0,0,NULL,0,0,0,1),
--  (6,0,0,0,0,0,NULL,0,0,0,1),
--  (9,0,0,1,0,2,NULL,1,0,0,0),
--  (10,0,0,0,1,0,NULL,0,0,0,0),
--  (15,1,0,0,0,0,'2020-12-18',0,0,0,0),
--  (19,0,0,0,0,0,NULL,0,0,0,1),
--  (22,0,0,0,0,1,NULL,0,0,0,0)
-- ;

INSERT INTO `sys_repl_exclude` (`user_id`, `datExclude`)
VALUES
 (170295,'2020-12-16 20:24:38'),
 (170289,'2020-12-17 14:32:49'),
 (170291,'2020-12-18 08:47:00'),
 (170290,'2020-12-18 08:57:37'),
 (170293,'2020-12-18 09:25:11'),
 (170294,'2020-12-18 10:01:41'),
 (170296,'2020-12-18 10:33:26'),
 (170292,'2020-12-18 10:45:18'),
 (170298,'2020-12-18 11:12:56'),
 (107469,'2020-12-18 11:15:32'),
 (170299,'2020-12-18 11:23:18')
;

INSERT INTO `watches_logqueue` (`log_id`, `user_id`)
VALUES
 (1,170291),
 (2,170291),
 (3,170291),
 (7,170293),
 (9,170293)
;
