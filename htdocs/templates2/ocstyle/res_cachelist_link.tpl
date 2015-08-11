{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}

<a href="cachelist.php?id={$cachelist.id}">{$cachelist.name|escape}</a>{if $cachelist.description}<span {if $cachelist.description}onmouseover="Tip('<div class=\'cachelist-tooltip\'>{$cachelist.description|stripcrlf|escapejs}</div>', BGCOLOR, '#FFFFFF', BORDERCOLOR, '#707070', CLICKCLOSE, true, DELAY, 0, FADEIN, false, FADEOUT, false)" onmouseout="UnTip()"{/if}> <img src="resource2/{$opt.template.style}/images/viewcache/16x16-info.png" /></span>{/if}{if $show_bookmarks && $cachelist.bookmarked}<a href="mylists.php#bookmarks"><img src="resource2/{$opt.template.style}/images/viewcache/cache-rate.png" title="{t}I have bookmarked this list.{/t}" style="padding-left:{if $cachelist.description}2px{else}4px{/if}" /></a>{/if}{if !$disable_listwatchicon && $cachelist.watched_by_me}<img src="resource2/{$opt.template.style}/images/viewcache/16x16-watch.png" title="{t}I am watching this list.{/t}" style="padding-left:{if $cachelist.description || ($markprivlists && $cachelist.visibility==2)}3px{else}0.4em{/if}; padding-right:2px" />{/if}
