<div style="width:{$itemwidth}px; height:{if $loguser || $logdate}150{else}120{/if}px; overflow:hidden">
	<table width="100%" height="100%"><tr>
		<td style="text-align:center; padding:0" align="center" valign="middle">
			<div style="max-width:{$itemwidth}px; overflow:hidden">
				<a name="piclink" href="{$picture.pic_url}">
					<img src="thumbs.php?uuid={$picture.pic_uuid}" class="img-shadow-loggallery" onclick="enlarge(this);" longdesc="{$picture.pic_url}" onload="this.alt='{$picture.title}'" />
				</a>
				{if $logdate || $loguser}
					<div style="line-height:1.2em; max-height:2.4em; margin-top:5px">
						{if $logdate}
							{if  $fullyear}
								{assign var=dateformat value=$opt.format.date}
							{elseif $picture.oldyear == "1" || $shortyear}
								{assign var=dateformat value=$opt.format.dateshort}
							{else}
								{assign var=dateformat value=$opt.format.dm}
							{/if}
							{if !$loguser}<a href="viewlogs.php?cacheid={$picture.cache_id}#log{$picture.logid}">{/if}{$picture.picdate|date_format:$dateformat}{if !$loguser}</a>{/if}{/if}&nbsp;{if $loguser}<a href="{if $profilelink}viewprofile.php?userid={$picture.user_id}{else}viewlogs.php?cacheid={$picture.cache_id}#log{$picture.logid}{/if}">{$picture.username|escape}</a>
						{/if}
					</div>
				{/if}
			</div>
		</td>
	</tr></table>
</div>
