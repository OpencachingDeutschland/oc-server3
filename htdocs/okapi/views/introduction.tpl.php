<!doctype html>
<html lang='en'>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title>OKAPI - Opencaching API</title>
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
                <?php include 'installations_box.tpl.php'; ?>
                <table cellspacing='0' cellpadding='0'><tr>
                    <td class='apimenu'>
                        <?= $vars['menu'] ?>
                    </td>
                    <td class='article'>

<h1>
    The OKAPI Project
    <div class='subh1'>:: <b>Opencaching API</b> Reference</div>
</h1>

<p><b>OKAPI</b> is a publically available
<a href='http://en.wikipedia.org/wiki/Application_programming_interface'>API</a>
for "National Opencaching" sites (also known as Opencaching National Nodes).</p>

<ul>
    <li>It provides OC site with a set of useful well-documented API methods,</li>
    <li>Allows external developers to easily <b>read public</b> Opencaching data,</li>
    <li>Allows <b>read and write private</b> (user-related) data with OAuth Authentication.</li>
</ul>

<p>The project has grown to become a standard and common API for all National
Opencaching.<i>xx</i> sites. This OKAPI installation provides services for the
<a href='<?= $vars['site_url']; ?>'><?= $vars['site_url']; ?></a> site.
Here is the list of other OKAPI installations:</p>

<ul>
    <?php foreach ($vars['installations'] as $inst) { ?>
        <li>
            <?= $inst['site_name'] ?><?php if ($inst['site_name'] == "Opencaching.DE") { print "*"; } ?>
            - <a href='<?= $inst['okapi_base_url'] ?>'><?= $inst['okapi_base_url'] ?></a>
        </li>
    <?php } ?>
</ul>

<p>Other links you might want to check out:</p>

<ul>
    <li>OKAPI Project Homepage - <a href='https://github.com/opencaching/okapi/'>https://github.com/opencaching/okapi/</a></li>
    <li>OKAPI News blog - <a href='http://opencaching-api.blogspot.com/'>http://opencaching-api.blogspot.com/</a></li>
</ul>

<p>* - Opencaching.DE includes other sites - Opencaching.IT and Opencaching.FR - which are in fact the one site visible
on multiple domains. All three share one database, so you can access all their
data through Opencaching.DE OKAPI installation.</p>

<div class='issue-comments' issue_id='28'></div>

<h2 id='howto'>How can I use OKAPI?</h2>

<p>We assume that you're a software developer and you know the basics.</p>

<p>OKAPI is a set of simple web services. Basicly, you make a proper HTTP
request, and you receive a JSON-formatted response, that you may parse and use
within your application.</p>

<p><b>Example.</b> Click the following link to run a method that prints out the
list of all available methods:</p>

<ul>
    <li>
        <p><a href='<?= $vars['site_url'] ?>okapi/services/apiref/method_index'><?= $vars['site_url'] ?>okapi/services/apiref/method_index</a></p>
        <p>Note: You might need to install a proper browser addon (like
        <a href='https://chrome.google.com/webstore/detail/chklaanhfefbnpoihckbnefhakgolnmc'>this one</a>
        or <a href='https://addons.mozilla.org/en-US/firefox/addon/jsonview/'>this one</a>)
        in order to view formatted JSON data directly in your browser.</p>
    </li>
</ul>

<p>You've made your first OKAPI request! This method was a simple one though.
It didn't require any arguments and it didn't require you to use a Consumer Key.
Other methods are more complex and require you to use
<a href='<?= $vars['site_url'] ?>okapi/signup.html'>your own API key</a>.</p>

<h2 id='auth_levels'>Authentication Levels</h2>

<p>Each OKAPI method has a <b>minimum authentication level</b>.</p>

<p>This means, that if you want to call a method which requires "Level 1"
authentication, you have to use "Level 1" authentication <b>or higher</b>
("Level 2" or "Level 3" will also work).</p>

<p><b>Important:</b> Most developers will only need to use "Level 1"
authentication and don't have learn OAuth.</p>

<ul>
    <li>
        <p><b>Level 0.</b> Anonymous. You may call such methods with no extra
        authentication-related arguments.</p>

        <p><code>some_method?arg=44</code></p>
    </li>
    <li>
        <p><b>Level 1.</b> Simple Consumer Authentication. You must call this
        method with <b>consumer_key</b> argument and provide the key which has
        been generated for your application on the
        <a href='<?= $vars['site_url'] ?>okapi/signup.html'>Sign up</a> page.</p>

        <p><code>some_method?arg=44&consumer_key=a7Lkeqf8CjNQTL522dH8</code></p>
    </li>
    <li>
        <p><b>Level 2.</b> OAuth Consumer Signature. You must call this method
        with proper OAuth Consumer signature (based on your Consumer Secret).</p>

        <p><code>some_method<br>
        ?arg=44<br>
        &oauth_consumer_key=a7Lkeqf8CjNQTL522dH8<br>
        &oauth_nonce=1987981<br>
        &oauth_signature_method=HMAC-SHA1<br>
        &oauth_timestamp=1313882320<br>
        &oauth_version=1.0<br>
        &oauth_signature=mWEpK2e%2fm8QYZk1BMm%2fRR74B3Co%3d</code></p>
    </li>
    <li>
        <p><b>Level 3.</b> OAuth Consumer+Token Signature. You must call this
        method with proper OAuth Consumer+Token signature (based on both
        Consumer Secret and Token Secret).</p>

        <p><code>some_method<br>
        ?arg=44<br>
        &oauth_consumer_key=a7Lkeqf8CjNQTL522dH8<br>
        &oauth_nonce=2993717<br>
        &oauth_signature_method=HMAC-SHA1<br>
        &oauth_timestamp=1313882596<br>
        &oauth_token=AKQbwa28Afp1YvQAqSyK<br>
        &oauth_version=1.0<br>
        &oauth_signature=qbNiWkUS93fz6ADoNcjuJ7psB%2bQ%3d</code></p>
    </li>
</ul>

<div class='issue-comments' issue_id='38'></div>

<h2 id='http_methods'>GET or POST?</h2>

<p>Whichever you want. OKAPI will treat GET and POST requests as equal. You may
also use the HTTP <code>Authorization</code> header for passing OAuth
arguments. OKAPI does not support other HTTP request types (such as PUT or
DELETE).</p>

<h2 id='html'>A warning about HTML fields</h2>

<p>Many of the fields returned in OKAPI responses are formatted in HTML.
However, most of these fields come directly from the underlying Opencaching
database. Currently, these fields are <b>not validated by OKAPI</b>. They
<b>should</b> be validated by the Opencaching site itself (prior to inserting
it to the database), but we cannot guarantee that they will be. And you
shouldn't count on it too.</p>

<p>You must assume that our HTML content may contain anything, including
invalid HTML markup, tracking images (pixels), or even
<a href='http://en.wikipedia.org/wiki/Cross-site_scripting'>XSS vectors</a>.
This also applies to the descriptions included in the GPX files.</p>

<h2 id='common-formatting'>Common formatting parameters</h2>

<p>Most of the methods return simple objects, such as lists and dictionaries of
strings and integers. We may format such objects for you in several ways. If
you want to change the default (JSON) then you should include <i>common
formatting parameters</i> in your request:</p>

<ul>
    <li>
        <p><b>format</b> - name of the format in which you'd like your result
        to be returned in. Currently supported output formats include:</p>

        <ul>
            <li>
                <p><b>json</b> - <a href='http://en.wikipedia.org/wiki/JSON'>JSON</a>
                format (default),</p>
            </li>
            <li>
                <b>jsonp</b> - <a href='http://en.wikipedia.org/wiki/JSONP'>JSONP</a>
                format (if you choose this one then you have to specify the
                <b>callback</b> parameter too),
            </li>
            <li class='deprecated'>
                <b>xmlmap</b> - deprecated (<a href='https://github.com/opencaching/okapi/issues/128'>why?</a>),
            </li>
            <li>
                <b>xmlmap2</b> - XML format. This is produced by mapping JSON
                data types to XML elements.
            </li>
        </ul>
    </li>
    <li>
        <b>callback</b> - (when using JSONP output format) name of the
        JavaScript function to be executed with the result as its parameter.
    </li>
</ul>

<p>Some methods expose formatting of their own, for example, they may return a
JPEG or GPX file. Such methods do not accept <i>common formatting
parameters</i>.</p>

<p><b><u>Important:</u></b> Almost all of the returned data types are
<b>extendible</b>. This means, that (in future) they <b>may contain data that
currently they don't</b>. Such data will be included in backward-compatible
manner, but still you should remember about it in some cases (i.e. when
iterating over attributes of an object). This additional data may appear as
extra elements in GPX files or extra keys in JSON responses. Your software
<b>must ignore</b> such occurrences if it doesn't understand them!</p>

<div class='issue-comments' issue_id='30'></div>


<h2 id='oauth'>OAuth Dance URLs</h2>

<p>If you want to use <b>Level 3</b> methods, you will first have to perform
"the OAuth dance" (series of method calls and redirects which provide you with
an Access Token).</p>

<p>The three OAuth request URLs defined in the
<a href='http://oauth.net/core/1.0a/'>OAuth specification</a> are:</p>

<ul>
    <li>
        <a href='<?= $vars['site_url'] ?>okapi/services/oauth/request_token'><?= $vars['site_url'] ?>okapi/services/oauth/request_token</a>
        (documentation <a href='<?= $vars['site_url'] ?>okapi/services/oauth/request_token.html'>here</a>)
    </li>
    <li>
        <a href='<?= $vars['site_url'] ?>okapi/services/oauth/authorize'><?= $vars['site_url'] ?>okapi/services/oauth/authorize</a>
        (documentation <a href='<?= $vars['site_url'] ?>okapi/services/oauth/authorize.html'>here</a>)
    </li>
    <li>
        <a href='<?= $vars['site_url'] ?>okapi/services/oauth/access_token'><?= $vars['site_url'] ?>okapi/services/oauth/access_token</a>
        (documentation <a href='<?= $vars['site_url'] ?>okapi/services/oauth/access_token.html'>here</a>)
    </li>
</ul>

<p>Things you should pay attention to:</p>

<ul>
    <li>
        <p>The <b>oauth_callback</b> argument of the <b>request_token</b>
        method is <b>required</b>.</p>

        <p>As the OAuth 1.0a specification states, it should be set to
        "<i>oob</i>" or a callback URL (this usually starts with http:// or
        https://, but you can use any other myapp:// scheme).</p>

        <p>For most OAuth client libraries, you just should provide
        "<i><?= $vars['site_url'] ?>okapi/services/oauth/request_token?oauth_callback=oob</i>"
        as the request_token URL, to get it started. Later, probably you'd want
        to switch "oob" to something more useful.</p>
    </li>
    <li>
        <p>The <b>oauth_verifier</b> argument of the <b>access_token</b> method
        is also <b>required</b>.</p>

        <p>When user authorizes your application, he will receive a PIN code
        (OAuth verifier). You have to use this code to receive your Access
        Token.</p>
    </li>
    <li>
        <p><b>Access Tokens do not expire</b> (but can be revoked). This means,
        that once the user authorizes your application, you receive a "lifetime
        access" to his/her account. User may still <b>revoke access</b> to his
        account from your application - when this happens, you will have to
        redo the authorization dance.</p>
    </li>
</ul>

<div class='issue-comments' issue_id='29'></div>

<h2 id='errors'>Advanced error handling</h2>

<p>Basic rules apply:</p>

<ul>
    <li>If all goes well, OKAPI will respond with a <b>HTTP 200</b> status.</li>

    <li>If there is something wrong with your request, you will get a <b>HTTP
    4xx</b> response. The body will contain a JSON object describing the error
    (see below). These kind of responses should trigger some kind of an
    exception inside your application.</li>

    <li>If something goes wrong <b>on our part</b>, you will get a <b>HTTP
    5xx</b> response. Usually you should treat such errors as other I/O errors
    (e.g. display a "No connection, try later" notice). We will try to fix such
    errors as soon as possible.</li>

    <li>Sometimes, due to invalid server configuration, you may receive <b>HTTP
    200</b> instead of <b>HTTP 500</b>. We know that's "unprofessional", but we
    cannot guarantee that all OC servers are configured properly
    (<a href='https://github.com/opencaching/okapi/issues/293'>example</a>).
    If you get <b>HTTP 200</b> <u>and</u> you cannot parse the server response,
    you should treat it as <b>HTTP 500</b>.</li>
</ul>

<p>Every <b>HTTP 4xx</b> error will contain an additional description in the
response body. This description will be <b>always</b> formatted in JSON
(regardless of the <b>format</b> parameter you might have used in your
request). You can use this information to pinpoint the source of the error.</p>

<p>The error response is a dictionary with a single <b>error</b> key. Its value
contains <b>at least</b> the following keys:</p>

<ul>
    <li><b>developer_message</b> - description of the error,</li>
    <li>
        <b>reason_stack</b> - a list of keywords which depicts the exception's
        position in our exception class hierarchy (see below for valid values),
    </li>
    <li>
        <b>status</b> - HTTP status code (the same which you'll get in response
        HTTP header),
    </li>
    <li>
        <b>more_info</b> - url pointing to a more detailed description of the
        error (or, more probably, to the page you're now reading).
    </li>
</ul>

<p>Depending on the values on the <b>reason_stack</b>, the <b>error</b>
dictionary may contain additional keys. Currently possible values of the
<b>reason_stack</b> include:</p>

<ul>
    <li>
        <p><b>["bad_request"]</b> - you've made a bad request.

        <p>Subclasses:</p>

        <ul>
            <li>
                <p><b>[ "bad_request", "missing_parameter"]</b> - you didn't
                supply a required parameter. Extra keys:</p>

                <ul>
                    <li><b>parameter</b> - the name of the missing parameter.</li>
                </ul>
            </li>
            <li>
                <p><b>[ "bad_request", "invalid_parameter"]</b> - one of your
                parameters has an invalid value. Extra keys:</p>

                <ul>
                    <li><b>parameter</b> - the name of the parameter,</li>
                    <li>
                        <b>whats_wrong_about_it</b> - a string, pretty
                        self-explanatory,
                    </li>
                </ul>
            </li>
        </ul>
    </li>
    <li>
        <p><b>["invalid_oauth_request"]</b> - you've tried to use OAuth, but
        your request was invalid.</p>

        <p>Subclasses:</p>

        <ul>
            <li>
                <p><b>[ "invalid_oauth_request", "unsupported_oauth_version"]</b>
                - you have tried to use unsupported OAuth version (OKAPI
                requires OAuth 1.0a).</p>
            </li>
            <li>
                <p><b>[ "invalid_oauth_request", "missing_parameter"]</b> - you
                didn't supply a required parameter. Extra keys:</p>

                <ul>
                    <li><b>parameter</b> - the name of the missing parameter.</li>
                </ul>
            </li>
            <li>
                <p><b>[ "invalid_oauth_request", "unsupported_signature_method"]</b>
                - you tried to use an unsupported OAuth signature method (OKAPI
                requires HMAC-SHA1).</p>
            </li>
            <li>
                <p><b>[ "invalid_oauth_request", "invalid_consumer"]</b> - your
                consumer does not exist.</p>
            </li>
            <li>
                <p><b>[ "invalid_oauth_request", "invalid_token"]</b> - your
                token does not exist. This error is pretty common! Your token
                may have expired (in case of request tokens) or may have been
                revoked (in case of access tokens). You should ask your user to
                redo the authorization dance.</p>
            </li>
            <li>
                <p><b>[ "invalid_oauth_request", "invalid_signature"]</b> -
                your request signature was invalid.</p>
            </li>
            <li>
                <p><b>[ "invalid_oauth_request", "invalid_timestamp"]</b> - you
                used a timestamp which was too far off, compared to the current
                server time. This error is pretty common, especially if your
                app is for mobile phones! You may ask your user to fix his
                clock, <b><u>or</u></b> you could use the provided extra keys
                to adjust your <b>oauth_timestamp</b> parameter yourself. Extra
                keys:</p>

                <ul>
                    <li>
                        <b>yours</b> - UNIX timestamp you have supplied (this
                        used to be a string, but now it is being casted to an
                        integer, see
                        <a href='https://github.com/opencaching/okapi/issues/314'>here</a>),
                    </li>
                    <li><b>ours</b> - UNIX timestamp on our server,</li>
                    <li>
                        <b>difference</b> - the difference, in seconds (to be
                        added to your clock),
                    </li>
                    <li><b>threshold</b> - the maximum allowed difference.</li>
                </ul>
            </li>
            <li>
                <p><b>[ "invalid_oauth_request", "nonce_already_used"]</b> -
                most probably, you have supplied the same request twice (user
                double-clicked something?). Or, you have some error in the
                nonce generator in your OAuth client.</p>
            </li>
        </ul>
    </li>
</ul>

<p>In most cases, you should be fine with catching just the following (order
significant):</p>

<ul>
    <li><b>["invalid_oauth_request", "invalid_token"]</b> (reauthorize),</li>
    <li><b>["invalid_oauth_request", "invalid_timestamp"]</b> (adjust the timestamp),</li>
    <li><i>any other 4xx status exception</i> (send yourself a bug report).</li>
    <li>
        All other errors - including HTTP 5xx responses, JSON parsing errors,
        timeouts etc. - should be treated as connection errors (ask the user to
        try later).
    </li>
</ul>

<p><b>Important backward compatibility note:</b> If Z is a subclass of X, then
it will forever stay a subclass of X. However, class X won't necessarilly stay
Z's <i>direct</i> parent. This means that <b>["x", "z"]</b> may become <b>["x",
"y", "z"]</b> or even <b>["w", "x", "y", "z"]</b> one day. In other words,
instead writing <code>if (reason_stack == ["x", "z"]) { ... }</code> you should
rather use <code>if ("z" in reason_stack) { ... }</code>.</p>


<div class='issue-comments' issue_id='117'></div>


<h2 id='participate'>How can I participate in OKAPI development?</h2>

<p>OKAPI is Open Source and everyone is welcome to participate in the development.</p>

<p>We have our <a href='https://github.com/opencaching/okapi/issues/'>Issue tracker</a>.
You can use it to contact us!<br>You may also contact some of
<a href='https://github.com/opencaching/okapi/graphs/contributors'>the developers</a>
directly, if you want.</p>

<p>Visit <b>project homepage</b> for details:
<a href='https://github.com/opencaching/okapi/'>https://github.com/opencaching/okapi/</a></p>


<h2 id='method_index'>List of available methods</h2>

<p>OKAPI web services (methods) currently available on this server:</p>

<ul>
    <?php foreach ($vars['method_index'] as $method_info) { ?>
        <li><a href='<?= $vars['site_url']."okapi/".$method_info['name'].".html" ?>'><?= $method_info['name'] ?></a> - <?= $method_info['brief_description'] ?></li>
    <?php } ?>
</ul>


                    </td>
                </tr></table>
            </div>
            <div class='okd_bottom'>
            </div>
        </div>
    </body>
</html>
