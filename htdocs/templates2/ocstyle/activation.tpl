{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
{strip}
{if $sucess==true}

	<div class="content2-pagetitle">
		<img src="resource2/{$opt.template.style}/images/profile/32x32-profile.png" style="margin-right: 10px;" width="32" height="32" alt="{t}Activation{/t}" />
		{t}Activate account{/t}
	</div>
	<div class="content-txtbox-noshade">
		<p style="line-height: 1.6em;">
			{t}The activation of your account was successfull. You can now login on the login page.<br /><br />
			<a href="login.php">Go to the login page</a>{/t}
		</p>
		<div class="buffer" style="width: 500px;">&nbsp;</div>
	</div> 

{else}

	<form action="activation.php" method="post" enctype="application/x-www-form-urlencoded" style="display: inline;">
		<input type="hidden" name="submit" value="1" /> 
	  <div class="content2-pagetitle">
			<img src="resource2/{$opt.template.style}/images/profile/32x32-profile.png" style="margin-right: 10px;" width="32" height="32" alt="{t}Activation{/t}" />
			{t}Activate account{/t}
		</div>

	  <p style="line-height: 1.6em;">
			<img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" alt="{t}Hint{/t}" align="middle" />
			{t}To complete the registration, you have to enter your E-Mail and activation code.{/t}
		</p>

		<table class="table">
	  	<tr>
				<td>{t}E-Mail:{/t}</td>
				<td><input type="text" name="email" maxlength="60" value="{$email|escape}" class="input200" />{if $errorEMail==true} &nbsp;<span class="errormsg">{t}E-Mail-Adress is not valid.{/t}</span>{/if}</td>
	  	</tr>
	  	<tr>
				<td>{t}Activation code:{/t}</td>
				<td><input type="text" name="code" maxlength="20" value="{$code|escape}" class="input200" /></td>
	  	</tr>
	  	<tr><td class="spacer"></td></tr>
		</table>

		{if $errorCode==true}
		
		  <p style="line-height: 1.6em;">
				{t 1=$opt.mail.contact}The activation code or E-Mail was incorrect.<br />
				The activation of your account was not successfull<br />
				Please use the E-Mail where the registration mail was sent to.<br />
				<br />
				Please check if you have mistyped - if you cannot solve the problem yourself,
				contact us via <a href="mailto:%1">%1</a>{/t}
			</p>

		{/if}

		{if $errorAlreadyActivated==true}
			<p style="line-height: 1.6em;">
				{t 1=$opt.cms.login}The account is already activated. Please try to <a href="login.php">login</a>.<br />
				If you cannot login, please read the following page: <a href="%1">problems with login</a>{/t}
		  </p>
		{/if}

		<p style="line-height: 1.6em;">
			<!-- <input type="reset" name="reset" value="{t}Reset{/t}" class="formbutton" onclick="flashbutton('reset')" />&nbsp;&nbsp; -->
			<input type="submit" name="submit" value="{t}Submit{/t}" class="formbutton" onclick="submitbutton('submit')" />
		</p>
	</form>

{/if}
{/strip}
