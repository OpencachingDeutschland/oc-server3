{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}

{* webchat for freenode irc using qwebirc embedded via iframe *}

<div class="content2-container bg-blue02">
	<p class="content-title-noshade-size3">
		<img src="resource2/{$opt.template.style}/images/misc/32x32-news.png" style="align: left; margin-right: 10px;" width="24" height="24" alt="" />
		{t}Webchat/IRC{/t}
	</p>
</div>
<div style="padding:2px 0 6px 0">
	<iframe style="margin: 0 auto; display: block;" src="http://webchat.freenode.net/?nick={$chatusername}&amp;channels=opencaching.de&amp;prompt=1" width="647" height="400"></iframe>
</div>
