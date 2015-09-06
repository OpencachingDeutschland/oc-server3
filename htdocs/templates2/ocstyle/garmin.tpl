{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
<table class="content">
	<tr>
		<td class="header">
			<table class="null" border="0">
				<tr>
					<td width="30">
						<img src="images/newlogo.png"  height="66" border="0" alt="" title="" align="left" style="margin-bottom:8px" />
					</td>
					<td>&nbsp;</td>
					<td align="center"><font size="4">
						{t}Waypoint:{/t} {$cache.wpoc}
					</font>
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
					<td align="left" valign="top" width="397"><font size="3"><b>{$cache.name|escape}</b></font><br />
						<span class="garmintext">{t}by{/t} {$cache.username|escape}<br />
						{if $cache.shortdesc!=''}
							{$cache.shortdesc|escape}
						{/if}
						</span>
					</td>
					<td class="garmintext" align= "right" valign="top" nowrap="1" width="140">
						{t}Difficulty:{/t}
						<img src="./resource2/{$opt.template.style}/images/difficulty/diff-{$cache.difficulty*5}.gif" border="0" width="19" height="16" hspace="2" alt="{t 1=$cache.difficulty*0.5}Difficulty:&nbsp;%1&nbsp;of&nbsp;5{/t}" /><br />
						{t}Terrain:{/t}					
						<img src="./resource2/{$opt.template.style}/images/difficulty/terr-{$cache.terrain*5}.gif" border="0" width="19" height="16" hspace="2" alt="{t 1=$cache.terrain*0.5|sprintf:'%01.1f'}Terrain:&nbsp;%1&nbsp;of&nbsp;5{/t}" />
					</td>
				</tr>
				<tr><td colspan="2" class="garmintext"><br />{t}Waypoint download{/t}</td></tr>
			</table>
		</td>
	</tr>
</table>

<table class="table">
	<tr>
		<td width="350px">
			<div id="garminDisplay"></div>
		</td>
		<td>
			<a href="http://www.garmin.com" target="_blank"><img src="images/garmin.png" width="200px" height="78px" alt="Garmin Mobile Navigation" title="Garmin Mobile Navigation" /></a>
		</td>
	</tr>
</table>
