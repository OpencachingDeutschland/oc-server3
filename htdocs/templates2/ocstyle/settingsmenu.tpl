{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}

<div class="nav4" style="margin-bottom: 50px;">
    <ul>
        <li class="group noicon {if $set_profiledata}selected{/if}"><a href="myprofile.php">{t}Personal data{/t}</a></li>
        <li class="group noicon {if $set_publicprofile}selected{/if}"><a href="mydetails.php">{t}Additional Profile settings{/t}</a></li>
        <li class="group noicon {if $set_statpic}selected{/if}"><a href="mystatpic.php">{t}Statistics picture{/t}</a></li>
        <li class="group noicon {if $set_ocsettings}selected{/if}"><a href="myocsettings.php">{t}OC settings{/t}</a></li>
        <li class="group noicon {if $set_email}selected{/if}"><a href="myemailsettings.php">{t}E-mail settings{/t}</a></li>
        <li class="group noicon {if $set_pw}selected{/if}"><a href="newpw.php">{t}Change password{/t}</a></li>
    </ul>
</div>