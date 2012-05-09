<?php

  // Unicode Reminder メモ

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>{t}Redirect - opencaching{/t}</title>
		<meta http-equiv="content-type" content="text/xhtml; charset=UTF-8" />
		<meta http-equiv="content-language" content="de" />
		<meta http-equiv="gallerimg" content="no" />
		<meta http-equiv="refresh" content="3;url={url}" />
		<meta http-equiv="pragma" content="no-cache" />
		<style type="text/css">
		<!--
			table, tr, td     
			{ 
				font:11px Tahoma; 
				color:#404040;
			}
			body
			{
				font:10px Tahoma;
				color:#404040;
				background-color:#FFFFFF;
			}

			a:link, a:visited, a:active
			{
				text-decoration:underline;
				color:#37496D;
			}
			a:hover                      
			{
				color:#404040;
			}
			#redirect         
			{
				font-size:11px; 
				font-weight:bold; 
				border:1 solid #C0C0C0;
			}
		-->
		</style>
	</head>
	<body bgcolor='#C2CFDF'>
		&nbsp;<p/>&nbsp;<p/>
		<table cellpadding='0' cellspacing='0' border='0' width="98%" align='center' height='85%'>
			<tr align='center' valign='middle'>
				<td>
					<table cellpadding='10' cellspacing='0' border='0' width="80%" align='center'>
						<tr>
							<td valign='middle' align='center' bgcolor='#E9E9E9' id='redirect'>
								<p>{message}</p>
								(<a href='{url}'>{t}If your browser does not support automatic forwarding please click here.{/t}</a>)
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>