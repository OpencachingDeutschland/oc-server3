{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
<script type="text/javascript" src="resource2/{$opt.template.style}/js/wz_tooltip.js"></script>
<script type="text/javascript" src="resource2/{$opt.template.style}/js/tip_balloon.js"></script>
<script type="text/javascript" src="resource2/{$opt.template.style}/js/tip_centerwindow.js"></script>
<script type="text/javascript" src="resource2/{$opt.template.style}/js/rot13.js"></script>

<script type="text/javascript">
{literal}
<!--
	var bNoCrypt = 0;
	var last="";var rot13map;function decryptinit(){var a=new Array();var s="abcdefghijklmnopqrstuvwxyz";for(i=0;i<s.length;i++)a[s.charAt(i)]=s.charAt((i+13)%26);for(i=0;i<s.length;i++)a[s.charAt(i).toUpperCase()]=s.charAt((i+13)%26).toUpperCase();return a}
	function decrypt(elem){if(elem.nodeType != 3) return; var a = elem.data;if(!rot13map)rot13map=decryptinit();s="";for(i=0;i<a.length;i++){var b=a.charAt(i);s+=(b>='A'&&b<='Z'||b>='a'&&b<='z'?rot13map[b]:b)}elem.data = s}
	
	function visitCounter()
	{
		var xmlReq = createXMLHttp();
		var params = 'cacheid={/literal}{$cache.cacheid}{literal}&visitcounter=1';
		if (!xmlReq) return;
	
		xmlReq.open('POST', 'viewcache.php', true);
		xmlReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlReq.setRequestHeader("Content-length", params.length);
		xmlReq.setRequestHeader("Connection", "close");
		xmlReq.send(params);
	}

	function createXMLHttp()
	{
		if (typeof XMLHttpRequest != 'undefined')
			return new XMLHttpRequest();
		else if (window.ActiveXObject)
		{
			var avers = ["Microsoft.XmlHttp", "MSXML2.XmlHttp","MSXML2.XmlHttp.3.0", "MSXML2.XmlHttp.4.0","MSXML2.XmlHttp.5.0"];
			for (var i = avers.length -1; i >= 0; i--)
			{
				try
				{
					httpObj = new ActiveXObject(avers[i]);
					return httpObj;
				}
				catch(e)
				{
				}
			}
		}
		return null;
	}

	window.setTimeout("visitCounter()", 1000);
//-->
{/literal}
</script>

{if $cache.status==7}
<div class="buffer" style="width: 500px;">&nbsp;</div>
<p style="line-height: 1.6em; color: red; font-weight: 900;">
	{t}The geocache was locked by an administrator because it did not follow the Opencaching terms of use.
	If you wish to unlock it, contact us using the "report cache"-link. Please choose "other" as reason
	and explain shortly what you have changed to make the listing compliant to our terms of use. Thank you!{/t}
</p>
<div class="buffer" style="width: 500px;">&nbsp;</div>
{/if}

<!-- Already found this cache? -->
{if $cache.userhasfound}
	<div id="havefound">
		<p><img src="resource2/{$opt.template.style}/images/viewcache/have-found.png" width="35" height="35" align="left" style="padding-right: 5px;" alt="{if $cache.type==6}{t}You have attended this event!{/t}{else}{t}You have already found this cache!{/t}{/if}" title="{if $cache.type==6}{t}You have attended this event!{/t}{else}{t}You have already found this cache!{/t}{/if}" /></p>
	</div>
{/if}

<!-- Cachemeta -->
<div class="content2-container line-box">
	<div class="">
		<div class="nav4">
			<ul id="cachemenu">
				<li class="title" >Cache Menu</li>
				{if $login.userid!=0}
					{if $cache.log_allowed}
					<li class="group"><a style="background-image: url(resource2/{$opt.template.style}/images/viewcache/new-entry-18.png);background-repeat:no-repeat;" href="log.php?cacheid={$cache.cacheid|urlencode}">{t}Log this cache{/t}</a></li>
					{/if}
				
					{if $watched==1}
						<li class="group"><a style="background-image: url(resource2/{$opt.template.style}/images/viewcache/watch-18.png);background-repeat:no-repeat;" href="mywatches.php?action=remove&amp;cacheid={$cache.cacheid|urlencode}&amp;target=viewcache.php%3Fcacheid%3D{$cache.cacheid|urlencode}">{t}Don't watch{/t}</a></li>
					{else}
						<li class="group"><a style="background-image: url(resource2/{$opt.template.style}/images/viewcache/watch-18.png);background-repeat:no-repeat;" href="mywatches.php?action=add&amp;cacheid={$cache.cacheid|urlencode}&amp;target=viewcache.php%3Fcacheid%3D{$cache.cacheid|urlencode}">{t}Watch{/t}</a></li>
					{/if}
					{if $ignored==1}
						<li class="group"><a style="background-image: url(resource2/{$opt.template.style}/images/viewcache/ignore-18.png);background-repeat:no-repeat;" href="ignore.php?cacheid={$cache.cacheid|urlencode}&amp;action=removeignore">{t}Don't ignore{/t}</a></li>
					{else}
						<li class="group"><a style="background-image: url(resource2/{$opt.template.style}/images/viewcache/ignore-18.png);background-repeat:no-repeat;" href="ignore.php?cacheid={$cache.cacheid|urlencode}&amp;action=addignore">{t}Ignore{/t}</a></li>
					{/if}
				{/if} 
				{if $login.userid==$cache.userid}
					<li class="group"><a style="background-image: url(resource2/{$opt.template.style}/images/viewcache/page.png);background-repeat:no-repeat;" href="editcache.php?cacheid={$cache.cacheid|urlencode}">{t}Edit{/t}</a></li>
				{/if}	
				{if $login.userid!=0}
					<li class="group"><a style="background-image: url(resource2/{$opt.template.style}/images/viewcache/report-problem-18.png);background-repeat:no-repeat;" href="reportcache.php?cacheid={$cache.cacheid|urlencode}">{t}Report this cache{/t}</a></li>
				{/if}
			</ul>
		</div>
		<div class="buffer" style="width: 500px;">&nbsp;</div>

		<div class="content2-container-2col-left" style="width:60px; clear: left;">
			<div><a href="articles.php?page=cacheinfo#cachetype">{include file="res_cacheicon.tpl" cachetype=$cache.type status=$cache.status}</a></div>
			<div><a href="articles.php?page=cacheinfo#difficulty"><img src='./resource2/{$opt.template.style}/images/difficulty/diff-{$cache.difficulty*5}.gif' border='0' width='19' height='16' hspace='2' alt='{t 1=$cache.difficulty*0.5|sprintf:'%01.1f'}Difficulty: %1 of 5.0{/t}' title='{t 1=$cache.difficulty*0.5|sprintf:'%01.1f'}Difficulty: %1 of 5.0{/t}' /></a></div>
			<div><a href="articles.php?page=cacheinfo#difficulty"><img src='./resource2/{$opt.template.style}/images/difficulty/terr-{$cache.terrain*5}.gif' border='0' width='19' height='16' hspace='2' title='{t 1=$cache.terrain*0.5|sprintf:'%01.1f'}Terrain: %1 of 5.0{/t}' alt='{t 1=$cache.terrain*0.5|sprintf:'%01.1f'}Terrain: %1 of 5.0{/t}' /></a></div>
			<div></div>
		</div>

		<div class="content2-container-2col-left" id="cache_name_block">
			<span class="content-title-noshade-size5">{$cache.name|escape}</span>
			{if $cache.shortdesc!=''}
				<!-- <br /> --><p class="content-title-noshade-size1">&nbsp;{$cache.shortdesc|escape}</p>
			{/if}

			<p>{t}by{/t}&nbsp;<a class="links" href="viewprofile.php?userid={$cache.userid}">{$cache.username|escape}</a>&nbsp;&nbsp;
				<span style="color: rgb(88, 144, 168); font-weight: bold;">
					{if $cache.code1=="" or $cache.code1 != $cache.countryCode}
						<img src="images/flags/{$cache.countryCode|lower}.gif" style="vertical-align:middle" />&nbsp; {$cache.country|escape}
					{else}
						<img src="images/flags/{$cache.code1|lower}.gif" style="vertical-align:middle" />&nbsp;
						{$cache.adm1}{if $cache.adm2!=""},
							{$cache.adm2}{if $cache.adm4!=""}
								&nbsp;=>&nbsp;{$cache.adm4}
							{/if}
						{/if}
					{/if}
				</span>
			</p>
			{if $cache.type==6}
				<span class="participants"><img src="resource2/{$opt.template.style}/images/cacheicon/16x16-event.gif" width="16" height="16" alt="{t}List of participants{/t}" />&nbsp;<a href="#" onClick="javascript:window.open('event_attendance.php?id={$cache.cacheid}&popup=y','{t escape=js}List{/t}','width=320,height=440,resizable=no,scrollbars=1')">{t}List of participants{/t}</a></span>
			{/if}
		</div>
	</div>
</div>
<!-- End Cachemeta -->

<!-- Warning, if temporary not available, archived or locked -->
	{include file="res_state_warning.tpl" cache=$cache}
<!--  End Warning -->

<!-- Cachedetails -->
<div class="content2-container">
	<div class="content2-container-2col-left" id="viewcache-baseinfo">
		<p class="content-title-noshade-size2">
			<img src="resource2/{$opt.template.style}/images/viewcache/kompass.png" class="icon32" alt="" title="" />
			<b><nobr>{$coordinates.lat|escape}</nobr> <nobr>{$coordinates.lon|escape}</nobr></b> <span class="content-title-noshade-size0">(WGS84)</span><br />
		</p>
		<p style="line-height: 1.6em;">
			<img src="resource2/{$opt.template.style}/images/viewcache/map.png" class="icon16" alt="" title="" align="middle" />&nbsp;<a href="#" onClick="javascript:window.open('coordinates.php?lat={$cache.latitude}&lon={$cache.longitude}&popup=y&wp={$cache.wpoc}','{t escape=js}Coordinates{/t}','width=280,height=430,resizable=no,scrollbars=0')">{t}Convert coordinates{/t}</a><br />
			<!-- <img src="resource2/{$opt.template.style}/images/viewcache/box.png" class="icon16" alt="" title="" align="middle" />&nbsp;Cache type: <b>Traditional</b><br /> -->
			<img src="resource2/{$opt.template.style}/images/viewcache/package_green.png" class="icon16" alt="" title="" align="middle" />&nbsp;{t}Size{/t}: <b>{$cache.sizeName|escape}</b><br />
			<img src="resource2/{$opt.template.style}/images/viewcache/page.png" class="icon16" alt="" title="" align="middle" />{if $cache.status!=1}
				{t}State{/t}: <span class="errormsg">{$cache.statusName|escape}</span>
			{else}
				{t}State{/t}: {$cache.statusName|escape}
			{/if}<br />
			{if $cache.searchtime>0}
			<img src="resource2/{$opt.template.style}/images/viewcache/time.png" class="icon16" alt="" title="" align="middle" />&nbsp;{t}Time required{/t}: {$cache.searchtime|format_hour} h
			{/if}
			{if $cache.waylength>0}
				<img src="resource2/{$opt.template.style}/images/viewcache/arrow_switch.png" class="icon16" alt="" title="" align="middle" />&nbsp;{t}Waylength{/t}: {$cache.waylength|sprintf:'%01.2f'} km
			{/if}
			{if $cache.searchtime>0 || $cache.waylength>0}<br />{/if}
			<img src="resource2/{$opt.template.style}/images/viewcache/date.png" class="icon16" alt="" title="" align="middle" />&nbsp;{if $cache.type==6}{t}Event date{/t}{else}{t}Hidden at{/t}{/if}: {$cache.datehidden|date_format:$opt.format.datelong}<br />
			<img src="resource2/{$opt.template.style}/images/viewcache/date.png" class="icon16" alt="" title="" align="middle" />&nbsp;{if $cache.is_publishdate==0}{t}Listed since{/t}{else}{t}Published on{/t}{/if}: {$cache.datecreated|date_format:$opt.format.datelong}<br />
			<img src="resource2/{$opt.template.style}/images/viewcache/date.png" class="icon16" alt="" title="" align="middle" />&nbsp;{t}Last update{/t}: {$cache.lastmodified|date_format:$opt.format.datelong}<br />
			<img src="resource2/{$opt.template.style}/images/viewcache/arrow_in.png" class="icon16" alt="" title="" align="middle" />&nbsp;{t}Waypoint{/t}: <b>{$cache.wpoc}</b><br />
			{if $cache.wpgc!='' || $cache.wpnc!=''}<img src="resource2/{$opt.template.style}/images/viewcache/link.png" class="icon16" alt="" title="" align="middle" />
				{t}Also listed at{/t}:
				{if $cache.wpgc!=''}
					<a href="http://www.geocaching.com/seek/cache_details.aspx?wp={$cache.wpgc}" target="_blank">geocaching.com&nbsp;</a>
				{/if}
				{if $cache.wpnc!=''}
					<a href="http://www.navicache.com/cgi-bin/db/displaycache2.pl?CacheID={nccacheid wp=$cache.wpnc}" target="_blank">navicache.com</a>
				{/if}
			{/if}
		</p>
		<p>
			<a style="background-image: url(resource2/{$opt.template.style}/images/viewcache/print-18.png);background-repeat:no-repeat;" onClick="javascript:window.location='viewcache.php?cacheid={$cache.cacheid}&print=y&log=A&nocrypt=' + bNoCrypt"><input name="PrintA" id="PrintA" value="{t}Print{/t}" type="button" /></a>
			<a style="background-image: url(resource2/{$opt.template.style}/images/viewcache/print-18.png);background-repeat:no-repeat;" onClick="javascript:window.location='viewcache.php?cacheid={$cache.cacheid}&print=y&log=N&nocrypt=' + bNoCrypt"><input name="PrintN" id="PrintN" value="{t}Print no logs{/t}" type="button" /></a>
			<a style="background-image: url(resource2/{$opt.template.style}/images/viewcache/print-18.png);background-repeat:no-repeat;" onClick="javascript:window.location='viewcache.php?cacheid={$cache.cacheid}&print=y&log=5&nocrypt=' + bNoCrypt"><input name="Print5" id="Print5" value="{t}Print last logs{/t}" type="button" /></a>
		</p>
		<p>
			<a class="send-to-gps" href="#" onClick="javascript:window.open('garmin.php?lat={$cache.latitude}&lon={$cache.longitude}&wp={$cache.wpoc}','{t escape=js}Send{/t}','width=640,height=290,resizable=no,scrollbars=1')"><input name="SendToGPS" value="{t}Send to GPS device{/t}" id="SendToGPS" type="button" /></a>

			&nbsp;&nbsp;<img src="resource2/{$opt.template.style}/images/viewcache/16x16-save.png" class="icon16" alt="" />

			<select name="wpdownload" class="wpdownload" onChange="location.href=this.options[this.selectedIndex].value"> 
				<option value="#">{t}Download as...{/t}</option>
				<option value="search.php?searchto=searchbycacheid&showresult=1&f_inactive=0&f_ignored=0&startat=0&cacheid={$cache.cacheid}&output=gpx">GPX</option>
				<option value="search.php?searchto=searchbycacheid&showresult=1&f_inactive=0&f_ignored=0&startat=0&cacheid={$cache.cacheid}&output=loc">LOC</option>
				<option value="search.php?searchto=searchbycacheid&showresult=1&f_inactive=0&f_ignored=0&startat=0&cacheid={$cache.cacheid}&output=kml">KML</option>
				<option value="search.php?searchto=searchbycacheid&showresult=1&f_inactive=0&f_ignored=0&startat=0&cacheid={$cache.cacheid}&output=ov2">OV2</option>
				<option value="search.php?searchto=searchbycacheid&showresult=1&f_inactive=0&f_ignored=0&startat=0&cacheid={$cache.cacheid}&output=ovl">OVL</option>
				<option value="search.php?searchto=searchbycacheid&showresult=1&f_inactive=0&f_ignored=0&startat=0&cacheid={$cache.cacheid}&output=txt">TXT</option>
			</select>
		</p>
		<p>&nbsp;</p>
	</div>

	<div class="content2-container-2col-right" id="viewcache-maptypes">
		<div class="content2-container-2col-left" id="viewcache-numstats">
			<p style="line-height: 1.4em;"><br /><br />
				<img src="resource2/{$opt.template.style}/images/{if $cache.type==6}log{else}viewcache{/if}/16x16-{if $cache.type==6}attended{else}found{/if}.png" class="icon16" alt="" /> {$cache.found} {if $cache.type==6} {t}Attended{/t}{else}{t}Found{/t}{/if}<br />
				<img src="resource2/{$opt.template.style}/images/{if $cache.type==6}log{else}viewcache{/if}/16x16-{if $cache.type==6}will_attend{else}dnf{/if}.png" class="icon16" alt="" /> {if $cache.type==6} {$cache.willattend} {t}Will attend{/t}{else} {$cache.notfound} {t}Not found{/t}{/if}<br />
				<img src="resource2/{$opt.template.style}/images/viewcache/16x16-note.png" class="icon16" alt="" /> {$cache.note} {t}Notes{/t}<br />
				<img src="resource2/{$opt.template.style}/images/viewcache/16x16-watch.png" class="icon16" alt="" /> {$cache.watcher} {t}Watched{/t}<br />
				<img src="resource2/{$opt.template.style}/images/viewcache/ignore-16.png" class="icon16" alt="" /> {$cache.ignorercount} {t}Ignored{/t}<br />
				<img src="resource2/{$opt.template.style}/images/viewcache/16x16-visitors.png" class="icon16" alt="" /> {$cache.visits} {t}Page visits{/t}<br />
				<span style="white-space:nowrap;"><img src="resource2/{$opt.template.style}/images/viewcache/gk.png" class="icon16" alt="" title="GeoKrety visited" /> <a class="links" href="http://geokrety.org/szukaj.php?lang=de_DE.UTF-8&wpt={$cache.wpoc}" target="_blank">{t}Geokrety history{/t}</a></span><br />
				{if $cache.topratings>0}
					<img src="resource2/{$opt.template.style}/images/viewcache/rating-star.gif" class="icon16" alt="" /> {$cache.topratings} {t}Recommendations{/t}<br />
				{/if}
			</p>
		</div>
		<div id="viewcache-map" class="content2-container-2col-right" style="overflow:hidden">
			{if $cachemap.iframe}
				<div class="img-shadow">
					<iframe src="{$cachemap.url}" width="200px" height="200px" frameborder="0">
					</iframe>
				</div>
			{else}
				<img src="{$cachemap.url}" height="200px" width="200px" />
			{/if}
		</div>

		<b>{t}Maps:{/t}</b> 
		<a href="map2.php?wp={$cache.wpoc}" target="_blank">{t}Opencaching.de{/t}</a>,<br /><a href="http://www.mapquest.com/maps/map.adp?latlongtype=decimal&latitude={$cache.latitude}&longitude={$cache.longitude}" target="_blank">Mapquest</a>,<br />
		<a href="http://maps.google.de/maps?q={$cache.latitude},{$cache.longitude}+({$cache.wpoc}%20-%20{$cache.name|replace:'(':''|replace:')':''|escape:'url'})&z=15" target="_blank">{t}Google Maps{/t}</a>			
	</div>
</div>
<!-- End Cachedetails -->

<!-- Attribute -->

{if count($attributes)>0}
	<div class="content2-container bg-blue02">
		<p class="content-title-noshade-size2"><img src="resource2/{$opt.template.style}/images/description/22x22-encrypted.png" style="align: left; margin-right: 10px;" width="22" height="22" alt="{t}Cache attributes{/t}" /> {t}Cache attributes{/t}</p>
	</div>
	<div class="content2-container">
		<p style="line-height: 1.6em;">
			{include file="res_attribgroup.tpl" attriblist=$attributes}
		</p>
	</div>
{/if}
<!-- End Attribute -->

<!-- Beschreibung -->
<div class="content2-container bg-blue02">
	<p class="content-title-noshade-size2">
		<img src="resource2/{$opt.template.style}/images/description/22x22-description.png" style="align: left; margin-right: 10px;" width="22" height="22" alt="{t}Description{/t}" /> {t}Description{/t}&nbsp;&nbsp;
		{foreach from=$cache.desclanguages item=desclanguagesItem name=desclanguagesItem}
			{strip}
				{if $smarty.foreach.desclanguagesItem.first==false},&nbsp;{/if}
				<img src="images/flags/{$desclanguagesItem|lower}.gif" style="vertical-align:middle" />&nbsp;
				<a href="viewcache.php?wp={$cache.wpoc}&desclang={$desclanguagesItem|escape}">
					{if $cache.desclanguage==$desclanguagesItem}
						<i>{$desclanguagesItem|escape}</i>
					{else}
						{$desclanguagesItem|escape}
					{/if}
				</a>
			{/strip}
		{foreachelse}
			<b>{$cache.desclanguage|escape}</b>
		{/foreach}  		
  </p>
</div>

<div class="content2-container cachedesc">
	<p style="line-height: 1.6em;"></p>  
		{if $cache.deschtml==0}
			{$cache.desc|smiley|hyperlink}
		{else}
			{$cache.desc|smiley}
		{/if}
		
</div>
<!-- End Beschreibung -->

{if $enableCacheNote}
	<div class="content2-container bg-blue02">
		<p class="content-title-noshade-size2">
			<img src="resource2/{$opt.template.style}/images/description/20x20-cache-note.png" style="align: left; margin-right: 10px;" alt="{t}Personal cache note{/t}" /> 
			{t}Personal cache note{/t}
		</p>
	</div>

	<div class="content2-container">
		<form action="viewcache.php" method="post" name="cache_note">
			{include file='cache_note.tpl'}
		</form>
	</div>
{/if}

{if count($childWaypoints)>0}
	<div class="content2-container bg-blue02">
		<p class="content-title-noshade-size2">
			<img src="resource2/{$opt.template.style}/images/description/20x20-compass.png" style="align: left; margin-right: 10px;" alt="{t}Additional waypoints{/t}" /> 
			{t}Additional waypoints{/t}
		</p>
	</div>

	<div class="content2-container" style="margin-left:10px;">
		<table bgcolor="#dddddd" width="95%" cellpadding="5%">
		{foreach from=$childWaypoints item=childWaypoint}
			<tr bgcolor="{cycle values="#ffffff,#eeeeee"}">
				<td width="25%"><img src="{$childWaypoint.image}" />{$childWaypoint.name|escape}</td>
				<td width="20%">{$childWaypoint.coordinateHtml}</td>
				<td>{$childWaypoint.description|escape}</td>
			</tr>
		{/foreach}
		</table>
		<img src="resource2/{$opt.template.style}/images/viewcache/16x16-info.png" class="icon16" alt="Info" />
		{t}Additional waypoints can make searching easier, for example by pointing to a suitable parking location or start of a path. The waypoints are included in GPX-file downloads and will be sent to the GPS device.{/t}
	</div>
{/if}

<!-- Hints -->
{if $cache.hint!=''}
	<div class="content2-container bg-blue02">
  	<p class="content-title-noshade-size2">
  		<img src="resource2/{$opt.template.style}/images/description/22x22-encrypted.png" style="align: left; margin-right: 10px;" width="22" height="22" alt="{t}Additional hint{/t}" /> {t}Additional hint{/t}&nbsp;&nbsp;
  		<span id="decrypt-info">{if $crypt}
				<img src="resource2/{$opt.template.style}/images/viewcache/decrypt.png" class="icon32" width="22" height="22" alt="" />
				<span style="font-weight: 400;"><a href="viewcache.php?wp={$cache.wpoc}&nocrypt=1&desclang={$cache.desclanguage|urlencode}#decrypt-info" {literal}onclick="var ch = document.getElementById('decrypt-hints').childNodes;for(var i=0;i < ch.length;++i) {var e = ch[i]; decrypt(e);} document.getElementById('decrypt-info').style.display = 'none';
				bNoCrypt = 1;
				return false;"{/literal}>{t}Decrypt{/t}</a>{/if}</span>
			</span>
		</p>
	</div>

	<div class="content2-container">
		<p id="decrypt-hints">{if $crypt}{$cache.hint|rot13html}{else}{$cache.hint}{/if}</p>
		<div style="width: 200px; float: right;">
			<font style="font-family: 'Courier New',FreeMono,Monospace;" face="Courier" size="2">A|B|C|D|E|F|G|H|I|J|K|L|M</font>
			<font style="font-family: 'Courier New',FreeMono,Monospace;" face="Courier" size="2">N|O|P|Q|R|S|T|U|V|W|X|Y|Z</font>
		</div>
	</div>
{/if}
<!-- End Hints -->

<!-- Bilder -->
{if count($pictures)>0}
	<div class="content2-container bg-blue02">
		<p class="content-title-noshade-size2">
			<img src="resource2/{$opt.template.style}/images/description/22x22-image.png" style="align: left; margin-right: 10px;" width="22" height="22" alt="{t}Pictures{/t}" /> 
			{t}Pictures{/t}
		</p>
	</div>

	<div class="content2-container">
		{foreach from=$pictures item=pictureItem}
			<div class="viewcache-pictureblock">
				<div class="img-shadow">
					<a href="{$pictureItem.url|escape}" target="_blank">
						<img src="thumbs.php?uuid={$pictureItem.uuid|urlencode}" alt="{$pictureItem.title|escape}" title="{$pictureItem.title|escape}" border="0" align="bottom" onclick="enlarge(this)" class="viewcache-thumbimg"  />
					</a>
				</div>
				<span class="title">{$pictureItem.title|escape}</span>
			</div>
		{/foreach}
	</div>
{/if}
<!-- End Bilder -->

<!-- Utilities -->
<div class="content2-container bg-blue02">
	<p class="content-title-noshade-size2">
		<img src="resource2/{$opt.template.style}/images/description/22x22-utility.png" style="align: left; margin-right: 10px;" width="22" height="22" alt="{t}Utilities{/t}" /> 
		{t}Utilities{/t}
	</p>
</div>

<div class="content2-container">
	{if count($npaareasWarning) > 0}
		<div style="border: solid 1px red;padding:10px;">
			<p style="line-height: 1.6em;">
				<img src="resource2/{$opt.template.style}/images/viewcache/npa.gif" align="left" style="margin-right: 25px;" width="32" height="32" alt="NSG/NPA" />
				{t 1=$opt.cms.npa}This geocache is probably placed within a nature protection area! See <a href="%1">here</a> for further informations, please.{/t}<br />
				{foreach from=$npaareasWarning item=npaItem name=npaareas}
					{$npaItem.npaTypeName|escape} 
					{$npaItem.npaName|escape} (<a href="http://www.google.de/search?q={$npaItem.npaTypeName|urlencode}+{$npaItem.npaName|urlencode}" target="_blank">{t}Info{/t}</a>){if !$smarty.foreach.npaareas.last},{/if}
				{/foreach}
			</p>
		</div>
	{/if}

	{if count($npaareasNoWarning) > 0}
		<p style="line-height: 1.6em;">
			{t 1=$opt.cms.npa}This geocache is probably placed within the following nature protection areas (<a href="%1">Info</a>):{/t}
			{foreach from=$npaareasNoWarning item=npaItem name=npaareas}
				{$npaItem.npaTypeName|escape} 
				{$npaItem.npaName|escape} (<a href="http://www.google.de/search?q={$npaItem.npaTypeName|urlencode}+{$npaItem.npaName|urlencode}" target="_blank">{t}Info{/t}</a>){if !$smarty.foreach.npaareas.last},{/if}
			{/foreach}
		</p>
	{/if}

	{if $print!=true}
		<p>
			{if $cache.topratings>0}
				<img src="resource2/{$opt.template.style}/images/viewcache/rating-star.gif" class="icon16" alt="" /> 
				{t 1=$cache.cacheid}Show cache recommendations from users that recommended this geocache: <a href="recommendations.php?cacheid=%1">alle</a>{/t}
				<br />
			{/if}

			<img src="resource2/{$opt.template.style}/images/viewcache/16x16-search.png" class="icon16" alt="" />
			{t}Search geocaches nearby:{/t} 
			<a href="search.php?searchto=searchbydistance&showresult=1&expert=0&output=HTML&sort=bydistance&f_userowner=0&f_userfound=0&f_inactive=1&lat={$cache.latitude}&lon={$cache.longitude}&distance=150&unit=km" rel="nofollow">{t}all{/t}</a> - 
			<a href="search.php?searchto=searchbydistance&showresult=1&expert=0&output=HTML&sort=bydistance&f_userowner=1&f_userfound=1&f_inactive=1&lat={$cache.latitude}&lon={$cache.longitude}&distance=150&unit=km" rel="nofollow">{t}searchable{/t}</a> - 
			<a href="search.php?searchto=searchbydistance&showresult=1&expert=0&output=HTML&sort=bydistance&f_userowner=1&f_userfound=1&f_inactive=1&lat={$cache.latitude}&lon={$cache.longitude}&distance=150&unit=km&cachetype={$cache.type}" rel="nofollow">{t}same type{/t}</a>
			<br />

			<img src="resource2/{$opt.template.style}/images/viewcache/16x16-search.png" class="icon16" alt="" /> {t}Send this waypoint to GPS device:{/t} <a href="#" onClick="javascript:window.open('garmin.php?&lat={$cache.latitude}&lon={$cache.longitude}&wp={$cache.wpoc}','{t escape=js}Send{/t}','width=640,height=290,resizable=no,scrollbars=1')">Garmin</a><br />
			<img src="resource2/{$opt.template.style}/images/viewcache/16x16-save.png" class="icon16" alt="" /> {t}Download as file:{/t} 
			<a href="search.php?searchto=searchbycacheid&showresult=1&f_inactive=0&f_ignored=0&startat=0&cacheid={$cache.cacheid}&output=gpx" rel="nofollow" title="{t}GPS Exchange Format .gpx{/t}">GPX</a> - 
			<a href="search.php?searchto=searchbycacheid&showresult=1&f_inactive=0&f_ignored=0&startat=0&cacheid={$cache.cacheid}&output=loc" rel="nofollow" title="{t}Waypointfile .loc{/t}">LOC</a> - 
			<a href="search.php?searchto=searchbycacheid&showresult=1&f_inactive=0&f_ignored=0&startat=0&cacheid={$cache.cacheid}&output=kml" rel="nofollow" title="{t}Google Earth .kml{/t}">KML</a> - 
			<a href="http://maps.google.de/maps?f=q&hl=de&q={$opt.page.absolute_url|escape:'url'}search.php%3Fsearchto%3Dsearchbydistance%26showresult%3D1%26expert%3D0%26output%3Dkml%26sort%3Dbydistance%26f_userowner%3D0%26f_userfound%3D0%26f_inactive%3D0%26%26f_ignored%3D0%26lat%3D{$cache.latitude}%26lon%3D{$cache.longitude}%26distance%3D50%26unit%3Dkm%26zip%3D1%26count%3Dmax" rel="nofollow" title="{t}Show in Google Maps{/t}">{t}(in GM){/t}</a> - 
			<a href="search.php?searchto=searchbycacheid&showresult=1&f_inactive=0&f_ignored=0&startat=0&cacheid={$cache.cacheid}&output=ov2" rel="nofollow" title="{t}TomTom POI .ov2{/t}">OV2</a> - 
			<a href="search.php?searchto=searchbycacheid&showresult=1&f_inactive=0&f_ignored=0&startat=0&cacheid={$cache.cacheid}&output=ovl" rel="nofollow" title="{t}TOP50-Overlay .ovl{/t}">OVL</a> - 
			<a href="search.php?searchto=searchbycacheid&showresult=1&f_inactive=0&f_ignored=0&startat=0s&cacheid={$cache.cacheid}&output=txt" rel="nofollow" title="{t}Textfile .txt{/t}">TXT</a>
			<br />

			<small>
				<img src="resource2/{$opt.template.style}/images/viewcache/16x16-info.png" class="icon16" alt="Terms of use" /> 
				{t}When downloading this file, you accept our <a href="articles.php?page=impressum#tos">terms of use</a> and <a href="articles.php?page=impressum#datalicense" target="_blank">Datalicense</a>.{/t}			</small>
			<br />
		</p>
	{/if}
</div>
<!-- End Utilities -->

<!-- GK -->
{if $geokret_count!=0}
	<div class="content2-container bg-blue02">
		<p class="content-title-noshade-size2">
			<img src="resource2/{$opt.template.style}/images/description/22x22-geokret.gif" style="align: left; margin-right: 10px;" width="22" height="22" alt="{t}Geokrets{/t}" />
			{t}Geokrets{/t}
		</p>
	</div>

	<div class="content2-container">
 		<p>
 			{foreach from=$geokret item=geokretItem name=geokret}
				<a href="http://geokrety.org/konkret.php?id={$geokretItem.id}" target="_blank">{$geokretItem.itemname|escape}</a> 
				{t}by{/t} 
				{$geokretItem.username|escape}

				{if !$smarty.foreach.geokret.last}<br />{/if}
			{/foreach}
		</p>
	</div>
{/if}
<!-- End GK -->

<!-- Logs -->
{include file="res_logentry.tpl" header=true footer=true footbacklink=false logs=$logs cache=$cache}

{if $showalllogs}
	<div class="content2-container bg-blue02">
  	<p class="content-title-noshade-size2">
  		<img src="resource2/{$opt.template.style}/images/action/16x16-showall.png" style="align: left; margin-right: 10px;" width="16" height="16" alt="{t}Show all logentries{/t}" />  
  		[<a href="viewlogs.php?cacheid={$cache.cacheid}">{t}Show all logentries{/t}</a>]
  	</p>
	</div>
{/if}
<!-- End Logs -->
