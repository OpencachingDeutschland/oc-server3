{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{if $copy==true}{t}C O P Y{/t}

{/if}
{if $sendemailaddress==true}
{t 1=$fromusername 2=$fromuseremail 3=$opt.page.absolute_url 4=$fromuserid}'%1' with the E-Mail address %2 contacted you via %3
The user profile: %3viewprofile.php?userid=%4
Use the reply button of your E-Mail agent to answer this E-Mail.{/t}
{else}
{t 1=$fromusername 2=$opt.page.absolute_url 3=$fromuserid}'%1' contacted you via %2
The user profile: %2viewprofile.php?userid=%3
To reply this E-Mail use the E-Mail link on the users profile.{/t}
{/if}

{t}Subject:{/t} {$usersubject}
----------------------
{$text}
----------------------

