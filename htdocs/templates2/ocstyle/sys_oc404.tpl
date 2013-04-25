{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{*	
 * Template for opencaching.de "page not found" and entry page
 * for nonexisting subdomains 
 *}
 
{* OCSTYLE *}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>
			404 opencaching.de - DNF
		</title>
		<meta name="KEYWORDS" content="geocaching, opencaching, geocashing, longitude, latitude, utm, coordinates, treasure hunting, treasure, GPS, global positioning system, garmin, magellan, mapping, geo, hiking, outdoors, sport, hunt, stash, cache, geocaching, geocache, cache, treasure, hunting, satellite, navigation, tracking, bugs, travel bugs" />
		<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<meta http-equiv="Content-Language" content="{$opt.template.locale}" />
		<meta http-equiv="gallerimg" content="no" />
		<meta http-equiv="cache-control" content="no-cache" />
		<link rel="SHORTCUT ICON" href="favicon.ico">
		<link rel="apple-touch-icon" href="{$rootpath}resource2/{$opt.template.style}/images/oclogo/apple-touch-icon-iphone.png" />
		<link rel="apple-touch-icon" sizes="72x72" href="{$rootpath}resource2/{$opt.template.style}/images/oclogo/apple-touch-icon-ipad.png" />
		<link rel="apple-touch-icon" sizes="114x114" href="{$rootpath}resource2/{$opt.template.style}/images/oclogo/apple-touch-icon-iphone-retina.png" />
		<link rel="apple-touch-icon" sizes="144x144" href="{$rootpath}resource2/{$opt.template.style}/images/oclogo/apple-touch-icon-ipad-retina.png" />
		<link rel="stylesheet" type="text/css" href="{$rootpath}resource2/{$opt.template.style}/css/style_screen.css" />
		<link rel="stylesheet" type="text/css" href="{$rootpath}resource2/{$opt.template.style}/css/style_oc404.css" />
	</head>
	<body>
		<div id="frame">
			<div class="header">
				<div class="headerimage">
					<img src="{$rootpath}resource2/{$opt.template.style}/images/head/rotator.php?path={$opt.page.headimagepath}" class="headerimagecontent" />
				</div>
				<div class="headerlogo">
					<img src="{$rootpath}resource2/{$opt.template.style}/images/oclogo/oc_head_alpha3.png" class="headerimagecontent" />
				</div>
			</div>
			<div id="head">
		    	<p>
					<img id="oc404" src="{$rootpath}resource2/{$opt.template.style}/images/oc_404.png" title="opencaching.de 404" alt="opencaching.de 404" />
					<span class="dnf">- {t}Page not found{/t} - DNF</span>
		      	</p>
			</div>
			<div id="content">
				<p class="text">{t 1=$website|escape}The visited website <b>%1</b> does not exists, we found the following suitable pages:{/t}</p>
				<div class="sresult">
					<p class="content-title-noshade-size2"><a class="links" href="http://www.opencaching.de" title="{t}Cachedatabase{/t}">www.opencaching.de</a>&nbsp;{t}Here you can find a lot of individual caches.{/t}</p>
					<div>
						<p>{t}The newest caches:{/t}</p>
						{* newest cache template *}
						{include file="res_newcaches.tpl" newcaches=$newcaches rootpath=$rootpath}
					</div>
				</div>
				<div class="sresult">
					<p class="content-title-noshade-size2"><a class="links" href="http://forum.opencaching-network.org" title="Opencaching Forum">forum.opencaching-network.org</a>&nbsp;{t}Here you can discuss, improve or ask questions.{/t}</p>
					<div>
						<p>{t}The newest forumposts:{/t}</p>
						{* newest forum-entries template (RSSParser) *}
						{include file="res_rssparser.tpl" rss=$forum}
					</div>
				</div>
				<div class="sresult">
					<p class="content-title-noshade-size2"><a class="links" href="http://blog.opencaching.de" title="Opencaching Blog">blog.opencaching.de</a>&nbsp;{t}Any time there are news to post, you'll find them here.{/t}</p>
					<div>
						<p>{t}The newest blogposts:{/t}</p>
						{* newest blogpost template (RSSParser) *}
						{include file="res_rssparser.tpl" rss=$blog}
					</div>
				</div>
				<div class="sresult">
					<p class="content-title-noshade-size2"><a class="links" href="http://wiki.opencaching.de" title="Opencaching Wiki">wiki.opencaching.de</a>&nbsp;{t}Here you get tutorials, howtos and any information about opencaching.{/t}</p>
					<div>
						<p>{t}The newest wikiarticles:{/t}</p>
						{* newest wiki article template (RSSParser) *}
						{include file="res_rssparser.tpl" rss=$wiki}
					</div>
				</div>
			</div>
			<div id="foot">
				<p class="text">{t}Not found? Contact us using{/t} <a class="links" href="mailto:contact@opencaching.de" title="{t}Contact{/t}">contact@opencaching.de</a>.</p>
				<p>&nbsp;</p>
				<p class="center"><a class="links" href="/articles.php?page=impressum" title="{t}Impressum{/t}">{t}Imprint{/t}</a> - <a class="links" href="/articles.php?page=verein" title="{t}Nonprofit organization{/t}">Opencaching Deutschland e.V.</a> - <a class="links" href="/articles.php?page=contact" title="{t}Contact{/t}">{t}Contact{/t}</a></p>
				<p class="center"><a class="links" href="/fb" title="{t}Opencaching on Facebook{/t}">Facebook</a> - <a class="links" href="/+" title="{t}Opencaching on Google+{/t}">Google+</a> - <a class="links" href="/t" title="{t}Opencaching on Twitter{/t}">Twitter</a></p>
			</div>
		</div>
	</body>
</html>