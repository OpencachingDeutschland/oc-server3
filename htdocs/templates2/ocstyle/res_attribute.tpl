{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
 {* OCSTYLE *}
{*
  id				attribute id

  icon      icon name

  state			ATTRIB_NO           undef / greyed
						ATTRIB_SELECTED     selected
						ATTRIB_UNSELECTED   not selected

  name			attribute name
  
  html_desc	attribute description (html code)
  
  hoverfunc	true/false

  color

	onmousedown := javascript funtion name to call on mouse down (to change attribute icons)
	inputprefix := name/id-prefix of hidden inputbox with state
*}
{capture name=filename}{strip}
	resource2/{$opt.template.style}/images/attributes/
	{$icon}
  {if $state==2}{* ATTRIB_UNSELECTED *}
    -no.png
  {elseif $state==3}{* ATTRIB_UNDEF *}
    -disabled.png
  {else}{* ATTRIB_SELECTED *}
		.png
  {/if}
{/strip}{/capture}

<img {if $inputprefix!=''}id="img{$inputprefix}{$id}"{/if}
     src="{$smarty.capture.filename}" 
     border="0" 
     onmouseover="Tip('{$html_desc|escapejs}', TITLE, '{$name|escapejs}', TITLEBGCOLOR, '{$color|escapejs}', TITLEFONTCOLOR, '#000000', BGCOLOR, '#FFFFFF', BORDERCOLOR, '{$color|escapejs}', CLICKCLOSE, true, DELAY, 0, FADEIN, false, FADEOUT, false, FONTCOLOR, '#000080', WIDTH, 500)" 
     onmouseout="UnTip()" 
     {if $onmousedown!=''}onmousedown="{$onmousedown}({$id}, '{$icon}')"{/if}
     />

{if $inputprefix!=''}
	<input type="hidden" id="{$inputprefix}{$id}" name="{$inputprefix}{$id}" value="{$state}" />
{/if}