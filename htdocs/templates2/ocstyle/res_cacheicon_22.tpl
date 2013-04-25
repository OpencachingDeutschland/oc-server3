{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{strip} {* OCSTYLE *}
{*cachetype=1 status=1 logtype=0 owner=false small=false*}

{if $cachetype==1}
	<img src="{$rootpath}resource2/{$opt.template.style}/images/cacheicon/unknown.gif" alt="{t}Unknown Geocache{/t}" border="0" width="22" height="22" align="left" style="margin-right: 5px;" />
{elseif $cachetype==2}
	<img src="{$rootpath}resource2/{$opt.template.style}/images/cacheicon/traditional.gif" alt="{t}Traditional Geocache{/t}" border="0" width="22" height="22" align="left" style="margin-right: 5px;" />
{elseif $cachetype==3}
	<img src="{$rootpath}resource2/{$opt.template.style}/images/cacheicon/multi.gif" alt="{t}Multicache{/t}" border="0" width="22" height="22" align="left" style="margin-right: 5px;" />
{elseif $cachetype==4}
	<img src="{$rootpath}resource2/{$opt.template.style}/images/cacheicon/virtual.gif" alt="{t}Virtual Geocache{/t}" border="0" width="22" height="22" align="left" style="margin-right: 5px;" />
{elseif $cachetype==5}
	<img src="{$rootpath}resource2/{$opt.template.style}/images/cacheicon/webcam.gif" alt="{t}Webcam Geocache{/t}" border="0" width="22" height="22" align="left" style="margin-right: 5px;" />
{elseif $cachetype==6}
	<img src="{$rootpath}resource2/{$opt.template.style}/images/cacheicon/event.gif" alt="{t}Event Geocache{/t}" border="0" width="22" height="22" align="left" style="margin-right: 5px;" />
{elseif $cachetype==7}
	<img src="{$rootpath}resource2/{$opt.template.style}/images/cacheicon/mystery.gif" alt="{t}Quizcache{/t}" border="0" width="22" height="22" align="left" style="margin-right: 5px;" />
{elseif $cachetype==8}
	<img src="{$rootpath}resource2/{$opt.template.style}/images/cacheicon/mathe.gif" alt="{t}Math/Physics Geocache{/t}" border="0" width="22" height="22" align="left" style="margin-right: 5px;" />
{elseif $cachetype==9}
	<img src="{$rootpath}resource2/{$opt.template.style}/images/cacheicon/moving.gif" alt="{t}Moving Geocache{/t}" border="0" width="22" height="22" align="left" style="margin-right: 5px;" />
{elseif $cachetype==10}
	<img src="{$rootpath}resource2/{$opt.template.style}/images/cacheicon/drivein.gif" alt="{t}Drive-In Geocache{/t}" border="0" width="22" height="22" align="left" style="margin-right: 5px;" />
{else}
	&nbsp;
{/if}
{/strip}