<!doctype html>
<html lang='en'>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title>OKAPI Examples</title>
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

<h1>Examples, libraries and tools</h1>

<div id='toc'></div>

<h2 id='tools'>Tools</h2>

If you're just getting to know OKAPI, you might want to check out
<a href='https://github.com/wrygiel/okapi-browser'>OKAPI Browser</a>
(<a href='https://raw.github.com/wrygiel/okapi-browser/master/extras/screenshot.png'>screenshot</a>),
a basic OAuth Console for OKAPI methods. It is an open-source project. You can
<a href='https://github.com/wrygiel/okapi-browser'>fork it</a> on GitHub or simply
<a href='http://opencaching.pl/download/okapi-browser/'>install it here</a>.

<h2 id='libraries'>Are there any client libraries?</h2>

<p>OKAPI <b>does not</b> require you to use any special libraries. You should use
OKAPI "as is", via basic HTTP requests and responses.</p>

<p>However, if you know of any useful third-party libraries, then let us know.
We may choose to post a link somewhere here.</p>

<div class='issue-comments' issue_id='96'></div>

<h2 id='php1'>PHP Example 1 - simple query</h2>

<p>Please note that the examples below use extremely simplied error checking
routines. If you want to be "professional", then you should catch all HTTP 400
Responses, read their bodies (OKAPI error messages), and deal with them more
gracefully.</p>

<p>This will print the number of users in the <?= $vars['site_name'] ?> installation:</p>

<script src="https://gist.github.com/4231796.js?file=users.php"></script>

<h2 id='php2'>PHP Example 2 - search for nearest geocaches</h2>

<p>This will print the codes of some nearest unfound caches:</p>

<script src="https://gist.github.com/4231824.js?file=nearest_unfound.php"></script>

<h2 id='js1'>JavaScript Example</h2>

<p>It is possible to access OKAPI directly from user's browser, without the
need for server backend. OKAPI allows
<a href='http://en.wikipedia.org/wiki/XMLHttpRequest#Cross-domain_requests'>Cross-domain
XHR requests</a>. You can also use
<a href='http://en.wikipedia.org/wiki/JSONP'>JSONP</a> output format.
There are some limitations of both these techniques though.</p>

<p>This example does the following:</p>

<ul>
    <li>
        Pulls the
        <a href='<?= $vars['okapi_base_url'] ?>services/apisrv/installations.html'>list of all OKAPI installations</a>
        from one of the OKAPI servers and displays it in a select-box. Note,
        that this method does not require Consumer Key (Level 0 Authentication).
    </li>
    <li>Asks you to share your location (modern browser can do that).</li>
    <li>
        Retrieves a list of nearest geocaches. (This time, it needs to use a
        Consumer Key which you will have to supply.)
    </li>
</ul>

<p><a href='<?= $vars['okapi_base_url'] ?>static/examples/javascript_nearest.html' style='font-size: 130%; font-weight: bold'>Run this example</a></p>

<h2 id='cs1'>C# Example</h2>

<p><a href='https://github.com/wrygiel/okapi-browser'>OKAPI Browser</a>
(already mentioned in the Tools section) is an open-source project. Written in C#.NET, uses
the <a href='<?= $vars['okapi_base_url'] ?>services/apisrv/installations.html'>apisrv</a>
and <a href='<?= $vars['okapi_base_url'] ?>services/apiref/method_index.html'>apiref</a>
modules to dynamically retrieve the current list of OKAPI installations and methods.
<a href='https://github.com/wrygiel/okapi-browser'>Get the source</a> or
<a href='http://opencaching.pl/download/okapi-browser/'>try it</a> first.</p>

<h2 id='comments'>Comments</h2>

<div class='issue-comments' issue_id='36'></div>

                    </td>
                </tr></table>
            </div>
            <div class='okd_bottom'>
            </div>
        </div>
    </body>
</html>
