{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{assign var='lastCountry' value=''}
{strip}
    {foreach name=newCaches from=$newCaches item=newCache}
        {if $newCache.country_name!=$lastCountry}{if $lastCountry != ''}, {/if}
            <a href="{$smarty.server.PHP_SELF}#country_{$newCache.country}" class="systemlink">
                {$newCache.country_name}
            </a>
        {/if}
        {assign var='lastCountry' value=$newCache.country_name}
    {/foreach}
{/strip}
