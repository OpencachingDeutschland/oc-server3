{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *} 
{if $action=='view'}
	<div class="content2-pagetitle">
		<img src="resource2/{$opt.template.style}/images/misc/32x32-searchresults.png" style="align: left; margin-right: 10px;" width="32" height="32" alt="{t}Stored queries{/t}" />
		{t}Stored queries{/t}
	</div>

	<table  class="null" border="0" cellspacing="0" width="98%">
		<tr>
			<td>
				<table class="table" style="width:100%">
					<tr>
						<td class="header-small" width="300px">{t}Name{/t}</td>
						<td class="header-small" width="200px">{t}Download{/t}</td>
						<td class="header-small" width="200px">&nbsp;</td>
					</tr>
					{foreach from=$queries item=queriesItem}
						{cycle assign=bgcolor values="#eeeeee,#e0e0e0"}
						<tr>
							<td bgcolor="{$bgcolor}"><a href="search.php?queryid={$queriesItem.id}">{$queriesItem.name|escape}</a></td>
							<td bgcolor="{$bgcolor}">
								<a href="search.php?queryid={$queriesItem.id}&output=gpx&count=max&zip=1">GPX</a> 
								<a href="search.php?queryid={$queriesItem.id}&output=loc&count=max&zip=1">LOC</a> 
								<a href="search.php?queryid={$queriesItem.id}&output=kml&count=max&zip=1">KML</a> 
								<a href="search.php?queryid={$queriesItem.id}&output=ov2&count=max&zip=1">OV2</a> 
								<a href="search.php?queryid={$queriesItem.id}&output=ovl&count=max&zip=1">OVL</a>
							</td>
							<td bgcolor="{$bgcolor}"><span style="float: right;">[<a href="search.php?queryid={$queriesItem.id}&showresult=0">{t}Edit{/t}</a>] [<a href="javascript:if(confirm('{t escape=js}Do you really want to delete the saved search?{/t}'))location.href='query.php?queryid={$queriesItem.id}&action=delete'">{t}Delete{/t}</a>]</span></td>
						</tr>
						<tr><td class="spacer" colspan="3" bgcolor="{$bgcolor}"></td></tr>
					{foreachelse}
						<tr><td colspan="2">{t}No stored queries found{/t}</td></tr>
					{/foreach}
					<tr>
						<td class="help" align="right">&nbsp;</td>
						<td class="help" colspan="2">{t}With the download you accept the <a href="articles.php?page=impressum#tos">term of use</a> from opencaching.de{/t}</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

{elseif $action=='save'}

	<form action="query.php" method="post">
		<input type="hidden" name="action" value="save" />
		<input type="hidden" name="queryid" value="{$queryid}" />
		<input type="hidden" name="submit" value="1" />

		<div class="content2-pagetitle">
			<img src="resource2/{$opt.template.style}/images/misc/32x32-searchresults.png" style="align: left; margin-right: 10px;" width="32" height="32" alt="{t}Store query{/t}" />
			{t}Store query{/t}
		</div>

		<table class="table">
			<tr>
				<td class="header-small" colspan="2">{t}Store options as new search{/t}</td>
			</tr>
			<tr>
				<td>{t}Name of the search{/t}:</td>
				<td>
					<input type="text" name="queryname" class="input300" maxlength="60" value="{$queryname}" /> 
				</td>
			</tr>
			{if $errorEmptyName==true}
				<tr><td colspan="2"><span class="errormsg">{t}You have to enter a name for this search.{/t}</span></td></tr>
			{elseif $errorNameExists==true}
				<tr><td colspan="2" class="errormsg">{t}There already exists a search with this name.{/t}</td></tr>
			{/if}
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="submit" value="{t}Store{/t}" class="formbuttons" />
				</td>
			</tr>
		</table>
	</form>

	<p>&nbsp;</p>

	<form action="query.php" method="post">
		<input type="hidden" name="action" value="saveas" />
		<input type="hidden" name="queryid" value="{$queryid}" />
		<input type="hidden" name="submit" value="1" />
		<table class="table">
			<tr>
				<td class="header-small" colspan="2">{t}Overwrite old search options{/t}</td>
			</tr>
			<tr>
				<td>{t}Name of the search{/t}:</td>
				<td>
					<select name="oldqueryid" class="input350">
						{foreach from=$queries item=queriesItem name="queries"}
							{if $smarty.foreach.queries.first}
								<option value="0" selected="selected">{t}-- Select search to overwrite --{/t}</option>
							{/if}
							<option value="{$queriesItem.id}">{$queriesItem.name|escape}</option>
						{foreachelse}
							<option value="0">{t}-- no stored search found --{/t}</option>
						{/foreach}
					</select>
				</td>
			</tr>
			{if $errorMustSelectQuery==true}
				<tr><td colspan="2" class="errormsg">{t}You must select a search to overwrite.{/t}</td></tr>
			{/if}
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="submit" value="{t}Store{/t}" class="formbuttons" />
				</td>
			</tr>
		</table>
	</form>
{/if}
