{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}

<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/misc/32x32-tools.png" style="margin-right: 10px;" width="32" height="32" alt="" />Erlaubte HTML-Tags und Attribute
</div>
<div class="content-txtbox-noshade" style="padding-right: 25px;">

	<p>Alle hier nicht aufgeführten Tags und Attribute werden gefiltert. 
	Gegebenenfalls wird die Liste erweitert oder um das eine oder andere Element gekürzt. 
	Alle bereits eingestellten Beschreibungen bleiben davon jedoch unberührt.</p>
	<p><i>Kursiv</i> wiedergegebene Tags und Attribute werden in <a href="https://de.wikipedia.org/wiki/HTML5" target="_blank">HTML5</a> nicht mehr unterstützt und sollten vermieden werden.<br /><br /></p>

	<div class="content2-container bg-blue02">
	  <p class="content-title-noshade-size2" style="margin:0 !important">&nbsp;Tags</p>
	</div>

	<p><b>Die folgenden HTML-Tags sind erlaubt:</b></p>

	<p class="indent">!--, a, abbr, <i>acronym</i>, address, area, article<sup>1</sup>, aside<sup>1</sup>, b, bdo, <i>big</i>, blockquote, br, caption, cite, code, col, colgroup, dd, del, dfn, div, dl, dt, em, fieldset, <i>font</i><sup>1</sup>, h1, h2, h3, h4, h5, h6, header<sup>1</sup>, hr, i, img, ins, kbd, legend, li, map, ol, p, pre, q, samp, small, span, strike<sup>1</sup>, strong, sub, sup, table, tbody, td, th, thead, tfoot, tr, <i>tt</i>, u, ul, var<br />
		
	<p><b>Ersatz für nicht erlaubte oder veraltete Tags:</b></p>
	<p class="indent">
		<span class="html_replacetags">acronym</span>  &rarr; &nbsp; abbr<br />
		<span class="html_replacetags">big</span>      &rarr; &nbsp; span style="font-size:larger"<br />
		<span class="html_replacetags">center</span>   &rarr; &nbsp; p style="text-align:center"<br />
		<span class="html_replacetags">s</span>        &rarr; &nbsp; span style="text-decoration:line-through"<br />
		<span class="html_replacetags">tt</span>       &rarr; &nbsp; code
		<br />
	</p>

	<p>
		<sup>1</sup> Wird beim Speichern in andere Elemente umgewandelt.<br />
		<br />
	</p>

	<div class="content2-container bg-blue02">
	  <p class="content-title-noshade-size2" style="margin:0 !important">&nbsp;Attribute</p>
	</div>

	<p><b>Die folgenden allgemeinen HTML-Attribute sind erlaubt:</b></p>

	<p class="indent">class, dir, id<sup>2</sup>, lang, style, title</p>

	<p><b>Die folgenden speziellen HTML-Attribute sind erlaubt:</b></p>

	<p class="indent">
		<span class="html_attributes">a</span>           href, <i>name</i><sup>2</sup>, target='_blank'<br />
		<span class="html_attributes">area</span>        alt, coords, href, shape, target='_blank'<br />
		<span class="html_attributes">bdo</span>         dir<br />
		<span class="html_attributes">blockquote</span>  cite<br />
		<span class="html_attributes">col</span>         <i>align</i>, span, <i>width</i><br />
		<span class="html_attributes">colgroup</span>    span<br />
		<span class="html_attributes">del</span>         cite<br />
		<span class="html_attributes"><i>font</i></span> color, size<br />
		<span class="html_attributes">hr</span>          <i>size</i>, <i>noshade</i><br />
		<span class="html_attributes">img</span>         <i>align</i>, alt, <i>border</i>, height, <i>hspace</i>, src, usemap, <i>vspace</i>, width<br />
		<span class="html_attributes">ins</span>         cite<br />
		<span class="html_attributes">map</span>         name<sup>2</sup><br />
		<span class="html_attributes">ol</span>          <i>compact</i>, type<br />
		<span class="html_attributes">q</span>           cite<br />
		<span class="html_attributes">table</span>       <i>align</i>, <i>bgcolor</i>, <i>border</i>, <i>cellpadding</i>, <i>cellspacing</i>, <i>frame</i>, <i>rules</i>, <i>summary</i>, <i>width</i><br />
		<span class="html_attributes">td, th</span>      <i>abbr</i>, <i>align</i>, <i>bgcolor</i>, colspan, <i>height</i>, <i>nowrap</i>, rowspan, <i>scope</i>, <i>valign</i>, <i>width</i><br />
		<span class="html_attributes">tr</span>          <i>align</i>, <i>bgcolor</i>, <i>valign</i><br />
		<span class="html_attributes">ul</span>          <i>compact</i><br />		
	</p>

	<p><b>Ersatz für nicht erlaubte oder veraltete Attribute:</b></p>
	<p class="indent">
		<span class="html_replaceattrs">align</span>    &rarr; &nbsp; style="text-align:...; vertical-align:..."<br />
		<span class="html_replaceattrs">bgcolor</span>  &rarr; &nbsp; style="background-color:..."<br />
		<span class="html_replaceattrs">border</span>   &rarr; &nbsp; style="border:..."<br />
		<span class="html_replaceattrs">color</span>    &rarr; &nbsp; style="text-color:..."<br />
		<span class="html_replaceattrs">hspace</span>   &rarr; &nbsp; style="margin-left:...; margin-right:..."<br />
		<span class="html_replaceattrs">name</span>     &rarr; &nbsp; id="..."<br />
		<span class="html_replaceattrs">vspace</span>   &rarr; &nbsp; style="margin-top:...; margin-bottom:..."<br />
		<span class="html_replaceattrs">width</span>    &rarr; &nbsp; style="width:..."<br />
	</p>

	<p>
		<sup>2</sup> Die IDs bzw. Namen müssen mit <code>custom_</code> beginnen.
		<br /><br />
	</p>

</div>
