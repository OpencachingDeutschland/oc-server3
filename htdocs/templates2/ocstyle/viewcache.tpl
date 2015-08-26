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

	window.setTimeout("visitCounter()", 1000);

	function loadRestOfLogs()
	{
		var xmlhttp = createXMLHttp();
		if (!xmlhttp)
			return;

		document.getElementById('showalllogs_img').src = 'resource2/ocstyle/images/misc/16x16-ajax-loader.gif';
		document.getElementById('showalllogs_text').innerHTML = "{/literal}{t}Loading more log entries ...{/t}{literal}";

		xmlhttp.onreadystatechange = function()
		{
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
			{
				var logblockstart = xmlhttp.responseText.indexOf('<ocloadlogs>');
				var logblockend = xmlhttp.responseText.indexOf('</ocloadlogs>');
				if (logblockstart > 0 && logblockend > logblockstart)
				{
					document.getElementById('logblock').innerHTML = xmlhttp.responseText.substring(logblockstart+12, logblockend);
					init_enlargeit_for_logentries();
				}
			}
		}
		xmlhttp.open("GET", "viewlogs.php?cacheid={/literal}{$cache.cacheid}{literal}&tagloadlogs=1", true);
		xmlhttp.send();
	}

	function onScroll(oEvent)
	{
		if (scrolledToBottom(70))
		{
			window.onscroll = null;
			loadRestOfLogs();
		}
	}

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
	<!--[if IE]><div></div><![endif]-->
{/if}

<!-- Cachemeta -->
<div class="content2-container line-box" style="float:left">  {* div height will be wroing without float:left - why *}  
	<div class="">
		<div class="nav4">
			<ul id="cachemenu">
				<li class="title" >Cache Menu</li>
				{if $cache.log_allowed || $cache.adminlog}
					<li class="group {if $cache.adminlog}hilite{/if}"><a style="background-image: url(resource2/{$opt.template.style}/images/viewcache/new-entry-18.png);background-repeat:no-repeat;background-position:left center;" href="{if $login.userid!=0}log.php?cacheid={$cache.cacheid|urlencode}{else}login.php?target=log.php%3Fcacheid%3D{$cache.cacheid|urlencode|urlencode}{/if}">{t}Log this cache{/t}</a></li>
				{/if}

				{if $watched==1}  {* is always false of not logged in *}
					<li class="group"><a style="background-image: url(resource2/{$opt.template.style}/images/viewcache/watch-18.png);background-repeat:no-repeat;background-position:left center;" href="mywatches.php?action=remove&amp;cacheid={$cache.cacheid|urlencode}&amp;target=viewcache.php%3Fcacheid%3D{$cache.cacheid|urlencode}%26watchinfo=1">{t}Don't watch{/t}</a></li>
				{else}
					<li class="group"><a style="background-image: url(resource2/{$opt.template.style}/images/viewcache/watch-18.png);background-repeat:no-repeat;background-position:left center;" href="{if $login.userid!=0}mywatches.php?action=add&amp;cacheid={$cache.cacheid|urlencode}&amp;target=viewcache.php%3Fcacheid%3D{$cache.cacheid|urlencode}%26watchinfo=1{else}login.php?target=mywatches.php%3Faction%3Dadd%26cacheid%3D{$cache.cacheid|urlencode|urlencode}%26target%3Dviewcache.php%253Fcacheid%253D{$cache.cacheid|urlencode|urlencode}{/if}">{t}Watch{/t}</a></li>
				{/if}

				{if $login.userid!=0}
					{if $ignored==1}
						<li class="group"><a style="background-image: url(resource2/{$opt.template.style}/images/viewcache/ignore-18.png);background-repeat:no-repeat;background-position:left center;" href="ignore.php?cacheid={$cache.cacheid|urlencode}&amp;action=removeignore">{t}Don't ignore{/t}</a></li>
					{else}
						<li class="group"><a style="background-image: url(resource2/{$opt.template.style}/images/viewcache/ignore-18.png);background-repeat:no-repeat;background-position:left center;" href="ignore.php?cacheid={$cache.cacheid|urlencode}&amp;action=addignore">{t}Ignore{/t}</a></li>
					{/if}
				{/if}
				<li class="group"><a style="background-image: url(resource2/{$opt.template.style}/images/viewcache/list-18.png);background-repeat:no-repeat;background-position:left center;" href="addtolist.php?cacheid={$cache.cacheid|urlencode}">{t}Add to list{/t}</a></li>
				{if $login.userid!=0}
					{if $login.userid==$cache.userid || $listing_admin }
						<li class="group {if $login.userid!=$cache.userid}hilite{/if}"><a style="background-image: url(resource2/{$opt.template.style}/images/viewcache/page.png);background-repeat:no-repeat;background-position:left center;" href="editcache.php?cacheid={$cache.cacheid|urlencode}">{t}Edit{/t}</a></li>
					{/if}
				{/if}	

				<li class="group"><a style="background-image: url(resource2/{$opt.template.style}/images/viewcache/report-problem-18.png);background-repeat:no-repeat;background-position:left center;" href="{if $login.userid!=0}reportcache.php?cacheid={$cache.cacheid|urlencode}{else}login.php?target=reportcache.php%3Fcacheid%3D{$cache.cacheid|urlencode|urlencode}{/if}">{t}Report this cache{/t}</a></li>
			</ul>
		</div>
		{if $watchclinfo && $watched==0}
			<div style="clear:both; padding-top:4px">
				<p>
					<img src="resource2/{$opt.template.style}/images/viewcache/16x16-info.png" class="icon16" alt="Info" />
					{t}You are still watching this geocache via one or more cache lists.{/t}
				</p>
			</div>
		{/if}
		<div class="buffer" style="width: 500px;">&nbsp;</div>

		<div class="content2-container-2col-left" style="width:60px; clear: left;">
			<div><a href="articles.php?page=cacheinfo#cachetype">{include file="res_cacheicon.tpl" cachetype=$cache.type status=$cache.status}</a></div>
			<div><a href="articles.php?page=cacheinfo#difficulty">{include file="res_difficon.tpl" difficulty=$cache.difficulty}</a></div>
			<div><a href="articles.php?page=cacheinfo#difficulty">{include file="res_terricon.tpl" terrain=$cache.terrain}</a></div>
			<div></div>
		</div>

		<div class="content2-container-2col-left" id="cache_name_block">
			<span class="content-title-noshade-size5">{$cache.name|escape}</span>
			{if $cache.shortdesc!=''}
				<!-- <br /> --><p class="content-title-noshade-size1">&nbsp;{$cache.shortdesc|escape}</p>
			{/if}

			<p>{t}by{/t}&nbsp;<b><a href="viewprofile.php?userid={$cache.userid}">{$cache.username|escape}</a></b>&nbsp;&nbsp;
				<span style="color: rgb(88, 144, 168); font-weight: bold;">
					{if $cache.code1=="" or $cache.code1 != $cache.countryCode}
						<img src="images/flags/{$cache.countryCode|lower}.gif" style="vertical-align:middle" />&nbsp; {$cache.country|escape}
					{else}
						<img src="images/flags/{$cache.code1|lower}.gif" style="vertical-align:middle" />&nbsp;
						{$cache.adm1|escape} {if $cache.adm1!=null & $cache.adm2!=null} &gt; {/if}
						{$cache.adm2|escape} {if ($cache.adm2!=null & $cache.adm4!=null) | ($cache.adm1!=null & $cache.adm4!=null)} &gt; {/if}
						{$cache.adm4|escape}
					{/if}
				</span>
			</p>
			{if $cache.type==6}
				<span class="participants"><img src="resource2/{$opt.template.style}/images/cacheicon/16x16-event.gif" width="16" height="16" alt="{t}List of participants{/t}" />&nbsp;<a href="#" onclick="window.open('event_attendance.php?id={$cache.cacheid}&popup=y','{t escape=js}List{/t}','width=320,height=440,resizable=no,scrollbars=1')">{t}List of participants{/t}</a></span>
			{/if}
		</div>
	</div>
</div>
<!-- End Cachemeta -->

{if $show_logpics}
	<!-- picture gallery -->
	<div class="content2-container">
		{include file="res_logpictures.tpl" logdate=true loguser=true profilelink=true shortyear=true}

		{if $cache.type != 5 && $cache.type != 6}
			<br />
			<img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" alt="" align="middle" />
			{t}"Spoiler" pictures which show details of the stash should not be visible here. If you encounter an open visible spoiler, you may contact the logger by the e-mail button in his profile and ask him to mark it as spoiler.{/t}<br />
		{/if}
	</div>

{else}
<!-- Warning, if temporary not available, archived or locked -->
	{include file="res_state_warning.tpl" cache=$cache}
<!--  End Warning -->

<!-- Cachedetails -->
<div class="content2-container" style="margin-bottom:8px">
	<table cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td style="vertical-align:top">
				<table>
					<tr><td colspan="2">
						<p class="content-title-noshade-size2">
							<img src="resource2/{$opt.template.style}/images/viewcache/kompass.png" class="icon32" alt="" title="" />
							<b><nobr>{$coordinates.lat|escape}</nobr> <nobr>{$coordinates.lon|escape}</nobr></b> <span class="content-title-noshade-size0">(WGS84)</span><br />  {* Ocprop: <b><nobr>([N|S].*?)&#039;<\/nobr> <nobr>([E|W].*?)&#039;<\/nobr><\/b>.*?WGS84 *}
						</p>
					</td></tr>
					<tr><td style="vertical-align:top; width:370px">
		<p style="line-height: 1.6em;">
			<img src="resource2/{$opt.template.style}/images/viewcache/map.png" class="icon16" alt="" title="" align="middle" />&nbsp;<a href="#" onclick="window.open('coordinates.php?lat={$cache.latitude}&lon={$cache.longitude}&popup=y&wp={$cache.wpoc}','{t escape=js}Coordinates{/t}','width=280,height=500,resizable=no,scrollbars=1')">{t}Convert coordinates{/t}</a><br />
			<!-- <img src="resource2/{$opt.template.style}/images/viewcache/box.png" class="icon16" alt="" title="" align="middle" />&nbsp;Cache type: <b>Traditional</b><br /> -->
			<img src="resource2/{$opt.template.style}/images/viewcache/package_green.png" class="icon16" alt="" title="" align="middle" />&nbsp;{t}Size:{/t} <b>{$cache.sizeName|escape}</b><br />
			<img src="resource2/{$opt.template.style}/images/viewcache/page.png" class="icon16" alt="" title="" align="middle" />
			{if $cache.status!=1}  {* Ocprop: Status: <span class=\"errormsg\">Gesperrt<\/span> *}
				{t}State:{/t} <span class="errormsg">{$cache.statusName|escape}</span>
			{else}
				{t}State:{/t} {$cache.statusName|escape}
			{/if}<br />
			{if $cache.searchtime>0}
			<img src="resource2/{$opt.template.style}/images/viewcache/time.png" class="icon16" alt="" title="" align="middle" />&nbsp;{t}Time required:{/t} {$cache.searchtime|format_hour} h&nbsp;
			{/if}
			{if $cache.waylength>0}
				<span style="white-space:nowrap"><img src="resource2/{$opt.template.style}/images/viewcache/arrow_roundtrip.png" class="icon16" alt="" title="" align="middle" />&nbsp;{t}Waylength:{/t} {$cache.waylength} km</span>
			{/if}
			{if $cache.searchtime>0 || $cache.waylength>0}<br />{/if}
			<img src="resource2/{$opt.template.style}/images/viewcache/date.png" class="icon16" alt="" title="" align="middle" />&nbsp;{if $cache.type==6}{t}Event date:{/t}{else}{t}Hidden on:{/t}{/if} {$cache.datehidden|date_format:$opt.format.datelong}<br />
			<img src="resource2/{$opt.template.style}/images/viewcache/date.png" class="icon16" alt="" title="" align="middle" />&nbsp;{if $cache.is_publishdate==0}{t}Listed since:{/t}{else}{t}Published on:{/t}{/if} {$cache.datecreated|date_format:$opt.format.datelong}<br />
			<img src="resource2/{$opt.template.style}/images/viewcache/date.png" class="icon16" alt="" title="" align="middle" />&nbsp;{t}Last update:{/t} {$cache.lastmodified|date_format:$opt.format.datelong}<br />  {* Ocprop: <br />\s*Wegpunkt: (OC[A-Z0-9]+)\s*<br /> -- Waypoint: <b>(OC[A-Z0-9]+)<\/b><br \/> *}
			<!-- Ocprop: <br /> Wegpunkt: <b>{$cache.wpoc}</b><br /> -->
			<img src="resource2/{$opt.template.style}/images/viewcache/arrow_in.png" class="icon16" alt="" title="" align="middle" />&nbsp;{t}Listing{/t}{t}#colonspace#{/t}: {if $shortlink_domain !== false}{$shortlink_domain}/{/if}<b>{$cache.wpoc}</b><br />
			{if $cache.wpgc!='' || $cache.wpnc!=''}<img src="resource2/{$opt.template.style}/images/viewcache/link.png" class="icon16" alt="" title="" align="middle" />
				{t}Also listed at:{/t}  {* Ocprop: Auch gelistet auf: <a href=\"http://www\.geocaching\.com/seek/cache_details\.aspx\?wp=(GC[0-9A-Z]{1,5})\" target=\"_blank\">geocaching.com</a> *}
				{if $cache.wpgc!=''}
					<a href="http://www.geocaching.com/seek/cache_details.aspx?wp={$cache.wpgc}" target="_blank">geocaching.com&nbsp;</a>
				{/if}
				{if $cache.wpnc!=''}
					<a href="http://www.navicache.com/cgi-bin/db/displaycache2.pl?CacheID={nccacheid wp=$cache.wpnc}" target="_blank">navicache.com</a>
				{/if}
			{/if}
		</p>
					</td>
					<td style="vertical-align:top">
						<p style="line-height: 1.4em;">
							<img src="resource2/{$opt.template.style}/images/{if $cache.type==6}log{else}viewcache{/if}/16x16-{if $cache.type==6}attended{else}found{/if}.png" class="icon16" alt="" /> {$cache.found} {if $cache.type==6} {t}Attended{/t}{else}{t}Found{/t}{/if}<br />
							<img src="resource2/{$opt.template.style}/images/{if $cache.type==6}log{else}viewcache{/if}/16x16-{if $cache.type==6}will_attend{else}dnf{/if}.png" class="icon16" alt="" /> {if $cache.type==6} {$cache.willattend} {t}Will attend{/t}{else} {$cache.notfound} {t}Not found{/t}{/if}<br />
							<img src="resource2/{$opt.template.style}/images/viewcache/16x16-note.png" class="icon16" alt="" /> {$cache.note} {if $cache.note==1}{t}Note{/t}{else}{t}Notes{/t}{/if}<br />
							{if $cache.maintenance}<img src="resource2/{$opt.template.style}/images/viewcache/16x16-maintenance.png" class="icon16" alt="" /> {$cache.maintenance} {if $cache.maintenance==1}{t}Maintenance log{/t}{else}{t}Maintenance logs{/t}{/if}<br />{/if}
							<img src="resource2/{$opt.template.style}/images/viewcache/16x16-watch.png" class="icon16" alt="" /> {$cache.watcher} {if $cache.watcher==1}{t}Watcher{/t}{else}{t}Watchers{/t}{/if}<br />
							<img src="resource2/{$opt.template.style}/images/viewcache/ignore-16.png" class="icon16" alt="" /> {$cache.ignorercount} {if $cache.ignorecount==1}{t}Ignorer{/t}{else}{t}Ignorers{/t}{/if}<br />
							<img src="resource2/{$opt.template.style}/images/viewcache/16x16-visitors.png" class="icon16" alt="" /> {$cache.visits} {if $cache.visits==1}{t}Page visit{/t}{else}{t}Page visits{/t}{/if}<br />
							<span style="white-space:nowrap;"><img src="resource2/{$opt.template.style}/images/viewcache/16x16-pictures.png" class="icon16" alt="" /> {$logpics} {if $logpics>0}<a class="link" href="viewcache.php?cacheid={$cache.cacheid|urlencode}&logpics=1">{/if}{if $logpics==1}{t}Log picture{/t}{else}{t}Log pictures{/t}{/if}{if $logpics>0}</a>{/if}</span><br />
							<span style="white-space:nowrap;"><img src="resource2/{$opt.template.style}/images/viewcache/gk.png" class="icon16" alt="" title="" /> <a href="http://geokrety.org/szukaj.php?lang={$opt.geokrety.locales[$opt.template.locale]}&wpt={$cache.wpoc}" target="_blank">{t}Geokrety history{/t}</a></span><br />
							{if $cache.topratings>0}
								<img src="resource2/{$opt.template.style}/images/viewcache/rating-star.gif" class="icon16" alt="" /> {$cache.topratings} {t}Recommendations{/t}<br />
							{/if}
						</p>
					</td>
					</tr>
				</table>
			</td>
			
			<td style="text-align:right">
				<a href="map2.php?wp={$cache.wpoc}" target="_blank">
				{if $cachemap.iframe}
					<div class="img-shadow">
						<iframe src="{$cachemap.url}" width="185px" height="185px" frameborder="0">
						</iframe>
					</div>
				{else}
					<img src="{$cachemap.url}" height="185px" width="185px" />
				{/if}
				</a>
				<p style="margin-right:0"><a href="map2.php?wp={$cache.wpoc}" target="_blank"><span style="line-height:1.5em">{t}Large map{/t}</span></a></p>
			</td>
		</tr>
		
		<tr>
			<td colspan="2"><p>
				<img src="resource2/{$opt.template.style}/images/viewcache/print-18.png" class="icon16" alt="" />
				<select class="exportlist" onchange="location.href=this.options[this.selectedIndex].value+'&nocrypt='+bNoCrypt">
					<option value="#">{t}Print{/t} …</option>
					<option value="viewcache.php?cacheid={$cache.cacheid}&print=y&log=N">{t}without logs{/t}</option>
					<option value="viewcache.php?cacheid={$cache.cacheid}&print=y&log=5">{t}with 5 logs{/t}</option>
					<option value="viewcache.php?cacheid={$cache.cacheid}&print=y&log=10">{t}with 10 logs{/t}</option>
					<option value="viewcache.php?cacheid={$cache.cacheid}&print=y&log=A">{t}with all logs{/t}</option>
				</select>&nbsp;
				<img src="resource2/{$opt.template.style}/images/viewcache/16x16-save.png" class="icon16" alt="" />
				<select class="exportlist" onchange="location.href=this.options[this.selectedIndex].value"> 
					<option value="#">{t}Download as...{/t}</option>
					<option value="search.php?searchto=searchbycacheid&showresult=1&f_inactive=0&f_ignored=0&startat=0&cacheid={$cache.cacheid}&output=gpx">GPX</option>
					<option value="search.php?searchto=searchbycacheid&showresult=1&f_inactive=0&f_ignored=0&startat=0&cacheid={$cache.cacheid}&output=loc">LOC</option>
					<option value="search.php?searchto=searchbycacheid&showresult=1&f_inactive=0&f_ignored=0&startat=0&cacheid={$cache.cacheid}&output=kml">KML</option>
					<option value="search.php?searchto=searchbycacheid&showresult=1&f_inactive=0&f_ignored=0&startat=0&cacheid={$cache.cacheid}&output=ov2">OV2</option>
					<option value="search.php?searchto=searchbycacheid&showresult=1&f_inactive=0&f_ignored=0&startat=0&cacheid={$cache.cacheid}&output=ovl">OVL</option>
					<option value="search.php?searchto=searchbycacheid&showresult=1&f_inactive=0&f_ignored=0&startat=0&cacheid={$cache.cacheid}&output=txt">TXT</option>
				</select>&nbsp;
				<img src="resource2/{$opt.template.style}/images/viewcache/14x19-gps-device.png" class="icon16" alt="" />
				<input type="button" class="exportbutton" value="{t}Send to GPS device{/t}" onclick="window.open('garmin.php?lat={$cache.latitude}&lon={$cache.longitude}&wp={$cache.wpoc}','{t escape=js}Send{/t}','width=640,height=320,resizable=no,scrollbars=1')" />
			</p></td>	
		</tr>
	</table>

</div>
<!-- End Cachedetails -->

<!-- Attributes & cache lists -->
<script type="text/javascript">
function showalllists()
{literal}{{/literal}
	document.getElementById('morelists').style.display = 'none';
	for (n=5; n<={$cachelists|@count}; ++n)
		document.getElementById('cachelist' + n.toString()).style.display = '';
{literal}}{/literal}
</script>

{if $attributes|@count || $cachelists|@count}
	<div class="content2-container line-box" style="height:0px"></div> {* MSIE needs explicit height *}
	<div class="content2-container">
		{if $cachelists|@count}
			<div style="float:right; min-width:183px; max-width:350px; margin-left:32px; margin-bottom:12px; padding-left:16px; {if $attributes|@count}border-left:1px solid #c0c0c0{/if}">
				<p><a href="cachelists.php"><img src="resource2/{$opt.template.style}/images/misc/16x16-list.png" title="{t}Cache list{/t}" style="padding-right:1px"/></a> <b>{t}Cache lists{/t}</b></p>
				{assign var="n" value="0"}
				{foreach from=$cachelists item=cachelist}
					{assign var="n" value=$n+1}
					{if $n==5 && $cachelists|@count > 5}
						<p id="morelists" style="padding:0; line-height:1em"><a href="javascript:showalllists()">{t}more{/t} ...</a></p>
					{/if}
					<p id="cachelist{$n}" style="padding:0; line-height:1em; {if $n>=5 && $cachelists|@count > 5}display:none{/if}">
					{include file="res_cachelist_link.tpl"}{if $cachelist.user_id != $login.userid || ($cachelist.visibility==3 && $cache.show_cachelists)} {t}by{/t} <a href="viewprofile.php?userid={$cachelist.user_id}">{$cachelist.username|escape}</a>{else}<span class="greytext"> ({if $cachelist.visibility==3}{t}hidden[cachelist]{/t}{elseif $cachelist.visibility==2}{t}semi-public{/t}{else}{t}private{/t}{/if})</span>{/if}
					</p>
				{/foreach}
			</div>
		{/if}
		{if $attributes|@count}
			<div style="padding-left:2px">
				<p style="line-height: 1.6em;">
					{include file="res_attribgroup.tpl" attriblist=$attributes}
				</p>
			</div>
		{/if}
	</div>
	<div style="clear:both; height:2px"></div>
{else}
	<br />
{/if}
<!-- End Attributes & cache lists-->

<!-- Description -->
<div class="content2-container bg-blue02">
	<p class="content-title-noshade-size2">
		<img src="resource2/{$opt.template.style}/images/description/22x22-description.png" style="margin-right: 10px;" width="22" height="22" alt="{t}Description{/t}" /> {t}Description{/t}&nbsp;&nbsp;&nbsp;
		<span class="content-title-link" style="font-weight:bold" >
		{foreach from=$cache.desclanguages item=desclanguagesItem name=desclanguagesItem}
				{if $smarty.foreach.desclanguagesItem.first==false} &nbsp;&middot;&nbsp; {/if}
				{if $cache.desclanguage==$desclanguagesItem.code}
					<span class="txt-black" >{$desclanguagesItem.native_name|escape}</span>
					{if $desclanguagesItem.name != $desclanguagesItem.native_name}
						<span style="font-weight:normal" class="txt-black">({$desclanguagesItem.name})</span>
					{/if}
				{else}
					<a href="viewcache.php?wp={$cache.wpoc}&desclang={$desclanguagesItem.code|escape}" class="systemlink" title="{$desclanguagesItem.name}" >{$desclanguagesItem.native_name|escape}</a>
				{/if}
		{/foreach}
		</span>
  </p>
</div>

<div style="height:2px"></div>
<div class="content2-container cachedesc">
	{if $cache.deschtml==0}
		{$cache.desc|smiley|hyperlink}
	{else}
		{$cache.desc|smiley}
	{/if}
</div>
<!-- End Description -->

<!-- Personal Note -->
{if $enableCacheNote}
	<div class="content2-container bg-blue02" id="personal_note">
		<p class="content-title-noshade-size2">
			<img src="resource2/{$opt.template.style}/images/description/20x20-cache-note.png" style="margin-right: 10px;" alt="{t}Personal cache note{/t}" /> 
			{t}Personal cache note{/t}
		</p>
	</div>

	<div class="content2-container">
		<form action="viewcache.php" method="post" name="cache_note">
			{include file='cache_note.tpl'}
		</form>
	</div>
{/if}
<!-- End Personal Note -->

<!-- Additional Waypoints / Child Waypoints-->
{if count($childWaypoints)>0}
	<div class="content2-container bg-blue02 content2-section-no-p">
		<p class="content-title-noshade-size2">
			<img src="resource2/{$opt.template.style}/images/description/20x20-compass.png" style="margin-right: 10px;" alt="{t}Additional waypoints{/t}" /> 
			{t}Additional waypoints{/t}
		</p>
	</div>

	<div class="content2-container content2-section-no-p" style="margin:4px 0 0 10px" >
		<table class="waypointtable" cellpadding="5%" cellspacing="1">
		{foreach from=$childWaypoints item=childWaypoint}
			<tr bgcolor="{cycle values="#ffffff,#f4f4f4"}">
				<td width="25%"><table cellspacing="0" cellpadding="0"><tr><td><img src="{$childWaypoint.image}" /></td><td>{$childWaypoint.name|escape}</td></tr></table></td>
				<td class="wpt_text" width="18%">{$childWaypoint.coordinateHtml}</td>
				<td class="wpt_text" >{$childWaypoint.description|escape|replace:"\r\n":"<br />"}</td>
			</tr>
		{/foreach}
		</table>
		<div style="padding-top:4px">
			<img src="resource2/{$opt.template.style}/images/viewcache/16x16-info.png" class="icon16" alt="Info" />
		{t}The additional waypoints are shown on the map when the cache is selected, are included in GPX file downloads and will be sent to the GPS device.{/t}
		</div>
	</div>
{/if}
<!-- End Addtional Waypoints -->

<!-- Hints -->
{if $cache.hint!=''}
	<div class="content2-container bg-blue02">
  	<p class="content-title-noshade-size2">
  		<img src="resource2/{$opt.template.style}/images/description/22x22-encrypted.png" style="margin-right: 10px;" width="22" height="22" alt="{t}Additional hint{/t}" /> {t}Additional hint{/t}&nbsp;&nbsp;
  		<span id="decrypt-info">{if $crypt}
				<img src="resource2/{$opt.template.style}/images/viewcache/decrypt.png" class="icon32" width="22" height="22" alt="" />
				<span style="font-weight: 400;"><a href="viewcache.php?wp={$cache.wpoc}&nocrypt=1&desclang={$cache.desclanguage|urlencode}#decrypt-info" {literal}onclick="var ch = document.getElementById('decrypt-hints').childNodes;for(var i=0;i < ch.length;++i) {var e = ch[i]; decrypt(e);} document.getElementById('decrypt-info').style.display = 'none';
				bNoCrypt = 1;
				return false;"{/literal}>{t}Decrypt{/t}</a>{/if}</span>
			</span>
		</p>
	</div>

	<div class="content2-container">
		<div style="float:left; margin:0 0 12px 10px">
			<p id="decrypt-hints">{if $crypt}{$cache.hint|rot13html}{else}{$cache.hint}{/if}</p>
		</div>
		<div style="float:right; margin-bottom:12px">
			<p>
				<font style="font-family: 'Courier New',FreeMono,Monospace;" face="Courier" size="2">A|B|C|D|E|F|G|H|I|J|K|L|M</font>
				<br />
				<font style="font-family: 'Courier New',FreeMono,Monospace;" face="Courier" size="2">N|O|P|Q|R|S|T|U|V|W|X|Y|Z</font>
			</p>
		</div>
	</div>
{/if}
<!-- End Hints -->

<!-- Pictures -->
{if count($pictures)>0}
	<div class="content2-container bg-blue02">
		<p class="content-title-noshade-size2">
			<img src="resource2/{$opt.template.style}/images/description/22x22-image.png" style="margin-right: 10px;" width="22" height="22" alt="{t}Pictures{/t}" /> 
			{t}Pictures{/t}
		</p>
	</div>

	<div class="content2-container content2-section-no-p" style="float:left"> {* bottom margin would not work without float:left - why? *}
		{foreach from=$pictures item=pictureItem}
			<div class="viewcache-pictureblock">
				<div class="img-shadow">
					<!-- a href="{$pictureItem.url|escape}" target="_blank" -->
						<img src="thumbs.php?uuid={$pictureItem.uuid|urlencode}" alt="{$pictureItem.title|escape}" title="{$pictureItem.title|escape}" longdesc="{$pictureItem.url|escape}" border="0" align="bottom" onclick="enlarge(this)" />
					<!-- /a -->
				</div>
				<span class="title">{$pictureItem.title|escape}</span>
			</div>
		{/foreach}
	</div>
{/if}
<!-- End Pictures -->

<!-- Utilities -->
<div class="content2-container bg-blue02">
	<p class="content-title-noshade-size2">
		<img src="resource2/{$opt.template.style}/images/description/22x22-utility.png" style="margin-right: 10px;" width="22" height="22" alt="{t}Utilities{/t}" /> 
		{t}Utilities{/t}
	</p>
</div>

<div class="content2-container">
	{if count($npaareasWarning) > 0}
		<div style="border: solid 1px red; padding:10px 10px 0px 10px; margin: 3px 0 8px 0">
			<p style="line-height: 1.6em;">
				<img src="resource2/{$opt.template.style}/images/viewcache/npa.gif" align="left" style="margin-right: 25px;" width="32" height="32" alt="NSG/NPA" />
				{t 1=$npahelplink}This geocache is probably placed within a nature protection area! See %1here</a> for further informations, please.{/t}<br />
				{foreach from=$npaareasWarning item=npaItem name=npaareas}
					{$npaItem.npaTypeName|escape} 
					{$npaItem.npaName|escape} (<a href="http://www.google.de/search?q={$npaItem.npaTypeName|urlencode}+{$npaItem.npaName|urlencode}" target="_blank">{t}Info{/t}</a>){if !$smarty.foreach.npaareas.last},{/if}
				{/foreach}
			</p>
		</div>
	{/if}

	{if count($npaareasNoWarning) > 0}
		<p style="line-height: 1.6em;">
			{t 1=$npahelplink}This geocache is probably placed within the following protected areas (%1Info</a>):{/t}
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

			<img src="resource2/{$opt.template.style}/images/viewcache/16x16-search.png" class="icon16" alt="" /> {t}Send this waypoint to GPS device:{/t} <a href="#" onclick="window.open('garmin.php?&lat={$cache.latitude}&lon={$cache.longitude}&wp={$cache.wpoc}','{t escape=js}Send{/t}','width=640,height=290,resizable=no,scrollbars=1')">Garmin</a><br />
			<img src="resource2/{$opt.template.style}/images/viewcache/16x16-save.png" class="icon16" alt="" /> {t}Download as file:{/t} 
			<a href="search.php?searchto=searchbycacheid&showresult=1&f_inactive=0&f_ignored=0&startat=0&cacheid={$cache.cacheid}&output=gpx" rel="nofollow" title="{t}GPS Exchange Format .gpx{/t}">GPX</a> - 
			<a href="search.php?searchto=searchbycacheid&showresult=1&f_inactive=0&f_ignored=0&startat=0&cacheid={$cache.cacheid}&output=loc" rel="nofollow" title="{t}Waypointfile .loc{/t}">LOC</a> - 
			<a href="search.php?searchto=searchbycacheid&showresult=1&f_inactive=0&f_ignored=0&startat=0&cacheid={$cache.cacheid}&output=kml" rel="nofollow" title="{t}Google Earth .kml{/t}">KML</a> - 
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
			<img src="resource2/{$opt.template.style}/images/description/22x22-geokret.gif" style="margin-right: 10px;" width="22" height="22" alt="{t}Geokrets{/t}" />
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
<div id="logblock">
	{include file="res_logentry.tpl" header_footer=true footbacklink=false logs=$logs cache=$cache}

	{if $morelogs}
		<div class="content2-container bg-blue02">
			<p id="showalllogs" class="content-title-noshade-size2">
				<img id="showalllogs_img" src="resource2/{$opt.template.style}/images/action/16x16-showall.png" style="margin-right: 10px;" width="16" height="16" alt="{t}Show all logentries{/t}" />  
				<span id="showalllogs_text">[<a href="viewcache.php?cacheid={$cache.cacheid}&log=A#logentries">{t}Show all logentries{/t}</a>]</span>
			</p>
		</div>
		<div style="clear:both"></div>  {* MSIE needs this to keep some space below the show-all-logs block *}
	{/if}
</div>

{if $morelogs && $autoload_logs}
<script type="text/javascript">
	window.onscroll = onScroll;
</script>
{/if}

{if $enableCacheNote && isset($coord_error)}
<script type="text/javascript">
	document.getElementById('personal_note').scrollIntoView();
</script>
{/if}

<!-- End Logs -->
{/if}  {* not $show_logpics *}
