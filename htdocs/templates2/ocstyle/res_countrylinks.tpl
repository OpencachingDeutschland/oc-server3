{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{assign var='lastCountry' value=''}
{foreach name=newCaches from=$newCaches item=newCache}{if $newCache.country_name!=$lastCountry}{if $lastCountry != ''}, {/if}<a href="#country_{$newCache.country}" class="systemlink">{$newCache.country_name}</a>{/if}{assign var='lastCountry' value=$newCache.country_name}{/foreach}
