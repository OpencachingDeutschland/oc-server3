{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
 {* OCSTYLE *}

{*
	params
  attriblist := list of attributes from class attribute::getAttrbutesListArray*

	onmousedown := javascript funtion name to call on mouse down (to change attribute icons)
	inputprefix := name/id-prefix of hidden inputbox with state
	
	stateDisable := array with disabled attribute ids
	stateNot     := array with not-attribute ids
*}

{foreach from=$attriblist item=attribGroupItem}
	<div class="attribgroup">
		<table cellspacing="0">
			<tr>
				<td bgcolor="{$attribGroupItem.color|escape}" style="line-height:9px;padding-top:2px;margin:0 0 0 0;border-left:1px solid gray;border-right:1px solid gray;border-top:1px solid gray;">
					<font size="1">{$attribGroupItem.name|escape}</font>
				</td>
			</tr>
			<tr>
				<td bgcolor="#F8F8F8" style="margin:0 0 0 0;border-left:1px solid gray;border-right:1px solid gray;border-bottom:1px solid gray;">
					{foreach from=$attribGroupItem.attr item=attribItem}
						{array_search var=thisDisabled needle=$attribItem.id haystack=$stateDisable}
						{if $thisDisabled!==false}
							{assign var=attrState value=3} {* ATTRIB_UNDEF *}
						{else}
							{assign var=attrState value=1} {* ATTRIB_SELECTED *}
						{/if}
						{* TODO: assign var=attrState value=2 *} {* ATTRIB_UNSELECTED *}
						{include file="res_attribute.tpl" id=$attribItem.id state=$attrState name=$attribItem.name icon=$attribItem.icon html_desc=$attribItem.html_desc color=$attribGroupItem.color onmousedown=$onmousedown inputprefix=$inputprefix}
					{/foreach}
				</td>
			</tr>
		</table>
	</div>
{/foreach}