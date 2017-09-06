<!doctype html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title><?= _("Authorization Succeeded") ?></title>
    </head>
    <style>
        .okapi { font-size: 15px; max-width: 600px; font-family: "lucida grande", "Segoe UI", tahoma, arial, sans-serif; color: #555; margin: 20px 60px 0 40px; }
        .okapi a.opencaching { font-size: 20px; font-weight: bold; padding-top: 13px; color: #333; text-decoration: none; outline:none; display: block; }
        .okapi * { padding: 0; margin: 0; border: 0; }
        .okapi input, select { font-size: 15px; font-family: "lucida grande", "Segoe UI", tahoma, arial, sans-serif; color: #444; }
        .okapi a, .okapi a:hover, .okapi a:visited { cursor: pointer; color: #3e48a8; text-decoration: underline; }
        .okapi h1 { padding: 12px 0 30px 0; font-weight: bold; font-style: italic; font-size: 22px; color: #bb4924; }
        .okapi p { margin-bottom: 15px; font-size: 15px; }
        .okapi .form { text-align: center; margin: 20px; }
        .okapi .form input { padding: 5px 15px; background: #ded; border: 1px solid #aba; margin: 0 20px 0 20px; cursor: pointer; }
        .okapi .form input:hover {background: #ada; border: 1px solid #7a7; }
        .okapi span.note { color: #888; font-size: 70%; font-weight: normal; }
        .okapi .pin { margin: 20px 20px 20px 0; background: #eee; border: 1px solid #ccc; padding: 20px 40px; text-align: center; font-size: 24px; }
    </style>
    <body>

        <div class='okapi'>
            <a href='<?= $vars['okapi_base_url'] ?>'><img src='<?= $vars['okapi_base_url'] ?>static/logo-xsmall.gif' alt='OKAPI' style='float: right; margin-left: 10px;'></a>
            <a href='<?= $vars['site_url'] ?>'><img src="<?= $vars['site_logo'] ?>" alt='Opencaching' style='float: left; margin-right: 10px'></a>
            <a href='<?= $vars['site_url'] ?>' class='opencaching'><?= $vars['site_name'] ?></a>

            <h1 style='clear: both'><?= _("Access successfully granted") ?></h1>
            <?= sprintf(_("
                <p><b>You've just granted %s application access to your %s account.</b>
                To complete the operation, go back to %s and enter the following PIN code:</p>
            "), $vars['token']['consumer_name'], $vars['site_name'], $vars['token']['consumer_name']) ?>

            <div class='pin'><?= $vars['verifier'] ?></div>

            <p><a href='/'><?= $vars['site_name'] ?></a></p>
        </div>

    </body>
</html>
