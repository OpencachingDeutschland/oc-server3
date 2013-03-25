<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<!-- Unicode Reminder メモ -->
	<head>
		<title>Opencaching.de - Datenbankproblem</title>
		<meta http-equiv="content-type" content="text/xhtml; charset=UTF-8" />
		<meta http-equiv="Content-Language" content="de" />
		<meta http-equiv="gallerimg" content="no" />
		<link rel="shortcut icon" href="favicon.ico" />
		<link rel="stylesheet" type="text/css" href="resource2/ocstyle/css/style_screen.css" />
	</head>
	<body style="margin:20px 40px 0 40px; background:#fff">
		<table border="0" cellspacing="0" cellpadding="2" width="100%">
			<tr>
				<td width="80px">
					<a href="/index.php"><img src="images/newlogo.png" alt="oc-Logo" style="border:0px;"/></a>
				</td>
				<td width="100%">
					&nbsp;
				</td>
				<td valign="bottom">
					&nbsp;
				</td>
			</tr>
		</table>
  <div id="content">
		<h1>Fehler beim Datenbankzugriff</h1>
		<?php
			echo '<p>Entschuldigung, bei der Darstellung der Seite ist ein interner Fehler aufgetreten.
			      Falls dieses Problem für längere Zeit bestehen sollte, informiere uns bitte per
			      <a href="mailto:contact@opencaching.de">Email</a>.</p>
			      <p>' . $dberrmsg  . '</p>
			      <p><a href="index.php">Zurück zur Startseite</a></p>

			      <p><br /><br /><em>An error occured while displaying the requested page.
			      If this problem persists for a longer time, please inform us via
			      <a href="mailto:contact@opencaching.de">email</a>.</em></p>';
		?>
  </div>
	</body>
</html>