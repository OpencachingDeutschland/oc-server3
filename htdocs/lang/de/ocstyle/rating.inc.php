<?php
/****************************************************************************
        ./lang/de/ocstyle/rating.inc.php
        -------------------
        begin                : July 4 2004

        For license information see LICENSE.md
 ****************************************************************************/

/****************************************************************************

     set template specific language variables

     template replacements:

 ****************************************************************************/

$rating_tpl = '<tr>
                <td valign="top">' . _('Recommendations:') . '</td>
                <td valign="top">
                    {rating_msg}
                    <noscript><br />' . _('A recommendation can only be made within a "found"-log!') . '</noscript>
                </td>
            </tr>
            <tr><td class="spacer" colspan="2"></td></tr>';

$rating_allowed = '<input type="hidden" name="ratingoption" value="1"><input type="checkbox" name="rating" value="1" class="checkbox" {chk_sel}/>&nbsp;' . _('This cache is one of my recommendations.');
$rating_too_few_founds = _('You need additional {anzahl} finds, to make another recommandation.');
$rating_maywithdraw = _('Alternatively, you can withdraw a <a href="mytop5.php">existing recommendation</a>.');
$rating_stat = _('You have given {curr} of {max} possible recommendations.');
