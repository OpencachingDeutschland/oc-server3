{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
<table class="table print" width="100%">
	<tr>
		<td class="header-print">

			<table class="null" border="0">
				<tr>
					<td width="30">
						<img src="images/newlogo.png" height="66" border="0" alt="" align="left" />
					</td>
					<td align="center">
						<font size="4">{if $shortlink_domain !== false}{$shortlink_domain}/{else}{t}Waypoint:{/t} {/if}{$cache.wpoc}</font>
					</td>
					<td class="null" border="0">
						{if $opt.page.sponsor.popup!=''}
							{$opt.page.sponsor.popup}
						{/if}
					</td>
				</tr>
			</table>

			<table border="0">
				<tr>
					<td align="right" valign="top" width="20">
						{include file="res_cacheicon.tpl" cachetype=$cache.type status=$cache.status}
					</td>
					<td align="left" valign="top" width="99%">
						<font size="3"><b>{$cache.name|escape}</b></font><br />
						<span style="font-weight:400">&nbsp;{t}by{/t} {$cache.username|escape}</span><br />
						{if $cache.shortdesc!=''}
							{$cache.shortdesc|escape}<br />
						{/if}
					</td>
					<td valign="top" nowrap="1" width="1%" style="text-align:right"><nobr>
						{t}Difficulty:{/t}
						<img src="./resource2/{$opt.template.style}/images/difficulty/diff-{$cache.difficulty*5}.gif" border="0" width="19" height="16" hspace="2" /><br />
						{t}Terrain:{/t}
						<img src="./resource2/{$opt.template.style}/images/difficulty/terr-{$cache.terrain*5}.gif" border="0" width="19" height="16" hspace="2" />
						</nobr>
					</td>
				</tr>
			</table>
		</td>
	</tr>

	{if $cache.status==7}
		<tr>
			<td class="errormsg">
				{t}The geocache was locked by an administrator because it did not follow the Opencaching terms of use.
			  If you wish to unlock it, contact us using the "report cache"-link. Please choose "other" as reason
			  and explain shortly what you have changed to make the listing compliant to our terms of use. Thank you!{/t}
			</td>
		</tr>
	{/if}

	<tr>
		<td class="inner-print">

			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td valign="top" style="padding-left:0">
						<img src="resource2/{$opt.template.style}/images/description/22x22-location.png"  width="22" height="22" border="0" alt="" title="" align="left" />&nbsp;
						<font size="3"><b><nobr>{$coordinates.lat|escape}</nobr> <nobr>{$coordinates.lon|escape}</nobr></b></font> <font size="1">(WGS84)</font><br />
						<div style="height:0.5em"></div>
						<table class="print-cachemeta" cellspacing="0" cellpadding="0">
							<tr><td>{t}Size:{/t}</td><td>{$cache.sizeName|escape}<br /></td></tr>
						{if $cache.searchtime>0}
							<tr><td><nobr>{t}Time required:{/t}</td><td>{$cache.searchtime|format_hour}
								h{if $cache.waylength>0}, &nbsp;{t}Distance:{/t} {$cache.waylength} km{/if}
							</nobr></td>
						{elseif $cache.waylength>0}
							<tr><td><nobr>{t}Waylength:{/t}</td><td>{$cache.waylength} km</nobr></td></tr>
						{/if}
						{if $cache.status!=1}
							<tr><td>{t}State:{/t}</td><td><span class="errormsg">{$cache.statusName|escape}</span></td></tr>
						{else}
							<tr><td>{t}State:{/t}</td><td>{$cache.statusName|escape}</td></tr>
						{/if}
						<tr><td>{t}Hidden on:{/t}</td><td>{$cache.datehidden|date_format:$opt.format.datelong}</td></tr>
						<tr><td>{if $cache.is_publishdate==0}{t}Listed since:{/t}{else}{t}Published on:{/t}{/if}</td><td>{$cache.datecreated|date_format:$opt.format.datelong}</td></tr>
						<tr><td>{t}Last update:{/t}</td><td>{$cache.lastmodified|date_format:$opt.format.datelong}</td></tr>
						
						{if $cache.wpgc!='' || $cache.wpnc!=''}
							<tr>
							<td>{t}Also listed as:{/t}</td>
							<td>
								{if $cache.wpgc!=''}{$cache.wpgc}{if $cache.wpnc!=''}, {/if}{/if}
								{if $cache.wpnc!=''}{$cache.wpnc}{/if}
							</td>
						{/if}

						<tr><td class="spacer-print"></td></td>
						</table>
					</td>
					<td valign="top">
						{if $cache.code1=="" or $cache.code1 != $cache.countryCode}
							<img src="images/flags/{$cache.countryCode|lower}.gif" style="vertical-align:middle" />&nbsp; <b>{$cache.country|escape}</b><br />
						{else}
							<img src="images/flags/{$cache.code1|lower}.gif" style="vertical-align:middle" />&nbsp;
							<b>{$cache.adm1}</b><br />
							{if ($cache.adm2!=null | $cache.adm4!=null)}
								<font size="1">
								{$cache.adm2|escape} {if ($cache.adm2!=null & $cache.adm4!=null)} &gt; {/if} {$cache.adm4|escape}
								</font>
								<br />
							{/if}
						{/if}

						<div style="height:2px"></div>

						<img src="resource2/{$opt.template.style}/images/log/16x16-found.png" width="16" height="16" border="0" /> {$cache.found} {if $cache.type==6}{t}Attended{/t}{else}{t}Found{/t}{/if}<br />
						<nobr><img src="resource2/{$opt.template.style}/images/log/16x16-dnf.png" width="16" height="16" border="0" />{if $cache.type==6} {$cache.willattend} {t}Will attend{/t}{else} {$cache.notfound} {t}Not found{/t}{/if}</nobr><br />
						<img src="resource2/{$opt.template.style}/images/viewcache/16x16-note.png" class="icon16" alt="" /> {$cache.note} {if $cache.note==1}{t}Note{/t}{else}{t}Notes{/t}{/if}<br />
						{if $cache.maintenance}<img src="resource2/{$opt.template.style}/images/viewcache/16x16-maintenance.png" class="icon16" alt="" /> {$cache.maintenance} {if $cache.maintenance==1}{t}Maintenance log{/t}{else}{t}Maintenance logs{/t}{/if}<br />{/if}
						<img src="resource2/{$opt.template.style}/images/viewcache/16x16-watch.png" class="icon16" alt="" /> {$cache.watcher} {if $cache.watcher==1}{t}Watcher{/t}{else}{t}Watchers{/t}{/if}<br />
						<img src="resource2/{$opt.template.style}/images/viewcache/ignore-16.png" class="icon16" alt="" /> {$cache.ignorercount} {if $cache.ignorecount==1}{t}Ignorer{/t}{else}{t}Ignorers{/t}{/if}<br />
						<img src="resource2/{$opt.template.style}/images/viewcache/16x16-visitors.png" class="icon16" alt="" /> {$cache.visits} {if $cache.visits==1}{t}Page visit{/t}{else}{t}Page visits{/t}{/if}<br />
						{if $cache.topratings>0}
							<img src="images/rating-star.gif" border="0"> {$cache.topratings} {t}Recommendations{/t}<br />
						{/if}
					</td>
					
                                   <!-- Begin changes Uwe Neumann 20100614 - Take out the google map.                                                                                                                                              -->
                                   <!--     <td valign="top">                                                                                                                                                                                      -->
				   <!--		<div style="position:relative;height:200px;width:200px;">                                                                                                                                          -->
                                   <!--                 <iframe src="http://maps.geocaching.de/gm/oc-mini-mitcaches.php?lat={$cache.latitude}&lon={$cache.longitude}&zoom={$userzoom}" width="200px" height="200px" frameborder="0"></iframe>      -->
				   <!--		</div>                                                                                                                                                                                             -->
        			   <!--	    </td>                                                                                                                                                                                                  -->
                                   <!-- End changes Uwe Neumann 20100614 - Take out the google map.                                                                                                                                                -->

				</tr>
			</table>
		</td>
	</tr>

	{* Attributes *}
  {if count($attributes)>0}
	  <tr>
		  <td valign="top">
				{foreach from=$attributes item=attributGroup}
					<div class="attribgroup" style="padding-right:6px">
						<table cellspacing="0">
							<tr>
								<td bgcolor="{$attributGroup.color|escape}" style="line-height:9px;padding-top:2px;margin:0 0 0 0;border-left:1px solid gray;border-right:1px solid gray;border-top:1px solid gray;">
									<font size="1">{$attributGroup.name|escape}</font>
								</td>
							</tr>
							<tr>
								<td bgcolor="#F8F8F8" style="margin:0 0 0 0;border-left:1px solid gray;border-right:1px solid gray;border-bottom:1px solid gray;">
									{foreach from=$attributGroup.attr item=attributeItem}
										{include file="res_attribute.tpl" id=$attributeItem.id state=1 name=$attributeItem.name icon=$attributeItem.icon html_desc=$attributeItem.html_desc color=$attributGroup.color}
									{/foreach}
								</td>
							</tr>
						</table>
					</div>
				{/foreach}
		  </td>
	  </tr>
	  <tr><td class="spacer-print"><div style="height:0.5em"></div></td></tr>
	{/if}

	{* Description *}
	<tr>
		<td class="header-small-print">
			<!-- <img src="resource2/{$opt.template.style}/images/description/22x22-description.png" width="22" height="22" style="vertical-align:middle" border="0" /> -->
			{t}Description{/t}&nbsp;&nbsp;
			{if $cache.desclanguages|@count > 1}
				<span style="font-weight: 400;">
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
				</span>
			{/if}
		</td>
	</tr>
	<tr>
		<td class="inner-print">
			<div class="cachedesc-print">
				{if $cache.deschtml==0}
					{$cache.desc|smiley|hyperlink}
				{else}
					{$cache.desc|smiley}
				{/if}
			</div>
		</td>
	</tr>

	{* personal note *}
	{if $enableCacheNote && ($note != "" || $inclCoord)}
		<tr>
			<td class="header-small-print">
				<br />
				<!-- <img src="resource2/{$opt.template.style}/images/description/22x22-description.png" width="20" height="20" style="vertical-align:middle" border="0" /> -->
				{t}Personal cache note{/t}
			</td>
		</tr>
		{if $inclCoord}
			<tr><td>{$lat_hem} {$lat_deg}° {$lat_min}' &nbsp; {$lon_hem} {$lon_deg}° {$lon_min}'</td></tr>
		{/if}
		<tr><td><font size="2">{$note|escape|nl2br}</font></td></tr>
	{/if}

	{* Additional waypoints *}
	{if count($childWaypoints)>0}
		<tr>
			<td class="header-small-print">
				<br />
				<!-- <img src="resource2/{$opt.template.style}/images/description/20x20-compass.png" width="20" height="20" style="vertical-align:middle" border="0" /> -->
				{t}Additional waypoints{/t}
			</td>
		</tr>

		<tr>
			<td class="inner-print">
				<table class="table printwptable">
				{foreach from=$childWaypoints item=childWaypoint}
					<tr>
						<td class="framed" width="1%">
							<table class="table">
								<tr>
									<td style="margin:0; padding:0"><img src="{$childWaypoint.image}" /></td>
									<td><nobr>{$childWaypoint.name|escape}</nobr></td>
								</tr>
							</table>
						</td>
						<td class="framed" width="1%" style="white-space:norwap"><nobr>{$childWaypoint.coordinateHtml}</nobr></td>
						<td class="framed" width="1%"></td>
						<td class="framed" >{$childWaypoint.description|escape|replace:"\r\n":"<br />"}</td>
						<td></td>
					</tr>
				{/foreach}
				</table>
			</td>
		</tr>
	{/if}

	{* Hint *}
	{if $cache.hint!=''}
		<tr>
			<td class="header-small-print">
				<br />
				<!-- <img src="resource2/{$opt.template.style}/images/description/22x22-encrypted.png" width="22" height="22" style="vertical-align:middle" border="0" /> -->
				{t}Additional hint{/t}&nbsp;&nbsp;
				<img src="resource2/{$opt.template.style}/images/action/16x16-encrypt.png" width="16" height="16" style="vertical-align:middle" border="0" />
				{if $crypt==true}
					{if $log=="5"}
						<span style="font-weight:400">[<a href="viewcache.php?cacheid={$cache.cacheid}&log=5&print=y&nocrypt=1&desclang={$cache.desclanguage|urlencode}">{t}Decrypt{/t}</a>]
						</span>
					{elseif $log =="N"} 
						<span style="font-weight:400">
						[<a href="viewcache.php?cacheid={$cache.cacheid}&log=N&print=y&nocrypt=1&desclang={$cache.desclanguage|urlencode}">{t}Decrypt{/t}</a>]
						</span>
					{else}
						<span style="font-weight:400">
						[<a href="viewcache.php?cacheid={$cache.cacheid}&log=A&print=y&nocrypt=1&desclang={$cache.desclanguage|urlencode}">{t}Decrypt{/t}</a>]
						</span>
					{/if}
				{/if}
			</td>
		</tr>
		<tr>
			<td class="inner-print">
				{if $crypt==true}
					<table width="100%" cellspacing="0" border="0" cellpadding="0">
						<tr>
							<td valign="top">
								<p>
									{$cache.hint|rot13html}
								</p>
							</td>
							<td width="100" valign="top">
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td style="border-bottom-color : #808080; border-bottom-style : solid; border-bottom-width : 1px;" nowrap="1">
											<font face="Courier" style="font-family : 'Courier New', FreeMono, Monospace;">A|B|C|D|E|F|G|H|I|J|K|L|M</font>
										</td>
									</tr>
									<tr>
										<td>
											<font face="Courier" style="font-family : 'Courier New', FreeMono, Monospace;">N|O|P|Q|R|S|T|U|V|W|X|Y|Z</font>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				{else}
					{$cache.hint}
				{/if}
			</td>
		</tr>
	{/if}

	{* Pictures *}
	{if count($pictures)>0}
		<tr>
			<td class="header-small-print">
				<br />
				<!-- <img src="resource2/{$opt.template.style}/images/description/22x22-image.png" width="22" height="22" style="vertical-align:middle" border="0" /> -->
				{t}Pictures{/t}
			</td>
		</tr>
		<tr>
			<td class="header-small-print">
				{foreach from=$pictures item=pictureItem}
					<a href="{$pictureItem.url|escape}" target="_blank"><img src="thumbs.php?uuid={$pictureItem.uuid|urlencode}" alt="{$pictureItem.title|escape}" title="{$pictureItem.title|escape}" border="0" align="bottom" /></a>
				{/foreach}
			</td>
		</tr>
	{/if}

	{* Nature protection areas *}
	{if count($npaareasWarning) + count($npaareasNoWarning) > 0}
		<tr>
			<td class="header-small-print" valign="middle">
				<br />
				<!-- <img src="resource2/{$opt.template.style}/images/description/22x22-utility.png" width="22" height="22" style="vertical-align:middle" border="0" title="" /> -->
				{t}Nature protection{/t}
			</td>
		</tr>
	{/if}

	{if count($npaareasWarning) > 0}
		<tr>
			<td>
				<p align="center">
					<table border="0" cellpadding="0" cellspacing="0" style="background-color:#FEFEFE;border:solid 1px black;">
						<tr>
							<td width="70px" style="vertical-align:top">
								<img src="./images/npa.gif" alt="{t}Nature protection area{/t}" />
							</td>
							<td style="text-align:left; vertical-align:top">
								{t 1=$opt.cms.npa}This geocache is probably placed within a nature protection area! See %1here</a> for further informations, please.{/t}<br />
								<font size="2">
									{foreach from=$npaareasWarning item=npaItem name=npaareas}
										{$npaItem.npaTypeName|escape} 
										{$npaItem.npaName|escape}{if !$smarty.foreach.npaareas.last},{/if}
									{/foreach}
								</font>
							</td>
						</tr>
					</table>
				</p>
			</td>
		</tr>
	{/if}
	{if count($npaareasNoWarning) > 0}
		<tr>
			<td>
				<font size="2">
					{t 1=$opt.cms.npa}This geocache is probably placed within the following protection areas:{/t}
					{foreach from=$npaareasNoWarning item=npaItem name=npaareas}
						{$npaItem.npaTypeName|escape} 
						{$npaItem.npaName|escape}{if !$smarty.foreach.npaareas.last},{/if}
					{/foreach}
				</font>
			</td>
		</tr>
	{/if}

	{* Geokrets *}
	{if $geokret_count!=0}
		<tr>
			<td class="header-small-print" valign="middle">
				<br />
				<!-- <img src="resource2/{$opt.template.style}/images/description/22x22-geokret.gif" width="22" height="22" style="vertical-align:middle" border="0" title="" /> -->
				{t}Geokrets{/t}
			</td>
		</tr>
		<tr><td class="spacer-print"><br /></td></tr>
		<tr>
			<td>
			  {foreach from=$geokret item=geokretItem name=geokret}
					<a href="http://geokrety.org/konkret.php?id={$geokretItem.id}" target="_blank">{$geokretItem.itemname|escape}</a> von {$geokretItem.username|escape}
					{if !$smarty.foreach.geokret.last}<br />{/if}
				{/foreach}
			</td>
		</tr>
	{/if}

	{* Logs *}
	<tr><td class="spacer-print"><br /></td></tr>
	<tr>
		<td class="inner-print">
			{include file="res_logentry.tpl" header_footer=true footbacklink=false logs=$logs cache=$cache}
		</td>
	</tr>
</table>
