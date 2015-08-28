{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
*
*  Output formatter for the 'html' search option:
*  Show search results.
***************************************************************************}

<script type="text/javascript" src="resource2/{$opt.template.style}/js/wz_tooltip.js"></script>

{if $cachelist}
	<div class="content2-container cachelistinfo" style="margin-top:10px" >
	<p style="margin-top:0.5em; padding-left:10px; padding-right:8px">
		<img src="resource2/ocstyle/images/misc/16x16-list.png" />
		<a class="systemlink" href="cachelists.php">{t}Cache list{/t}</a> <b>{$cachelist.name|escape}</b>
		{if $cachelist.bookmarked}<a href="mylists.php#bookmarks"><img src="resource2/{$opt.template.style}/images/viewcache/cache-rate.png" title="{t}I have bookmarked this list.{/t}" /></a>{/if}
		{if $cachelist.watched_by_me}<img src="resource2/{$opt.template.style}/images/viewcache/16x16-watch.png" title="{t}I am watching this list.{/t}" />{/if}
		{if $cachelist.user_id != $login.userid}{t}by{/t} <a href="viewprofile.php?userid={$cachelist.user_id}">{$cachelist.username|escape}</a>{else}({if $cachelist.password}<a class="jslink" onclick="cl = getElementById('sharelist_{$cachelist.id}'); cl.style.display = (cl.style.display=='none'?'block':'none'); getElementById('permalink_text_{$cachelist.id}').select();" {if $cachelist.user_id==$login.userid}title="{t}List has password; click here to share it{/t}"{/if} >{t}private{/t} <img src="resource2/{$opt.template.style}/images/action/18x16-offer.png" /></a>{else}{t}private{/t}{/if}){/if}
		{if $cachelist.password}
			<div id="sharelist_{$cachelist.id}" class="cachelist-popup mapboxframe mapboxshadow" style="display:none" >
				<table>
					<tr><td><img src="resource2/ocstyle/images/viewcache/link.png" alt="" height="16" width="16" /> {t}Link to share this cache list:{/t}</td><td align="right"><a class="jslink" onclick="getElementById('sharelist_{$cachelist.id}').style.display='none'"><img src="resource2/ocstyle/images/navigation/19x19-close.png" style="opacity:0.7" /></a></td></tr>
					<tr><td><input id="permalink_text_{$cachelist.id}" type="text" value="{$opt.page.absolute_url}cachelist.php?id={$cachelist.id}&key={$cachelist.password|urlencode}" size="65" /></td></tr>
				</table>
			</div>
		{/if}
		&nbsp;
		{if $cachelist.user_id==$login.userid}[<a class="systemlink" href="mylists.php?edit={$cachelist.id}&fromsearch=1">{t}edit{/t}</a>]{/if}
		{if $login.userid}[<a class="systemlink" href="cachelist.php?id={$cachelist.id}&{if $cachelist.watched_by_me}dont{/if}watch">{if $cachelist.watched_by_me}{t}don't watch{/t}{else}{t}watch{/t}{/if}</a>]{/if}
		{if $login.userid && !$cachelist.bookmarked && $cachelist.user_id!=$login.userid}[<a class="systemlink" href="cachelist.php?id={$cachelist.id}&key={$cachelist_pw|urlencode}&bookmark">{t}bookmark{/t}</a>]{/if}
	</p>
	{if $cachelist.description != ''}
	<div style="padding: 0 8px 4px 10px">
		{$cachelist.description}
	</div>
	{/if}
	</div>
{/if}

<div class="content2-container bg-blue02" style="margin-top:20px;">
	<table cellspacing="0" cellpadding="0" width="100%" border="0"><tr>
		<td style="white-space:nowrap; min-width:150px">
			{if $search_headline_caches}
				{assign var=showmatched value=''}
			{else}
				{assign var=showmatched value='display:none'}
			{/if}
			<p class="content-title-noshade-size15" style="padding:0; margin:4px;">&nbsp;{t 1=$results_count 2=$showmatched}%1 caches<span style="%2"> matched</span>{/t}&nbsp;</p>
		</td>
		{if $caches|@count && $enable_mapdisplay}
			<td style="text-align:right; width:1px">
				<a href="search.php?queryid={$queryid}&output=map2bounds&showresult=1&skipqueryid=1&expert=0&utf8=1" class="nooutline"><img src="resource2/ocstyle/images/misc/32x32-world.png" /></a>
			</td>
			<td style="white-space:nowrap; text-align:left; padding-bottom:2px">
				<p class="inheader"><a href="search.php?queryid={$queryid}&output=map2bounds&showresult=1&skipqueryid=1&expert=0&utf8=1">{t}Show on map{/t}</a></p>
			</td>
		{/if}
		<td style="text-align:right; padding-bottom:2px"><p class="inheader">
			<span style="white-space:nowrap">[<a href="query.php?action=save&queryid={$queryid}">{t}Save options{/t}</a>]</span>
			{if !$disable_edit_options}&nbsp;<span style="white-space:nowrap">[<a href="search.php?queryid={$queryid}&showresult=0">{t}Edit options{/t}</a>]</span>{/if}</p>
		</td>
	</tr></table>
</div>
<div class="buffer" style="height:5px;"></div>

<font size="2">
	<table cellspacing="0" cellpadding="0">

		{if $caches|@count}
		<tr>
			<td class="header-small" colspan="2">
				<table width="98.5%">
					<tr>
						<td rowspan="1" style="width:300px; padding:0; margin:0">{include file="res_pager.tpl" smallnumbers=true}</td>
						<td style="text-align:right; padding:0; margin:0">{t}Download{/t}{t}#colonspace#{/t}:&nbsp;</td>
						<td><nobr>
							<select class="exportlist" onChange="location.href=this.options[this.selectedIndex].value">
								<option value="#">{t}Results on this page{/t}</option>
								<option value="search.php?queryid={$queryid}&output=gpx&startat={$startat}">GPX</option>
								<option value="search.php?queryid={$queryid}&output=loc&startat={$startat}">LOC</option>
								<option value="search.php?queryid={$queryid}&output=kml&startat={$startat}">KML</option>
								<option value="search.php?queryid={$queryid}&output=ov2&startat={$startat}">OV2</option>
								<option value="search.php?queryid={$queryid}&output=ovl&startat={$startat}">OVL</option>
								<option value="search.php?queryid={$queryid}&output=txt&startat={$startat}">TXT</option>
							</select>
							<select class="exportlist" onChange="location.href=this.options[this.selectedIndex].value">
								<option value="#">{t 1=$startatp1 2=$endat}Result %1 to %2 (as zip){/t}</option>
								<option value="search.php?queryid={$queryid}&output=gpx&startat={$startat}&count=max&zip=1">GPX</option>
								<option value="search.php?queryid={$queryid}&output=loc&startat={$startat}&count=max&zip=1">LOC</option>
								<option value="search.php?queryid={$queryid}&output=kml&startat={$startat}&count=max&zip=1">KML</option>
								<option value="search.php?queryid={$queryid}&output=ov2&startat={$startat}&count=max&zip=1">OV2</option>
								<option value="search.php?queryid={$queryid}&output=ovl&startat={$startat}&count=max&zip=1">OVL</option>
								<option value="search.php?queryid={$queryid}&output=txt&startat={$startat}&count=max&zip=1">TXT</option>
							</select></nobr>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		{/if}

		<tr><td class="spacer" colspan="2">&nbsp;</td></tr>
		<tr>
			<td colspan="2" style="padding-left: 0px; padding-right: 0px;">
				<table class="searchtable" border="0" cellspacing="0" cellpadding="0" width="98.5%">
					<tr>
					<th width="18">&nbsp;#&nbsp;</th>
					<th width="45">{$distanceunit|escape}</th>
					<th width="32">{t}Type{/t}</th>
					<th width="46">&nbsp;{t}D/T{/t}</th>
					<th width="460">{t}Name{/t}</th>
					<th width="48">&nbsp;</th>
					<th width="106">{if $displayownlogs}{t}Own logs{/t}{else}{t}Last logs{/t}{/if}</th>
					</tr>
					<tr><td></td></tr>
					<!--a-->
					{assign var=position value=$startat+1}
					{foreach from=$caches item=cache}
						{cycle assign=listcolor values="search_listcolor1,search_listcolor2"}
						{include file="search.result.caches.row.tpl"}
						{assign var=position value=$position+1}
					{/foreach}
					<!--z-->
				</table>
			</td>
		</tr>
		<tr><td class="spacer" colspan="2">&nbsp;</td></tr>
		{if $pages_list}
			<tr>
				<td colspan="2" class="header-small">{include file="res_pager.tpl"}</td>
			</tr>
			<tr><td style="height:0.6em"></td></tr>
		{/if}
	</table>

	{if $caches|@count}
	<table width="100%">
		<tr>
			<td style="text-align:right; width:50%">{t}Download{/t}{t}#colonspace#{/t}:&nbsp;&nbsp;</td>
			<td align="right" style="padding-right:20px; white-space:nowrap">
				<b>{t}Results on this page:{/t}</b>
				<a href="search.php?queryid={$queryid}&output=gpx&startat={$startat}" title="{t}GPS Exchange Format .gpx{/t}">GPX</a>
				<a href="search.php?queryid={$queryid}&output=loc&startat={$startat}" title="{t}Waypointfile .loc{/t}">LOC</a>
				<a href="search.php?queryid={$queryid}&output=kml&startat={$startat}" title="{t}Google Earth .kml{/t}">KML</a>
				<a href="search.php?queryid={$queryid}&output=ov2&startat={$startat}" title="{t}TomTom POI .ov2{/t}">OV2</a>
				<a href="search.php?queryid={$queryid}&output=ovl&startat={$startat}" title="{t}TOP50-Overlay .ovl{/t}">OVL</a>
				<a href="search.php?queryid={$queryid}&output=txt&startat={$startat}" title="{t}Textfile .txt{/t}">TXT</a>
				<br />
				<b>{t 1=$startatp1 2=$endat}Result %1 to %2 (as zip){/t}{t}#colonspace#{/t}:</b>
				<a href="search.php?queryid={$queryid}&output=gpx&startat={$startat}&count=max&zip=1" title="{t}GPS Exchange Format .gpx{/t}">GPX</a>
				<a href="search.php?queryid={$queryid}&output=loc&startat={$startat}&count=max&zip=1" title="{t}Waypointfile .loc{/t}">LOC</a>
				<a href="search.php?queryid={$queryid}&output=kml&startat={$startat}&count=max&zip=1" title="{t}Google Earth .kml{/t}">KML</a>
				<a href="search.php?queryid={$queryid}&output=ov2&startat={$startat}&count=max&zip=1" title="{t}TomTom POI .ov2{/t}">OV2</a>
				<a href="search.php?queryid={$queryid}&output=ovl&startat={$startat}&count=max&zip=1" title="{t}TOP50-Overlay .ovl{/t}">OVL</a>
				<a href="search.php?queryid={$queryid}&output=txt&startat={$startat}&count=max&zip=1" title="{t}Textfile .txt{/t}">TXT</a>
			</td>
		</tr>
		<tr>
			<td class="help" colspan="2" align="right" style="line-height:2em;">
				{t}With the download you accept the <a href="articles.php?page=impressum#tos">terms of use</a> from opencaching.de.&nbsp;&nbsp;{/t}
			</td>
		</tr>
	</table>
	{/if}
</font>
