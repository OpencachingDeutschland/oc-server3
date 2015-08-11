{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
*
*  select on of multiple locations which matched on a location search
***************************************************************************}

<div class="content2-pagetitle"><img src="resource2/ocstyle/images/cacheicon/traditional.gif" style="margin-right: 10px;" width="32" height="32" alt="{t}Search result{/t}" />{t}Selection of city{/t} - {t 1=$resultscount}Total of %1 cities matched{/t}</div>


<p>{t}For the search criterion no clear result was found. Please choose the desired location:{/t}</p>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr><td colspan="2" style="height:4px"></td></tr>
	<tr><td colspan="2"><p>{include file="res_pager.tpl"}</p></td></tr>
	<tr><td>&nbsp;</td></td></tr>
	{$locations}
	<tr><td colspan="2" style="height:8px"></td></tr>
	<tr><td colspan="2"><p>{include file="res_pager.tpl"}</p></td></tr>
	<tr><td>&nbsp;</td></td></tr>
</table>
