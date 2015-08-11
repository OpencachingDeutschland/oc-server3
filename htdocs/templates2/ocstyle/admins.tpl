{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}

<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/misc/32x32-tools.png" style="margin-right: 10px;" width="32" height="32" alt="World" />
	{t}OC Admins{/t}
</div>

<div class="content2-container">

<table class="table">
	<tr><th>{t}ID{/t}</th><th>{t}Name{/t}</th><th>{t}Rights{/t}</th></tr>
	{foreach from=$admins item=admin}
	<tr>
		<td>{$admin.id}</td>
		<td><a href="viewprofile.php?userid={$admin.id}">{$admin.name}</a></td>
		<td>{$admin.rights|escape}</td>
	</tr>
	{/foreach}
</table>

<p>&nbsp;</p>
<p>{t}Admin rights can be granted and revoked by a system administrator with database access.{/t}</p>

</div>
