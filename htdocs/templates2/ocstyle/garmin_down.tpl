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
						<img src="images/logo_new_small.gif"  width="66" height="66" border="0" alt="" title="" align="left" />
					</td>
					<td align="center">
						<font size="4">{t}Waypoint{/t}: {$cache.wpoc}</font>
					</td>
					<td align="right" width="265">
						<font size="1">
							Unterst&uuml;tzt und gef&ouml;rdert durch<br>
							die Deutsche Wanderjugend
						</font>
					</td>
					<td width="40">
						<img src="images/dwj.gif" border="0" alt="" title="" align="left" />
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
							<a href="#" onClick="javascript:window.open('event_attendance.php?id={$cache.cacheid}&popup=y','{t escape=js}Send to GPS device{/t}','width=320,height=440,resizable=no,scrollbars=1')">{t}List of participants{/t}</a>
						{/if}
					</td>
					<td align= "right" valign="top" nowrap="1" width="140">
						{t}Difficulty{/t}:
						<img src="./resource2/{$opt.template.style}/images/difficulty/diff-{$cache.difficulty*5}.gif" border="0" width="19" height="16" hspace="2" alt="{t 1=$cache.difficulty*0.5|sprintf:'%01.1f'}Difficulty: %1 of 5.0{/t}"><br />
						{t}Terrain{/t}:					
						<img src="./resource2/{$opt.template.style}/images/difficulty/terr-{$cache.terrain*5}.gif" border="0" width="19" height="16" hspace="2" alt="{t 1=$cache.terrain*0.5|sprintf:'%01.1f'}Terrain: %1 of 5.0{/t}">
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<p>{t}Waypoint download{/t}</p>
<div id="garminDisplay">