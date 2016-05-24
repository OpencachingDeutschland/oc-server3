<?php
// no references to any other PHP code here except $errtitle and $errmsg, to minimize
// possibilities of error recursions
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!-- Unicode Reminder メモ -->
<head>
    <title>Opencaching.de</title>
    <meta http-equiv="content-type" content="text/xhtml; charset=UTF-8"/>
    <meta http-equiv="Content-Language" content="de"/>
    <meta http-equiv="gallerimg" content="no"/>
    <link rel="shortcut icon" href="favicon.ico"/>
    <link rel="stylesheet" type="text/css" href="resource2/ocstyle/css/style_screen.css"/>
</head>
<body style="margin:20px 40px 0 40px; background:#fff">
<table border="0" cellspacing="0" cellpadding="2" width="100%">
    <tr>
        <td width="80px">
            <a href="index.php"><img src="images/newlogo.png" alt="OC-Logo" style="border:0px;"/></a>
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
    <!-- no references to any other PHP code here except $errtitle and $errmsg, to minimize possibilities of error recursions -->
    <p>
        <br />Diese Seite steht momentan nicht zur Verfügung. Das Opencaching-Team
        arbeitet bereits daran, das Problem zu beheben.
    </p>
    <?php if (basename($_SERVER["SCRIPT_FILENAME"]) != 'index.php') { ?>
        <p><a href="index.php">Zurück zur Startseite</a></p>
    <?php } ?>
    <p>
        <br /><br /><em>This page is currently not available. The Opencaching team
        already works on fixing the problem.
    </p>
    <?php if ($errmsg != '') { ?>
        <br />
        <h1><?= $errtitle ?></h1>
        <p class="errormsg"><?= $errmsg ?></p>
    <?php } ?>
</div>
</body>
</html>
