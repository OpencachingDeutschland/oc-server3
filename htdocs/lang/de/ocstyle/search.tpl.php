<?php
/***************************************************************************
												  ./lang/de/ocstyle/search.simple.tpl.php
															-------------------
		begin                : July 25 2004
		copyright            : (C) 2004 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

	***************************************************************************/

/***************************************************************************
	*
	*   This program is free software; you can redistribute it and/or modify
	*   it under the terms of the GNU General Public License as published by
	*   the Free Software Foundation; either version 2 of the License, or
	*   (at your option) any later version.
	*
	***************************************************************************/

/****************************************************************************

   Unicode Reminder メモ

	 simple filter template for XHTML search form

 ****************************************************************************/
?>
<script type="text/javascript" src="resource2/ocstyle/js/wz_tooltip.js"></script>
<script type="text/javascript">
<!--
var mnAttributesShowCat2 = 1;
var maAttributes = new Array({attributes_jsarray});

function _sbn_click()
{
	if (document.searchbyname.cachename.value == "")
	{
		alert("{t}Enter a name, please!{/t}");
		return false;
	}
	return true;
}

function _sbft_click()
{
	if (document.searchbyfulltext.fulltext.value == "")
	{
		alert("{t}Fill out the text field, please!{/t}");
		return false;
	}

	if ((document.searchbyfulltext.ft_name.checked == false) &&
	   (document.searchbyfulltext.ft_desc.checked == false) &&
	   (document.searchbyfulltext.ft_logs.checked == false) &&
	   (document.searchbyfulltext.ft_pictures.checked == false))
	{
		alert("{t}You have to check at least one field!{/t}");
		return false;
	}

	return true;
}

function _sbd_click()
{
	if (isNaN(document.searchbydistance.lon_h.value) || isNaN(document.searchbydistance.lon_min.value))
	{
		alert("{t}Longitude has to be a number!\nFormat: hh° mm.mmm{/t}");
		return false;
	}
	else if (isNaN(document.searchbydistance.lat_h.value) || isNaN(document.searchbydistance.lat_min.value))
	{
		alert("{t}Latitude has to be a number!\nFormat: hh° mm.mmm{/t}");
		return false;
	}
	else if (isNaN(document.searchbydistance.distance.value))
	{
		alert("{t}The maximum distance has to be a number!{/t}");
		return false;
	}
	else if (document.searchbydistance.distance.value <= 0 || document.searchbydistance.distance.value > 9999)
	{
		alert("{t}The distance has to be between 0 and 9999{/t}");
		return false;
	}
	return true;
}

function _sbplz_click()
{
	if (document.searchbyplz.plz.value == "")
	{
		alert("{t}Enter the postal code, please!{/t}");
		return false;
	}
	return true;
}

function _sbort_click()
{
	if (document.searchbyort.ort.value == "")
	{
		alert("{t}Enter the city, please!{/t}");
		return false;
	}
	return true;
}

function _sbo_click()
{
	if (document.searchbyowner.owner.value == "")
	{
		alert("{t}Enter the owner, please!{/t}");
		return false;
	}
	return true;
}

function _sbf_click()
{
	if (document.searchbyfinder.finder.value == "")
	{
		alert("{t}Enter the username, please!{/t}");
		return false;
	}
	return true;
}

function sync_options(element)
{
	var formnames = new Array();
	formnames[0] = "searchbyname";
	formnames[1] = "searchbydistance";
	formnames[2] = "searchbyowner";
	formnames[3] = "searchbyfinder";
	formnames[4] = "searchbyplz";
	formnames[5] = "searchbyort";
	formnames[6] = "searchbyfulltext";

	var sortby = "";
	if (document.optionsform.sort[0].checked == true)
		sortby = "byname";
	else if (document.optionsform.sort[1].checked == true)
		sortby = "bydistance";
	else if (document.optionsform.sort[2].checked == true)
		sortby = "bycreated";
	else if (document.optionsform.sort[3].checked == true)
		sortby = "bylastlog";

	var tmpattrib = "";
	for (i = 0; i < maAttributes.length; i++)
		if (maAttributes[i][1] == 1)
			tmpattrib = '' + tmpattrib + maAttributes[i][0] + ';';
	if(tmpattrib.length > 0)
		tmpattrib = tmpattrib.substr(0, tmpattrib.length-1);

	var tmpattrib_not = "";
	for (i = 0; i < maAttributes.length; i++)
		if (maAttributes[i][1] == 2)
			tmpattrib_not = '' + tmpattrib_not + maAttributes[i][0] + ';';
	if(tmpattrib_not.length > 0)
		tmpattrib_not = tmpattrib_not.substr(0, tmpattrib_not.length-1);

	var tmpcachetype = "";
	for (i = 1; i <= 10; i++)
	{
		if (document.getElementById('cachetype' + i).checked == true)
		{
			if (tmpcachetype != "") tmpcachetype = tmpcachetype + ";";
			tmpcachetype = tmpcachetype + i;
		}
	}
	if (tmpcachetype == "") tmpcachetype = "none";

	var tmpcachesize = "";
	for (i = 1; i <= 7; i++)
	{
		if (document.getElementById('cachesize' + i).checked == true)
		{
			if (tmpcachesize != "") tmpcachesize = tmpcachesize + ";";
			tmpcachesize = tmpcachesize + i;
		}
	}
	if (tmpcachesize == "") tmpcachesize = "none";

	for (var i in formnames)
	{
		document.forms[formnames[i]].sort.value = sortby;
		document.forms[formnames[i]].orderRatingFirst.value = document.optionsform.orderRatingFirst.checked ? 1 : 0;
		document.forms[formnames[i]].f_userowner.value = document.optionsform.f_userowner.checked ? 1 : 0;
		document.forms[formnames[i]].f_userfound.value = document.optionsform.f_userfound.checked ? 1 : 0;
		document.forms[formnames[i]].f_inactive.value = document.optionsform.f_inactive.checked ? 1 : 0;
		document.forms[formnames[i]].f_ignored.value = document.optionsform.f_ignored.checked ? 1 : 0;
		document.forms[formnames[i]].f_otherPlatforms.value = document.optionsform.f_otherPlatforms.checked ? 1 : 0;
		document.forms[formnames[i]].country.value = document.optionsform.country.value;
		document.forms[formnames[i]].difficultymin.value = document.optionsform.difficultymin.value;
		document.forms[formnames[i]].difficultymax.value = document.optionsform.difficultymax.value;
		document.forms[formnames[i]].terrainmin.value = document.optionsform.terrainmin.value;
		document.forms[formnames[i]].terrainmax.value = document.optionsform.terrainmax.value;
		document.forms[formnames[i]].cachetype.value = tmpcachetype;
		document.forms[formnames[i]].cachesize.value = tmpcachesize;
		document.forms[formnames[i]].cache_attribs.value = tmpattrib;
		document.forms[formnames[i]].cache_attribs_not.value = tmpattrib_not;
	}
}

function switchAttribute(id)
{
	var attrImg1 = document.getElementById("attrimg1_" + id);
	var attrImg2 = document.getElementById("attrimg2_" + id);
	var nArrayIndex = 0;

	for (nArrayIndex = 0; nArrayIndex < maAttributes.length; nArrayIndex++)
	{
		if (maAttributes[nArrayIndex][0] == id)
			break;
	}

	if (maAttributes[nArrayIndex][1] == 0)
	{
		if (attrImg1) attrImg1.src = maAttributes[nArrayIndex][3];
		if (attrImg2) attrImg2.src = maAttributes[nArrayIndex][3];
		maAttributes[nArrayIndex][1] = 1;
	}
	else if (maAttributes[nArrayIndex][1] == 1)
	{
		if (attrImg1) attrImg1.src = maAttributes[nArrayIndex][4];
		if (attrImg2) attrImg2.src = maAttributes[nArrayIndex][4];
		maAttributes[nArrayIndex][1] = 2;
	}
	else if (maAttributes[nArrayIndex][1] == 2)
	{
		if (attrImg1) attrImg1.src = maAttributes[nArrayIndex][5];
		if (attrImg2) attrImg2.src = maAttributes[nArrayIndex][5];
		maAttributes[nArrayIndex][1] = 0;
	}

	sync_options(null);
}

function hideAttributesCat2()
{
	mnAttributesShowCat2 = 0;
	document.getElementById('attributesCat1').style.display = "block";
	document.getElementById('attributesCat2').style.display = "none";
	document.getElementById('toggleAttributesCaption').firstChild.nodeValue = "{t}Show all{/t}";
}

function showAttributesCat2()
{
	mnAttributesShowCat2 = 1;
	document.getElementById('attributesCat1').style.display = "none";
	document.getElementById('attributesCat2').style.display = "block";
	document.getElementById('toggleAttributesCaption').firstChild.nodeValue = "{t}Less{/t}";
}

function switchAttributeCat2()
{
	if (mnAttributesShowCat2 != 0)
		hideAttributesCat2();
	else
		showAttributesCat2();
}
//-->
</script>

		  <div class="content2-pagetitle"><img src="resource2/ocstyle/images/cacheicon/traditional.gif" style="align: left; margin-right: 10px;" width="32" height="32" alt="{t}Search for caches{/t}" />{t}Search for caches{/t}</div>

<form name="optionsform" style="display:inline;">

<div class="searchdiv">
	<table class="table">
		
		<tr><td class="spacer" colspan="3"><span id="scriptwarning" class="errormsg">{t}JavaScrupt is not activated, you cannot use the following options - basic search is supported nevertheless.{/t}</span></td></tr>
		<tr>
			<td>{t}Sorting of result:{/t}</td>
			<td colspan="2">
				<input type="radio" name="sort" value="byname" index="0" id="l_sortbyname" class="radio" onclick="javascript:sync_options(this)" {byname_checked}> <label for="l_sortbyname">{t}Cachename{/t}</label>&nbsp;
				<input type="radio" name="sort" value="bydistance" index="1" id="l_sortbydistance" class="radio" onclick="javascript:sync_options(this)" {bydistance_checked}> <label for="l_sortbydistance">{t}Distance{/t}</label>&nbsp;
				<input type="radio" name="sort" value="bycreated" index="2" id="l_sortbycreated" class="radio" onclick="javascript:sync_options(this)" {bycreated_checked}> <label for="l_sortbycreated">{t}Listed since{/t}</label>&nbsp;
				<input type="radio" name="sort" value="bylastlog" index="3" id="l_sortbylastlog" class="radio" onclick="javascript:sync_options(this)" {bylastlog_checked}> <label for="l_sortbylastlog">{t}Last log{/t}</label>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<input id="orderRatingFirst" type="checkbox" name="orderRatingFirst" class="checkbox" value="1" onclick="javascript:sync_options(this)" {orderRatingFirst_checked} />
				<label for="orderRatingFirst">{t}Show recommendation from other users first{/t}</label>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td class="help" colspan="2">
				<img src="lang/de/ocstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="{t}Notice{/t}" title="{t}Notice{/t}" align="middle">
				{t}Distance can only be used if you are signed in and entered your home coordinates.<br />For the search by distance the entered coordinates are used to calculate the distance.{/t}
			</td>
		</tr>
	</table>
</div>
<div class="searchdiv">
	<table class="table">
		<tr>
			<td>{t}Hide following caches:{/t}</td>
			<td colspan="2">
				<input type="checkbox" name="f_userowner" value="1" id="l_userowner" class="checkbox" onclick="javascript:sync_options(this)" {f_userowner_disabled} /> <label for="l_userowner">{t}My owned{/t}</label>&nbsp;&nbsp;
				<input type="checkbox" name="f_userfound" value="1" id="l_userfound" class="checkbox" onclick="javascript:sync_options(this)" {f_userfound_disabled} /> <label for="l_userfound">{t}My finds{/t}</label>&nbsp;&nbsp;
				<input type="checkbox" name="f_ignored" value="1" id="l_ignored" class="checkbox" onclick="javascript:sync_options(this)" {f_ignored_disabled} > <label for="l_ignored">{t}My ignored{/t}</label>&nbsp;&nbsp;
				<img src="lang/de/ocstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="{t}Notice{/t}" title="{t}Notice{/t}" align="middle">{t}Only usable if signed in.{/t}
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan="2">
				<input type="checkbox" name="f_inactive" value="1" id="l_inactive" class="checkbox" onclick="javascript:sync_options(this)" {f_inactive_checked} > <label for="l_inactive">{t}Inactive{/t}</label>
				<input type="checkbox" name="f_otherPlatforms" value="1" id="l_otherPlatforms" class="checkbox" onclick="javascript:sync_options(this)" {f_otherPlatforms_checked} > <label for="l_otherPlatforms">{t}Multiple listings (waypoint is also set on gc.com or nc.com){/t}</label>
			</td>
		</tr>
	</table>
</div>
<div class="searchdiv">
	<table class="table">
		<tr>
			<td valign="top">{t}Cachetype:{/t}</td>
			<td>
				<table class="table">
					<tr>
						<td><input type="checkbox" id="cachetype2" name="cachetype2" value="2" onclick="javascript:sync_options(this)" class="checkbox" {cachetype2checked} /> <label for="cachetype2">{t}Traditional Cache{/t}</label></td>
						<td><input type="checkbox" id="cachetype3" name="cachetype3" value="3" onclick="javascript:sync_options(this)" class="checkbox" {cachetype3checked} /> <label for="cachetype3">{t}Multicache{/t}</label></td>
						<td><input type="checkbox" id="cachetype5" name="cachetype5" value="5" onclick="javascript:sync_options(this)" class="checkbox" {cachetype5checked} /> <label for="cachetype5">{t}Webcam Cache{/t}</label></td>
						<td><input type="checkbox" id="cachetype6" name="cachetype6" value="6" onclick="javascript:sync_options(this)" class="checkbox" {cachetype6checked} /> <label for="cachetype6">{t}Event Cache{/t}</label></td>
					</tr>
					<tr>
						<td><input type="checkbox" id="cachetype7" name="cachetype7" value="7" onclick="javascript:sync_options(this)" class="checkbox" {cachetype7checked} /> <label for="cachetype7">{t}Quizcache{/t}</label></td>
						<td><input type="checkbox" id="cachetype8" name="cachetype8" value="8" onclick="javascript:sync_options(this)" class="checkbox" {cachetype8checked} /> <label for="cachetype8">{t}Math/Physics-Cache{/t}</label></td>
						<td><input type="checkbox" id="cachetype9" name="cachetype9" value="9" onclick="javascript:sync_options(this)" class="checkbox" {cachetype9checked} /> <label for="cachetype9">{t}Moving Cache{/t}</label></td>
						<td><input type="checkbox" id="cachetype10" name="cachetype10" value="10" onclick="javascript:sync_options(this)" class="checkbox" {cachetype10checked} /> <label for="cachetype10">{t}Drive-In{/t}</label></td>
					</tr>
					<tr>
						<td><input type="checkbox" id="cachetype4" name="cachetype4" value="4" onclick="javascript:sync_options(this)" class="checkbox" {cachetype4checked} /> <label for="cachetype4">{t}virtual Cache{/t}</label></td>
						<td><input type="checkbox" id="cachetype1" name="cachetype1" value="1" onclick="javascript:sync_options(this)" class="checkbox" {cachetype1checked} /> <label for="cachetype1">{t}unknown cachetyp{/t}</label></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
<div class="searchdiv">
	<table class="table">
		<tr>
			<td valign="top">{t}Cachesize:{/t}</td>
			<td>
				<table class="table">
					<tr>
						<td><input type="checkbox" id="cachesize2" name="cachesize2" value="2" onclick="javascript:sync_options(this)" class="checkbox" {cachesize2checked} /> <label for="cachesize2">{t}micro{/t}</label></td>
						<td><input type="checkbox" id="cachesize3" name="cachesize3" value="3" onclick="javascript:sync_options(this)" class="checkbox" {cachesize3checked} /> <label for="cachesize3">{t}small{/t}</label></td>
						<td><input type="checkbox" id="cachesize4" name="cachesize4" value="4" onclick="javascript:sync_options(this)" class="checkbox" {cachesize4checked} /> <label for="cachesize4">{t}normal{/t}</label></td>
						<td><input type="checkbox" id="cachesize5" name="cachesize5" value="5" onclick="javascript:sync_options(this)" class="checkbox" {cachesize5checked} /> <label for="cachesize5">{t}large{/t}</label></td>
					</tr>
					<tr>
						<td><input type="checkbox" id="cachesize6" name="cachesize6" value="6" onclick="javascript:sync_options(this)" class="checkbox" {cachesize6checked} /> <label for="cachesize6">{t}very large{/t}</label></td>
						<td><input type="checkbox" id="cachesize7" name="cachesize7" value="7" onclick="javascript:sync_options(this)" class="checkbox" {cachesize7checked} /> <label for="cachesize7">{t}no container{/t}</label></td>
						<td><input type="checkbox" id="cachesize1" name="cachesize1" value="1" onclick="javascript:sync_options(this)" class="checkbox" {cachesize1checked} /> <label for="cachesize1">{t}other size{/t}</label></td>
						<td>&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
<div class="searchdiv">
	<table class="table">
		<tr>
			<td>{t}Difficulty:{/t}</td>
			<td>
				<select name="difficultymin" class="input80" onChange="javascript:sync_options(this)">
					{difficultymin_options}
				</select>
				&nbsp;&nbsp;&nbsp;{t}to{/t}&nbsp;&nbsp;&nbsp;
				<select name="difficultymax" class="input80" onChange="javascript:sync_options(this)">
					{difficultymax_options}
				</select>
			</td>
		</tr>
		<tr>
			<td>{t}Terrain:{/t}</td>
			<td>
				<select name="terrainmin" class="input80" onChange="javascript:sync_options(this)">
					{terrainmin_options}
				</select>
				&nbsp;&nbsp;&nbsp;{t}to{/t}&nbsp;&nbsp;&nbsp;
				<select name="terrainmax" class="input80" onChange="javascript:sync_options(this)">
					{terrainmax_options}
				</select>
			</td>
		</tr>
	</table>
</div>
<div class="searchdiv">
	<table class="table">
		<tr>
			<td>{t}Country:{/t}</td>
			<td>
				<select name="country" class="input200" onChange="javascript:sync_options(this)">
					{countryoptions}
				</select>
			</td>
		</tr>
	</table>
</div>
<div class="searchdiv">
	<table class="table">
		<tr>
			<td valign="top">
				{t}Cache attributes:{/t}<br />
				(<a href="javascript:switchAttributeCat2()"><span id="toggleAttributesCaption">{t}Show all{/t}</span></a>)
			</td>
			<td>
				<div id="attributesCat1" style="display:none;">{cache_attribCat1_list}</div>
				<div id="attributesCat2" style="display:block;">{cache_attribCat2_list}</div>
			</td>
		</tr>
	</table>
</div>
</form>

<script language="javascript">
<!--
	document.getElementById("scriptwarning").firstChild.nodeValue = "";

	// hide advanced attributes if none is selected
	var i = 0;
	var bHide = true;
	for (i = 0; i < maAttributes.length; i++)
	{
		if (maAttributes[i][1] != 0 && maAttributes[i][6] != 1)
		{
			bHide = false;
			break;
		}
	}

	if (bHide == true)
		hideAttributesCat2();
-->
</script>

<form action="search.php" onsubmit="javascript:return(_sbn_click());" method="{formmethod}" enctype="application/x-www-form-urlencoded" name="searchbyname" dir="ltr" style="display:inline;">
	<input type="hidden" name="searchto" value="searchbyname" />
	<input type="hidden" name="showresult" value="1" />
	<input type="hidden" name="expert" value="0" />
	<input type="hidden" name="output" value="HTML" />
	<input type="hidden" name="utf8" value="1" />

	<input type="hidden" name="sort" value="{hidopt_sort}" />
	<input type="hidden" name="orderRatingFirst" value="{hidopt_orderRatingFirst}" />
	<input type="hidden" name="f_userowner" value="{hidopt_userowner}" />
	<input type="hidden" name="f_userfound" value="{hidopt_userfound}" />
	<input type="hidden" name="f_inactive" value="{hidopt_inactive}" />
	<input type="hidden" name="f_ignored" value="{hidopt_ignored}" />
	<input type="hidden" name="f_otherPlatforms" value="{hidopt_otherPlatforms}" />
	<input type="hidden" name="country" value="{country}" />
	<input type="hidden" name="cachetype" value="{cachetype}" />
	<input type="hidden" name="cachesize" value="{cachesize}" />
	<input type="hidden" name="difficultymin" value="{difficultymin}" />
	<input type="hidden" name="difficultymax" value="{difficultymax}" />
	<input type="hidden" name="terrainmin" value="{terrainmin}" />
	<input type="hidden" name="terrainmax" value="{terrainmax}" />
	<input type="hidden" name="cache_attribs" value="{hidopt_attribs}" />
	<input type="hidden" name="cache_attribs_not" value="{hidopt_attribs_not}" />

	<div class="buffer" style="width: 500px;">&nbsp;</div>
	<div class="content2-container bg-blue02">
		<p class="content-title-noshade-size2">
			<img src="resource2/ocstyle/images/misc/32x32-searchresults.png" style="align: left; margin-right: 10px;" width="22" height="22" alt="" /> 
			{t}Search by cachename{/t}
		</p>
	</div>
  		
	<table class="table">
		<colgroup>
			<col width="200">
			<col width="220">
			<col>
		</colgroup>
		<tr><td class="spacer" colspan="3"></td></tr>
		<tr>
			<td>{t}Name:{/t}</td>
			<td><input type="text" name="cachename" value="{cachename}" class="input200" /></td>
			<td><input type="submit" value="{t}Search{/t}" class="formbuttons" /></td>
		</tr>
		<tr><td class="spacer" colspan="3"></td></tr>
	</table>
</form>

<form action="search.php" onsubmit="javascript:return(_sbd_click());" method="{formmethod}" enctype="application/x-www-form-urlencoded" name="searchbydistance" dir="ltr" style="display:inline;">
	<input type="hidden" name="searchto" value="searchbydistance" />
	<input type="hidden" name="showresult" value="1" />
	<input type="hidden" name="expert" value="0" />
	<input type="hidden" name="output" value="HTML" />
	<input type="hidden" name="utf8" value="1" />

	<input type="hidden" name="sort" value="{hidopt_sort}" />
	<input type="hidden" name="orderRatingFirst" value="{hidopt_orderRatingFirst}" />
	<input type="hidden" name="f_userowner" value="{hidopt_userowner}" />
	<input type="hidden" name="f_userfound" value="{hidopt_userfound}" />
	<input type="hidden" name="f_inactive" value="{hidopt_inactive}" />
	<input type="hidden" name="f_ignored" value="{hidopt_ignored}" />
	<input type="hidden" name="f_otherPlatforms" value="{hidopt_otherPlatforms}" />
	<input type="hidden" name="country" value="{country}" />
	<input type="hidden" name="cachetype" value="{cachetype}" />
	<input type="hidden" name="cachesize" value="{cachesize}" />
	<input type="hidden" name="difficultymin" value="{difficultymin}" />
	<input type="hidden" name="difficultymax" value="{difficultymax}" />
	<input type="hidden" name="terrainmin" value="{terrainmin}" />
	<input type="hidden" name="terrainmax" value="{terrainmax}" />
	<input type="hidden" name="cache_attribs" value="{hidopt_attribs}" />
	<input type="hidden" name="cache_attribs_not" value="{hidopt_attribs_not}" />

	<div class="buffer" style="width: 500px;">&nbsp;</div>
	<div class="content2-container bg-blue02">
		<p class="content-title-noshade-size2">
			<img src="resource2/ocstyle/images/misc/32x32-searchresults.png" style="align: left; margin-right: 10px;" width="22" height="22" alt="" /> 
			{t}Search by distance{/t}
		</p>
	</div>

	<table class="table">
		<colgroup>
			<col width="200">
			<col width="220">
			<col>
		</colgroup>
		<tr><td class="spacer" colspan="3"></td></tr>
		<tr>
			<td valign="top">{t}Of coordinates:{/t}</td>
			<td colspan="2" valign="top">
				<select name="latNS" class="input40">
					<option value="N" {latN_sel}>{t}N{/t}</option>
					<option value="S" {latS_sel}>{t}S{/t}</option>
				</select>&nbsp;
				<input type="text" name="lat_h" maxlength="2" value="{lat_h}" class="input30" />&nbsp;°&nbsp;
				<input type="text" name="lat_min" maxlength="6" value="{lat_min}" class="input40" />&nbsp;'&nbsp;
				<br>
				<select name="lonEW" class="input40">
					<option value="E" {lonE_sel}>{t}E{/t}</option>
					<option value="W" {lonW_sel}>{t}W{/t}</option>
				</select>&nbsp;
				<input type="text" name="lon_h" maxlength="3" value="{lon_h}" class="input30" />&nbsp;°&nbsp;
				<input type="text" name="lon_min" maxlength="6" value="{lon_min}" class="input40" />&nbsp;'&nbsp;
			</td>
		</tr>
		<tr>
			<td>{t}Maximum distance:{/t}</td>
			<td>
				<input type="text" name="distance" value="{distance}" maxlength="4" class="input50" />&nbsp;
				<select name="unit" class="input100">
					<option value="km" {sel_km}>{t}Kilometer{/t}</option>
					<option value="sm" {sel_sm}>{t}Miles{/t}</option>
					<option value="nm" {sel_nm}>{t}Seamiles{/t}</option>
				</select>
			</td>
			<td><input type="submit" value="{t}Search{/t}" class="formbuttons" /></td>
		</tr>
		<tr><td class="spacer" colspan="3"></td></tr>
	</table>
</form>

<div class="buffer" style="width: 500px;">&nbsp;</div>
<div class="content2-container bg-blue02">
	<p class="content-title-noshade-size2">
		<img src="resource2/ocstyle/images/misc/32x32-searchresults.png" style="align: left; margin-right: 10px;" width="22" height="22" alt="" /> 
		{t}Search for city{/t}
	</p>
</div>

<table class="table">
	<colgroup>
		<col width="200">
		<col width="220">
		<col>
	</colgroup>
	
	<tr><td class="spacer" colspan="3"></td></tr>
	{ortserror}
</table>

<form action="search.php" onsubmit="javascript:return(_sbplz_click());" method="{formmethod}" enctype="application/x-www-form-urlencoded" name="searchbyplz" dir="ltr" style="display:inline;">
	<input type="hidden" name="searchto" value="searchbyplz" />
	<input type="hidden" name="showresult" value="1" />
	<input type="hidden" name="expert" value="0" />
	<input type="hidden" name="output" value="HTML" />
	<input type="hidden" name="utf8" value="1" />

	<input type="hidden" name="sort" value="{hidopt_sort}" />
	<input type="hidden" name="orderRatingFirst" value="{hidopt_orderRatingFirst}" />
	<input type="hidden" name="f_userowner" value="{hidopt_userowner}" />
	<input type="hidden" name="f_userfound" value="{hidopt_userfound}" />
	<input type="hidden" name="f_inactive" value="{hidopt_inactive}" />
	<input type="hidden" name="f_ignored" value="{hidopt_ignored}" />
	<input type="hidden" name="f_otherPlatforms" value="{hidopt_otherPlatforms}" />
	<input type="hidden" name="country" value="{country}" />
	<input type="hidden" name="cachetype" value="{cachetype}" />
	<input type="hidden" name="cachesize" value="{cachesize}" />
	<input type="hidden" name="difficultymin" value="{difficultymin}" />
	<input type="hidden" name="difficultymax" value="{difficultymax}" />
	<input type="hidden" name="terrainmin" value="{terrainmin}" />
	<input type="hidden" name="terrainmax" value="{terrainmax}" />
	<input type="hidden" name="cache_attribs" value="{hidopt_attribs}" />
	<input type="hidden" name="cache_attribs_not" value="{hidopt_attribs_not}" />

	<table class="table">
		<colgroup>
			<col width="200">
			<col width="220">
			<col>
		</colgroup>
		<tr>
			<td>{t}Postal code:{/t}</td>
			<td><input type="text" name="plz" value="{plz}" maxlength="5" class="input50" /></td>
			<td><input type="submit" value="{t}Search{/t}" class="formbuttons" /></td>
		</tr>
	</table>
</form>

<form action="search.php" onsubmit="javascript:return(_sbort_click());" method="{formmethod}" enctype="application/x-www-form-urlencoded" name="searchbyort" dir="ltr" style="display:inline;">
	<input type="hidden" name="searchto" value="searchbyort" />
	<input type="hidden" name="showresult" value="1" />
	<input type="hidden" name="expert" value="0" />
	<input type="hidden" name="output" value="HTML" />
	<input type="hidden" name="utf8" value="1" />

	<input type="hidden" name="sort" value="{hidopt_sort}" />
	<input type="hidden" name="orderRatingFirst" value="{hidopt_orderRatingFirst}" />
	<input type="hidden" name="f_userowner" value="{hidopt_userowner}" />
	<input type="hidden" name="f_userfound" value="{hidopt_userfound}" />
	<input type="hidden" name="f_inactive" value="{hidopt_inactive}" />
	<input type="hidden" name="f_ignored" value="{hidopt_ignored}" />
	<input type="hidden" name="f_otherPlatforms" value="{hidopt_otherPlatforms}" />
	<input type="hidden" name="country" value="{country}" />
	<input type="hidden" name="difficultymin" value="{difficultymin}" />
	<input type="hidden" name="difficultymax" value="{difficultymax}" />
	<input type="hidden" name="terrainmin" value="{terrainmin}" />
	<input type="hidden" name="terrainmax" value="{terrainmax}" />
	<input type="hidden" name="cachetype" value="{cachetype}" />
	<input type="hidden" name="cachesize" value="{cachesize}" />
	<input type="hidden" name="cache_attribs" value="{hidopt_attribs}" />
	<input type="hidden" name="cache_attribs_not" value="{hidopt_attribs_not}" />

	<table class="table">
		<colgroup>
			<col width="200">
			<col width="220">
			<col>
		</colgroup>
		<tr>
			<td>{t}City:{/t}</td>
			<td><input type="text" name="ort" value="{ort}" class="input200" /></td>
			<td><input type="submit" value="{t}Search{/t}" class="formbuttons" /></td>
		</tr>
	</table>
</form>

<table class="table">
	<colgroup>
		<col width="200">
		<col width="220">
		<col>
	</colgroup>
	<tr><td class="spacer" colspan="3"></td></tr>
</table>

<form action="search.php" onsubmit="javascript:return(_sbft_click());" method="{formmethod}" enctype="application/x-www-form-urlencoded" name="searchbyfulltext" dir="ltr" style="display:inline;">
	<input type="hidden" name="searchto" value="searchbyfulltext" />
	<input type="hidden" name="showresult" value="1" />
	<input type="hidden" name="expert" value="0" />
	<input type="hidden" name="output" value="HTML" />
	<input type="hidden" name="utf8" value="1" />

	<input type="hidden" name="sort" value="{hidopt_sort}" />
	<input type="hidden" name="orderRatingFirst" value="{hidopt_orderRatingFirst}" />
	<input type="hidden" name="f_userowner" value="{hidopt_userowner}" />
	<input type="hidden" name="f_userfound" value="{hidopt_userfound}" />
	<input type="hidden" name="f_inactive" value="{hidopt_inactive}" />
	<input type="hidden" name="f_ignored" value="{hidopt_ignored}" />
	<input type="hidden" name="f_otherPlatforms" value="{hidopt_otherPlatforms}" />
	<input type="hidden" name="country" value="{country}" />
	<input type="hidden" name="difficultymin" value="{difficultymin}" />
	<input type="hidden" name="difficultymax" value="{difficultymax}" />
	<input type="hidden" name="terrainmin" value="{terrainmin}" />
	<input type="hidden" name="terrainmax" value="{terrainmax}" />
	<input type="hidden" name="cachetype" value="{cachetype}" />
	<input type="hidden" name="cachesize" value="{cachesize}" />
	<input type="hidden" name="cache_attribs" value="{hidopt_attribs}" />
	<input type="hidden" name="cache_attribs_not" value="{hidopt_attribs_not}" />

	<div class="buffer" style="width: 500px;">&nbsp;</div>
	<div class="content2-container bg-blue02">
  	<p class="content-title-noshade-size2">
			<img src="resource2/ocstyle/images/misc/32x32-searchresults.png" style="align: left; margin-right: 10px;" width="22" height="22" alt="" /> 
			{t}Search for text{/t}
		</p>
	</div>

	<table class="table">
		<colgroup>
			<col width="200">
			<col width="220">
			<col>
		</colgroup>
		<tr><td class="spacer" colspan="3"></td></tr>
		{fulltexterror}
		<tr>
			<td>Text:</td>
			<td><input type="text" name="fulltext" value="{fulltext}" class="input200" /></td>
			<td><input type="submit" value="{t}Search{/t}" class="formbuttons" /></td>
		</tr>
		<tr>
			<td></td>
			<td colspan="2">
				<table width="250px">
					<tr>
						<td><input type="checkbox" name="ft_name" id="ft_name" class="checkbox" value="1" {ft_name_checked} /> <label for="ft_name">{t}Name{/t}</label></td>
						<td><input type="checkbox" name="ft_desc" id="ft_desc" class="checkbox" value="1" {ft_desc_checked} /> <label for="ft_desc">{t}Description{/t}</label></td>
					</tr>
					<tr>
						<td><input type="checkbox" name="ft_logs" id="ft_logs" class="checkbox" value="1" {ft_logs_checked} /> <label for="ft_logs">{t}Logs{/t}</label></td>
						<td><input type="checkbox" name="ft_pictures" id="ft_pictures" class="checkbox" value="1" {ft_pictures_checked} /> <label for="ft_pictures">{t}Pictures{/t}</label></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td class="spacer" colspan="3"></td></tr>
	</table>
</form>

<form action="search.php" onsubmit="javascript:return(_sbo_click());" method="{formmethod}" enctype="application/x-www-form-urlencoded" name="searchbyowner" dir="ltr" style="display:inline;">
	<input type="hidden" name="searchto" value="searchbyowner" />
	<input type="hidden" name="showresult" value="1" />
	<input type="hidden" name="expert" value="0" />
	<input type="hidden" name="output" value="HTML" />
	<input type="hidden" name="utf8" value="1" />

	<input type="hidden" name="sort" value="{hidopt_sort}" />
	<input type="hidden" name="orderRatingFirst" value="{hidopt_orderRatingFirst}" />
	<input type="hidden" name="f_userowner" value="{hidopt_userowner}" />
	<input type="hidden" name="f_userfound" value="{hidopt_userfound}" />
	<input type="hidden" name="f_inactive" value="{hidopt_inactive}" />
	<input type="hidden" name="f_ignored" value="{hidopt_ignored}" />
	<input type="hidden" name="f_otherPlatforms" value="{hidopt_otherPlatforms}" />
	<input type="hidden" name="country" value="{country}" />
	<input type="hidden" name="difficultymin" value="{difficultymin}" />
	<input type="hidden" name="difficultymax" value="{difficultymax}" />
	<input type="hidden" name="terrainmin" value="{terrainmin}" />
	<input type="hidden" name="terrainmax" value="{terrainmax}" />
	<input type="hidden" name="cachetype" value="{cachetype}" />
	<input type="hidden" name="cachesize" value="{cachesize}" />
	<input type="hidden" name="cache_attribs" value="{hidopt_attribs}" />
	<input type="hidden" name="cache_attribs_not" value="{hidopt_attribs_not}" />

	<div class="buffer" style="width: 500px;">&nbsp;</div>
	<div class="content2-container bg-blue02">
		<p class="content-title-noshade-size2">
			<img src="resource2/ocstyle/images/misc/32x32-searchresults.png" style="align: left; margin-right: 10px;" width="22" height="22" alt="" /> 
			{t}Search for Owner{/t}
		</p>
	</div>

	<table class="table">
		<colgroup>
			<col width="200">
			<col width="220">
			<col>
		</colgroup>
		<tr><td class="spacer" colspan="3"></td></tr>
		<tr>
			<td>{t}Owner:{/t}</td>
			<td><input type="text" name="owner" value="{owner}" maxlength="40" class="input200" /></td>
			<td><input type="submit" value="{t}Search{/t}" class="formbuttons" /></td>
		</tr>
		<tr><td class="spacer" colspan="3"></td></tr>
	</table>
</form>

<form action="search.php" onsubmit="javascript:return(_sbf_click());" method="{formmethod}" enctype="application/x-www-form-urlencoded" name="searchbyfinder" dir="ltr" style="display:inline;">
	<input type="hidden" name="searchto" value="searchbyfinder" />
	<input type="hidden" name="showresult" value="1" />
	<input type="hidden" name="expert" value="0" />
	<input type="hidden" name="output" value="HTML" />
	<input type="hidden" name="utf8" value="1" />

	<input type="hidden" name="sort" value="{hidopt_sort}" />
	<input type="hidden" name="orderRatingFirst" value="{hidopt_orderRatingFirst}" />
	<input type="hidden" name="f_userowner" value="{hidopt_userowner}" />
	<input type="hidden" name="f_userfound" value="{hidopt_userfound}" />
	<input type="hidden" name="f_inactive" value="{hidopt_inactive}" />
	<input type="hidden" name="f_ignored" value="{hidopt_ignored}" />
	<input type="hidden" name="f_otherPlatforms" value="{hidopt_otherPlatforms}" />
	<input type="hidden" name="country" value="{country}" />
	<input type="hidden" name="difficultymin" value="{difficultymin}" />
	<input type="hidden" name="difficultymax" value="{difficultymax}" />
	<input type="hidden" name="terrainmin" value="{terrainmin}" />
	<input type="hidden" name="terrainmax" value="{terrainmax}" />
	<input type="hidden" name="cachetype" value="{cachetype}" />
	<input type="hidden" name="cachesize" value="{cachesize}" />
	<input type="hidden" name="cache_attribs" value="{hidopt_attribs}" />
	<input type="hidden" name="cache_attribs_not" value="{hidopt_attribs_not}" />

	<div class="buffer" style="width: 500px;">&nbsp;</div>
	<div class="content2-container bg-blue02">
		<p class="content-title-noshade-size2">
			<img src="resource2/ocstyle/images/misc/32x32-searchresults.png" style="align: left; margin-right: 10px;" width="22" height="22" alt="" /> 
			{t}Search for Logs{/t}
		</p>
	</div>

	<table class="table">
		<colgroup>
			<col width="200">
			<col width="220">
			<col>
		</colgroup>
		<tr><td class="spacer" colspan="3"></td></tr>
		<tr>
			<td>{t}Logtype:{/t}</td>
			<td colspan="2">
				<select name="logtype">
					{logtype_options}
				</select> 
			</td>
		</tr>
		<tr>
			<td>{t}Username:{/t}</td>
			<td><input type="text" name="finder" value="{finder}" maxlength="40" class="input200" /></td>
			<td><input type="submit" value="{t}Search{/t}" class="formbuttons" /></td>
		</tr>
		<tr><td class="spacer" colspan="3"></td></tr>
	</table>
</form>
<div class="buffer" style="width: 500px;">&nbsp;</div>
