{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}

<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/profile/32x32-profile.png" style="margin-right: 10px;" width="32" height="32" alt="" />
	{t}My profile details{/t}
</div>

<form action="mydetails.php" method="post" enctype="application/x-www-form-urlencoded" name="editform" dir="ltr">
	<input type="hidden" name="action" value="changetext" />
	<input id="descMode" type="hidden" name="descMode" value="3" />

<p>{t}The following text is displayed in your <a href="viewprofile.php">public profile</a>:{/t}</p>

<table class="table">
	<tr>
		<td colspan="2">
			<div class="menuBar">
				<span id="descHtmlEdit" class="buttonNormal" onclick="btnSelect(3)" onmouseover="btnMouseOver(3)" onmouseout="btnMouseOut(3)">{t}Editor{/t}</span>
				<span class="buttonSplitter">|</span>
				<span id="descHtml" class="buttonNormal" onclick="btnSelect(2)" onmouseover="btnMouseOver(2)" onmouseout="btnMouseOut(2)">{t}&lt;html&gt;{/t}</span>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<span id="scriptwarning" class="errormsg">{t}JavaScript is disabled in your browser, you can enter (HTML) text only. To use the editor, please enable JavaScript.{/t}</span>
		</td>
	</tr>
	<tr>
		<td>
			<textarea name="desctext" id="desctext" cols="68" rows="25" class="userdesc" >{$desctext}</textarea>
    </td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td colspan="2">
			{t}By submitting I accept the <a href="articles.php?page=impressum#tos" target="_blank">Opencaching.de Terms of Service</a> and the <a href="articles.php?page=impressum#datalicense" target="_blank">Opencaching.de Datalicense</a>{/t}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td class="header-small" colspan="2">
			<input type="submit" name="cancel" value="{t}Cancel{/t}" class="formbutton" onclick="submitbutton('cancel')" />&nbsp;&nbsp;
			<input type="submit" name="save" value="{t}Submit{/t}" class="formbutton" onclick="submitbutton('save')"(/>
		</td>
	</tr>
</table>

</form>

<script type="text/javascript">
<!--
	OcInitEditor();
//-->
</script>
