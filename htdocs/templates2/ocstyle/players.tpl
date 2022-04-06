{***************************************************************************
* You can find the license in the docs directory
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

<div id="hos" class="content2-pagetitle"><img src="resource2/ocstyle/images/misc/32x32-search.png" style="margin-right: 10px;" width="32" height="32" alt="" />Suche nach anderen Spielern</div> <!-- head of search -->

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

    <form action="players.php" onsubmit="return(_sbo_click('submit_owner'));" method="{$formmethod}" enctype="application/x-www-form-urlencoded" name="searchbyowner" dir="ltr" style="display:inline;">
        <input type="hidden" name="searchto" value="searchbyowner" />
        <input type="hidden" name="showresult" value="1" />
        <input type="hidden" name="expert" value="0" />
        <input type="hidden" name="output" value="HTML" />
        <input type="hidden" name="utf8" value="1" />

        <tr class="search_byowner">
            <td class="formlabel">Spieler-Name:</td>
            <td><input type="text" name="owner" value="{$owner}" maxlength="40" class="input200" /></td>
            <td><input type="submit" name="submit_owner" value="{t}Search{/t}" class="formbutton" onclick="submitbutton('submit_owner')" /></td>
        </tr>
    </form>

    </table>
</div>

<div class="buffer" style="width: 500px;">&nbsp;</div>
