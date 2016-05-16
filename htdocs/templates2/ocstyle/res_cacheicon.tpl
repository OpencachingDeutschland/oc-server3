{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{strip} {* OCSTYLE *}
{*cachetype=1 status=1 logtype=0 owner=false small=false*}

{if $cachetype==1}
    <img src="resource2/{$opt.template.style}/images/cacheicon/unknown{if $greyed}-grey{/if}.gif" alt="{t}Unknown Geocache{/t}" title="{t}Unknown Geocache{/t}" border="0" width="32" height="32" class="icon32" id="{if $typeid}cacheicon{$cachetype}{else}viewcache-cacheicon{/if}" onclick="{$onclick}" />
{elseif $cachetype==2}
    <img src="resource2/{$opt.template.style}/images/cacheicon/traditional{if $greyed}-grey{/if}.gif" alt="{t}Traditional Geocache{/t}" title="{t}Traditional Geocache{/t}" border="0" width="32" height="32" class="icon32" id="{if $typeid}cacheicon{$cachetype}{else}viewcache-cacheicon{/if}" onclick="{$onclick}" />
{elseif $cachetype==3}  {* Ocprop: \/cacheicon\/multi\.gif"\s+alt="Multicache" *}
    <img src="resource2/{$opt.template.style}/images/cacheicon/multi{if $greyed}-grey{/if}.gif" alt="{t}Multicache{/t}" title="{t}Multicache{/t}" border="0" width="32" height="32" class="icon32" id="{if $typeid}cacheicon{$cachetype}{else}viewcache-cacheicon{/if}" onclick="{$onclick}" />
{elseif $cachetype==4}
    <img src="resource2/{$opt.template.style}/images/cacheicon/virtual{if $greyed}-grey{/if}.gif" alt="{t}Virtual Geocache{/t}" title="{t}Virtual Geocache{/t}" border="0" width="32" height="32" class="icon32" id="{if $typeid}cacheicon{$cachetype}{else}viewcache-cacheicon{/if}" onclick="{$onclick}" />
{elseif $cachetype==5}
    <img src="resource2/{$opt.template.style}/images/cacheicon/webcam{if $greyed}-grey{/if}.gif" alt="{t}Webcam Geocache{/t}" title="{t}Webcam Geocache{/t}" border="0" width="32" height="32" class="icon32" id="{if $typeid}cacheicon{$cachetype}{else}viewcache-cacheicon{/if}" onclick="{$onclick}" />
{elseif $cachetype==6}
    <img src="resource2/{$opt.template.style}/images/cacheicon/event{if $greyed}-grey{/if}.gif" alt="{t}Event Geocache{/t}" title="{t}Event Geocache{/t}" border="0" width="32" height="32" class="icon32" id="{if $typeid}cacheicon{$cachetype}{else}viewcache-cacheicon{/if}" onclick="{$onclick}" />
{elseif $cachetype==7}
    <img src="resource2/{$opt.template.style}/images/cacheicon/mystery{if $greyed}-grey{/if}.gif" alt="{t}Quiz Cache{/t}" title="{t}Quiz Cache{/t}" border="0" width="32" height="32" class="icon32" id="{if $typeid}cacheicon{$cachetype}{else}viewcache-cacheicon{/if}" onclick="{$onclick}" />
{elseif $cachetype==8}
    <img src="resource2/{$opt.template.style}/images/cacheicon/mathe{if $greyed}-grey{/if}.gif" alt="{t}Math/Physics Geocache{/t}" title="{t}Math/Physics Geocache{/t}" border="0" width="32" height="32" class="icon32" id="{if $typeid}cacheicon{$cachetype}{else}viewcache-cacheicon{/if}" onclick="{$onclick}" />
{elseif $cachetype==9}
    <img src="resource2/{$opt.template.style}/images/cacheicon/moving{if $greyed}-grey{/if}.gif" alt="{t}Moving Geocache{/t}" title="{t}Moving Geocache{/t}" border="0" width="32" height="32" class="icon32" id="{if $typeid}cacheicon{$cachetype}{else}viewcache-cacheicon{/if}" onclick="{$onclick}" />
{elseif $cachetype==10}
    <img src="resource2/{$opt.template.style}/images/cacheicon/drivein{if $greyed}-grey{/if}.gif" alt="{t}Drive-In Geocache{/t}" title="{t}Drive-In Geocache{/t}" border="0" width="32" height="32" class="icon32" id="{if $typeid}cacheicon{$cachetype}{else}viewcache-cacheicon{/if}" onclick="{$onclick}" />
{else}
    &nbsp;
{/if}

{/strip}
