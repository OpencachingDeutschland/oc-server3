<?php
/****************************************************************************
 * ./lang/de/ocstyle/editcache.tpl.php
 * -------------------
 * begin                : Mon July 6 2004
 *
 * For license information see LICENSE.md
 ****************************************************************************/

/****************************************************************************

     edit a cache listing

     template replacement(s):

            cacheid
            show_all_countries
            name
            typeoptions
            sizeoptions
            selLatN
            selLatS
            selLonE
            selLonW
            lat_h
            lat_min
            lon_h
            lon_min
            lon_message
            lat_message
            countryoptions
            show_all_countries_submit
            difficultyoptions
            terrainoptions
            cache_descs
            date_day
            date_month
            date_year
            date_message
            reset
            submit
            cacheid_urlencode
            statusoptions
            search_time
            way_length
            styleoptions

 ****************************************************************************/
?>
<script type="text/javascript" src="resource2/ocstyle/js/wz_tooltip.js"></script>
<script type="text/javascript">
<!--
var maAttributes = new Array({jsattributes_array});

function setListingModified()
{
    document.getElementById('listing_modified').value = '1';
    return true;
}

function testListingModified(a)
{
    if (document.getElementById('listing_modified').value == '1') {
        alert('{savealert}');
        a.href = 'javascript:void(0)';
    }
}

function _chkVirtual () {
    if (document.editcache_form.type.value == '4' || document.editcache_form.type.value == '5') {
        document.editcache_form.size.value = '7';
        document.editcache_form.size.disabled = true;
    }
    else {
        document.editcache_form.size.disabled = false;
    }
    return false;
}

function rebuildCacheAttr()
{
    var i = 0;
    var sAttr = '';
    for (i = 0; i < maAttributes.length; i++)
    {
        if (maAttributes[i][1] == 1)
        {
            if (sAttr != '') sAttr += ';';
            sAttr = sAttr + maAttributes[i][0];

            document.getElementById('attr' + maAttributes[i][0]).src = maAttributes[i][3];
        }
        else
            document.getElementById('attr' + maAttributes[i][0]).src = maAttributes[i][2];

        document.getElementById('cache_attribs').value = sAttr;
    }
}

function toggleAttr(id)
{
    var i = 0;
    for (i = 0; i < maAttributes.length; i++)
    {
        if (maAttributes[i][0] == id)
        {
            if (maAttributes[i][1] == 0) {
                maAttributes[i][1] = 1;
                for (a = 0; a < maAttributes[i][4].length; a++)
                    for (n = 0; n < maAttributes.length; n++)
                        if (maAttributes[n][0] == maAttributes[i][4][a]) {
                            maAttributes[n][1] = 0;
                            break;
                        }
            }
            else
                maAttributes[i][1] = 0;

            rebuildCacheAttr();
            setListingModified();
            break;
        }
    }
}
//-->
</script>

<div class="content2-pagetitle">
    <img src="resource2/ocstyle/images/cacheicon/traditional.gif" style="margin-right: 10px;" width="32" height="32" alt="" />
    {t}Edit cache <a href="viewcache.php?cacheid={cacheid}">{name}</a>{/t}
</div>

<form action="editcache.php" method="post" enctype="application/x-www-form-urlencoded" name="editcache_form" dir="ltr">
<input type="hidden" name="cacheid" value="{cacheid}"/> <!-- Ocprop -->
<input type="hidden" id="cache_attribs" name="cache_attribs" value="{cache_attribs}" />
<input type="hidden" name="show_all_countries" value="{show_all_countries}"/>
<input type="hidden" name="listing_modified" value="{listing_modified}" id="listing_modified" />
<table class="table">
    <tr>
        <td>{t}Name:{/t}</td>
        <td>
            <input type="text" name="name" value="{name}" maxlength="60" class="input400" onchange="setListingModified()" />{name_message}
        </td>
    </tr>
    <tr>
        <td>{t}Owner:{/t}</td>
        <td>
            {ownername} [<a href="adoptcache.php?action=listbycache&cacheid={cacheid_urlencode}" onclick="testListingModified(this)" >{t}offer for adoption{/t}</a>]
        </td>
    </tr>
    <tr>
        <td class="spacer" colspan="2"></td>
    </tr>

    <tr>
        <td>{t}State:{/t}</td>
        <td>
            <select name="status" class="input200" onchange="setListingModified()" >
                {statusoptions}
            </select>{status_message}{statuschange}
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>

    <tr>
        <td>{t}Cachetype:{/t}</td>
        <td>
            <select name="type" class="input200" onchange="setListingModified(); return _chkVirtual()" >
                {typeoptions}
            </select>
        </td>
    </tr>
    <tr>
        <td>{t}Size:{/t}</td>
        <td>
            <select name="size" class="input200" onchange="setListingModified(); return _chkVirtual()" >
                {sizeoptions}
            </select>{size_message}
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td valign="top">{t}Coordinates:{/t}</td>
        <td>
            <select name="latNS" onchange="setListingModified()" >
                <option value="N"{selLatN}>{t}N{/t}</option>
                <option value="S"{selLatS}>{t}S{/t}</option>
            </select>
            &nbsp;<input type="text" name="lat_h" maxlength="3" value="{lat_h}" class="input30" onchange="setListingModified()" />
            °&nbsp;<input type="text" name="lat_min" maxlength="6" value="{lat_min}" class="input50" onchange="setListingModified()" />&nbsp;'&nbsp;
            {lat_message}
            &nbsp;&nbsp;
            <select name="lonEW" onchange="setListingModified()" >
                <option value="E"{selLonE}>{t}E{/t}</option>
                <option value="W"{selLonW}>{t}W{/t}</option>
            </select>
            &nbsp;<input type="text" name="lon_h" maxlength="3" value="{lon_h}" class="input30" onchange="setListingModified()" />
            °&nbsp;<input type="text" name="lon_min" maxlength="6" value="{lon_min}" class="input50" onchange="setListingModified()" />&nbsp;'&nbsp;
            {lon_message}
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>

    <tr>
        <td>{t}Country:{/t}</td>
        <td>
            <select name="country" class="input200" onchange="setListingModified()" >
                {countryoptions}
            </select>
            &nbsp;{show_all_countries_submit}
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>

    <tr><td>{t}Rating:{/t}</td>
        <td>
            {t}Difficulty:{/t}
            <select name="difficulty" class="input60" onchange="setListingModified()" >
                {difficultyoptions}
            </select>&nbsp;&nbsp;
            {t}Terrain:{/t}
            <select name="terrain" class="input60" onchange="setListingModified()" >
                {terrainoptions}
            </select>{diff_message}
        </td>
    </tr>
    <tr><td>{t}Time and effort (optional):{/t}</td>
      <td>
            {t}Time effort:{/t}
                        <input type="text" name="search_time" maxLength="10" value="{search_time}" class="input30" onchange="setListingModified()" /> h
            &nbsp;&nbsp;
            {t}Distance:{/t}
            <input type="text" name="way_length" maxlength="10" value="{way_length}" class="input30" onchange="setListingModified()" /> km &nbsp; {effort_message}
      </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td class="help"><img src="lang/de/ocstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="{t}Notice{/t}" title="{t}Notice{/t}" />
            {t}Of course, this effort can only be estimated and can vary depending on enviromental influences. If you cannot make sufficiently detailed information, fill both fields up with a O (zero).{/t}
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>

    <tr>
        <td>{t}Waypoints (optional):{/t}</td>
        <!-- allow wp_gc copy&paste with leading spaces; will be trimmed later -->
        <td>geocaching.com: <input type="text" name="wp_gc" value="{wp_gc}" maxlength="12" class="input70 waypoint" onchange="setListingModified()" />
            {wpgc_message}
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td class="help">
            <img src="lang/de/ocstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="{t}Notice{/t}" title="{t}Notice{/t}" />
            {t}If the cache was published at another geocaching website, enter the corresponding waypoint here.{/t} <u>{t}Important:{/t}</u> {t}The geocache description at Opencaching must be kept up to date! Description changes on other websites must be applied to the Opencaching listing timely.{/t}<br />
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>

    <tr>
        <td class="header-small" colspan="2">
            <div class="content2-container bg-blue02">
                <p class="content-title-noshade-size2">
                    <img src="lang/de/ocstyle/images/description/22x22-description.png" width="22" height="22" align="middle" border="0" />
                    {t}Cache attributes{/t}&nbsp;&nbsp;
                </p>
            </div>
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td colspan="2">{cache_attrib_list}{safari_message}</td>
    </tr>
    <tr><td class="spacer" colspan="2">&nbsp;</td></tr>

    <tr>
        <td class="header-small" colspan="2">
            <div class="content2-container bg-blue02">
                <p class="content-title-noshade-size2">
                    <img src="lang/de/ocstyle/images/description/22x22-description.png" width="22" height="22" align="middle" border="0" />
                    {t}Descriptions{/t}&nbsp;&nbsp;
                    <img src="lang/de/ocstyle/images/action/16x16-adddesc.png" width="16" height="16" align="middle" border="0" alt="" >
                    <span style="font-weight: 400;font-size: small;">[<a href="newdesc.php?cacheid={cacheid_urlencode}" onclick="testListingModified(this)" >{t}Add description in another language{/t}</a>]</span>
                </p>
            </div>
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    {cache_descs}
    {gc_com_refs_start}
    <tr><td class="help" colspan="2"><img src="lang/de/ocstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="{t}Notice{/t}" title="{t}Notice{/t}" /><span style="color:red">{t}Your description contains at least one link to a picture hosted on geocaching.com!{/t}<br />
    {gc_com_msg2}</span>
    </td></tr>
    {gc_com_refs_end}
    <tr><td colspan="2" id="pictures">&nbsp;</td></tr>

    <tr>
        <td class="header-small" colspan="2">
            <div class="content2-container bg-blue02">
                <p class="content-title-noshade-size2">
                    <img src="lang/de/ocstyle/images/description/22x22-image.png" width="22" height="22" align="middle" border="0" />
                    {t}Pictures{/t}&nbsp;&nbsp;
                    <img src="lang/de/ocstyle/images/action/16x16-addimage.png" width="16" height="16" align="middle" border="0" />
                    <span style="font-weight: 400;font-size: small;">[<!-- Ocprop >> --><a href="picture.php?action=add&cacheuuid={cacheuuid_urlencode}" onclick="testListingModified(this)" >{t}Upload a picture{/t}</a>]</span>
                </p>
            </div>
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    {pictures}
    <tr><td class="spacer" colspan="2"></td></tr>

    <tr>
        <td class="header-small" colspan="2">
            <div class="content2-container bg-blue02">
                <p class="content-title-noshade-size2">
                    <img src="resource2/ocstyle/images/description/20x20-compass.png" align="middle" border="0" />
                    {t}Additional waypoints{/t}&nbsp;&nbsp;
                    <span style="font-weight: 400;font-size: small;">[<a href="childwp.php?cacheid={cacheid_urlencode}" onclick="testListingModified(this)" >{t}Add a waypoint{/t}</a>]</span>
                </p>
            </div>
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    {waypoints}
    <tr>
        <td class="help" colspan="2">
            <img src="lang/de/ocstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="{t}Notice{/t}" title="{t}Notice{/t}" />
            {t}Additional waypoints can be entered to make searching easier, for example by pointing to a suitable parking location or start of a path (the waypoint's description may contain more information). They may also specify the stages of a multicache. The waypoints are shown on the map when the cache is selected, are included in GPX file downloads and will be sent to the GPS device.{/t}
        </td>
    </tr>
    <tr><td class="spacer" colspan="2">&nbsp;</td></tr>
    <tr id="others">
        <td class="header-small" colspan="2">
            <div class="content2-container bg-blue02">
                <p class="content-title-noshade-size2">
                    <img src="lang/de/ocstyle/images/description/22x22-misc.png" width="22" height="22" align="middle" border="0" />
                    {t}Others{/t}
                </p>
            </div>
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td>{t}Hidden since:{/t}</td><!-- << Ocprop -->
        <td>
            <input class="input20" type="text" name="hidden_day" maxlength="2" value="{date_day}" onchange="setListingModified()" />.
            <input class="input20" type="text" name="hidden_month" maxlength="2" value="{date_month}" onchange="setListingModified()" />.
            <input class="input40" type="text" name="hidden_year" maxlength="4" value="{date_year}" onchange="setListingModified()" />&nbsp;
            {date_message}
            &nbsp;
            <img src="lang/de/ocstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="{t}Notice{/t}" title="{t}Notice{/t}" />
            {t}For Events: The date of event!{/t}
        </td>
    </tr>

    {activation_form}

    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td><nobr>{t}Password for 'found' logs:{/t}</nobr></td>
        <td><input class="input100" type="text" name="log_pw" value="{log_pw}" maxlength="20" onchange="setListingModified()" /> &nbsp; {t}(leave blank for no password){/t}</td>
    </tr>
    <tr>
        <td class="help" colspan="2">
            <img src="lang/de/ocstyle/images/misc/hint-link.gif" border="0" width="15" height="11" alt="{t}Notice{/t}" title="{t}Notice{/t}" />
            {t}Please note the <a href="articles.php?page=cacheinfo#logpw" target="_blank">description</a>{/t}
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td><nobr><label for="showlists">{t}Show all cache lists{/t}</label></nobr></td>
        <td><input type="checkbox" id="showlists" name="showlists" value="1" {showlists_checked} onchange="setListingModified()" /></td>
    </tr>
    <tr>
        <td><nobr><label for="protect_old_coords">{t}Protect old coordinates{/t}</label></nobr></td>
        <td><input type="checkbox" id="protect_old_coords" name="protect_old_coords" value="1" {protectcoords_checked} onchange="setListingModified()" /></td>
    </tr>

    <tr><td class="spacer" colspan="2"></td></tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td colspan="2">
            {t}By submitting I accept the <a href="articles.php?page=impressum#tos" target="_blank">Opencaching.de Terms of Service</a> and the <a href="articles.php?page=impressum#datalicense" target="_blank">Opencaching.de Datalicense</a>{/t}
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td class="header-small" colspan="2">
            <!-- <input type="reset" name="reset" value="{reset}" class="formbutton" onclick="flashbutton('reset')" />&nbsp;&nbsp; -->
            <input type="submit" name="submit" value="{submit}" class="formbutton" onclick="submitbutton('submit')" />
        </td>
    </tr>
    <tr><td class="spacer"></td></tr>
</table>
</form>
<script type="text/javascript">
<!--
_chkVirtual();
//-->
</script>
