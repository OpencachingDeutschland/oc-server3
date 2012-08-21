<!doctype html>
<html lang='en'>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>OKAPI Examples</title>
		<link rel="stylesheet" href="<?= $vars['okapi_base_url'] ?>static/common.css?<?= $vars['okapi_rev'] ?>">
		<link type="text/css" rel="stylesheet" href="<?= $vars['okapi_base_url'] ?>static/syntax_highlighter/SyntaxHighlighter.css"></link>
		<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js'></script>
		<script>
			var okapi_base_url = "<?= $vars['okapi_base_url'] ?>";
		</script>
		<script src='<?= $vars['okapi_base_url'] ?>static/common.js?<?= $vars['okapi_rev'] ?>'></script>
		<script language="javascript" src="<?= $vars['okapi_base_url'] ?>static/syntax_highlighter/shCore.js"></script>
		<script language="javascript" src="<?= $vars['okapi_base_url'] ?>static/syntax_highlighter/shBrushPhp.js"></script>
		<script language="javascript">
			$(function() {
				dp.SyntaxHighlighter.ClipboardSwf = '<?= $vars['okapi_base_url'] ?>static/syntax_highlighter/clipboard.swf';
				dp.SyntaxHighlighter.HighlightAll('code');
			});
		</script>
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

<h1>Examples and libraries</h1>

<p>Here you will find basic examples of OKAPI usage with popular programming languages.</p>

<h2>Are there any client libraries?</h2>

<p>OKAPI <b>does not</b> require you to use any special libraries, usually you will want to
use OKAPI "as is", via basic HTTP requests and responses.</p>
<p>However, some third-party libraries exist and you can use them if you want. With proper
libraries, OKAPI might be easier to use. We give you the list of all libraries we know of.
It's your choice to decide which are "proper".</p>
<ul>
	<li>If you're developing with .NET, you may want to check out
	<a target='_blank' href='http://code.google.com/p/okapi-dotnet-client/'>.NET
	client library</a> by Oliver Dietz.</li>
	<li>(if you've developed your own library and would like to include it here,
	post the details in a comment thread below)</li>
</ul>
<p>You should check with the author of the library before you use it, to make sure it is
up-to-date. If you believe it is not, then keep in mind that learning to use our REST
protocol might be the safest choice.</p>

<div class='issue-comments' issue_id='96'></div>

<h2>PHP Example</h2>

<p><b>Example 1.</b> This will print the number of users in the <?= $vars['site_name'] ?> installation:

<pre name="code" class="php:nogutter:nocontrols">
&lt;?

$json = file_get_contents("<?= $vars['okapi_base_url'] ?>services/apisrv/stats");
$data = json_decode($json);
print "Number of <?= $vars['site_name'] ?> users: ".$data->user_count;

?>
</pre>

<p><b>Example 2.</b> This will print the codes of some nearest unfound caches:</p>

<pre name="code" class="php:nogutter:nocontrols">
&lt;?

/* Enter your OKAPI's URL here. */
$okapi_base_url = "http://opencaching.pl/okapi/";

/* Enter your Consumer Key here. */
$consumer_key = "YOUR_KEY_HERE";

/* Username. Caches found by the given user will be excluded from the results. */
$username = "USERNAME_HERE";

/* Your location. */
$lat = 54.3;
$lon = 22.3;

/* 1. Get the UUID of the user. */
$json = @file_get_contents($okapi_base_url."services/users/by_username".
	"?username=".$username."&amp;fields=uuid&amp;consumer_key=".$consumer_key);
if (!$json)
	die("ERROR! Check your consumer_key and/or username!\n");
$user_uuid = json_decode($json)->uuid;
print "Your UUID: ".$user_uuid."\n";
	
/* 2. Search for caches. */
$json = @file_get_contents($okapi_base_url."services/caches/search/nearest".
	"?center=".$lat."|".$lon."&amp;not_found_by=".$user_uuid."&amp;limit=5".
	"&amp;consumer_key=".$consumer_key);
if (!$json)
	die("ERROR!");
$cache_codes = json_decode($json)->results;

/* Display them. */
print "Five nearest unfound caches: ".implode(", ", $cache_codes)."\n";

?>
</pre>

<p>Please note that the above examples use very simple error checking routines.
If you want to be "professional", you should catch HTTP 400 Responses, read their
bodies (OKAPI error messages), and deal with them more gracefully.</p>

<h2>JavaScript Example</h2>

<p>It is possible to access OKAPI directly from user's browser, without the
need for server backend. OKAPI allows <a href='http://en.wikipedia.org/wiki/XMLHttpRequest#Cross-domain_requests'>Cross-domain
XHR requests</a>. You can also use <a href='http://en.wikipedia.org/wiki/JSONP'>JSONP</a> output format.
There are some limitations of both these techniques though.</p>

<p>This example does the following:</p>
<ul>
	<li>Pulls the <a href='<?= $vars['okapi_base_url'] ?>services/apisrv/installations.html'>list of all OKAPI installations</a>
	from one of the OKAPI servers and displays it in a select-box. Note, that this method does not
	require Consumer Key (Level 0 Authentication).</li>
	<li>Asks you to share your location (modern browser can do that).</li>
	<li>Retrieves a list of nearest geocaches. (This time, it uses the Consumer Key you have to supply.)</li>
</ul>

<p><a href='<?= $vars['okapi_base_url'] ?>static/examples/javascript_nearest.html' style='font-size: 130%; font-weight: bold'>Run this example</a></p>

<h2>Comments</h2>

<div class='issue-comments' issue_id='36'></div>

					</td>
				</tr></table>
			</div>
			<div class='okd_bottom'>
			</div>
		</div>
	</body>
</html>
