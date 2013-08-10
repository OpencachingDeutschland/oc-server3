<?php
	/***************************************************************************
												./lang/de/ocstyle/search.result.tpl.php
																-------------------
			begin                : July 25 2004
			copyright            : (C) 2004 The OpenCaching Group
			forum contact at     : http://www.opencaching.com/phpBB2

		***************************************************************************/

	/***************************************************************************
		*
		*   This program is free software; you can redistribute it and/or modify
		*   it under the terms of the GNU General Public License as published by
		*   the Free Software Foundation; either version 2 of the License, or
		*   (at your option) any later version.
		*
		***************************************************************************/

	/****************************************************************************

    Unicode Reminder メモ

		(X)HTML search output template

	****************************************************************************/
?>

<div class="content2-container bg-blue02" style="margin-top:20px;">
	<table cellspacing="0" cellpadding="0" width="100%" border="0"><tr>
		<td style="white-space:nowrap">
			<p class="content-title-noshade-size15" style="padding:0; margin:4px;">&nbsp;{t}{results_count} caches<span style="{search_headline_caches}"> matched</span>{/t}&nbsp;</p>
		</td>
		<td style="text-align:right; width:1px">
			<a href="search1.php?queryid={queryid}&output=map2bounds&showresult=1&skipqueryid=1&expert=0&utf8=1" class="nooutline"><img src="resource2/ocstyle/images/misc/32x32-world.png" /></a>
		</td>
		<td style="white-space:nowrap; text-align:left; padding-bottom:2px">
			<p class="inheader"><a href="search1.php?queryid={queryid}&output=map2bounds&showresult=1&skipqueryid=1&expert=0&utf8=1">{showonmap}</a></p>
		</td>
		<td style="text-align:right; padding-bottom:2px"><p class="inheader">
			<span style="white-space:nowrap">[<a href="query.php?action=save&queryid={queryid}">{t}Save options{/t}</a>]</span>
			&nbsp;<span style="white-space:nowrap">[<a href="search1.php?queryid={queryid}&showresult=0">{t}Edit options{/t}</a>]</span></p>
		</td>
	</tr></table>
</div>
<div class="buffer" style="height:5px;"></div>

<font size="2">
	<table class="content" border="0" cellspacing="0px" cellpadding="0px">
		<tr>
			<td class="header-small" colspan="2">
				<table width="98.5%">
					<tr>
						<td rowspan="1" style="vertical-align:top; width:300px">{pages}</td>
						<td style="text-align:right;">{t}Download{/t}:&nbsp;</td>
						<td><nobr>
							<select name="wpdownload-page" class="exportlist" onChange="location.href=this.options[this.selectedIndex].value"> 
								<option value="#">{t}Results on this page{/t}</option>
								<option value="search1.php?queryid={queryid}&output=gpx&startat={startat}">GPX</option>
								<option value="search1.php?queryid={queryid}&output=loc&startat={startat}">LOC</option>
								<option value="search1.php?queryid={queryid}&output=kml&startat={startat}">KML</option>
								<option value="search1.php?queryid={queryid}&output=ov2&startat={startat}">OV2</option>
								<option value="search1.php?queryid={queryid}&output=ovl&startat={startat}">OVL</option>
								<option value="search1.php?queryid={queryid}&output=txt&startat={startat}">TXT</option>
							</select>
							<select name="wpdownload-all" class="exportlist" onChange="location.href=this.options[this.selectedIndex].value"> 
								<option value="#">{t}Result {startatp1} to {endat} (as zip){/t}</option>
								<option value="search1.php?queryid={queryid}&output=gpx&startat={startat}&count=max&zip=1">GPX</option>
								<option value="search1.php?queryid={queryid}&output=loc&startat={startat}&count=max&zip=1">LOC</option>
								<option value="search1.php?queryid={queryid}&output=kml&startat={startat}&count=max&zip=1">KML</option>
								<option value="search1.php?queryid={queryid}&output=ov2&startat={startat}&count=max&zip=1">OV2</option>
								<option value="search1.php?queryid={queryid}&output=ovl&startat={startat}&count=max&zip=1">OVL</option>
								<option value="search1.php?queryid={queryid}&output=txt&startat={startat}&count=max&zip=1">TXT</option>
							</select></nobr>
						</td>
					</tr>
				</table> 
			</td>
		</tr>
		<tr><td class="spacer" colspan="2">&nbsp;</td></tr>
		<tr>
			<td colspan="2" style="padding-left: 0px; padding-right: 0px;">
				<table border="0" cellspacing="0px" cellpadding="0px" class="searchtable">
					<tr>
					<th width="18" height="13" >&nbsp;#&nbsp;</th>
					<th width="45" height="13">{distanceunit}</th>
					<th width="32" height="13">{t}Type{/t}</th>
					<th width="46" height="13">{t}D/T{/t}</th>
					<th width="448" height="13">{t}Name{/t}</th>
					<th width="48" height="13">&nbsp;</th>
					<th width="126" height="13"><span style="display:{displaylastlogs}">{t}Last logs{/t}<span style="display:{displayownlogs}">{t}Own logs{/t}</th>
					</tr>
					<tr><td></td></tr>
					<!--a-->{results}<!--z-->
				</table>
			</td>
		<tr><td class="spacer" colspan="2">&nbsp;</td></tr>
		</tr>
		<tr>
			<td colspan="2" class="header-small">{pages}</td>
		</tr>
		<tr><td style="height:0.6em"></td></tr>
	</table>
	<table width="100%">
		<tr>
			<td style="text-align:right; width:50%">{t}Download{/t}:&nbsp;&nbsp;</td>
			<td align="right" style="padding-right:20px; white-space:nowrap">
				<b>{t}Results on this page:{/t}</b>
				<a href="search1.php?queryid={queryid}&output=gpx&startat={startat}" title="{t}GPS Exchange Format .gpx{/t}">GPX</a>
				<a href="search1.php?queryid={queryid}&output=loc&startat={startat}" title="{t}Waypointfile .loc{/t}">LOC</a>
				<a href="search1.php?queryid={queryid}&output=kml&startat={startat}" title="{t}Google Earth .kml{/t}">KML</a>
				{search_in_gm}
				<a href="search1.php?queryid={queryid}&output=ov2&startat={startat}" title="{t}TomTom POI .ov2{/t}">OV2</a>
				<a href="search1.php?queryid={queryid}&output=ovl&startat={startat}" title="{t}TOP50-Overlay .ovl{/t}">OVL</a>
				<a href="search1.php?queryid={queryid}&output=txt&startat={startat}" title="{t}Textfile .txt{/t}">TXT</a>
				<br />
				<b>{t}Result {startatp1} to {endat} (as zip){/t}:</b>
				<a href="search1.php?queryid={queryid}&output=gpx&startat={startat}&count=max&zip=1" title="{t}GPS Exchange Format .gpx{/t}">GPX</a>
				<a href="search1.php?queryid={queryid}&output=loc&startat={startat}&count=max&zip=1" title="{t}Waypointfile .loc{/t}">LOC</a>
				<a href="search1.php?queryid={queryid}&output=kml&startat={startat}&count=max&zip=1" title="{t}Google Earth .kml{/t}">KML</a>
				{search_in_gm_zip}
				<a href="search1.php?queryid={queryid}&output=ov2&startat={startat}&count=max&zip=1" title="{t}TomTom POI .ov2{/t}">OV2</a>
				<a href="search1.php?queryid={queryid}&output=ovl&startat={startat}&count=max&zip=1" title="{t}TOP50-Overlay .ovl{/t}">OVL</a>
				<a href="search1.php?queryid={queryid}&output=txt&startat={startat}&count=max&zip=1" title="{t}Textfile .txt{/t}">TXT</a>
			</td>
		</tr>
		<tr>
			<td class="help" colspan="2" align="right" style="line-height:2em;">
				{t}With the download you accept the <a href="articles.php?page=impressum#tos">terms of use</a> from opencaching.de.&nbsp;&nbsp;{/t}
			</td>
		</tr>
	</table>

	<p>({t}old{/t})</p>

</font>
