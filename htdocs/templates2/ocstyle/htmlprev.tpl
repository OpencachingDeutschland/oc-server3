{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
<table class="content">
	<tr>
		<td class="header">
			<img src="resource2/{$opt.template.style}/images/misc/32x32-tools.png" border="0" width="32" height="32" alt="{t}HTML preview{/t}" align="middle" />
			<font size="4"> <b>{t}HTML preview{/t}</b></font>
		</td>
	</tr>
	<tr><td class="spacer"></td></tr>
	{if $step==1}
		<tr>
			<td>
				{t}With this wizard, you can make some part of your description <b>bold</b> or <i>italic</i>. 
				No HTML knowledge required.{/t}
				<p><b>{t}Step 1{/t}:</b> {t}Enter the text in following field:{/t}
					<form action="htmlprev.php" name="text2html" method="post" enctype="application/x-www-form-urlencoded">
						<input type="hidden" name="step" value="1" />
						<table class="table">
							<tr>
								<td colspan="2">
									<textarea name="thetext" class="logs">{$thetext}</textarea>
								</td>
							</tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							<tr>
								<td alignment="left" width="50%">
								</td>
								<td align="right" width="50%">
									<input type="submit" name="toStep2" value="{t}Next{/t}" class="formbuttons">
								</td>
							</tr>
						</table>
					</form>
				</p>
			</td>
		</tr>
	{elseif $step==2}
		<tr>
			<td>
				<b>{t}Step 2{/t}:</b> {t}Here you can see the HTML code generated from your text. If you want to
				make a word <b>bold</b>, insert before the word &lt;b&gt; and after the word &lt;/b&gt;{/t}
					<form action="htmlprev.php" name="text2html" method="post" enctype="application/x-www-form-urlencoded">
						<input type="hidden" name="step" value="2" />
						<input type="hidden" name="thetext" value="{$thetext|escape}"/>
						<table class="table">
							<tr>
								<td colspan="2">
									<textarea name="thehtml" class="logs">{$thehtml|escape}</textarea>
								</td>
							</tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							<tr>
								<td alignment="left" width="50%">
									<input type="submit" name="toStep1" value="{t}Back{/t}" class="formbuttons"/>
								</td>
								<td align="right" width="50%">
									<input type="submit" name="toStep3" value="{t}Preview{/t}" class="formbuttons"/>
								</td>
							</tr>
						</table>
					</form>
			</td>
		</tr>
	{elseif $step==3}
		<tr>
			<td>
				<b>{t}Step 3{/t}:</b> {t}The Browser will show your HTML code like the following:{/t}<br>
				---<br>
					{$thehtml}
				<br>---<br>
				<p>{t}You can save the following HTML code and enter it in the cache description.{/t}
				<br>---<br>
					{$thehtml|escape|nl2br}
				<br>---</p>
					<form action="htmlprev.php" name="text2html" method="post" enctype="application/x-www-form-urlencoded">
						<input type="hidden" name="step" value="3" />
						<input type="hidden" name="thetext" value="{$thetext|escape}" />
						<input type="hidden" name="thehtml" value="{$thehtml|escape}" />
						<input type="submit" name="toStep2" value="{t}Back{/t}" class="formbuttons"/>
					</form>
				</p>
			</td>
		</tr>
	{/if}
</table>