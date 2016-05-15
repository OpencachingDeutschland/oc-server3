{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
 {* OCSTYLE *}
{if $opt.msie}
    <table cellspacing=0 cellpadding=0><tr>
{/if}
{foreach from=$stat item=stats}
    {if $opt.msie}
        <td style="line-height:0.5em">
    {else}
        <div style="display:inline-block; line-height:1.8em; padding-right:1em; text-align:center">
    {/if}
        {$stats.anzahl}
        <a href="search.php?showresult=1&expert=0&output=HTML&sort=byname&f_inactive=0&{if $logs}&finderid={$userid}&searchbyfinder={else}&ownerid={$userid}&searchbyowner={/if}&cachetype={$stats.id}{if $oconly}&cache_attribs=6{/if}calledbysearch=0">{include file="res_cacheicon_22.tpl" cachetype=$stats.id alignicon=""}</a>
        {if $stat|@count > 1}
            <br /><span class="percent">{$stats.anzahl/$total*100+0.5|floor}%&nbsp;</span>
        {/if}
    {if $opt.msie}
        </td>
    {else}
        </div>
    {/if}
{/foreach}
{if $opt.msie}
    </tr></table>
{/if}
