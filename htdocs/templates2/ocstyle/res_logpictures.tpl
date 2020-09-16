{* see lib2/logic/logpics.class.php for data retreival *}

{if $pages_list}
    <div class="container-fluid">
        <table>
            <tr>
                <td>{$subtitle}</td>
                <td class="picpaging">{include file="res_pager.tpl"}</td>
            </tr>
        </table>
    </div>
{elseif "$subtitle" != ""}
    {$subtitle}
{/if}


<div class="row">

        {assign var=piccount value=0}
        {assign var=lines value=0}
        {foreach from=$pictures item=picture}
            {if $piccount == 6}
                {assign var=piccount value=0}
                {assign var=lines value=$lines+1}
            {/if}
            {if !$maxlines || $lines < $maxlines}

                {include file="res_logpicture.tpl" picture=$picture}

                {assign var=piccount value=$piccount+1}
            {/if}
        {/foreach}

</div>
