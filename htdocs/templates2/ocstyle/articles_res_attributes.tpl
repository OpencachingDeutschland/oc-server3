{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}

<table class="narrowtable">
    {foreach from=$attributes item=attribGroupItem}
        <tr><td><div style="height:4px"></div></td></tr>
        <tr><td colspan="2" bgcolor="{$attribGroupItem.color|escape}" style="line-height:1.8em"><b><i>{$attribGroupItem.category|escape} / {$attribGroupItem.name|escape}</i></b></td></tr>
        <tr><td><div style="height:8px"></div></td></tr>

        {foreach from=$attribGroupItem.attr item=attribItem name=attrItem}
            <tr id="attr{$attribItem.id}">
                <td valign="top" style="padding:6px 5px 6px 8px"><img src="resource2/ocstyle/images/attributes/{$attribItem.icon|escape}.png" border="0" align="left" alt="{$attribItem.name|escape}" title="{$attribItem.name|escape}" /></td>
                <td valign="top">
                    <p><span class="subtitle-header">{$attribItem.name|escape}</span><br /> {$attribItem.html_desc}</p>
                </td>
            </tr>
        {/foreach}
    {/foreach}
</table>
