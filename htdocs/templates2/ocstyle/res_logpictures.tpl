{* see lib2/logic/logpics.inc.php for data retreival *}

{assign var=itemwidth value=120}

<div style="padding-right:14px; clear:both">
	<table width="100%">
		<tr>
			{assign var=piccount value=0}
			{assign var=lines value=0}
			{foreach from=$pictures item=picture}
				{if $piccount == 6}
					</tr><tr>
					{assign var=piccount value=0}
					{assign var=lines value=$lines+1}
				{/if}
				{if !$maxlines || $lines < $maxlines}
					<td width="{$itemwidth+6}px"> {* width is needed for empty fill-up cells *}
						{if $picture.pic_uuid != ""}
							{include file="res_logpicture.tpl" picture=$picture}
						{/if}
					</td>
					{assign var=piccount value=$piccount+1}
				{/if}
			{/foreach}
		</tr>
	</table>
	<div style="height:8px"></div>
</div>
