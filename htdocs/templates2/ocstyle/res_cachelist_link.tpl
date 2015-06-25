{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}

<a href="cachelist.php?id={$cachelist.id}">{$cachelist.name|escape}</a>{if $cachelist.description}<span {if $cachelist.description}onmouseover="Tip('<div class=\'cachelist-tooltip\'>{$cachelist.description|stripcrlf|escapejs}</div>', BGCOLOR, '#FFFFFF', BORDERCOLOR, '#707070', CLICKCLOSE, true, DELAY, 0, FADEIN, false, FADEOUT, false)" onmouseout="UnTip()"{/if}> <img src="resource2/{$opt.template.style}/images/viewcache/16x16-info.png" />{/if}
</span>{if !$disable_listwatchicon && $cachelist.watched_by_me}&nbsp;<img src="resource2/{$opt.template.style}/images/viewcache/16x16-watch.png" title="{t}I am watching this list.{/t}" />{/if}
