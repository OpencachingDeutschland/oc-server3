{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}

{* common Javascript code for including a switchable HTML/TinyMCE editor in templates;
   currently used for user profile descriptions and cache list descriptions

   TODO: use also for cache descriptions and logs, while discarding plaintext option *} 

{literal}
<script type="text/javascript">
<!--
	/*
		2 = direct HTML input
		3 = Wysywyg HTML editor
	*/
	var use_tinymce = 0;
	{/literal}
	var descMode = {$descMode};

	{literal}
	document.getElementById("scriptwarning").firstChild.nodeValue = "";

	// set descMode to 2 ... when editor is loaded, set back to 3
	descMode = 2;

	document.getElementById("descMode").value = descMode;
	mnuSetElementsNormal();


	function postInit()
	{
		descMode = 3;
		use_tinymce = 1;
		document.getElementById("descMode").value = descMode;
		mnuSetElementsNormal();
	}

	function SwitchToHtmlDesc()
	{
		document.getElementById("descMode").value = 2;
		CachelistSwitchHack();
		if (use_tinymce == 1)
			document.editdesc.submit();
	}

	function SwitchToHtmlEditDesc()
	{
		document.getElementById("descMode").value = 3;
		CachelistSwitchHack();
		if (use_tinymce == 0)
			document.editdesc.submit();
	}

	function CachelistSwitchHack()
	{
		switchfield = document.getElementById("switchDescMode");
		if (switchfield != null)
		{
			switchfield.value = "1";
			lform = document.getElementById("editlist_form");
			lform.action = lform.action.replace('cachelist.php', 'mylists.php');
		}
	}

	function mnuSelectElement(e)
	{
		e.backgroundColor = '#D4D5D8';
		e.borderColor = '#6779AA';
		e.borderWidth = '1px';
		e.borderStyle = 'solid';
	}

	function mnuNormalElement(e)
	{
		e.backgroundColor = '#F0F0EE';
		e.borderColor = '#F0F0EE';
		e.borderWidth = '1px';
		e.borderStyle = 'solid';
	}

	function mnuHoverElement(e)
	{
		e.backgroundColor = '#B6BDD2';
		e.borderColor = '#0A246A';
		e.borderWidth = '1px';
		e.borderStyle = 'solid';
	}

	function mnuUnhoverElement(e)
	{
		mnuSetElementsNormal();
	}

	function mnuSetElementsNormal()
	{
		var descHtml = document.getElementById("descHtml").style;
		var descHtmlEdit = document.getElementById("descHtmlEdit").style;

		switch (descMode)
		{
			case 2:
				mnuSelectElement(descHtml);
				mnuNormalElement(descHtmlEdit);
				break;
			case 3:
				mnuNormalElement(descHtml);
				mnuSelectElement(descHtmlEdit);
				break;
		}
	}

	function btnSelect(mode)
	{
		var descHtml = document.getElementById("descHtml").style;
		var descHtmlEdit = document.getElementById("descHtmlEdit").style;

		var oldMode = descMode;
		descMode = mode;
		mnuSetElementsNormal();

		switch (mode)
		{
			case 2:
				SwitchToHtmlDesc();
				break;
			case 3:
				SwitchToHtmlEditDesc();
				break;
		}
	}

	function btnMouseOver(id)
	{
		var descHtml = document.getElementById("descHtml").style;
		var descHtmlEdit = document.getElementById("descHtmlEdit").style;

		switch (id)
		{
			case 2:
				mnuHoverElement(descHtml);
				break;
			case 3:
				mnuHoverElement(descHtmlEdit);
				break;
		}
	}

	function btnMouseOut(id)
	{
		var descHtml = document.getElementById("descHtml").style;
		var descHtmlEdit = document.getElementById("descHtmlEdit").style;

		switch (id)
		{
			case 2:
				mnuUnhoverElement(descHtml);
				break;
			case 3:
				mnuUnhoverElement(descHtmlEdit);
				break;
		}
	}

//-->
</script>
{/literal}
