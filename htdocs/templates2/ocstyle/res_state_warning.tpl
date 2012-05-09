{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}

{if ($cache.status==3) || ($cache.status==6)}
	<div class="isarchived">
		<p><strong>{t 1=$cache.statusName|escape}Attention! This Geocache is &quot;<span class="errormsg">%1</span>&quot;!</strong> There is no physical container at the specified (or to be determined) coordinates. In the interest of the place it should not be necessarily to search!{/t}</p>
	</div>
{elseif $cache.status==2}
	<div class="isarchived">
		<p><strong>{t 1=$cache.statusName|escape}Attention! This Geocache is &quot;<span class="errormsg">%1</span>&quot;!</strong> The geocache itself or parts of it are missing or there are other issues that make a successful search impossible. In the interest of the place it should not be necessarily to search!{/t}</p>
	</div>
{/if}