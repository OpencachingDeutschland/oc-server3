{* see lib2/logic/logpics.class.php for data retreival *}
{* Unicode Reminder メモ *}

{if $pages_list}
    <div class="content2-container">
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td><font size="2">{$subtitle}</font></td>
                <td class="picpaging">{include file="res_pager.tpl"}</td>
            </tr>
        </table>
    </div>
{elseif "$subtitle" != ""}
    <p>{$subtitle}</p>
{/if}

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
                    <td>
                        {include file="res_logpicture.tpl" picture=$picture}
                    </td>
                    {assign var=piccount value=$piccount+1}
                {/if}
            {/foreach}
            {* fill up remaining cells so that 2..5 pictures will not spread over container width *}
            {if $piccount<6}<td width="{$itemwidth}px"></td>{/if}
            {if $piccount<5}<td width="{$itemwidth}px"></td>{/if}
            {if $piccount<4}<td width="{$itemwidth}px"></td>{/if}
            {if $piccount<3}<td width="{$itemwidth}px"></td>{/if}
            {if $piccount<2}<td width="{$itemwidth}px"></td>{/if}
        </tr>
    </table>
    <div style="height:8px"></div>
</div>
