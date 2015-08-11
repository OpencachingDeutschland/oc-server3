{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}

{* webchat for freenode irc using qwebirc embedded via iframe *}

<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/misc/32x32-news.png" style="margin-right: 10px;" width="24" height="24" alt="" />
	{t}Chat/IRC{/t}
</div>
<div style="padding:2px 0 6px 0">
	<p><a href="{$chatiframeurl}" target="_blank">{t}Open chat in a new tab/window{/t}</a></p>
	<iframe style="margin: 25px auto; display: block; border: 1px grey solid;" src="{$chatiframeurl}" width="{$chatiframewidth}" height="{$chatiframeheight}"></iframe>
</div>
