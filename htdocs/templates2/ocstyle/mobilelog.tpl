{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
 {* OCSTYLE - keine Änderungen *}
<form method="post" action="index.php">
	<input type="hidden" name="commit" value="1" />
	<table class="table">
		<tr>
			<td>{t}Username{/t}</td>
		</tr>
		<tr>
			<td><input type="text" name="username" value="{$username|escape}" size="20" /></td>
		</tr>
		<tr>
			<td>{t}Password{/t}</td>
		</tr>
		<tr>
			<td><input type="password" name="password" value="" size="20" /></td>
		</tr>
		<tr>
			<td>{t}OC-Waypoint{/t}</td>
		</tr>
		<tr>
			<td><input type="text" name="waypoint" value="{$waypoint|escape}" size="6" /></td>
		</tr>
		<tr>
			<td>{t}Date{/t}</td>
		</tr>
		<tr>
			<td>
				<select name="dateDay">
					{section name=value start=1 loop=32 step=1}
						<option value="{$smarty.section.value.index|escape}" {if $logdate.mday==$smarty.section.value.index}selected="selected"{/if}>{$smarty.section.value.index|escape}</option>
					{/section}
				</select>
				.
				<select name="dateMonth">
					{section name=value start=1 loop=13 step=1}
						<option value="{$smarty.section.value.index|escape}" {if $logdate.mon==$smarty.section.value.index}selected="selected"{/if}>{$smarty.section.value.index|escape}</option>
					{/section}
				</select>
				.
				<select name="dateYear">
					{section name=value start=$curdate.year-1 loop=$curdate.year+2 step=1}
						<option value="{$smarty.section.value.index|escape}" {if $logdate.year==$smarty.section.value.index}selected="selected"{/if}>{$smarty.section.value.index|escape}</option>
					{/section}
				</select>
			</td>
		</tr>
		<tr>
			<td>{t}Logtype{/t}</td>
		</tr>
		<tr>
			<td>
				<select name="logtype">
					{foreach item=item key=idx from=$logtypes}
						<option value="{$idx|escape}" {if $logtype==$idx}selected="selected"{/if}>{$item|escape}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td>{t}Text{/t}</td>
		</tr>
		<tr>
			<td>
				<textarea name="text" rows="5" cols="17">{$logtext|escape}</textarea>
			</td>
		</tr>
		<tr>
			<td>
			 {t}Recommend this cache?{/t}<br />
			 <font size="2">{t}(only if you have free ratings){/t}</font>
			</td>
		</tr>
		<tr>
			<td align="right">
				<select name="recommend">
					<option value="0" {if !$recommend}selected="selected"{/if}>{t}No{/t}</option>
					<option value="1" {if $recommend}selected="selected"{/if}>{t}Yes{/t}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<input type="submit" value="{t}Submit{/t}" />
			</td>
		</tr>
	</table>
</form>