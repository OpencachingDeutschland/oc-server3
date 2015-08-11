{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE - minimale Änderungen *}
{capture name="userlink"}
	<a href="viewprofile.php?userid={$userid}">{$username|escape}</a>
{/capture}
{if $success==true}
	<div class="content2-pagetitle">
		<img src="resource2/{$opt.template.style}/images/misc/22x22-email.png" style="margin-right: 10px;" width="32" height="32" alt="" />
		{t 1=$smarty.capture.userlink}E-Mail to %1 was sent{/t}
	</div>

	<table class="table">
		<tr>
			<td colspan="2">
				{t}Subject:{/t} {$subject|escape}
			</td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>

		<tr><td colspan="2">{t}Content:{/t}</td></tr>
		<tr>
			<td colspan="2">
				{$text|escape|nl2br}
			</td>
		</tr>
	</table>
{else}
	<form action="mailto.php" method="post">
		<input type="hidden" name="userid" value="{$userid}"/>
		<div class="content2-pagetitle">
			<img src="resource2/{$opt.template.style}/images/misc/22x22-email.png" style="margin-right: 10px;" width="32" height="32" alt="" />
			{t 1=$smarty.capture.userlink}Send E-Mail to %1{/t}
		</div>

		<table class="table">
			{if $email_problems > 0}
			<tr><!-- Tag for external page processing: email problems -->
				<td colspan="2" class="redtext">
					<p>{t}One ore more emails to this user could not be delivered. It might be a good idea to additionally log comments on the user's geocaches, and/or use alternative contact addresses like a message board account or another geocaching platform.{/t}</p>
				</td>
			</tr>
			{/if}

			<tr>
				<td colspan="2">{t}Subject:{/t} <input type="text" name="subject" value="{$subject|escape}" class="input400" /></td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>

			{if $errorSubjectEmpty==true}
				<tr><td colspan="2" class="errormsg">{t}You have to enter a subject!{/t}</td></tr>
			{/if}

			<tr>
				<td colspan="2">{t}Content:{/t}</td>
			</tr>
			<tr>
				<td colspan="2">
					<textarea class="logs" name="text" cols="68" rows="15">{$text|escape}</textarea>
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>
			{if $errorBodyEmpty==true}
				<tr><td colspan="2" class="errormsg">{t}You have to enter a content!{/t}</td></tr>
			{/if}

			<tr>
				<td colspan="2">
					<input type="checkbox" name="emailaddress" value="1" id="l_send_emailaddress" class="checkbox" {if $emailaddress==true}checked="checked" {/if}/>
					<label for="l_send_emailaddress">{t}Send my E-Mail address with this message{/t}</label>
				</td>
			</tr>
			<tr>
				<td class="help" colspan="2">
					<img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" align="middle" /> 
					{t}This enables the receiver to answer your E-Mail directly with his E-Mail agent. You may enable this option by default in your <a href="myprofile.php">user profile</a>.{/t}<br />
					<div style="padding: 10px 0 10px 0">
						<img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" align="middle" />
						<span class="smalltext">{t}To protect you and us from abuse:{/t}</span>
						<ul style="margin:0; line-height:1.1em">
							<li class="smalltext">{t}Only E-Mail that regards Geocaching are allowed. Spaming is not allowed!{/t}</li>
							<li class="smalltext">{t}All relevant informations will be logged (date/time, sender/receiver and IP address){/t}</li>
							<li class="smalltext">{t}All informations will be handled confidential and not published to third parties!{/t}</li>
						</ul>
					</div>
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>

			{if $errorUnkown==true}
				<tr><td colspan="2" class="errormsg">{t}An unkown error occured and the E-Mail was not sent.{/t}</td></tr>
			{/if}

			<tr>
				<td class="header-small" colspan="2">
					<!-- <input type="reset" name="cancel" value="{t}Reset{/t}" class="formbutton" onclick="flashbutton('cancel')" />&nbsp;&nbsp; -->
					<input type="submit" name="ok" value="{t}Send{/t}" class="formbutton" onclick="submitbutton('ok')" />
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>
		</table>
	</form>
{/if}
