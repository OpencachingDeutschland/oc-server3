<!doctype html>
<html lang='en'>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title>Sign up for an API Key</title>
        <link rel="stylesheet" href="<?= $vars['okapi_base_url'] ?>static/common.css?<?= $vars['okapi_rev'] ?>">
        <link rel="icon" type="image/x-icon" href="<?= $vars['okapi_base_url'] ?>static/favicon.ico">
        <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js'></script>
        <script>
            var okapi_base_url = "<?= $vars['okapi_base_url'] ?>";
        </script>
        <script src='<?= $vars['okapi_base_url'] ?>static/common.js?<?= $vars['okapi_rev'] ?>'></script>
        <script>
            $(function() {
                $('#submit').click(function() {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            'posted': 'true',
                            'appname': $('#appname').attr('value'),
                            'appurl': $('#appurl').attr('value'),
                            'email': $('#email').attr('value'),
                            'terms': $('#terms').attr('checked')
                        },
                        error: function() { alert("Oops... Something went wrong.\nContact the developers if problem persists."); },
                        success: function(result)
                        {
                            if (result.ok)
                                $('#signup').hide();
                            alert(result.notice);
                        }
                    });
                });
            });
        </script>
    </head>
    <body class='api'>
        <div class='okd_mid'>
            <div class='okd_top'>
                <?php include 'installations_box.tpl.php'; ?>
                <table cellspacing='0' cellpadding='0'><tr>
                    <td class='apimenu'>
                        <?= $vars['menu'] ?>
                    </td>
                    <td class='article'>

                        <h1>
                            Sign up for an API Key
                            <div class='subh1'>:: <b>Opencaching API</b> Reference</div>
                        </h1>

                        <form id='signup'>
                            <table cellspacing='1px' class='signup'>
                                <tr>
                                    <td>Your application name*:</td>
                                    <td><input type='text' class='text' id='appname' name='appname'></td>
                                </tr>
                                <tr>
                                    <td>Homepage URL:</td>
                                    <td><input type='text' class='text' id='appurl' name='appurl'></td>
                                </tr>
                                <tr>
                                    <td>Contact email address*:</td>
                                    <td><input type='text' class='text' id='email' name='email'></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <input type='checkbox' id='terms' name='terms'>
                                        <label for='terms'>I read and agree to both:</label>
                                        <ul>
                                            <li><?= $vars['site_name'] ?> <?= $vars['data_license_html'] ?>
                                            (these differ across various OC nodes!), and</li>
                                            <li>OKAPI Terms of Use (see below)</li>
                                        </ul>
                                        <input type='button' class='button' id='submit' value='Send me my API Key!'>
                                    </td>
                                </tr>
                            </table>
                            <p>* - required fields</p>

                            <p>If you want to test OKAPI only, use your email address as application name.
                            You must provide a valid email address at which we may contact you later.</p>

                            <p>Application homepage URL is optional and - if given - it may be published in
                            various contextx on the Opencaching site, to allow users to find your
                            application more easily. If provided, it must be a "http(s)://" web address.
                            (In case you're wondering, it has nothing to do with OAuth callback.)</p>
                        </form>

                        <h2>Terms of Use</h2>

                        <p>When using data from <b><?= $vars['site_name'] ?></b> you <b>must</b>
                        attribute it to the author and to <?= $vars['site_name'] ?> in the form
                        specifyed in the <?= $vars['site_name'] ?> <?= $vars['data_license_html'] ?>.
                        OKAPI supports you in doing so by either appending the proper attribution to
                        geocache descriptions or providing it in a separate data field (if you are not
                        showing cache descriptions you must attribute the data using this separate field).
                        Refer to <a href="<?= $vars['site_url'].'okapi/services/caches/geocache.html#arg_attribution_append' ?>">the geocache method</a>
                        for further advice.</p>

                        <p>You <b>must</b> comply with the requirements of the <?= $vars['site_name'] ?>
                        <?= $vars['data_license_html'] ?> when reproducing, displaying, copying,
                        formatting, compiling, or otherwise using Geocaching Data acquired through OKAPI.</p>

                        <p>When possible <?= $vars['site_name'] ?> <b>must</b> be a clickable link
                        to <?= $vars['site_name'] ?> site and if the information being displayed is
                        about a particular geocache, it should link to that geocache's page on
                        <?= $vars['site_name'] ?>.</p>

                        <p><b>You may not:</b></p>

                        <ul>
                            <li>Copy, publish, compile, print, display, format, assemble, or otherwise use Geocaching Data
                            acquired through OKAPI in a manner contrary to <?= $vars['site_name'] ?>
                            <?= $vars['data_license_html'] ?>.</li>

                            <li>Delete, obscure, modify, or in any manner alter any warning, notice, attribution, or link
                            that appears in OKAPI or the served content.</li>

                            <li>Hide, modify, or otherwise obscure your identity, your system's identity, or your user's
                            <?= $vars['site_name'] ?> account information, when using OKAPI.</li>
                        </ul>
                        <div class='issue-comments' issue_id='31'></div>
                    </td>
                </tr></table>
            </div>
            <div class='okd_bottom'>
            </div>
        </div>
    </body>
</html>
