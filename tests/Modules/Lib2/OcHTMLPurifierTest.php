<?php

namespace OcTest\Modules\Lib2;

use OcTest\Modules\AbstractModuleTest;

class OcHTMLPurifierTest extends AbstractModuleTest
{
    /**
     * @var \OcHTMLPurifier
     */
    private $htmlPurifier;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        global $opt;
        $this->htmlPurifier = new \OcHTMLPurifier($opt);
    }

    public function testAllowedHtmlTags()
    {
        // For backward compatibilty of editing existing cache listings, it can be
        // CRITICAL if this test fails.

        $allowed = [
            '<!-- * -->',
            '<a href="test.html" name="custom_test" rel="noopener noreferrer nofollow" target="_blank">test</a>',
            '<abbr>test</abbr>',
            '<acronym>test</acronym>',
            '<address>test</address>',
            '<b>test</b>',
            '<bdo dir="ltr">test</bdo>',
            '<big>test</big>',
            '<blockquote cite="test">test</blockquote>',
            '<br />',
            '<cite>test</cite>',
            '<code>test</code>',
            '<dl><dt>test</dt><dd>test</dd></dl>',
                // includes <dt> and <dd>
            '<del cite="test">test</del>',
            '<dfn>test</dfn>',
            '<div>test</div>',
            '<em>test</em>',
            '<fieldset><legend>test</legend>test</fieldset>',
                // includes <legend>
            '<h1>test</h1>',
            '<h2>test</h2>',
            '<h3>test</h3>',
            '<h4>test</h4>',
            '<h5>test</h5>',
            '<h6>test</h6>',
            '<hr size="10" width="10" />',
            '<i>test</i>',
            '<img align="left" alt="test" border="1" height="10" hspace="10" src="test.png" usemap="custom_map" vspace="10" width="10" />',
            '<ins cite="test">test</ins>',
            '<kbd>test</kbd>',
            '<map name="custom_test"><area alt="test" coords="0,0,1,1" href="test.html" shape="rect" target="_blank" /></map>',
                // includes <area>
            '<ol type="1" compact="compact"><li>test</li></ol>',
            '<p>test</p>',
            '<pre>test</pre>',
            '<q cite="test">test</q>',
            '<samp>test</samp>',
            '<small>test</small>',
            '<span class="test" dir="ltr" id="custom_test" lang="de" style="display:inline;" title="test" xml:lang="de">test</span>',
                // includes global attributes
            '<strong>test</strong>',
            'sub', '<sub>test</sub>',
            'sup', '<sup>test</sup>',
            '<table><caption align="left">test</caption><colgroup span="1"><col align="left" span="1" width="50" /></colgroup><thead><tr><th abbr="test" align="left" bgcolor="#FFFFFF" colspan="1" height="10" nowrap="nowrap" rowspan="1" scope="col" valign="top" width="10">test</th></tr></thead><tfoot><tr><td>test</td></tr></tfoot><tbody><tr align="left" bgcolor="#FFFFFF" valign="top"><td abbr="test" align="left" bgcolor="#FFFFFF" colspan="1" height="10" nowrap="nowrap" rowspan="1" scope="col" valign="top" width="10">test</td></tr></tbody></table>',
                // includes all table child tags
            '<tt>test</tt>',
            '<u>test</u>',
            '<ul compact="compact"><li>test</li></ul>',
            '<var>test</var>',
        ];

        $original = '';
        $purified = '';

        foreach ($allowed as $html) {
            $purifiedHtml = $this->htmlPurifier->purify($html);
            if ($purifiedHtml != $html) {
                $original .= "$html\n";
                $purified .= "$purifiedHtml\n";
            }
        }

        self::assertEquals($original, $purified);
    }

    public function testHtmlTagAIsAllowedAndProtectedWithNoOpener()
    {
        $string = '<a href="http://www.google.de" target="_blank">lorem ipsum</a>';
        $stringProtected = '<a href="http://www.google.de" target="_blank" rel="noreferrer noopener">lorem ipsum</a>';
        $newString = $this->htmlPurifier->purify($string);
        self::assertEquals($stringProtected, $newString);
    }

    public function testRemovedHtmlTags()
    {
        // Tags removed by HTMLPurifier.
        // TinyMCE removed also these tags: <center> <dir> <s>

        // The following tags are either problematic or too new (HTML5) to
        // be sufficiently backward compatible.

        $removed = [
            '<applet code="test.class">*</applet>',
            '<article><header>*</header></article>',
                // includes <header>
            '<aside>*</aside>',
            '<audio controls="controls"><source src="test.mp3" type="audio/mpeg" />*</audio>',
                // includes <source>
            '<base href="test.html" />',
            '<basefont color="#FFFFFF" />',
            '<bdi>*</bdi>',
            '<body>*</body>',
            '<button type="button">*</button>',
            '<canvas id="custom_canvas"></canvas>',
            '<details>*</details>',
            '<dialog open>*</dialog>',
            '<embed src="test" />',
            '<figure><figcaption>*</figcaption></figure>',
            '<footer>*</footer>',
            '<form><input id="custom_s" type="submit" value="submit" /><label for="custum_s">*</label><output name="custom_o" for="custom_s"></output><textarea rows="5" cols="10"></textarea></form>',
                // includes <input> <textarea> <label> <output>
            '<frameset cols="100%"><frame src="test.html"></frame></frameset>',
                // includes <frame>
            '<html><head><body>*</body></html>',
                // includes <head> <title> <meta> <link> <body>
            '<head><title>*</title></head>',
            '<iframe src="test.html"></iframe>',
            '<link rel="stylesheet" type="text/css" href="theme.css" />',
            '<main>*</main>',
            '<mark>*</mark>',
            '<menu type="context"><menuitem label="test" onclick="test"></menuitem></menu>',
                // includes <menuitem>
            '<meta charset="UTF-8" /><link rel="stylesheet" type="text/css" href="theme.css" />',
            '<meter value="1" min="0" max="2">*</meter>',
            '<nav><a href="test.html"></a></nav>|<a href="test.html"></a>',
                // <a> only included for completeness
            '<noframes>*</noframes>',
            '<noscript>*</noscript>',
            '<object width="400" height="400" data="test.swf"><param name="custom_p" value="1" /></object>',
                // includes <param>
            '<picture><source media="(min-width: 650px)" srcset="test.jpg" /><img src="test.jpg" alt="" /></picture>|<img src="test.jpg" alt="" />',
                // includes <source>; <img> only included for completeness
            '<progress value="10" max="100"></progress>',
            '<ruby>*<rt><rp>*</rp>*<rp>*</rp></rt></ruby>',
                // includes <rt> <rp>
            '<script>*</script>',
            '<section>*</section>',
            '<select><optgroup label="test"><option value="test">*</option></optgroup></select>',
                // includes <optgroup> <option>
            '<style>*</style>',
            '<summary>*</summary>',
            '<time>*</time>',
            '<title>*</title>',
            '<ul><li><datalist id="test"><option value="test"></option></datalist></li></ul>|<ul><li></li></ul>',
                // <ul> <li> only included for completeness
            '<video width="320" height="240"><track src="test.vtt" kind="subtitles" /></video>',
                // includes <track>
            '<wbr>*</wbr>',
        ];

        $purified = '';

        foreach ($removed as $html) {
            $parts = explode('|', $html);
            $original = $parts[0];
            $expected = (isset($parts[1]) ? $parts[1] : '');

            $purifiedHtml = $this->htmlPurifier->purify($original);
            $purifiedHtml = str_replace('*', '', $purifiedHtml);
            if ($purifiedHtml != $expected) {
                $purified .= "$purifiedHtml\n";
            }
        }

        self::assertEquals('', $purified);
    }

    public function testAllowedCss()
    {
        // This list is complete as of HTMLPurifier 4.9.3;
        // see vendor/ezyang/htmlpurifier/library/HTMLPurifier/CSSDefinition.php.

        $allowed = [
            'background:#000',
            'background-attachment:fixed',
            'background-color:#fff',
            'background-image:url(&quot;test.gif&quot;)',
            'background-position:center',
            'background-repeat:no-repeat',
            'border:1px solid #000',
            'border-bottom:1px dotted #000',
            'border-bottom-color:#000',
            'border-bottom-style:dotted',
            'border-bottom-width:1px',
            'border-color:#fff',
            'border-collapse:collapse',
            'border-left:1px dashed #000',
            'border-left-color:#000',
            'border-left-style:dashed',
            'border-left-width:1px',
            'border-right:1px double #000',
            'border-right-color:#000',
            'border-right-style:ridge',
            'border-right-width:1px',
            'border-spacing:1px 1px',
            'border-style:solid',
            'border-top:1px double #000',
            'border-top-color:#000',
            'border-top-style:outset',
            'border-top-width:1px',
            'border-width:2px',
            'caption-side:bottom',
            'clear:both',
            'color:#888',
            'display:inline',
            'float:left',
            'font:bold 12px Helvetica, sans-serif',
            'font-family:Helvetica, sans-serif',
            'font-size:12px',
            'font-style:italic',
            'font-variant:normal',
            'font-weight:bold',
            'height:12px',
            'letter-spacing:1px',
            'line-height:1em',
            'list-style:square url(&quot;test.gif&quot;)',
            'list-style-image:url(&quot;test.gif&quot;)',
            'list-style-position:inside',
            'list-style-type:circle',
            'margin:1px 2px 3px 4px',
            'margin-bottom:1em',
            'margin-top:1ex',
            'margin-left:1cm',
            'margin-right:2pt',
            'min-height:10px',
            'max-height:20em',
            'min-width:10ex',
            'max-width:50%',
            'padding:1% 2% 3% 4%',
            'padding-bottom:1em',
            'padding-left:inherit',
            'padding-right:1em',
            'padding-top:0px',
            'table-layout:fixed',
            'text-align:left',
            'text-decoration:none',
            'text-indent:1em',
            'text-transform:uppercase',
            'vertical-align:top',
            'visibility:visible',
            'white-space:nowrap',
            'width:50%',
            'word-spacing:3px',
        ];

        $original = '';
        $purified = '';

        foreach ($allowed as $style) {
            $html = '<div style="' . $style . ';">*</div>';
            $purifiedHtml = $this->htmlPurifier->purify($html);
            if ($purifiedHtml != $html) {
                $original .= "$html\n";
                $purified .= "$purifiedHtml\n";
            }
        }

        self::assertEquals($original, $purified);
    }
}
