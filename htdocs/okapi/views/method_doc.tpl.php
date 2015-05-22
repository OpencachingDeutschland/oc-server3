<?

# Shortcuts
$m = $vars['method'];

?>
<!doctype html>
<html lang='en'>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title><?= $m['brief_description'] ?> - OKAPI Reference</title>
        <link rel="stylesheet" href="<?= $vars['okapi_base_url'] ?>static/common.css?<?= $vars['okapi_rev'] ?>">
        <link rel="icon" type="image/x-icon" href="<?= $vars['okapi_base_url'] ?>static/favicon.ico">
        <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js'></script>
        <script>
            var okapi_base_url = "<?= $vars['okapi_base_url'] ?>";
        </script>
        <script src='<?= $vars['okapi_base_url'] ?>static/common.js?<?= $vars['okapi_rev'] ?>'></script>
    </head>
    <body class='api'>
        <div class='okd_mid'>
            <div class='okd_top'>
                <? include 'installations_box.tpl.php'; ?>
                <table cellspacing='0' cellpadding='0'><tr>
                    <td class='apimenu'>
                        <?= $vars['menu'] ?>
                    </td>
                    <td class='article'>

                        <h1>
                            <?= $m['brief_description'] ?>
                            <div class='subh1'>:: <b><?= $m['name'] ?></b> method</div>
                        </h1>
                        <table class='method' cellspacing='1px'>
                            <tr>
                                <td class='precaption' colspan='3'>
                                    <table><tr>
                                        <td>Minimum Authentication: <span class='level level<?= $m['auth_options']['min_auth_level'] ?>'>Level <?= $m['auth_options']['min_auth_level'] ?></span></td>
                                        <td>(see <a href='<?= $vars['okapi_base_url'] ?>introduction.html#auth_levels'>Authentication Levels</a>)</td>
                                    </tr></table>
                                </td>
                            </tr>
                            <tr>
                                <td class='caption' colspan='3'>
                                    <b><?= \okapi\Settings::get('SITE_URL')."okapi/".$m['name'] ?></b>
                                </td>
                            </tr>
                            <tr>
                                <td class='description' colspan='3'>
                                    <?= $m['description'] ?>
                                </td>
                            </tr>
                            <? foreach ($m['arguments'] as $arg) { ?>
                                <tr class='<?= $arg['class'] ?>' id='<?= 'arg_'.$arg['name'] ?>'>
                                    <td class='argname'><?= $arg['name'] ?></td>
                                    <td class='<? echo $arg['is_required'] ? 'required' : 'optional'; ?>'><? echo $arg['is_required'] ? 'required' : 'optional'; ?></td>
                                    <td class='argdesc'>
                                        <?= $arg['description'] ?>
                                    </td>
                                </tr>
                            <? } ?>
                            <tr>
                                <td colspan='3' class='oauth_args'>
                                    <? if ($m['auth_options']['min_auth_level'] == 0) { ?>
                                        No additional authentication parameters are required.
                                    <? } elseif ($m['auth_options']['min_auth_level'] == 1) { ?>
                                        <b>Plus required</b> <i>consumer_key</i> argument, assigned for your application.
                                    <? } else { ?>
                                        <b>Plus required</b>
                                        standard OAuth Consumer signing arguments:
                                        <i>oauth_consumer_key, oauth_nonce, oauth_timestamp, oauth_signature,
                                        oauth_signature_method, oauth_version</i>.
                                        <? if ($m['auth_options']['min_auth_level'] == 3) { ?>
                                            <b>Plus required</b> <i>oauth_token</i> for Token authorization.
                                        <? } ?>
                                    <? } ?>
                                </td>
                            </tr>
                            <tr><td colspan='3' class='returns'>
                                <p><b>Returned&nbsp;value:</b></p>
                                <?= $m['returns'] ?>
                            </td></tr>
                        </table>
                        <? if ($m['issue_id']) { ?>
                            <div class='issue-comments' issue_id='<?= $m['issue_id'] ?>'></div>
                        <? } ?>
                    </td>
                </tr></table>
            </div>
            <div class='okd_bottom'>
            </div>
        </div>
    </body>
</html>
