{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/cacheicon/webcam.gif" style="margin-right: 10px;" width="32" height="32" alt="" />
	{t 1=$username}Log pictures of %1{/t}
</div>

{include file="res_logpictures.tpl" logdate=true loguser=false fullyear=true}

<p><br />{t}All pictures are copyrighted and subject to the <a href="articles.php?page=impressum#datalicense">Opencaching.de data license</a>.{/t}</p>
<p>{t}In your <a href="mydetails.php">profile settings</a> you can choose if your log pictures stat and gallery is visible for other users.{/t} {t}Pictures marked as spoiler are not shown; therefore the number of pictures on this page can be smaller than in the profile statistics.{/t}</a></p>
<p>&nbsp;</p>
