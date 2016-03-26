{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE - keine Änderungen *}
{if $confirm==1}
	<table class="content table">
		<tr><td class="header"><font size="4"><b>{t}Newsentry saved{/t}</b></font></td></tr>
		<tr><td class="spacer" colspan="2"></td></tr>
		<tr><td>{$smarty.now|date_format:$opt.format.datetime} - {$newstopic}</td></tr>
		<tr><td>{$newstext}</td></tr>
		<tr><td class="spacer"></td></tr>
		<tr>
			<td>
				{t}Thank you very much for your newsentry. We'll try to validate your entry as soon as possible.<br />
				<br />
				Back to <a href="index.php">Start</a>{/t}
			</td>
		</tr>
	</table>
{else}
	<form action="newstopic.php" method="post" enctype="application/x-www-form-urlencoded">
		<input type="hidden" name="submit" value="1" />
		<table class="content">
			<tr><td class="header"><font size="4"><b>{t}Submit a newsentry{/t}</b></font></td></tr>
			<tr>
				<td>
					{t}Are there any news about Geocaching?<br />
					Here you can create a newsentry, that will be shown on the start page.{/t}
					<br />
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>
			<tr>
				<td>
					<img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" alt="Hinweis" title="Hinweis" align="middle" />
					<span style="color:#666666; font-size:10px;">
						{t 1=$opt.mail.contact}We have to approve your newsentry before we can display it. We try to do this within 24h to 48h. The newsentry cannot be modified
						after you have submitted it. If you want to take changes in the newsentry, please resubmit the correct newsentry.
						It is allowed to submit news about everything around Geocaching - but it is not allowed to submit advertisement for products or
						geocaches.
						
						The decission to approve or not approve a newsentry lies exclusively in the discretion of the operator of the web page - there 
						is not a requirement on publication. In the case of doubts, please submit your newsentry and wait if it will be shown - or ask 
						via E-Mail before submitting <a href="mailto:%1">%1</a>.{/t}
					</span>
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>
			<tr>
				<td>
					{t}Topic:{/t} 
					<select name="topic">
						{foreach from=$newsTopics item=topicItem}
							<option value="{$topicItem.id}"{if $topicItem.id==$topic} selected="selected"{/if}>{$topicItem.name|escape}</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr><td>{t}Newsentry:{/t}</td></tr>
			<tr>
				<td>
					<textarea name="newstext" cols="80" rows="10">{$newstext}</textarea>
				</td>
			</tr>
			<tr><td><input type="checkbox" name="newshtml" id="newshtml" value="1" style="border:0;" {if $newshtml==1}checked="checked"{/if} /> <label for="newshtml">{t}The newsentry is written with HTML tags{/t}</label></td></tr>
			<tr>
				<td>
					<img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" alt="" align="middle" />
					<span style="color:#666666; font-size:10px;">
						{t}Please only use HTML tags to format the text - eg. bold, color or hyperlinks. We do not want tables, divs etc.{/t}
					</span>
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>
			<tr>
				<td>
					{t}Your E-Mail for further questions:{/t} <input type="text" name="email" size="40" value="{$email|escape}" />
					{if $email_error==1}
						<span class="errormsg">{t}E-Mail-Adress is not valid.{/t}</span>
					{/if}
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>
			<tr><td>{t}And now fill in the code to protect against spammers:{/t}</td></tr>
			<tr><td><img src="{$captcha_filename}" alt=""/><input type="hidden" name="captcha_id" size="40" value="{$captcha_id}" /></td></tr>
			<tr>
				<td>
					{t}What does this picture show?{/t} <input type="text" name="captcha" size="40" value="" />
					{if $captcha_error==1}
						<span class="errormsg">{t}The code did not match.{/t}</span>
					{/if}
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>
			<tr>
				<td>
					<input type="submit" value="{t}Submit{/t}" class="formbutton" onclick="submitbutton('submit')" />
				</td>
			</tr>
		</table>
	</form>
{/if}
