{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
{if $action=='listbycache'}
	<form method="post" action="adoptcache.php">
		<input type="hidden" name="action" value="add" />
		<input type="hidden" name="submit" value="1" />
		<input type="hidden" name="cacheid" value="{$cacheid}" />

		<div class="content2-pagetitle">
			<img src="resource2/{$opt.template.style}/images/profile/32x32-adopt.png" style="margin-right: 10px;" width="32" height="32" alt="" />
			{t 1=$cachename|escape}List of users you offered %1 for adoption{/t}
		</div>

		<table class="table">
			<tr><td class="spacer"></td></tr>
			<tr>
				<th align="left">{t}User{/t}</th>
				<th align="left">{t}Date{/t}</th>
				<th>&nbsp;</th>
			</tr>

			<tr><td class="spacer"></td></tr>

			{foreach from=$adoptions item=adoptItem}
				<tr>
					<td>
						<a href="viewprofile.php?userid={$adoptItem.userid}">{$adoptItem.username|escape}</a>
					</td>
					<td>
						{$adoptItem.date_created|date_format:$opt.format.date}
					</td>
					<td>
						<a href="adoptcache.php?action=cancel&cacheid={$adoptItem.id}&userid={$adoptItem.userid}">{t}Withdraw adoption offer{/t}</a><br />
					</td>
				</tr>
			{foreachelse}
				<tr><td class="spacer" colspan="3"></td></tr>
				<tr>
					<td colspan="3">
						{t}You have no users invited to adopt your cache.{/t}
					</td>
				</tr>
			{/foreach}

			<tr><td colspan="3"></td></tr>
			<tr>
				<td class="header" colspan="3">
					{t 1=$cachename|escape}Offer <b>%1</b> for adoption{/t}
				</td>
			</tr>
			<tr>
				<td colspan="3">
					 <p>{t}If you find a user that wants to adopt your Geocache, fill in the username here
					 and submit. The user will then see this Geocache under My Profile &gt; Adoptions.
					 To complete, the user has to accept our terms of use and commit the adoption.{/t}</p>

					 <p>{t}You can offer this Geocache to more than one user. The first user committing the 
					 adoption will get the new owner of this Geocache.
					 With the adoption, you will give the committing user an unlimited right to use, modifiy, 
					 publish and sublicense content of this Geocache.{/t}</p>
				</td>
			</tr>
			<tr><td colspan="3">&nbsp;</td></tr>
			<tr>
				<td width="22%">{t}Username:{/t}</td>
				<td colspan="2"><input type="text" name="username" value="{$adoptusername|escape}" size="40" /></td>
			</tr>
			{if $error=='userunknown'}
				<tr><td>&nbsp;</td><td colspan="2"><span class="errormsg">{t}Username unknown{/t}</span></td></tr>
			{elseif $error=='userdisabled'}
				<tr><td>&nbsp;</td><td colspan="2"><span class="errormsg">{t}This user account is disabled.{/t}</span></td></tr>
			{elseif $error=='self'}
				<tr><td>&nbsp;</td><td colspan="2"><span class="errormsg">{t}You cannot adopt your own cache!{/t}</span></td></tr>
			{/if}
			<tr><td class="spacer" colspan="3"></td></tr>
			<tr>
				<td colspan="3">
					<input type="checkbox" name="tou" value="1" />
					<label for="tou">
						{t}Yes, i've read and understand the above and agree with it.{/t}
					</label>
				</td>
			</tr>
			{if $error=='tou'}
				<tr><td colspan="3"><span class="errormsg">{t}Sorry, you have to read and accept the above to proceed!{/t}</span></td></tr>
			{/if}
			<tr><td colspan="3">&nbsp;</td></tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2">
					<input type="submit" value="{t}Submit{/t}" class="formbutton" onclick="submitbutton('submit')" />
				</td>
			</tr>
		</table>
	</form>

{elseif $action=='listbyuser'}

	<div class="content2-pagetitle">
		<img src="resource2/{$opt.template.style}/images/profile/32x32-adopt.png" style="margin-right: 10px;" width="32" height="32" alt="" />
		{t}Geocaches the owner offers you for adoption{/t}
	</div>

	<table class="table" width="98%">
		<tr><td class="spacer"></td></tr>
		<tr>
			<th>{t}Name{/t}</th>
			<th>{t}Owner{/t}</th>
			<th></th>
		</tr>

		<tr><td class="spacer"></td></tr>

		{foreach from=$adoptions item=adoptItem}
			<tr>
				<td><a href="viewcache.php?cacheid={$adoptItem.id}">{$adoptItem.cachename|escape}</a></td>
				<td><a href="viewprofile.php?userid={$adoptItem.ownerid}">{$adoptItem.ownername|escape}</a></td>
				<td style="white-space:nowrap">
					<a href="adoptcache.php?action=commit&cacheid={$adoptItem.id}">{t}Adopt now{/t}</a>
					&nbsp;
					<a href="adoptcache.php?action=cancel&cacheid={$adoptItem.id}&userid={$login.userid}">{t}Reject adoption{/t}</a><br />
				</td>
			</tr>
		{foreachelse}
			<tr>
				<td colspan="3">
					<br />
					{t}There are no caches that can be adopted by you at the moment.{/t}
				</td>
			</tr>
		{/foreach}
	</table>

{elseif $action=='commit'}
	<form method="post" action="adoptcache.php">
		<input type="hidden" name="action" value="commit" />
		<input type="hidden" name="submit" value="1" />
		<input type="hidden" name="cacheid" value="{$cacheid}" />

		<div class="content2-pagetitle"><img src="resource2/{$opt.template.style}/images/profile/22x22-email.png" style="margin-right: 10px;" width="22" height="22" alt="" />{t 1=$cache.name|escape}Commit the adoption of %1{/t}</div>

		<p style="line-height: 1.6em;">
			{t 1=$cache.name|escape}Thank you for adopting <strong>%1</strong>.{/t}<br />
			{t 1=$cache.username|escape 2=$cache.date_created|date_format:$opt.format.date}<strong>%1</strong> offered you this Geocache for adoption at %2.{/t}
		</p>

		<p style="line-height: 1.6em;">
			{t}To complete the adoption, you have to submit this form.<br />
			With submitting this form you are the responsible owner of this Geocache.{/t}
		</p>

		<p style="line-height: 1.6em;">
			<br />
			<input type="checkbox" name="tou" value="1" />
			<label for="tou">{t}Yes, i've read and understand the <a href="articles.php?page=impressum#tos">terms of use</a> of Opencaching.de{/t}</label>
		</p>

		{if $error=='tou'}
			<p style="line-height: 1.6em; color: red; font-weight: bold;">
				{t}Sorry, you have to read and accept the terms of use to proceed!{/t}
			</p>
		{/if}
		<p style="line-height: 1.6em;"><input type="submit" value="{t}Submit{/t}" class="formbutton" onclick="submitbutton('submit')" /></p>
	</form>
{/if}
