{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
*
*  search form
***************************************************************************}

<script type="text/javascript" src="resource2/ocstyle/js/wz_tooltip.js"></script>
<script type="text/javascript">
<!--
var mnAttributesShowCat2 = 1;
var maAttributes = new Array({$attributes_jsarray});
var cachetypes = {$cachetypes|@count};
var cachesizes = {$cachesizes|@count};
var dfHideSearchOpt = [];

{literal}

function showSearchOpt(a)
{
    for (i=0; typeof document.getElementsByClassName(a)[i] != 'undefined'; i++) {
        var e = document.getElementsByClassName(a)[i];

        if(!e)return true;

        e.style.display = "table-row";
    }

    return true;
}

function hideSearchOpt(a)
{
    for (i=0; typeof document.getElementsByClassName(a)[i] != 'undefined'; i++) {
        var e = document.getElementsByClassName(a)[i];

        if(!e)return true;

        e.style.display = "none";
    }

    return true;
}

function showAllSearchOpt()
{
    for (var i = 0; i <= dfHideSearchOpt.length; i++) {
        showSearchOpt(dfHideSearchOpt[i]);
    }
    showHideAllButton();
}

function hideAllSearchOpt()
{
    for (var i = 0; i <= dfHideSearchOpt.length; i++) {
        hideSearchOpt(dfHideSearchOpt[i]);
    }
    showShowAllButton();
}

function showShowAllButton()
{
    document.getElementById("showAllButton").style.display = "table-row";
    document.getElementById("hideAllButton").style.display = "none";

    return true;
}

function showHideAllButton()
{
    document.getElementById("showAllButton").style.display = "none";
    document.getElementById("hideAllButton").style.display = "table-row";

    return true;
}

window.onload = function ()
{
    {/literal}
    {if !$load_query}
        showShowAllButton();
    {/if}
    {if $searchtype_byortplz || $searchtype_bywaypoint ||$searchtype_bycoords || $load_query}
        showSearchOpt('search_bydistance');
    {else}
        hideSearchOpt('search_bydistance');
        dfHideSearchOpt.push("search_bydistance");
    {/if}
    {if $searchtype_byname || $load_query}
        showSearchOpt('search_byname');
    {else}
        hideSearchOpt('search_byname');
        dfHideSearchOpt.push("search_byname");
    {/if}
    {if $searchtype_byfulltext || $load_query}
        showSearchOpt('search_byfulltext');
    {else}
        hideSearchOpt('search_byfulltext');
        dfHideSearchOpt.push("search_byfulltext");
    {/if}
    {if $searchtype_byowner || $load_query}
        showSearchOpt('search_byowner');
    {else}
        hideSearchOpt('search_byowner');
        dfHideSearchOpt.push("search_byowner");
    {/if}
    {if $searchtype_byfinder || $load_query}
        showSearchOpt('search_byfinder');
    {else}
        hideSearchOpt('search_byfinder');
        dfHideSearchOpt.push("search_byfinder");
    {/if}
    {if $searchtype_byall || $load_query}
        showSearchOpt('search_byall');
    {else}
        hideSearchOpt('search_byall');
        dfHideSearchOpt.push("search_byall");
    {/if}
    {literal}
};

function bydistance_set_radiobutton(index)
{
    document.searchbydistance.searchto[index].checked = "checked";
}

function _sbn_click(submitType)
{
    if (check_cachetypesize(submitType) == false) {
        return false;
    }

    if (document.searchbyname.cachename.value == "")
    {
        alert("{/literal}{t}Enter a name, please!{/t}{literal}");
        resetbutton(submitType);
        return false;
    }
    return true;
}

function _sbft_click(submitType)
{
    if (check_cachetypesize(submitType) == false) {
        return false;
    }

    if (document.searchbyfulltext.fulltext.value == "")
    {
        alert("{/literal}{t}Fill out the text field, please!{/t}{literal}");
        resetbutton(submitType);
        return false;
    }

    if ((document.searchbyfulltext.ft_name.checked == false) &&
       (document.searchbyfulltext.ft_desc.checked == false) &&
       (document.searchbyfulltext.ft_logs.checked == false) &&
       (document.searchbyfulltext.ft_pictures.checked == false))
    {
        alert("{/literal}{t}You have to check at least one field!{/t}{literal}");
        resetbutton(submitType);
        return false;
    }

    return true;
}

function _sbd_click(submitType)
{
    if (check_cachetypesize(submitType) == false) {
        return false;
    }

    if (isNaN(document.searchbydistance.distance.value))
    {
        alert("{/literal}{t}The maximum distance must be a number!{/t}{literal}");
        resetbutton('submit_dist');
        return false;
    }
    else if (document.searchbydistance.distance.value <= 0 || document.searchbydistance.distance.value > 9999)
    {
        alert("{/literal}{t}The distance must range between 0 and 9999.{/t}{literal}");
        resetbutton('submit_dist');
        return false;
    }

    if (document.getElementById('sbortplz').checked) {
        if (document.searchbydistance.ortplz.value == "") {
            alert("{/literal}{t}Enter a postal code or city, please!{/t}{literal}");
            resetbutton('submit_dist');
            return false;
        }
    }

    if (document.getElementById('sbwaypoint').checked) {
        if (document.searchbydistance.waypoint.value == "") {
            alert("{/literal}{t}Enter a valid waypoint, please!{/t}{literal}");
            resetbutton('submit_dist');
            return false;
        }
    }

    if (isNaN(document.searchbydistance.lon_h.value) || isNaN(document.searchbydistance.lon_min.value) || document.searchbydistance.lon_h.value == "" || document.searchbydistance.lon_min.value == "") {
        alert("{/literal}{t}Longitude must be a number!\nFormat: hh° mm.mmm{/t}{literal}");
        resetbutton('submit_dist');
        return false;
    }
    if (isNaN(document.searchbydistance.lat_h.value) || isNaN(document.searchbydistance.lat_min.value) || document.searchbydistance.lat_h.value == "" || document.searchbydistance.lat_min.value == "") {
        alert("{/literal}{t}Latitude must be a number!\nFormat: hh° mm.mmm{/t}{literal}");
        resetbutton('submit_dist');
        return false;
    }

    return true;
}

function _sbo_click(submitType)
{
    if (check_cachetypesize(submitType) == false) {
        return false;
    }

    if (document.searchbyowner.owner.value == "")
    {
        alert("{/literal}{t}Enter the owner, please!{/t}{literal}");
        resetbutton(submitType);
        return false;
    }
    return true;
}

function _sbf_click(submitType)
{
    if (check_cachetypesize(submitType) == false) {
        return false;
    }

    if (document.searchbyfinder.finder.value == "")
    {
        alert("{/literal}{t}Enter the username, please!{/t}{literal}");
        resetbutton(submitType);
        return false;
    }
    return true;
}

function _sba_click(submitType)
{
    return check_cachetypesize(submitType);
}

function check_cachetypesize(submitType)
{
    var check_cachetype = false;
    var check_cachesize = false;

    for (i = 1; i <= cachetypes; i++)
    {
        if (document.getElementById('cachetype' + i).checked == true)
        {
            check_cachetype = true;
        }
    }

    for (i = 1; i <= cachesizes; i++)
    {
        if (document.getElementById('cachesize' + i).checked == true)
        {
            check_cachesize = true;
        }
    }

    if (check_cachetype == false || check_cachesize == false) {
        if (check_cachetype == false) {
            alert("{/literal}{t}Select at least one cachetype!{/t}{literal}");
        }
        if (check_cachesize == false) {
            alert("{/literal}{t}Select at least one cachesize!{/t}{literal}");
        }
        resetbutton(submitType);
        return false;
    }
    return true;
}

function sync_options(element)
{
    var formnames = new Array(
        "searchbyname",
        "searchbydistance",
        "searchbyowner",
        "searchbyfinder",
        "searchbyfulltext"
        {/literal}{if $logged_in},"searchall"{/if}{literal}
    );

    var sortby = "";
    if (document.optionsform.sort[0].checked == true)
        sortby = "byname";
    else if (document.optionsform.sort[1].checked == true)
        sortby = "bydistance";
    else if (document.optionsform.sort[2].checked == true)
        sortby = "bycreated";
    else if (document.optionsform.sort[3].checked == true)
        sortby = "bylastlog";
    else if (document.optionsform.sort[4].checked == true)
        sortby = "bymylastlog";

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
    for (i = 1; i <= cachetypes; i++)
    {
        if (document.getElementById('cachetype' + i).checked == true)
        {
            if (tmpcachetype != "") tmpcachetype = tmpcachetype + ";";
            tmpcachetype = tmpcachetype + i;
        }
    }
    if (tmpcachetype == "") tmpcachetype = "none";

    var tmpcachesize = "";
    for (i = 1; i <= cachesizes; i++)
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
        document.forms[formnames[i]].f_disabled.value = document.optionsform.f_disabled.checked ? 1 : 0;
        document.forms[formnames[i]].f_ignored.value = document.optionsform.f_ignored.checked ? 1 : 0;
        document.forms[formnames[i]].f_otherPlatforms.value = document.optionsform.f_otherPlatforms.checked ? 1 : 0;
        document.forms[formnames[i]].f_geokrets.value = document.optionsform.f_geokrets.checked ? 1 : 0;
        document.forms[formnames[i]].country.value = document.optionsform.country.value;
        document.forms[formnames[i]].language.value = document.optionsform.language.value;
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

function toggleCachetype(id)
{
    var ctcb = document.getElementById('cachetype' + id);
    ctcb.checked = !ctcb.checked;
    sync_options(null);

    var icon = document.getElementById('cacheicon' + id);
    var iconpath = icon.src;
    greyed = iconpath.indexOf('-grey');
    if (greyed > 0)
        iconpath = iconpath.substr(0,greyed) + iconpath.substring(greyed+5);
    else
    {
        var extpos = iconpath.indexOf('.gif');
        if (extpos < 0) extpos = iconpath.indexOf('.png');
        iconpath = iconpath.substr(0,extpos) + "-grey" + iconpath.substring(extpos);
    }
    icon.src = iconpath;
}

function alltypes(enable)
{
    {/literal}
    {foreach from=$cachetypes item=ct}
        if (document.getElementById('cachetype{$ct.id}').checked != enable)
            toggleCachetype({$ct.id});
    {/foreach}
    {literal}
}

function allsizes(enable)
{
    {/literal}
    {foreach from=$cachesizes key=size_id item=dummy}
        document.getElementById('cachesize{$size_id}').checked = enable;
    {/foreach}
    {literal}
    sync_options();
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
    document.getElementById('toggleAttributesCaption').firstChild.nodeValue = "{/literal}{t}Show all{/t}{literal}";
}

function showAttributesCat2()
{
    mnAttributesShowCat2 = 1;
    document.getElementById('attributesCat1').style.display = "none";
    document.getElementById('attributesCat2').style.display = "block";
    document.getElementById('toggleAttributesCaption').firstChild.nodeValue = "{/literal}{t}Less{/t}{literal}";
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
{/literal}

<div id="hos" class="content2-pagetitle"><img src="resource2/ocstyle/images/misc/32x32-search.png" style="margin-right: 10px;" width="32" height="32" alt="" />{t}Search for caches{/t}</div> <!-- head of search -->

<table cellspacing="0" cellpadding="0" style="width:98.1%">
    <tr>
        <td class="nav4">
            <ul>
                <li class="group noicon"><a href="query.php">{t}Stored queries{/t}</a></li>
                {if $show_lastsearchbutton}<li class="group noicon"><a href="search.php?lastsearch=1#hos">{t}Last search{/t}</a>{/if}
                <li class="group noicon"><a href="search.php#hos" onclick="location.reload()">{t}Reset search{/t}</a></li>
            </ul>
        </td>
    </tr>
</table>

<form name="optionsform" style="display:inline;">

<div class="searchdiv2">
    <table class="table">
        <tr>
            <td class="formlabel">{t}Hide following caches:{/t}</td>
            <td colspan="4">
                <input type="checkbox" name="f_userowner" value="1" id="l_userowner" class="checkbox" onclick="sync_options(this)" {if $f_userowner_checked}checked="checked"{/if} {if !$logged_in}disabled="disabled"{/if} /> <label for="l_userowner" {if !$logged_in}class="disabled"{/if}>{t}My owned{/t}</label> &nbsp;
                <input type="checkbox" name="f_userfound" value="1" id="l_userfound" class="checkbox" onclick="sync_options(this)" {if $f_userfound_checked}checked="checked"{/if} {if !$logged_in}disabled="disabled"{/if} /> <label for="l_userfound" {if !$logged_in}class="disabled"{/if}>{t}My finds{/t}</label> &nbsp;
                <input type="checkbox" name="f_ignored" value="1" id="l_ignored" class="checkbox" onclick="sync_options(this)" {if $f_ignored_checked}checked="checked"{/if} {if !$logged_in}disabled="disabled"{/if} /> <label for="l_ignored" {if !$logged_in}class="disabled"{/if}>{t}My ignored{/t}</label> &nbsp;
                <input type="checkbox" name="f_disabled" value="1" id="l_disabled" class="checkbox" onclick="sync_options(this)" {if $f_disabled_checked}checked="checked"{/if} /> <label for="l_disabled">{t}disabled[pl]{/t}</label> &nbsp;
                <nobr><input type="checkbox" name="f_inactive" value="1" id="l_inactive" class="checkbox" onclick="sync_options(this)" {if $f_inactive_checked}checked="checked"{/if} /> <label for="l_inactive">{t}archived[pl]{/t}</label></nobr> &nbsp;
                <br />
                <nobr><input type="checkbox" name="f_otherPlatforms" value="1" id="l_otherPlatforms" class="checkbox" onclick="sync_options(this)" {if $f_otherPlatforms_checked}checked="checked"{/if} /> <label for="l_otherPlatforms">{t}also listed at GC.com{/t}</label></nobr> &nbsp;
                <nobr><input type="checkbox" name="f_geokrets" value="1" id="l_geokrets" class="checkbox" onclick="sync_options(this)" {if $f_geokrets_checked}checked="checked"{/if} /> <label for="l_geokrets">{t}without Geokrets{/t}</label></nobr>
            </td>
        </tr>

        <tr><td class="separator"></td></tr>

        <tr>
            <td class="formlabel">{t}Cachetype:{/t}</td>
            <td>
                {foreach from=$cachetypes item=ct}
                    <input style="display:none" type="checkbox" id="cachetype{$ct.id}" name="cachetype{$ct.id}" value="{$ct.id}" class="checkbox" {if $ct.checked}checked="checked"{/if} />
                    {capture name=onclick assign=onclick}toggleCachetype({$ct.id}){/capture}
                    {include file="res_cacheicon.tpl" cachetype=$ct.id typeid=true greyed=$ct.unchecked}&nbsp;
                {/foreach}
                &nbsp;&nbsp;<a href="javascript:alltypes(true)">{t}all{/t}</a>
                &nbsp;&nbsp;<a href="javascript:alltypes(false)">{t}none{/t}</a>
            </td>
        </tr>

        <tr><td class="separator"></td></tr>

        <tr>
            <td class="formlabel">{t}Cachesize:{/t}</td>
            <td colspan="4">
                <input type="checkbox" id="cachesize8" name="cachesize8" value="8" onclick="sync_options(this)" class="checkbox" {if $cachesizes.8.checked}checked="checked"{/if} /> <label for="cachesize8" style="font-size:0.72em">{t}nano{/t}</label> &nbsp;
                <input type="checkbox" id="cachesize2" name="cachesize2" value="2" onclick="sync_options(this)" class="checkbox" {if $cachesizes.2.checked}checked="checked"{/if} /> <label for="cachesize2" style="font-size:0.86em">{t}micro{/t}</label> &nbsp;
                <input type="checkbox" id="cachesize3" name="cachesize3" value="3" onclick="sync_options(this)" class="checkbox" {if $cachesizes.3.checked}checked="checked"{/if} /> <label for="cachesize3" style="font-size:1.0em">{t}small{/t}</label> &nbsp;
                <input type="checkbox" id="cachesize4" name="cachesize4" value="4" onclick="sync_options(this)" class="checkbox" {if $cachesizes.4.checked}checked="checked"{/if} /> <label for="cachesize4" style="font-size:1.14em">{t}normal{/t}</label> &nbsp;
                <input type="checkbox" id="cachesize5" name="cachesize5" value="5" onclick="sync_options(this)" class="checkbox" {if $cachesizes.5.checked}checked="checked"{/if} /> <label for="cachesize5" style="font-size:1.28em">{t}large{/t}</label> &nbsp;
                <nobr><input type="checkbox" id="cachesize6" name="cachesize6" value="6" onclick="sync_options(this)" class="checkbox" {if $cachesizes.6.checked}checked="checked"{/if} /> <label for="cachesize6" style="font-size:1.42em">{t}very large{/t}</label></nobr>
                <br />
                <input type="checkbox" id="cachesize1" name="cachesize1" value="1" onclick="sync_options(this)" class="checkbox" {if $cachesizes.1.checked}checked="checked"{/if} /> <label for="cachesize1">{t}other size{/t}</label> &nbsp; <input type="checkbox" id="cachesize7" name="cachesize7" value="7" onclick="sync_options(this)" class="checkbox" {if $cachesizes.7.checked}checked="checked"{/if} /> <label for="cachesize7">{t}no container{/t}</label>
                &nbsp;
                &nbsp;&nbsp;<a href="javascript:allsizes(true)">{t}all{/t}</a>
                &nbsp;&nbsp;<a href="javascript:allsizes(false)">{t}none{/t}</a>
            </td>
        </tr>

        <tr><td class="separator"></td></tr>

        <tr>
            <td class="formlabel">{t}Difficulty:{/t}</td>
            <td colspan="4">
                <select name="difficultymin" class="input80" onchange="sync_options(this)">
                    {foreach from=$difficulty_options item=difficulty_option}
                        <option value="{$difficulty_option}" {if $difficultymin==$difficulty_option}selected="selected"{/if}>{if $difficulty_option==0}-{else}{$difficulty_option/2|sprintf:"%1.1f"}{/if}</option>
                    {/foreach}
                </select>
                &nbsp;{t}to{/t}&nbsp;
                <select name="difficultymax" class="input80" onchange="sync_options(this)">
                    {foreach from=$difficulty_options item=difficulty_option}
                        <option value="{$difficulty_option}" {if $difficultymax==$difficulty_option}selected="selected"{/if}>{if $difficulty_option==0}-{else}{$difficulty_option/2|sprintf:"%1.1f"}{/if}</option>
                    {/foreach}
                </select>
                &nbsp; &nbsp; &nbsp; &nbsp;
                <span class="formlabel">{t}Terrain:{/t}</span> &nbsp;
                <select name="terrainmin" class="input80" onchange="sync_options(this)">
                    {foreach from=$terrain_options item=terrain_option}
                        <option value="{$terrain_option}" {if $terrainmin==$terrain_option}selected="selected"{/if}>{if $terrain_option==0}-{else}{$terrain_option/2|sprintf:"%1.1f"}{/if}</option>
                    {/foreach}
                </select>
                &nbsp;{t}to{/t}&nbsp;
                <select name="terrainmax" class="input80" onchange="sync_options(this)">
                    {foreach from=$terrain_options item=terrain_option}
                        <option value="{$terrain_option}" {if $terrainmax==$terrain_option}selected="selected"{/if}>{if $terrain_option==0}-{else}{$terrain_option/2|sprintf:"%1.1f"}{/if}</option>
                    {/foreach}
                </select>
            </td>
        </tr>

        <tr><td class="separator"></td></tr>

        <tr>
            <td class="formlabel">{t}Country:{/t}&nbsp;&nbsp;</td>
            <td>
                <select name="country" onchange="sync_options(this)">
                    <option value="" {if $all_countries}selected="selected"{/if}>{t}All countries{/t}</option>
                    {foreach from=$countryoptions item=countryoption}
                        <option value="{$countryoption.short|escape}" {if $countryoption.selected}selected="selected"{/if}>{$countryoption.name|escape}</option>
                    {/foreach}
                </select>
                &nbsp; &nbsp; &nbsp; &nbsp;
                <span class="formlabel">{t}Language:{/t}&nbsp;&nbsp;</span>
                <select name="language" onchange="sync_options(this)">
                    <option value="" {if $all_languages}selected="selected"{/if}>{t}All languages{/t}</option>
                    {foreach from=$languageoptions item=languageoption}
                        <option value="{$languageoption.short|escape}" {if $languageoption.selected}selected="selected"{/if}>{$languageoption.name|escape}</option>
                    {/foreach}
                </select>
            </td>
        </tr>

        <tr><td class="separator"></td></tr>

        <tr>
            <td valign="top">
                <span class="formlabel">{t}Cache attributes:{/t}</span>&nbsp;<br />
                (<a href="javascript:switchAttributeCat2()"><span id="toggleAttributesCaption" style="white-space:nowrap">{t}Show all{/t}</span></a>)
            </td>
            <td colspan="4" width="90%">
                <div id="attributesCat1" style="display:none;">{$cache_attribCat1_list}</div>
                <div id="attributesCat2" style="display:block;">{$cache_attribCat2_list}</div>
            </td>
        </tr>
    </table>
</div>

<div class="searchdiv2">
    <table class="table">
        <tr>
            <td class="formlabel" rowspan="2">{t}Sorting of result:{/t}</td>
            <td colspan="4" style="padding-bottom:0">
                <input type="radio" name="sort" value="byname" index="0" id="l_sortbyname" class="radio" onclick="sync_options(this)" {if $byname_checked}checked="checked"{/if} /> <label for="l_sortbyname">{t}Cachename{/t}</label> &nbsp;
                <input type="radio" name="sort" value="bydistance" index="1" id="l_sortbydistance" class="radio" onclick="sync_options(this)" {if $bydistance_checked}checked="checked"{/if} /> <label for="l_sortbydistance">{t}Distance{/t}</label> &nbsp;
                <input type="radio" name="sort" value="bycreated" index="2" id="l_sortbycreated" class="radio" onclick="sync_options(this)" {if $bycreated_checked}checked="checked"{/if} /> <label for="l_sortbycreated">{t}Listed since{/t}</label> &nbsp;
                <nobr><input type="radio" name="sort" value="bylastlog" index="3" id="l_sortbylastlog" class="radio" onclick="sync_options(this)" {if $bylastlog_checked}checked="checked"{/if} /> <label for="l_sortbylastlog" >{t}Last log{/t}</label> &nbsp;
                <input type="radio" name="sort" value="bymylastlog" index="4" id="l_sortbymylastlog" class="radio" onclick="sync_options(this)" {if $bymylastlog_checked}checked="checked"{/if} {if !$logged_in}disabled="disabled"{/if} /> <label for="l_sortbymylastlog" {if !$logged_in}class="disabled"{/if}>{t}My last log{/t}</label></nobr>
            </td>
        </tr>
        <tr>
            <td style="padding-top:0">
                <input id="orderRatingFirst" type="checkbox" name="orderRatingFirst" class="checkbox" value="1" onclick="sync_options(this)" {if $orderRatingFirst_checked}checked="checked"{/if} />
                <label for="orderRatingFirst">{t}Show recommendation from other users first{/t}</label>
            </td>
        </tr>
    </table>
</div>

</form>

<div id="scriptwarning" style="margin:0 5px 0 5px;">
    <p><span class="errormsg">{t}JavaScript is not activated, you cannot use the above options (hide caches .. sort results) - basic search works nevertheless.{/t}</span></p>
</div>

{literal}
<script type="text/javascript">
<!--
    document.getElementById("scriptwarning").style.display = "none";

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
{/literal}

<div class="searchdiv2">

    <table class="table">

        <tr id="showAllButton" style="display: none;">
            <td colspan="2">
                <input type="button" style="width: auto;" class="formbutton" value="&nbsp;&nbsp;{t}show all search options{/t}&nbsp;&nbsp;" onclick="return showAllSearchOpt();" />&nbsp;
            </td>
        </tr>
        <tr id="hideAllButton" style="display: none;">
            <td colspan="2">
                <input type="button" style="width: auto;" class="formbutton"  value="&nbsp;&nbsp;{t}hide all additional search options{/t}&nbsp;&nbsp;" onclick="return hideAllSearchOpt();" />&nbsp;
            </td>
        </tr>

        <tr class="search_bydistance"><td class="separator"></td></tr>

    {$ortserror}

    <form action="search.php" onsubmit="return(_sbd_click('submit_dist'));" method="{$formmethod}" enctype="application/x-www-form-urlencoded" name="searchbydistance" dir="ltr" style="display:inline;">
        <input type="hidden" name="showresult" value="1" />
        <input type="hidden" name="expert" value="0" />
        <input type="hidden" name="output" value="HTML" />
        <input type="hidden" name="utf8" value="1" />

        <input type="hidden" name="sort" value="{$hidopt_sort}" />
        <input type="hidden" name="orderRatingFirst" value="{$hidopt_orderRatingFirst}" />
        <input type="hidden" name="f_userowner" value="{$hidopt_userowner}" />
        <input type="hidden" name="f_userfound" value="{$hidopt_userfound}" />
        <input type="hidden" name="f_inactive" value="{$hidopt_inactive}" />
        <input type="hidden" name="f_disabled" value="{$hidopt_disabled}" />
        <input type="hidden" name="f_ignored" value="{$hidopt_ignored}" />
        <input type="hidden" name="f_otherPlatforms" value="{$hidopt_otherPlatforms}" />
        <input type="hidden" name="f_geokrets" value="{$hidopt_geokrets}" />
        <input type="hidden" name="country" value="{$country}" />
        <input type="hidden" name="language" value="{$language}" />
        <input type="hidden" name="cachetype" value="{$cachetype}" />
        <input type="hidden" name="cachesize" value="{$cachesize}" />
        <input type="hidden" name="difficultymin" value="{$difficultymin}" />
        <input type="hidden" name="difficultymax" value="{$difficultymax}" />
        <input type="hidden" name="terrainmin" value="{$terrainmin}" />
        <input type="hidden" name="terrainmax" value="{$terrainmax}" />
        <input type="hidden" name="cache_attribs" value="{$hidopt_attribs}" />
        <input type="hidden" name="cache_attribs_not" value="{$hidopt_attribs_not}" />

        <tr class="search_bydistance">
            <td class="formlabel">{t}Perimeter:{/t}</td>
            <td>
                <input type="text" tabindex="1" name="distance" value="{$distance}" maxlength="4" class="input50" />&nbsp;
                <select tabindex="2" name="unit" class="input100">
                    <option value="km" {if $sel_km}selected="selected"{/if}>{t}Kilometer{/t}</option>
                    <option value="sm" {if $sel_sm}selected="selected"{/if}>{t}Miles{/t}</option>
                    <option value="nm" {if $sel_nm}selected="selected"{/if}>{t}Seamiles{/t}</option>
                </select>
            </td>
        </tr>
        <tr class="search_bydistance">
            {* exchanged tab order for radio button and input; see http://redmine.opencaching.de/issues/239 *}
            <td class=""><input type="radio" tabindex="4" id="sbortplz" name="searchto" value="searchbyortplz" {if $dfromortplz_checked}checked="checked"{/if}><label for="sbortplz">... {t}from city or postal code:{/t}</label></td>
            <td><input type="text" tabindex="3" name="ortplz" value="{$ortplz}" class="input200" onfocus="bydistance_set_radiobutton(0)"/> &nbsp;</td>
            <td></td>  {* creates empty fourth column which is used by text search options *}
        </tr>
        <tr class="search_bydistance">
            {* exchanged tab order for radio button and input; see http://redmine.opencaching.de/issues/239 *}
            <td class=""><input type="radio" tabindex="5" id="sbwaypoint" name="searchto" value="searchbywaypoint" {if $dfromwaypoint_checked}checked="checked"{/if}><label for="sbwaypoint">... {t}from geocache:{/t}</label></td>
            <td><input type="text" tabindex="4" name="waypoint" value="{$waypoint}" maxlength="7" class="input70" onfocus="bydistance_set_radiobutton(1)"/>
            &nbsp;({t}waypoint{/t})</td>
        </tr>
        <tr class="search_bydistance">
            <td valign="top"><input type="radio" tabindex="6" id="sbcoords" name="searchto" value="searchbycoords" {if $dfromcoords_checked}checked="checked"{/if}><label for="sbcoords">... {t}from coordinates:{/t}</label></td>
            <td valign="top">
                <select tabindex="7" name="latNS" onfocus="bydistance_set_radiobutton(2)">
                    <option value="N" {if $latN_sel}selected="selected"{/if}>{t}N{/t}</option>
                    <option value="S" {if $latS_sel}selected="selected"{/if}>{t}S{/t}</option>
                </select>&nbsp;
                <input type="text" tabindex="8" name="lat_h" maxlength="2" value="{$lat_h}" class="input30" onfocus="bydistance_set_radiobutton(2)"/>&nbsp;°&nbsp;
                <input type="text" tabindex="9" name="lat_min" maxlength="6" value="{$lat_min}" class="input50" onfocus="bydistance_set_radiobutton(2)"/>&nbsp;'&nbsp;
                <br />
                <select tabindex="10" name="lonEW" onfocus="bydistance_set_radiobutton(2)">
                    <option value="E" {if $lonE_sel}selected="selected"{/if}>{t}E{/t}</option>
                    <option value="W" {if $lonW_sel}selected="selected"{/if}>{t}W{/t}</option>
                </select>&nbsp;
                <input type="text" tabindex="11" name="lon_h" maxlength="3" value="{$lon_h}" class="input30" onfocus="bydistance_set_radiobutton(2)"/>&nbsp;°&nbsp;
                <input type="text" tabindex="12" name="lon_min" maxlength="6" value="{$lon_min}" class="input50" onfocus="bydistance_set_radiobutton(2)"/>&nbsp;'&nbsp;
            </td>
            <td><input type="submit" tabindex="13" name="submit_dist" value="{t}Search{/t}" class="formbutton" onclick="submitbutton('submit_dist')" /></td>
        </tr>
    </form>

    <tr class="search_byname"><td class="separator"></td></tr>

    <form action="search.php" onsubmit="return(_sbn_click('submit_cachename'));" method="{$formmethod}" enctype="application/x-www-form-urlencoded" name="searchbyname" dir="ltr" style="display:inline;">
        <input type="hidden" name="searchto" value="searchbyname" />
        <input type="hidden" name="showresult" value="1" />
        <input type="hidden" name="expert" value="0" />
        <input type="hidden" name="output" value="HTML" />
        <input type="hidden" name="utf8" value="1" />

        <input type="hidden" name="sort" value="{$hidopt_sort}" />
        <input type="hidden" name="orderRatingFirst" value="{$hidopt_orderRatingFirst}" />
        <input type="hidden" name="f_userowner" value="{$hidopt_userowner}" />
        <input type="hidden" name="f_userfound" value="{$hidopt_userfound}" />
        <input type="hidden" name="f_inactive" value="{$hidopt_inactive}" />
        <input type="hidden" name="f_disabled" value="{$hidopt_disabled}" />
        <input type="hidden" name="f_ignored" value="{$hidopt_ignored}" />
        <input type="hidden" name="f_otherPlatforms" value="{$hidopt_otherPlatforms}" />
        <input type="hidden" name="f_geokrets" value="{$hidopt_geokrets}" />
        <input type="hidden" name="country" value="{$country}" />
        <input type="hidden" name="language" value="{$language}" />
        <input type="hidden" name="cachetype" value="{$cachetype}" />
        <input type="hidden" name="cachesize" value="{$cachesize}" />
        <input type="hidden" name="difficultymin" value="{$difficultymin}" />
        <input type="hidden" name="difficultymax" value="{$difficultymax}" />
        <input type="hidden" name="terrainmin" value="{$terrainmin}" />
        <input type="hidden" name="terrainmax" value="{$terrainmax}" />
        <input type="hidden" name="cache_attribs" value="{$hidopt_attribs}" />
        <input type="hidden" name="cache_attribs_not" value="{$hidopt_attribs_not}" />

        <tr class="search_byname">
            <td class="formlabel">{t}Cachename{/t}{t}#colonspace#{/t}:</td>
            <td><input type="text" name="cachename" value="{$cachename}" class="input200" /></td>
            <td><input type="submit" name="submit_cachename" value="{t}Search{/t}" class="formbutton" onclick="submitbutton('submit_cachename')" /></td>
        </tr>
    </form>

    <tr class="search_byfulltext"><td class="separator"></td></tr>

    <form action="search.php" onsubmit="return(_sbft_click('submit_ft'));" method="{$formmethod}" enctype="application/x-www-form-urlencoded" name="searchbyfulltext" dir="ltr" style="display:inline;">
        <input type="hidden" name="searchto" value="searchbyfulltext" />
        <input type="hidden" name="showresult" value="1" />
        <input type="hidden" name="expert" value="0" />
        <input type="hidden" name="output" value="HTML" />
        <input type="hidden" name="utf8" value="1" />

        <input type="hidden" name="sort" value="{$hidopt_sort}" />
        <input type="hidden" name="orderRatingFirst" value="{$hidopt_orderRatingFirst}" />
        <input type="hidden" name="f_userowner" value="{$hidopt_userowner}" />
        <input type="hidden" name="f_userfound" value="{$hidopt_userfound}" />
        <input type="hidden" name="f_inactive" value="{$hidopt_inactive}" />
        <input type="hidden" name="f_disabled" value="{$hidopt_disabled}" />
        <input type="hidden" name="f_ignored" value="{$hidopt_ignored}" />
        <input type="hidden" name="f_otherPlatforms" value="{$hidopt_otherPlatforms}" />
        <input type="hidden" name="f_geokrets" value="{$hidopt_geokrets}" />
        <input type="hidden" name="country" value="{$country}" />
        <input type="hidden" name="language" value="{$language}" />
        <input type="hidden" name="difficultymin" value="{$difficultymin}" />
        <input type="hidden" name="difficultymax" value="{$difficultymax}" />
        <input type="hidden" name="terrainmin" value="{$terrainmin}" />
        <input type="hidden" name="terrainmax" value="{$terrainmax}" />
        <input type="hidden" name="cachetype" value="{$cachetype}" />
        <input type="hidden" name="cachesize" value="{$cachesize}" />
        <input type="hidden" name="cache_attribs" value="{$hidopt_attribs}" />
        <input type="hidden" name="cache_attribs_not" value="{$hidopt_attribs_not}" />

        <tr class="search_byfulltext">
            <td class="formlabel">{t}Text{/t}{t}#colonspace#{/t}:</td>
            <td><input type="text" name="fulltext" value="{$fulltext}" class="input200" /></td>
            <td><input type="submit" name="submit_ft" value="{t}Search{/t}" class="formbutton" onclick="submitbutton('submit_ft')" /></td>
        </tr>
        <tr class="search_byfulltext">
            <td>... {t}in{/t}{t}#colonspace#{/t}:</td>
            <td colspan="4">
                <input type="checkbox" name="ft_desc" id="ft_desc" class="checkbox" value="1" {if $ft_desc_checked}checked="checked"{/if} /> <label for="ft_desc">{t}Description{/t}</label> &nbsp;
                <input type="checkbox" name="ft_name" id="ft_name" class="checkbox" value="1" {if $ft_name_checked}checked="checked"{/if} /> <label for="ft_name">{t}Cachename{/t}</label> &nbsp;
                <input type="checkbox" name="ft_pictures" id="ft_pictures" class="checkbox" value="1" {if $ft_pictures_checked}checked="checked"{/if} /> <label for="ft_pictures">{t}Pictures{/t}</label> &nbsp;
                <input type="checkbox" name="ft_logs" id="ft_logs" class="checkbox" value="1" {if $ft_logs_checked}checked="checked"{/if} /> <label for="ft_logs">{t}Logs{/t}</label>
            </td>
        </tr>
    </form>
    {$fulltexterror}
    <tr class="search_byowner"><td class="separator"></td></tr>

    <form action="search.php" onsubmit="return(_sbo_click('submit_owner'));" method="{$formmethod}" enctype="application/x-www-form-urlencoded" name="searchbyowner" dir="ltr" style="display:inline;">
        <input type="hidden" name="searchto" value="searchbyowner" />
        <input type="hidden" name="showresult" value="1" />
        <input type="hidden" name="expert" value="0" />
        <input type="hidden" name="output" value="HTML" />
        <input type="hidden" name="utf8" value="1" />

        <input type="hidden" name="sort" value="{$hidopt_sort}" />
        <input type="hidden" name="orderRatingFirst" value="{$hidopt_orderRatingFirst}" />
        <input type="hidden" name="f_userowner" value="{$hidopt_userowner}" />
        <input type="hidden" name="f_userfound" value="{$hidopt_userfound}" />
        <input type="hidden" name="f_inactive" value="{$hidopt_inactive}" />
        <input type="hidden" name="f_disabled" value="{$hidopt_disabled}" />
        <input type="hidden" name="f_ignored" value="{$hidopt_ignored}" />
        <input type="hidden" name="f_otherPlatforms" value="{$hidopt_otherPlatforms}" />
        <input type="hidden" name="f_geokrets" value="{$hidopt_geokrets}" />
        <input type="hidden" name="country" value="{$country}" />
        <input type="hidden" name="language" value="{$language}" />
        <input type="hidden" name="difficultymin" value="{$difficultymin}" />
        <input type="hidden" name="difficultymax" value="{$difficultymax}" />
        <input type="hidden" name="terrainmin" value="{$terrainmin}" />
        <input type="hidden" name="terrainmax" value="{$terrainmax}" />
        <input type="hidden" name="cachetype" value="{$cachetype}" />
        <input type="hidden" name="cachesize" value="{$cachesize}" />
        <input type="hidden" name="cache_attribs" value="{$hidopt_attribs}" />
        <input type="hidden" name="cache_attribs_not" value="{$hidopt_attribs_not}" />

        <tr class="search_byowner">
            <td class="formlabel">{t}Owner:{/t}</td>
            <td><input type="text" name="owner" value="{$owner}" maxlength="40" class="input200" /></td>
            <td><input type="submit" name="submit_owner" value="{t}Search{/t}" class="formbutton" onclick="submitbutton('submit_owner')" /></td>
        </tr>
    </form>
    <tr class="search_byfinder"><td class="separator"></td></tr>

    <form action="search.php" onsubmit="return(_sbf_click('submit_finder'));" method="{$formmethod}" enctype="application/x-www-form-urlencoded" name="searchbyfinder" dir="ltr" style="display:inline;">
        <input type="hidden" name="searchto" value="searchbyfinder" />
        <input type="hidden" name="showresult" value="1" />
        <input type="hidden" name="expert" value="0" />
        <input type="hidden" name="output" value="HTML" />
        <input type="hidden" name="utf8" value="1" />

        <input type="hidden" name="sort" value="{$hidopt_sort}" />
        <input type="hidden" name="orderRatingFirst" value="{$hidopt_orderRatingFirst}" />
        <input type="hidden" name="f_userowner" value="{$hidopt_userowner}" />
        <input type="hidden" name="f_userfound" value="{$hidopt_userfound}" />
        <input type="hidden" name="f_inactive" value="{$hidopt_inactive}" />
        <input type="hidden" name="f_disabled" value="{$hidopt_disabled}" />
        <input type="hidden" name="f_ignored" value="{$hidopt_ignored}" />
        <input type="hidden" name="f_otherPlatforms" value="{$hidopt_otherPlatforms}" />
        <input type="hidden" name="f_geokrets" value="{$hidopt_geokrets}" />
        <input type="hidden" name="country" value="{$country}" />
        <input type="hidden" name="language" value="{$language}" />
        <input type="hidden" name="difficultymin" value="{$difficultymin}" />
        <input type="hidden" name="difficultymax" value="{$difficultymax}" />
        <input type="hidden" name="terrainmin" value="{$terrainmin}" />
        <input type="hidden" name="terrainmax" value="{$terrainmax}" />
        <input type="hidden" name="cachetype" value="{$cachetype}" />
        <input type="hidden" name="cachesize" value="{$cachesize}" />
        <input type="hidden" name="cache_attribs" value="{$hidopt_attribs}" />
        <input type="hidden" name="cache_attribs_not" value="{$hidopt_attribs_not}" />

        <tr class="search_byfinder">
            <td class="formlabel">{t}Log entries:{/t}</td>
            <td colspan="2">
                <select name="logtype">
                    {foreach from=$logtype_options item=logtype_option}
                        <option value="{$logtype_option.id|escape}" {if $logtype_option.selected}selected="selected"{/if}>{$logtype_option.name|escape}</option>
                    {/foreach}
                </select>
            </td>
        </tr>
        <tr class="search_byfinder">
            <td>... {t}by user:{/t}</td>
            <td><input type="text" name="finder" value="{$finder}" maxlength="40" class="input200" /></td>
            <td><input type="submit" name="submit_finder" value="{t}Search{/t}" class="formbutton" onclick="submitbutton('submit_finder')" /></td>
        </tr>
    </form>
    {if $logged_in}
    <tr class="search_byall"><td class="separator"></td></tr>

    <form action="search.php" onsubmit="return(_sba_click('submit_all'));" method="{$formmethod}" enctype="application/x-www-form-urlencoded" name="searchall" dir="ltr" style="display:inline;">
        <input type="hidden" name="searchto" value="searchall" />
        <input type="hidden" name="showresult" value="1" />
        <input type="hidden" name="expert" value="0" />
        <input type="hidden" name="output" value="HTML" />
        <input type="hidden" name="utf8" value="1" />

        <input type="hidden" name="sort" value="{$hidopt_sort}" />
        <input type="hidden" name="orderRatingFirst" value="{$hidopt_orderRatingFirst}" />
        <input type="hidden" name="f_userowner" value="{$hidopt_userowner}" />
        <input type="hidden" name="f_userfound" value="{$hidopt_userfound}" />
        <input type="hidden" name="f_inactive" value="{$hidopt_inactive}" />
        <input type="hidden" name="f_disabled" value="{$hidopt_disabled}" />
        <input type="hidden" name="f_ignored" value="{$hidopt_ignored}" />
        <input type="hidden" name="f_otherPlatforms" value="{$hidopt_otherPlatforms}" />
        <input type="hidden" name="f_geokrets" value="{$hidopt_geokrets}" />
        <input type="hidden" name="country" value="{$country}" />
        <input type="hidden" name="language" value="{$language}" />
        <input type="hidden" name="difficultymin" value="{$difficultymin}" />
        <input type="hidden" name="difficultymax" value="{$difficultymax}" />
        <input type="hidden" name="terrainmin" value="{$terrainmin}" />
        <input type="hidden" name="terrainmax" value="{$terrainmax}" />
        <input type="hidden" name="cachetype" value="{$cachetype}" />
        <input type="hidden" name="cachesize" value="{$cachesize}" />
        <input type="hidden" name="cache_attribs" value="{$hidopt_attribs}" />
        <input type="hidden" name="cache_attribs_not" value="{$hidopt_attribs_not}" />

        <tr class="search_byall">
            <td class="formlabel">{t}All caches{/t}</td>
            <td></td>
            <td><input type="submit" name="submit_all" value="{t}Search{/t}" class="formbutton" onclick="submitbutton('submit_all')" /></td>
        </tr>
    </form>
    {/if}
    </table>
</div>

<div class="buffer" style="width: 500px;">&nbsp;</div>
