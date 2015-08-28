{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/misc/32x32-gears.png" style="margin-right: 10px;" width="32" height="32" alt="World" />
	{t}An error occured while processing the page{/t}
</div>

<div class="content-txtbox-noshade">
	<p style="line-height: 1.6em;">{t}An error occured while processing the page. If you've called this page from an hyperlink on our site and this error persists some time, please contact us via E-Mail.{/t}</p>
	<p style="line-height: 1.6em;"><strong>{t}The following error occured:{/t}</strong></p>
	<p style="line-height: 1.6em;">
		{t}Page:{/t} {$page|escape}<br/>
		{t}Error message{/t}{t}#colonspace#{/t}:

		{if $id==ERROR_UNKNOWN}
			({$id}) {t}An unkown error occured.{/t}
		{elseif $id==ERROR_TEMPLATE_NOT_FOUND}
			({$id}) {t}Template does not exist.{/t}
		{elseif $id==ERROR_COMPILATION_FAILED}
			({$id}) {t}The compilation of the template faild. This should be a temporary failure. Please try again in some minutes.{/t}
		{elseif $id==ERROR_NO_ACCESS}
			({$id}) {t}Sorry, you are not allowed to view this page.{/t}
		{elseif $id==ERROR_CACHE_NOT_EXISTS}
			({$id}) {t}Sorry, the requested cache does not exist.{/t}
		{elseif $id==ERROR_CACHELOG_NOT_EXISTS}
			({$id}) {t}Sorry, the requested cachelog does not exist.{/t}
		{elseif $id==ERROR_INVALID_OPERATION}
			({$id}) {t}Sorry, the requested operation cannot be performed.{/t}
		{elseif $id==ERROR_LOGIN_REQUIRED}
			({$id}) {t}Please login to continue:{/t}
		{elseif $id==ERROR_USER_NOT_EXISTS}
			({$id}) {t}Sorry, the requested user does not exist.{/t}
		{elseif $id==ERROR_USER_NOT_ACTIVE}
			({$id}) {t}Sorry, the requested user account is deactivated.{/t}
		{elseif $id==ERROR_USER_NO_EMAIL}
			({$id}) {t}Sorry, there is no E-Mail address for the user requested.{/t}
		{elseif $id==ERROR_CACHE_NOT_PUBLISHED}
			({$id}) {t}Sorry, the Geocache is not published.{/t}
		{elseif $id==ERROR_CACHE_LOCKED}
			({$id}) {t}Sorry, the Geocache is locked and can not be viewed.{/t}
		{elseif $id==ERROR_MAIL_TEMPLATE_NOT_FOUND}
			({$id}) {t}Mail template does not exist.{/t}
		{elseif $id==ERROR_NO_COOKIES}
			({$id}) {t 1=$opt.cms.login}Your browser has rejected our cookie.<br />
			You can find more informations about this topic in the <a href="%1">Opencaching.de-help</a>.{/t}
		{elseif $id==ERROR_ALREADY_LOGGEDIN}
			({$id}) {t}You are already logged in.<br />
			Please <a href="login.php?action=logout">logout</a> to login again.{/t}
		{elseif $id==ERROR_SEARCHPLUGIN_WAYPOINT_FORMAT}
			({$id}) {t}Unkown format of the given waypoint. The following formats are known OCxxxx, NCxxxx and GCxxxx, whereas xxxx can be any number or char.{/t}
		{elseif $id==ERROR_SEARCHPLUGIN_WAYPOINT_MANY}
			({$id}) {t 1=$p1|escape}There exists more than one cache with the waypoint &quot;%1&quot;.{/t}
		{elseif $id==ERROR_SEARCHPLUGIN_WAYPOINT_NOTFOUND}
			({$id}) {t 1=$p1|escape}The waypoint &quot;%1&quot; was not found.{/t}
		{elseif $id==ERROR_DB_COULD_NOT_RECONNECT}
			({$id}) {t}The database could not be reconnected.{/t}
		{elseif $id==ERROR_DB_NO_ROOT}
			({$id}) {t}Switching to db-root failed.{/t}
		{elseif $id==ERROR_PICTURE_NOT_EXISTS}
			({$id}) {t}Sorry, the requested picture does not exist.{/t}
		{else}
			{$id|escape|nl2br}
		{/if}
	</p>
</div>