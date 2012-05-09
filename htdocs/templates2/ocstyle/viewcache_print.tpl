{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
<table class="table">
	<tr>
		{if $print==y}
		<td class="header-print">
		{else}
		<td class="header">
		{/if}

			<table class="null" border="0">
				<tr>
					<td width="30">
						<img src="images/logo_new_small.gif" width="66" height="66" border="0" alt="" align="left" />
					</td>
					<td align="center">
						<font size="4">{t}Waypoint{/t}: {$cache.wpoc}</font>
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
					<td align="left" valign="top" width="397">
						<font size="3"><b>{$cache.name|escape}</b></font><br />
						<span style="font-weight:400">&nbsp;{t}by{/t} <a>{$cache.username|escape}</a></span><br />
						{if $cache.shortdesc!=''}
							{$cache.shortdesc|escape}<br />
						{/if}
		
						{if $cache.type==6}
							<a href="#" onClick="javascript:window.open('event_attendance.php?id={$cache.cacheid}&popup=y','{t escape=js}List of participants{/t}','width=320,height=440,resizable=no,scrollbars=1')">{t}List of participants{/t}</a>
						{/if}
					</td>
					<td valign="top" nowrap="1" width="140" style="text-align:right">
						{t}Difficulty{/t}:
						<img src="./resource2/{$opt.template.style}/images/difficulty/diff-{$cache.difficulty*5}.gif" border="0" width="19" height="16" hspace="2" alt="{t 1=$cache.difficulty*0.5|sprintf:'%01.1f'}Difficulty: %1 of 5.0{/t}" /><br />
						{t}Terrain{/t}:
						<img src="./resource2/{$opt.template.style}/images/difficulty/terr-{$cache.terrain*5}.gif" border="0" width="19" height="16" hspace="2" alt="{t 1=$cache.terrain*0.5|sprintf:'%01.1f'}Terrain: %1 of 5.0{/t}" />
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
		{if $print==y}
		<td class="inner-print">
		{else}
		<td>
		{/if}

			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td valign="top">
						<img src="resource2/{$opt.template.style}/images/description/22x22-location.png"  width="22" height="22" border="0" alt="" title="" align="left">
						<font size="3"><b><nobr>{$coordinates.lat|escape}</nobr> <nobr>{$coordinates.lon|escape}</nobr></b></font> <font size="1">(WGS84)</font><br />
						<font size="1"><a href="#" onClick="javascript:window.open('coordinates.php?lat={$cache.latitude}&lon={$cache.longitude}&popup=y&wp={$cache.wpoc}','{t escape=js}Coordinates{/t}','width=280,height=394,resizable=no,scrollbars=0')">{t}Convert coordinates{/t}</a></font><br />
						{t}Size{/t}: {$cache.sizeName|escape}<br />
						{if $cache.searchtime>0}
							<nobr>{t}Time required{/t}: {$cache.searchtime|format_hour} h</nobr>
						{/if}
						{if $cache.waylength>0}
							<nobr>{t}Distance{/t}: {$cache.waylength|sprintf:'%01.2f'} km</nobr>
						{/if}
						{if $cache.searchtime>0 || $cache.waylength>0}<br />{/if}
						{if $cache.status!=1}
							{t}State{/t}: <span class="errormsg">{$cache.statusName|escape}</span>
						{else}
							{t}State{/t}: {$cache.statusName|escape}
						{/if}
						<br />
						{t}Hidden at{/t}: {$cache.datehidden|date_format:$opt.format.datelong}<br />
						{t}Listed since{/t}: {$cache.datecreated|date_format:$opt.format.datelong}<br />
						{t}Last update{/t}: {$cache.lastmodified|date_format:$opt.format.datelong}<br />
						
						{if $cache.wpgc!='' || $cache.wpnc!=''}
							{t}Also listed at{/t}:
							{if $cache.wpgc!=''}
								<a href="http://www.geocaching.com/seek/cache_details.aspx?wp={$cache.wpgc}" target="_blank">geocaching.com</a>
							{/if}
							{if $cache.wpnc!=''}
								<a href="http://www.navicache.com/cgi-bin/db/displaycache2.pl?CacheID={nccacheid wp=$cache.wpnc}" target="_blank">navicache.com</a>
							{/if}
							<br />
						{/if}
					</td>
					<td valign="top">
						{if $cache.code1==""}
							<img src="images/flags/{$cache.countryCode|lower}.gif" style="vertical-align:middle" />&nbsp;
						{else}
							<img src="images/flags/{$cache.code1|lower}.gif" style="vertical-align:middle" />&nbsp;
						{/if}
						<b>{if $cache.adm1==""}{$cache.country|escape}{else}{$cache.adm1}{/if}</b><br />
						{if $cache.adm2!=""}
							<font size="1">
							{$cache.adm2}
							{if $cache.adm4!=""}
								&nbsp;=>&nbsp;{$cache.adm4}
							{/if}
							</font>
							<br />
						{/if}

						<!-- <a href="http://www.mapquest.com/maps/map.adp?latlongtype=decimal&latitude={$cache.latitude}&longitude={$cache.longitude}" target="_blank">Mapquest</a><br />    -->
						<!-- <a href="http://maps.geocaching.de/gm/oc.php?lat={$cache.latitude}&lon={$cache.longitude}&zoom=14" target="_blank">{t}Geocaching.de{/t}</a><br />                -->
						<!-- <a href="http://maps.google.com/maps?q={$cache.latitude}+{$cache.longitude}" target="_blank">{t}Google Maps{/t}</a><br />                                        -->

						<img src="resource2/{$opt.template.style}/images/log/16x16-found.png" width="16" height="16" border="0"> {$cache.found} {if $cache.type==6}{t}Attended{/t}{else}{t}Found{/t}{/if}<br />
						<nobr><img src="resource2/{$opt.template.style}/images/log/16x16-dnf.png" width="16" height="16" border="0">{if $cache.type==6} {$cache.willattend} {t}Will attend{/t}{else} {$cache.notfound} {t}Not Found{/t}{/if}</nobr><br />
						<img src="resource2/{$opt.template.style}/images/log/16x16-note.png" width="16" height="16" border="0"> {$cache.note} {t}Notes{/t}<br />
						<img src="resource2/{$opt.template.style}/images/action/16x16-watch.png" width="16" height="16" border="0"> {$cache.watcher} {t}Watched{/t}<br />
						<img src="resource2/{$opt.template.style}/images/action/16x16-ignore.png" width="16" height="16" border="0"> {$cache.ignorercount} {t}Ignored{/t}<br />
						<img src="resource2/{$opt.template.style}/images/description/16x16-visitors.png" width="16" height="16" border="0"> {$cache.visits} {t}Page visits{/t}<br />
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

  {if count($attributes)>0}
	  <tr>
		{if $print==y}
		<td class="header-small-print">
		{else}
		<td class="header-small">
		{/if}
			<img src="resource2/{$opt.template.style}/images/description/22x22-encrypted.png" width="22" height="22" style="vertical-align:middle" border="0">
			<b>{t}Cache attributes{/t}</b><br />
		 </td>
	  </tr>
	  <tr>
		  <td valign="top">
				{foreach from=$attributes item=attributGroup}
					<table cellspacing="0" style="display:inline;border-spacing:0px;">
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
					</table>&nbsp;
				{/foreach}
		  </td>
	  </tr>
	  <tr><td class="spacer"><br /></td></tr>
	{/if}

	<tr>
		{if $print==y}
		<td class="header-small-print">
		{else}
		<td class="header-small">
		{/if}
			<img src="resource2/{$opt.template.style}/images/description/22x22-description.png" width="22" height="22" style="vertical-align:middle" border="0">
			<b>{t}Description{/t}</b>&nbsp;&nbsp;
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
			&nbsp;
		</td>
	</tr>
	<tr>
		{if $print==y}
		<td class="inner-print">
		{else}
		<td>
		{/if}
			<p>
				{if $cache.deschtml==0}
					{$cache.desc|smiley|hyperlink}
				{else}
					{$cache.desc|smiley}
				{/if}
			</p>
		</td>
	</tr>

	{if $cache.hint!=''}
		<tr>
			{if $print==y}
			<td class="header-small-print">
			{else}
			<td class="header-small">
			{/if}
				<img src="resource2/{$opt.template.style}/images/description/22x22-encrypted.png" width="22" height="22" style="vertical-align:middle" border="0">
				<b>{t}Additional hint{/t}</b>&nbsp;&nbsp;
				<img src="resource2/{$opt.template.style}/images/action/16x16-encrypt.png" width="16" height="16" style="vertical-align:middle" border="0">
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

				{else}
				<!-- 	20100702 Uwe Neumann
					Taken out to inline the function with the view.

                                        {if $log=="5"}
						<span style="font-weight:400">[<a href="viewcache.php?cacheid={$cache.cacheid}&log=5&print=y&nocrypt=0&desclang={$cache.desclanguage|urlencode}">{t}Decrypt{/t}</a>]
						</span>
					{elseif $log =="N"} 
						<span style="font-weight:400">
						[<a href="viewcache.php?cacheid={$cache.cacheid}&log=N&print=y&nocrypt=0&desclang={$cache.desclanguage|urlencode}">{t}Decrypt{/t}</a>]
						</span>
					{else}
						<span style="font-weight:400">
						[<a href="viewcache.php?cacheid={$cache.cacheid}&log=A&print=y&nocrypt=0&desclang={$cache.desclanguage|urlencode}">{t}Decrypt{/t}</a>]
						</span>
					{/if}
                                -->
				{/if}

				<br />
			</td>
		</tr>
		<tr>
			{if $print==y}
			<td class="inner-print">
			{else}
			<td>
			{/if}
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
											<font face="Courier" style="font-family : 'Courier New', FreeMono, Monospace;">N|O|P|Q|R|S|T|U|V|W|X|Y|Z</font><br><br>
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

	{if count($pictures)>0}
		<tr>
			{if $print==y}
				<td class="header-small-print">
			{else}
				<td class="header-small">
			{/if}
				<img src="resource2/{$opt.template.style}/images/description/22x22-image.png" width="22" height="22" style="vertical-align:middle" border="0">
				<b>{t}Pictures{/t}</b> &nbsp;&nbsp;
				&nbsp;
			</td>
		</tr>
		<tr>
			{if $print==y}
				<td class="header-small-print">
			{else}
				<td class="header-small">
			{/if}

			{foreach from=$pictures item=pictureItem}
				<a href="{$pictureItem.url|escape}" target="_blank"><img src="thumbs.php?uuid={$pictureItem.uuid|urlencode}" alt="{$pictureItem.title|escape}" title="{$pictureItem.title|escape}" border="0" align="bottom" /></a>
			{/foreach}
			</td>
		</tr>
	
		{if $print==y}
			<tr><td class="spacer-print"><br></td></tr>
		{else}
			<tr><td class="spacer"><br></td></tr>
		{/if}
	{/if}

	<tr>
		{if $print==y}
		<td class="header-small-print" valign="middle">
		{else}
		<td class="header-small" valign="middle">
		{/if}
			<img src="resource2/{$opt.template.style}/images/description/22x22-utility.png" width="22" height="22" style="vertical-align:middle" border="0" title="">
			{t}Utilities{/t}
		</td>
	</tr>

	{if count($npaareasWarning) > 0}
		{if $print==y}
			<tr><td class="spacer-print"><br></td></tr>
		{else}
			<tr><td class="spacer"><br></td></tr>
		{/if}
		<tr>
			<td>
				<p align="center">
					<table border="0" cellpadding="0" cellspacing="0" style="background-color:#FEFEFE;border:solid 1px black;">
						<tr>
							<td width="70px" style="vertical-align:top">
								<img src="./images/npa.gif" alt="{t}Nature protection area{/t}" />
							</td>
							<td style="text-align:left; vertical-align:top">
								{t 1=$opt.cms.npa}This geocache is probably placed within a nature protection area! See <a href="%1">here</a> for further informations, please.{/t}<br />
								<font size="1">
									{foreach from=$npaareasWarning item=npaItem name=npaareas}
										{$npaItem.npaTypeName|escape} 
										{$npaItem.npaName|escape} <font size="1">(<a href="http://www.google.de/search?q={$npaItem.npaTypeName|urlencode}+{$npaItem.npaName|urlencode}" target="_blank">{t}Info{/t}</a>)</font>{if !$smarty.foreach.npaareas.last},{/if}
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
				<font size="1">
					{t 1=$opt.cms.npa}This geocache is probably placed within the following nature protection areas (<a href="%1">Info</a>):{/t}
					{foreach from=$npaareasNoWarning item=npaItem name=npaareas}
						{$npaItem.npaTypeName|escape} 
						{$npaItem.npaName|escape} <font size="1">(<a href="http://www.google.de/search?q={$npaItem.npaTypeName|urlencode}+{$npaItem.npaName|urlencode}" target="_blank">{t}Info{/t}</a>)</font>{if !$smarty.foreach.npaareas.last},{/if}
					{/foreach}
				</font>
			</td>
		</tr>
	{/if}

	{if $geokret_count!=0}
		<tr>
			{if $print==y}
			<td class="header-small-print" valign="middle">
			{else}
			<td class="header-small" valign="middle">
			{/if}
				<img src="resource2/{$opt.template.style}/images/description/22x22-geokret.gif" width="22" height="22" style="vertical-align:middle" border="0" title="">
				{t}Geokrets{/t}
			</td>
		</tr>
		{if $print==y}
			<tr><td class="spacer-print"><br></td></tr>
		{else}
			<tr><td class="spacer"><br></td></tr>
		{/if}
		<tr>
			<td>
			  {foreach from=$geokret item=geokretItem name=geokret}
					<a href="http://geokrety.org/konkret.php?id={$geokretItem.id}" target="_blank">{$geokretItem.itemname|escape}</a> von {$geokretItem.username|escape}
					{if !$smarty.foreach.geokret.last}<br />{/if}
				{/foreach}
			</td>
		</tr>
	{/if}

		{if $print==y}
			<tr><td class="spacer-print"><br></td></tr>
		{else}
			<tr><td class="spacer"><br></td></tr>
		{/if}
	<tr>
			{if $print==y}
			<td class="inner-print">
			{else}
			<td>
			{/if}
			{include file="res_logentry.tpl" header=true footer=true footbacklink=false logs=$logs cache=$cache}
		</td>
	</tr>
	{if $print!=y}
		{if $showalllogs}
			<tr>
				{if $print==y}
				<td class="header-small-print">
				{else}
				<td class="header-small">
				{/if}
					<a href="viewlogs.php?cacheid={$cache.cacheid}"><img src="resource2/{$opt.template.style}/images/action/16x16-showall.png" width="16" height="16" align="middle" border="0" align="left"></a>
					[<a href="viewlogs.php?cacheid={$cache.cacheid}">{t}Show all logentries{/t}</a>]
				</td>
			</tr>
		{/if}
	{/if}
</table>