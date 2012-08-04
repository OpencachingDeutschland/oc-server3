{* Template für opencaching.de *}
{* OCSTYLE *}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>
			{if ($opt.template.title=="")}
				{$opt.page.subtitle1|escape} {$opt.page.subtitle2|escape}
			{else}
				{$opt.template.title|escape} - {$opt.page.subtitle1|escape} {$opt.page.subtitle2|escape}
			{/if}
		</title>
		<meta name="KEYWORDS" content="geocaching, opencaching, geocashing, longitude, latitude, utm, coordinates, treasure hunting, treasure, GPS, global positioning system, garmin, magellan, mapping, geo, hiking, outdoors, sport, hunt, stash, cache, geocaching, geocache, cache, treasure, hunting, satellite, navigation, tracking, bugs, travel bugs" />
		<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<meta http-equiv="Content-Language" content="{$opt.template.locale}" />
		<meta http-equiv="gallerimg" content="no" />
		<meta http-equiv="cache-control" content="no-cache" />
		<link rel="SHORTCUT ICON" href="favicon.ico">
		<link rel="stylesheet" type="text/css" media="screen,projection" href="resource2/{$opt.template.style}/css/style_screen.css">
		<link rel="stylesheet" type="text/css" media="print" href="resource2/{$opt.template.style}/css/style_print.css">
		{literal}
			<script type="text/javascript">
			<!--
				var nWindowWidth = 9999;
				if (window.innerWidth)
					nWindowWidth = window.innerWidth;
				else if (screen.availWidth)
					nWindowWidth = screen.availWidth;
				if (nWindowWidth > 970)
					document.writeln('<link rel="stylesheet" type="text/css" media="screen,projection" href="{/literal}{season winter='resource2/ocstyle/css/seasons/style_winter.css' spring='resource2/ocstyle/css/seasons/style_spring.css' summer='resource2/ocstyle/css/seasons/style_summer.css' autumn='resource2/ocstyle/css/seasons/style_autumn.css'}{literal}">');

				function usercountry_change()
				{
					var sCurrentOption = "{/literal}{$opt.template.country|escapejs}{literal}";
					var oUserCountryCombo = document.getElementById('usercountry');

					if (sCurrentOption!=oUserCountryCombo.value)
					{
						window.location = 'index.php?usercountry=' + oUserCountryCombo.value;
					}
				}
			//-->
			</script>
		{/literal}
		<script type="text/javascript" src="resource2/{$opt.template.style}/js/enlargeit.js"></script>
		{if $opt.session.url==true}
			<script type="text/javascript">
				{literal}
				<!--
					var sSessionId = '{/literal}{$opt.session.id|escape:'js'}{literal}';
				//-->
				{/literal}
			</script>
			<script src="resource2/{$opt.template.style}/js/session.js" type="text/javascript"></script>
		{/if}
		{if $garmin==true}
		  <script type="text/javascript" src="resource2/{$opt.template.style}/js/GarminPrototype.js"></script>
		  <script type="text/javascript" src="http://developer.garmin.com/web/communicator-api/garmin/device/GarminDeviceDisplay.js"> </script>
		  <script type="text/javascript">var garmin_gpx_filename = '{$cache.wpoc}.gpx';</script>
		  <script type="text/javascript" src="resource2/{$opt.template.style}/js/GarminDisplay.js"></script>
		  {* <!-- <script type="text/javascript" src="resource2/{$opt.template.style}/js/search.js"></script> --> *}
		{/if}
		{foreach from=$opt.page.header_javascript item=scriptItem}
			<script type="text/javascript" src="{$scriptItem}"></script>
		{/foreach}
	</head>

{* JS onload() onunload() *}
<body{if $opt.session.url==true} onload="initSessionTimeout()"{/if}
{if $garmin==true} onload="load('{$cache.latitude}','{$cache.longitude}','{$cache.cacheid}','{$opt.lib.garmin.url}','{$opt.lib.garmin.key}','{$opt.template.locale}')"{/if}
{foreach from=$opt.page.body_load item=loadItem name=bodyload}{if $smarty.foreach.bodyload.first} onload="{/if}{$loadItem};{if $smarty.foreach.bodyload.last}"{/if}{/foreach}
{foreach from=$opt.page.body_unload item=unloadItem name=bodyunload}{if $smarty.foreach.bodyunload.first} onunload="{/if}{$unloadItem};{if $smarty.foreach.bodyunload.last}"{/if}{/foreach}
{if $opt.template.popup!=false} class="popup"{/if}>
	{if $opt.template.popup!=true}
		<div id="overall">
			<div id="langstripe">

				{* <!-- Navigation Level 1 --> *}
				<div class="nav1-container">
					{if $opt.session.url==true}
						<div id="sessionWarn">
							Automatische Abmeldung in <div id="sessionTimout">0</div>&nbsp;Minuten - <a href="#" onclick="cancelSessionTimeout()">Abbrechen</a>
						</div>
					{/if}
					<div class="nav1" style="text-align: right;">
						{nocache}
							{if ($login.userid==0)}
								<b><form action="login.php" method="post" enctype="application/x-www-form-urlencoded" name="login" dir="ltr" style="display: inline;">{t}User{/t}:&nbsp;<input name="email" size="10" type="text" class="textboxes" value="" />&nbsp;{t}Password{/t}:&nbsp;<input name="password" size="10" type="password" class="textboxes" value="" />&nbsp;<input type="hidden" name="action" value="login" /><input type="hidden" name="target" value="{$opt.page.target|escape}" /><input name="LogMeIn" value="{t}Login{/t}" class="formbuttons" style="width: 60px;" type="submit"></form></b>	
							{else}
								<b>{t}Logged in as{/t}:</b> <a href="myhome.php">{$login.username|escape}</a> - <a href="login.php?action=logout">{t}Logout{/t}</a>&nbsp;
							{/if}
						{/nocache}  
					</div>
				</div>
				<div class="navflag">
					<ul>
						<li><strong>{t}Language{/t}</strong></li>
						{foreach from=$opt.template.locales key=localeKey item=localeItem}
							{if $localeItem.show}
								<li><a style="text-decoration: none;" href="index.php?locale={$localeKey}"><img src="{$localeItem.flag}" alt="{$localeItem.name|escape}" width="24px" height="24px" /></a></li>
							{/if}
						{/foreach}
						<li>&nbsp;&nbsp;&nbsp;&nbsp;<strong>{t}Country:{/t}</strong></li>
						<li>
							<select id="usercountry" onclick="javascript:usercountry_change()">
								{foreach from=$opt.template.usercountrieslist item=countryItem name=userCountryList}
									{if $countryItem.begin_group==1 || $smarty.foreach.userCountryList.first}
										<option disabled="disabled">
											{if $countryItem.group==1}
												- {t}This OC node{/t} -
											{elseif $countryItem.group==2}
												- {t}Other OC nodes{/t} -
											{elseif $countryItem.group==3}
												- {t}Others{/t} -
											{else}
												-
											{/if}
										</option>
									{/if}
									<option value="{$countryItem.country|escape}"{if $opt.template.country==$countryItem.country} selected="selected"{/if}>{$countryItem.name|escape}</option>
								{/foreach}
							</select>
						</li>
					</ul>
				</div>
			</div>
			<div class="page-container-1" style="position: relative;">

				<div id="bg1">&nbsp;</div>
				<div id="bg2">&nbsp;</div>

				{* <!-- HEADER --> *}
				{* <!-- OC-Logo --> *}
				<div><img src="resource2/{$opt.template.style}/images/oc_logo.png" alt="" style="margin-top: 5px; margin-left: 3px;" /></div>

				{* <!-- Sitename --> *}
				<div class="site-name">
					<a href="index.php">
						<p class="title">{$opt.page.title|escape}</p>
						<p class="subtitle1">{$opt.page.subtitle1|escape}</p>
						<p class="subtitle2">{$opt.page.subtitle2|escape}</p>
					</a>
				</div>

				{* <!-- Site slogan --> *}
				<div class="site-slogan-container">
					{if $opt.page.sponsor.topright!=''}
						{$opt.page.sponsor.topright}
					{else}
						<div class="site-slogan" style="border-width:0px;">
							<div style="width: 100%; text-align: left;">
								<p class="search">&nbsp;<br />&nbsp;</p>
							</div>
    				</div>
					{/if}
			</div>

			{* <!-- Debugschalter hier wieder einsetzen --> *}
			{if ($opt.debug & DEBUG_DEVELOPER) == DEBUG_DEVELOPER}
				<div id="debugoc"><font size="5" face="arial" color="red"><center>{t}Developer system - only testing{/t}</center></font></div>
			{elseif ($opt.debug & DEBUG_TESTING) == DEBUG_TESTING}
				<div id="debugoc"><font size="5" face="arial" color="red"><center>{t}Testing - do not login, please{/t}</center></font></div>
			{/if}


			{* <!-- Header banner --> *}						    		 						
			<div class="header" style="height:81px;">
				<div style="width: 970px; padding-top: 1px;">
					<img src="resource2/{$opt.template.style}/images/head/rotator.php" width="970" height="80" alt="" style="border: 0px none ;" />
				</div>
			</div>

			{* <!-- Navigation Level 2 --> *}
			<div class="nav2">
				<ul>
					{nocache}
						{include file="sys_topmenu.tpl" items="$topmenu"}
					{/nocache}
				</ul>
			</div>

			{* <!-- Buffer after header --> *}
			<div class="buffer" style="height: 30px;"></div>

			{* <!-- Suchbox --> *}
			{if !$opt.page.nowpsearch}
				<div id="breadcrumb">{include file="sys_breadcrumb.tpl" items="$breadcrumb"}</div>
				<div id="suchbox"><form action="searchplugin.php" method="post" style="display:inline;"><b>{t}Waypoint-Search{/t}:</b><input type="hidden" name="source" value="waypoint-suche" /> <input type="text" name="userinput" size="10" /> <input type="submit" value="{t}Go{/t}" /></form></div>
			{else}
				<div id="breadcrumb_fullsize">{include file="sys_breadcrumb.tpl" items="$breadcrumb"}</div>
			{/if}

			{* <!-- NAVIGATION --> *}				
			{* <!-- Navigation Level 3 --> *}
			<div class="nav3">
				<ul>
					<li class="title">{t}Main menu{/t}</li>
					{nocache}
						{include file="sys_submenu.tpl" items="$submenu"}
					{/nocache}
				</ul>

				<p class="sidebar-maintitle">{t}Country (nodes){/t}</p>
				<div style="text-align: center;" class="nodeflags">
					<a href="http://www.opencaching.cz" target="_blank"><img src="resource2/{$opt.template.style}/images/nodes/oc-cz.png" width="100" height="22" /></a><br />
					<a href="http://www.opencaching.de" target="_blank"><img src="resource2/{$opt.template.style}/images/nodes/oc-de.png" width="100" height="22" /></a><br />
					<a href="http://www.opencachingspain.es" target="_blank"><img src="resource2/{$opt.template.style}/images/nodes/oc-es.png" width="100" height="22" /></a><br />
					<a href="http://www.opencaching.it" target="_blank"><img src="resource2/{$opt.template.style}/images/nodes/oc-it.png" width="100" height="22" /></a><br />
					<a href="http://www.opencaching.no" target="_blank"><img src="resource2/{$opt.template.style}/images/nodes/oc-no.png" width="100" height="22" /></a><br />
					<a href="http://www.opencaching.nl" target="_blank"><img src="resource2/{$opt.template.style}/images/nodes/oc-nl.png" width="100" height="22" /></a><br />
					<a href="http://www.opencaching.pl" target="_blank"><img src="resource2/{$opt.template.style}/images/nodes/oc-pl.png" width="100" height="22" /></a><br />
					<a href="http://www.opencaching.se" target="_blank"><img src="resource2/{$opt.template.style}/images/nodes/oc-se.png" width="100" height="22" /></a><br />
					<a href="http://www.opencaching.org.uk" target="_blank"><img src="resource2/{$opt.template.style}/images/nodes/oc-org-uk.png" width="100" height="22" /></a><br />
					<a href="http://www.opencaching.us" target="_blank"><img src="resource2/{$opt.template.style}/images/nodes/oc-us.png" width="100" height="22" /></a>
				</div>

				{* <!-- Paypalbutton --> *}
				{if $opt.page.showdonations}
					<p class="sidebar-maintitle">{t}Donations{/t}</p>
					<div style="margin-top:20px;width:100%;text-align:center;">
						<a href="http://www.opencaching.de/articles.php?page=donations">
							<img src="resource2/{$opt.template.style}/images/misc/donate.gif" alt="{t}Donations{/t}" style="border:0px;" />
						</a><br />
						&nbsp;
					</div>
				{/if}

				<div class="sidebar-txtbox-noshade">
					<p class="content-txtbox-noshade-size5">
						<small>
							{nocache}
								{t}Page timing{/t}: {$sys_runtime|sprintf:"%1.3f"} {t}sec{/t}<br />
								{if ($opt.template.caching == true)}
									{t}Page cached{/t}: {if $sys_cached==true}{t}Yes{/t}{else}{t}No{/t}{/if}<br />
								{/if}
								{t}DB connected{/t}: 
								{if $sys_dbconnected==true}{t}Yes{/t}{else}{t}No{/t}{/if}
								{if $sys_dbslave==true}, {t}Slave{/t}{/if}
								<br />
							{/nocache}
							{t}Created at{/t}: {"0"|date_format:$opt.format.datetime}
						</small>
					</p>			
				</div>

			</div>

			{* <!-- CONTENT --> *}
			<div class="content2">
{/if}{* Popup *}

				<div id="ocmain">
					{if $opt.template.popup!=false && $opt.template.popupmargin!=false}
						<div style="padding-left: 25px; padding-top: 10px; padding-right: 10px; padding-bottom: 20px; margin: 0; background: white;">
							{include file="$template.tpl"}
						</div>
					{else}
						{include file="$template.tpl"}
					{/if}
				</div>

{if $opt.template.popup!=true}
			</div>

			{* <!-- End Text Container --> *}

			{* <!-- FOOTER --> *}
			<div class="footer">
				<p><a href="articles.php?page=dsb">{t}Privacy statement{/t}</a> | <a href="articles.php?page=impressum">{t}Terms of use and legal information{/t}</a> | <a href="articles.php?page=contact">{t}Contact{/t}</a> | <a href="sitemap.php">{t}Sitemap{/t}</a></p>
				<p><strong>{$opt.page.sponsor.bottom}</strong></p>
			</div>
		</div>

{/if}{*popup*}

	</body>
</html>
