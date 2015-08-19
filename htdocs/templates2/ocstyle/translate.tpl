{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE - minimale Änderungen *}
{if $action=='selectlang'}
	<p>{t}Select target language:{/t}</p>
	{foreach from=$languages item=languageItem}
		<a href="translate.php?translang={$languageItem}">{$languageItem}</a><br />
	{/foreach}
{else}
	<p>
		{t}Languages:{/t}&nbsp; 
		{foreach from=$languages item=languageItem}
			{if $languageItem==$translang}
				<b>{$languageItem}</b>
			{else}
				<a href="translate.php?translang={$languageItem}">{$languageItem}</a>
			{/if}
		{/foreach}
	</p>

	{if $datasqlfailed}
		<div class="errormsg">
			{t}doc/sql/static-data/data.sql has been changed with last CVS checkout.<br />
			Before you change translastions, update database with data.sql!<br />
			&nbsp;{/t}
		</div>
	{/if}

	<p>
		{t}Quick translation{/t}<br />
		<a href="translate.php?translang={$translang}&action=quicknone">{t}Disable{/t}</a><br />
		<a href="translate.php?translang={$translang}&action=quicknew">{t}Enable - new{/t}</a><br />
		<a href="translate.php?translang={$translang}&action=quickall">{t}Enable - all{/t}</a><br />
		<br />
		<a href="translate.php?translang={$translang}&action=scan">{t}Scan source codes{/t}</a><br />
		<br />
		<a href="translate.php?translang={$translang}&action=listnew">{t}Show new translations{/t}</a><br />
		<a href="translate.php?translang={$translang}&action=clearcache">{t}Clear smarty cache{/t}</a><br />
		<a href="translate.php?translang={$translang}&action=listfaults">{t}Show translations no longer referenced{/t}</a><br />
		<a href="translate.php?translang={$translang}&action=listall">{t}Show all translations{/t}</a><br />
		<a href="translate.php?translang={$translang}&action=resetids">{t}Reorder ID's{/t}</a> {t}(before an export){/t}<br />
		<a href="translate.php?translang={$translang}&action=export">{t}SQL Export{/t}</a><br />
		<br />
		<a href="translate.php?translang={$translang}&action=xmlexport">{t}XML Download{/t}</a><br />
		<a href="translate.php?translang={$translang}&action=xmlimport">{t}XML Import{/t}</a><br />
		<br />
		<a href="translate.php?translang={$translang}&action=textexportnew">{t}Text Download (new){/t} {$translang}</a><br />
		<a href="translate.php?translang={$translang}&action=textexportall">{t}Text Download (all){/t} {$translang}</a><br />
		<a href="translate.php?translang={$translang}&action=textimport">{t}Text Import{/t} {$translang}</a><br />
		{if $translang == 'EN'}
			<a href="translate.php?translang={$translang}&action=copy_en">{t}Copy EN{/t}</a><br />
		{/if}
		<br />
	</p>

	{if $action=="listnew" || $action=="listfaults" || $action=="listall"}
		{if $action=="listnew"}
			{t}New translations:{/t}<br/>
		{elseif $action=="listfaults"}
			{t}Translations no longer referenced:{/t}<br/>
		{elseif $action=="listall"}
			{t}All translations:{/t}<br/>
		{/if}
		<br />
		<table class="table">
			{foreach from=$trans item=transItem}
				<tr>
					<td valign="top"><a href="translate.php?translang={$translang}&action=edit&id={$transItem.id}">{$transItem.id}</a></td>

					<td valign="top"><a href="translate.php?translang={$translang}&action=remove&id={$transItem.id}">X</a></td>

					<td>{$transItem.text|escape}</td>
				</tr>
				{if $action=="listall"}
					<tr>
						<td valign="top">{$translang}</td>
						<td>&nbsp;</td>
						<td>{$transItem.trans|escape}</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				{/if}
			{/foreach}
		</table>
	{elseif $action=="edit"}
		<form action="translate.php" method="post">
			<input type="hidden" name="action" value="edit" />
			<input type="hidden" name="translang" value="{$translang}" />
			<input type="hidden" name="id" value="{$id}" />
			<table class="table">
				<tr>
					<td>{$text|escape}</td>
				</tr>
				{foreach from=$transRef item=refItem}
					<tr>
						<td>@ {$refItem.style} {$refItem.resource_name} {$refItem.line}</td>
					</tr>
				{/foreach}
				<tr>
					<td>
						<textarea name="transText" cols="70" rows="10">{$transText|escape}</textarea>
					</td>
				</tr>
				<tr>
					<td align="right">
						<input type="submit" name="submit2" value="Speichern" class="formbuttons" onclick="submitbutton('submit2')" />
					<td>
				</tr>
				<tr><td>{t}Use existing translations:{/t}</td></tr>
				<tr>
					<td>
						<table class="table">
							{foreach from=$trans item=transItem}
								<tr>
									<td valign="top"><a href="translate.php?translang={$translang}&action=edit&id={$id}&usetrans={$transItem.id}">{$transItem.id}</a></td>
									<td>{$transItem.text}</td>
								</tr>
							{/foreach}
						</table>
					</td>
				</tr>
			</table>
		</form>
	{elseif $action=="xmlimport"}
		<form action="translate.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="action" value="xmlimport2" />
			<input type="hidden" name="translang" value="{$translang}" />

			{t}XML file containing the translations:{/t}<br />
			<input name="xmlfile" type="file" size="50" /><br />
			<br />
			{t}Check the following languages in the XML file:{/t}<br />
			{foreach from=$languages item=languageItem}
				<input type="checkbox" name="lang{$languageItem}" value="1" class="radio" />
				{$languageItem}
				&nbsp;&nbsp;&nbsp;
			{/foreach}
			<br />
			<br />
			<input type="submit" name="submitfile1" value="{t}Scan file{/t}" class="formbutton" onclick="submitbutton('submitfile1')" />
		</form>
	{elseif $action=="textimport"}
		<form action="translate.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="action" value="textimport2" />
			<input type="hidden" name="translang" value="{$translang}" />

			{t}Text file containing the translations:{/t} {$translang}<br />
			<input name="textfile" type="file" size="50" /><br />
			<br />
			<input type="submit" name="submitfile2" value="{t}Scan file{/t}" class="formbutton" onclick="submitbutton('submitfile2')" />
		</form>
	{elseif $action=="xmlimport2" || $action=="textimport2"}
		<form action="translate.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="action" value="xmlimport3" />
			<input type="hidden" name="translang" value="{$translang}" />
			<input type="hidden" name="count" value="{count array=$texts}" />

			<table>
				{foreach from=$texts item=textItem}
					<tr>
						<td>
							{if $textItem.type!=1}
								<input type="checkbox" id="useitem{$textItem.count}" name="useitem{$textItem.count}" value="1" class="radio" />
							{/if}
						</td>
						<td>
							{if $textItem.type==1}
								{t}Source code changed, text no longer used{/t}
							{elseif $textItem.type==2}
								{t}New translation{/t}
							{elseif $textItem.type==3}
								{t}Modified translation{/t}
							{/if}
						</td>
					</tr>
					<tr>
						<td>
							<input type="hidden" name="code{$textItem.count}" value="{$textItem.code|base64encode}" />
						</td>
						<td><b>CODE</b> {$textItem.code|escape}</td>
					</tr>
					{foreach from=$languages item=languageItem}
						{if $textItem.$languageItem}
							{if $textItem.type==3}
								<tr>
									<td></td>
									<td><b>{$languageItem}</b> ({t}old{/t}) {$textItem.$languageItem.old|escape}</td>
								</tr>
							{/if}
							<tr>
								<td>
									<input type="hidden" name="{$languageItem}{$textItem.count}old" value="{$textItem.$languageItem.old|base64encode}" />
									<input type="hidden" name="{$languageItem}{$textItem.count}new" value="{$textItem.$languageItem.new|base64encode}" />
								</td>
								<td><b>{$languageItem}</b> ({t}new{/t}) {$textItem.$languageItem.new|escape}</td>
							</tr>
						{/if}
					{/foreach}
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				{/foreach}
			</table>
			<a href="javascript:toggleAll()">{t}Toggle all{/t}</a><br />
			<br />
			<input type="submit" value="{t}Commit{/t}" class="formbuttons"/>
		</form>
		<script type="text/javascript">
			{literal}
			<!--
				function toggleAll()
				{
					var nCount = {/literal}{count array=$texts}{literal};
					for (var nIndex = 1; nIndex <= nCount; nIndex++)
					{
						var oCheckbox = document.getElementById('useitem' + nIndex);
						if (oCheckbox != null)
						{
							oCheckbox.checked = !oCheckbox.checked;
						}
					}
				}
			//-->
			{/literal}
		</script>
	{elseif $action=="scan"}
		<p>
			<input type="button" id="scanbutton" class="formbutton" value="Scan" onclick="scanFiles()" /><br />
			<br />{t}Current file:{/t} <span id="currentfile">-</span>
		</p>
		{foreach from=$files item=fileItem key=fileKey}
			<input type="hidden" id="filename{$fileKey}" value="{$fileItem|escape}" />
			{$fileKey+1} {$fileItem|escape}
			<img id="fileimg{$fileKey}" src="" height="16" width="16" style="display:none;" />
			<br />
		{/foreach}
		{literal}
			<script type="text/javascript">
			<!--
				var nFileMax = 0;
				var nFileIndex = 0;

				function scanFiles()
				{
					document.getElementById('scanbutton').disabled = true;
				
					nFileMax = -1;
					while (document.getElementById('filename' + (nFileMax+1)) != null)
						nFileMax++;

					nFileIndex = 0;
					callURL('translate.php', 'action=scanstart');
					scanNext();
				}

				function scanNext()
				{
					var oImgElement = document.getElementById('fileimg' + nFileIndex);
					var oNameElement = document.getElementById('filename' + nFileIndex);;
					var sFilename = '';

					if (oImgElement == null)
					{
						document.getElementById('currentfile').firstChild.nodeValue = '{/literal}{t escape=js}Scan complete{/t}{literal}';
						return;
					}

					sFilename = oNameElement.value;
					document.getElementById('currentfile').firstChild.nodeValue = (nFileIndex+1) + ' {/literal}{t escape=js}of{/t}{literal} ' + (nFileMax+1) + ' ' + sFilename;

					callURL('translate.php', 'action=scanfile&filename=' + sFilename);

					oImgElement.src = 'resource2/ocstyle/images/log/16x16-found.png';
					oImgElement.style.display = 'inline';
					nFileIndex++;

					window.setTimeout("scanNext()", 0);
				}
				
				function callURL(url, params)
				{
					var xmlReq = createXMLHttp();
					if (!xmlReq) return;
				
					xmlReq.open('POST', url, false);
					xmlReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xmlReq.setRequestHeader("Content-length", params.length);
					xmlReq.setRequestHeader("Connection", "close");
					xmlReq.send(params);
				}
			//-->
			</script>
		{/literal}
	{/if}
{/if}