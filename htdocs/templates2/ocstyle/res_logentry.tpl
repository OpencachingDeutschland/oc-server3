{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
{if $log != "N"}

{if $header==true}
  <div class="content2-container bg-blue02">
	  <p class="content-title-noshade-size2">
			<img src="resource2/{$opt.template.style}/images/description/22x22-logs.png" style="align: left; margin-right: 10px;" width="22" height="22" alt="Logs" />
			{capture name=cachename}<a href="viewcache.php?wp={$cache.wpoc|urlencode}">{$cache.name|escape}</a>{/capture}
			{t 1=$smarty.capture.cachename}Logentries for %1{/t}
			<span style="font-weight: 400;">&nbsp;&nbsp;
				<img src="resource2/{$opt.template.style}/images/log/16x16-{if $cache.type==6}attended{else}found{/if}.png" width="16" height="16" align="middle" border="0" align="left" alt="{if $cache.type==6}{t}Attended{/t}{else}{t}Found{/t}{/if}" title="{if $cache.type==6}{t}Attended{/t}{else}{t}Found{/t}{/if}"> {$cache.found}x

				<img src="resource2/{$opt.template.style}/images/log/16x16-{if $cache.type==6}will_attend{else}dnf{/if}.png" width="16" height="16" align="middle" border="0" align="left" alt="{if $cache.type==6}{t}Will attend{/t}{else}{t}Not Found{/t}{/if}" title="{if $cache.type==6}{t}Will attend{/t}{else}{t}Not Found{/t}{/if}"> {if $cache.type==6}{$cache.willattend}{else}{$cache.notfound}{/if}x 

				<img src="resource2/{$opt.template.style}/images/log/16x16-note.png" width="16" height="16" align="middle" border="0" align="left" align="{t}Note{/t}" title="{t}Note{/t}"> {$cache.note}x<br />
			</span>
		</p>
	</div>

	<div class="content2-container">

{/if}

		{foreach from=$logs item=logItem name=log} 
			{include file="res_logentry_logitem.tpl" logs=$logs cache=$cache}
		{/foreach}

{if $footer==true}

		{if $footbacklink==true}

					<p><span style="font-weight: 400;">[<a href="viewcache.php?wp={$cache.wpoc|urlencode}">{t}Back to the Geocache{/t}</a>]</span></p>

		{/if}

	</div>	

{* $footer==true *}
{/if}

{* $log != "N" *}
{/if}
