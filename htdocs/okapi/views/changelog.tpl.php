<!doctype html>
<html lang='en'>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title>OKAPI Changelog</title>
        <link rel="stylesheet" href="<?= $vars['okapi_base_url'] ?>static/common.css?<?= $vars['okapi_rev'] ?>">
        <link rel="icon" type="image/x-icon" href="<?= $vars['okapi_base_url'] ?>static/favicon.ico">
        <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js'></script>
        <script>
            var okapi_base_url = "<?= $vars['okapi_base_url'] ?>";
            $(function() {
                $('h2').each(function() {
                    $('#toc').append($("<div></div>").append($("<a></a>")
                        .text($(this).text()).attr("href", "#" + $(this).attr('id'))));
                });
            });
        </script>
        <script src='<?= $vars['okapi_base_url'] ?>static/common.js?<?= $vars['okapi_rev'] ?>'></script>
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
                        <div class="floaticonlink">
                            <a href="changelog_feed"><img src="static/rss-feed.svg" width="32px" /></a>
                        </div>

                        <h1>Changes to the OKAPI interface or administration</h1>

                        <?php if (!$vars['changes']['available']) { ?>
                        <p><em>The Changelog is currently not available.</em></p>
                        <?php } else { ?>

                        <p>Changes to the interface are always backward compatible.
                        You need not to update your applications after any change.
                        But there may be new <b>recommendations</b> (indicated by
                        bold text) on how to use OKAPI methods.</p>

                        <?php
                        $br = '';
                        foreach ($vars['changes'] as $type => $changes) {
                            if (count($changes)) {
                                if ($type == 'unavailable') {
                                    echo "<p>The following changes are not available yet at " . $vars['site_name'] . ":</p>";
                                    $br = '<br />';
                                } else {
                                    echo "<p>".$br."The following changes are available at " . $vars['site_name'] . ":</p>";
                                } ?>

                                <table cellspacing='1px' class='changelog'>
                                    <tr>
                                        <th>Version</th>
                                        <th>Date</th>
                                        <th>Change</th>
                                    </tr>
                                <?php foreach($changes as $change) { ?>
                                    <tr id="v<?= $change['version'] ?>">
                                        <td><a href="https://github.com/opencaching/okapi/tree/<?= $change['commit'] ?>"><?= $change['version'] ?></a></td>
                                        <td><?= substr($change['time'], 0, 10) ?></td>
                                        <td><?= ($change['type'] == 'bugfix' ? 'Fixed: ' : '') . $change['comment'] ?></td>
                                    </tr>
                                <?php } ?>
                                </table>
                            <?php } ?>
                        <?php } ?>

                        <br />
                        <p>This list shows only changes that are considered to be
                        relevant for developers and site admins. Please consult the
                        <a href="https://github.com/opencaching/okapi/commits/master">Git log</a>
                        for a complete history, including older changes.</p>

                        <p>OKAPI was started in August 2011 at the OCPL code branch,
                        and it was deployed to the OCDE branch in April 2013.</p>

                        <?php } ?>

                        <h2 id='comments'>Comments</h2>

                        <div class='issue-comments' issue_id='407'></div>

                    </td>
                </tr></table>
            </div>
            <div class='okd_bottom'>
            </div>
        </div>
    </body>
</html>
