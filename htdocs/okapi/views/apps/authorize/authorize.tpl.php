<!doctype html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title><?= _("Authorization Form") ?></title>
        <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js'></script>
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
            .okapi .pin { margin: 20px 20px 0 0; background: #eee; border: 1px solid #ccc; padding: 20px 40px; text-align: center; font-size: 24px; }
        </style>
        <script>
            $(function() {
                $('#langpref').change(function() {
                    document.location = "?oauth_token=<?= $vars['token']['key'] ?>&langpref=" + $('#langpref').attr('value');
                });
            });
        </script>
    </head>
    <body>

        <div class='okapi'>
            <a href='<?= $vars['okapi_base_url'] ?>'><img src='<?= $vars['okapi_base_url'] ?>static/logo-xsmall.gif' alt='OKAPI' style='float: right; margin-left: 10px;'></a>
            <a href='<?= $vars['site_url'] ?>'><img src="<?= $vars['site_logo'] ?>" alt='Opencaching' style='float: left; margin-right: 10px'></a>
            <a href='<?= $vars['site_url'] ?>' class='opencaching'><?= $vars['site_name'] ?></a>

            <div style='float: right; clear: right; margin: 10px 0 10px 30px'>
                Choose your language:
                <select id='langpref' style='border: 1px solid #ccc'>
                    <?php foreach ($vars['locales'] as $locale => $attrs) { ?>
                        <option value='<?= $attrs['lang'] ?>' <?= ($attrs['locale'] == $vars['locale_displayed']) ? "selected" : "" ?>><?= $attrs['name'] ?></option>
                    <?php } ?>
                </select>
            </div>

            <?php if (isset($vars['token_expired']) && $vars['token_expired']) { ?>
                <h1 style='clear: both'><?= _("Expired request") ?></h1>
                <p><?= _("Unfortunately, the request has expired. Please try again.") ?></p>
            <?php } elseif ($vars['token']) { ?>
                <h1 style='clear: both'><?= _("External application is requesting access...") ?></h1>
                <p><?= sprintf(_("<b>%s</b> wants to access your <b>%s</b> account. Do you agree to grant access to this application?"), htmlentities($vars['token']['consumer_name']), $vars['site_name']) ?></p>
                <form id='authform' method='POST' class='form'>
                    <input type='hidden' name='authorization_result' id='authform_result' value='denied'>
                    <input type='button' value="<?= _("I agree") ?>" onclick="document.getElementById('authform_result').setAttribute('value', 'granted'); document.forms['authform'].submit();">
                    <input type='button' value="<?= _("Decline") ?>" onclick="document.forms['authform'].submit();">
                </form>
                <?= sprintf(_("
                    <p>Once permission is granted it is valid until its withdrawal on
                    the <a href='%s'>applications management</a> page.</p>
                    <p>The application will access your acount via <a href='%s'>the OKAPI Framework</a>.
                    If you allow this request application will be able to access all methods delivered
                    by the OKAPI Framework, i.e. post log entries on geocaches in your name.
                    You can revoke this permission at any moment.</p>
                "), $vars['okapi_base_url']."apps/", $vars['okapi_base_url']) ?>
            <?php } ?>
        </div>

    </body>
</html>