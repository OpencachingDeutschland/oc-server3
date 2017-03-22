{***************************************************************************
 * You can find the license in the docs directory
 ***************************************************************************}
{* OCSTYLE *}

{* JS for cache list description tooltips *}
<script type="text/javascript" src="resource2/{$opt.template.style}/js/wz_tooltip.js"></script>

    <div class="content2-pagetitle">
        <img src="resource2/{$opt.template.style}/images/misc/32x32-list.png" style="margin-right: 10px;" width="32" height="32" />
        {t}Cache lists{/t}
    </div>

    {literal}
    <script type="text/javascript">
    function resetfilter()
    {
        document.getElementById('name_filter').value = '';
        document.getElementById('by_filter').value = '';
        submitbutton('reset');
    }
    </script>
    {/literal}

    <div class="content2-container">
        <div class="floatbox">
            <form method="get" action="cachelists.php">
                <table class="table" >
                    <tr>
                        <td>{t}List name:{/t}</td>
                        <td><input id="name_filter" name="name" value="{$name_filter}" class="input170" /></td>
                    </tr>
                    <tr>
                        <td>{t}By:{/t}</td>
                        <td><input id="by_filter" name="by" value="{$by_filter}" class="input170" /></td>
                    </tr>
                    <tr><td class="separator"></td></tr>
                    <tr>
                        <td colspan="2" style="text-align:right">
                            <input type="submit" name="filter" value="{t}Search{/t}" class="formbutton" onclick="submitbutton('filter')" />&nbsp;
                            <input type="submit" name="reset" value="{t}Reset{/t}" class="formbutton" onclick="resetfilter()" />
                        </td>
                    </tr>
                    <tr><td class="separator"></td></tr>
                    <tr><td class="separator"></td></tr>
                </table>
            </form>
        </div>

        <div>
            <div style="height:4px"></div>
            <p>
                {t}Since July 2015, all registered Opencaching users can create and publish own geocache lists via their <a href="mylists.php">user profile</a>. The following lists have been published so far:{/t}
            </p>
        </div>
    </div>

    <div style="height:6px"></div>

    <table>
        <tr>
            <td class="header-small">
                {include file="res_pager.tpl"}
            </td>
        </tr>
        <tr><td class="spacer"></td></tr>
    </table>

    {include file="res_cachelists.tpl"}

    <table>
        <tr><td class="spacer"></td></tr>
        <tr>
            <td class="header-small">
                {include file="res_pager.tpl"}
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
    </table>
