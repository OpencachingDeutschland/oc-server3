/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

/* common Javascript code for including a switchable HTML/TinyMCE editor

		descMode 2 = direct HTML input
		descMode 3 = Wysywyg HTML editor
*/

	var use_tinymce = 0;
	var descMode = 2;

	function OcInitEditor()
	{
		document.getElementById("scriptwarning").firstChild.nodeValue = "";
		document.getElementById("descMode").value = descMode;
		mnuSetElementsNormal();
	}

	function postEditorInit()    // called after TinyMCE initialization
	{
		descMode = 3;
		use_tinymce = 1;
		document.getElementById("descMode").value = descMode;
		mnuSetElementsNormal();
	}

	function SwitchToHtmlMode()
	{
		document.getElementById("descMode").value = 2;

		switchfield = document.getElementById("switchDescMode");
		if (switchfield != null)
			switchfield.value = "1";

		if (use_tinymce == 1)
			document.editform.submit();
	}

	function SwitchToTinyMCE()
	{
		document.getElementById("descMode").value = 3;

		switchfield = document.getElementById("switchDescMode");
		if (switchfield != null)
			switchfield.value = "1";

		if (use_tinymce == 0)
			document.editform.submit();
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
		mnuSetElementsNormal();

		switch (mode)
		{
			case 2:
				SwitchToHtmlMode();
				break;
			case 3:
				SwitchToTinyMCE();
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
