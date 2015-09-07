/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

/*
		common Javascript code for including a switchable HTML/TinyMCE editor

		descMode 1 = plain text
		descMode 2 = direct HTML input
		descMode 3 = Wysywyg HTML editor
*/

	var use_tinymce = 0;

	function OcInitEditor()
	{
		document.getElementById("scriptwarning").firstChild.nodeValue = "";
		if (descMode == 3)
		{
			// For the case that TinyMCE does not work, we first fall back to a simple editor mode:
			if (getDescElement().value == '')
				descMode = 1;
			else
				descMode = 2;
		}
		document.getElementById("descMode").value = descMode;
		mnuSetElementsNormal();
	}

	function postEditorInit()
	{
		// This function is called after loading of TinyMCE. TinyMCE JS code is only
		// included for descMode 3 (see newdesc.php, log.php etc.: adding JS header links),
		// so we know that we started in descMode 3 and can restore this mode:
		descMode = 3;
		use_tinymce = 1;
		document.getElementById("descMode").value = descMode;
		mnuSetElementsNormal();
	}

	function getDescElement()
	{
		// text field is named "logtext" for log editor, "desc" for all other editors
		var descElem = document.getElementById("desc");
		if (descElem == null)
			var descElem = document.getElementById("logtext");
		return descElem;
	}

	function SwitchToTextMode()
	{
		document.getElementById("descMode").value = 1;

		switchfield = document.getElementById("switchDescMode");
		if (switchfield != null)
			switchfield.value = "1";

		if (use_tinymce == 1)
			document.editform.submit();
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
		var descText = document.getElementById("descText");
		// descText not present in user profile and cache list desc editors 
		if (descText) descText = descText.style;

		var descHtml = document.getElementById("descHtml").style;
		var descHtmlEdit = document.getElementById("descHtmlEdit").style;

		switch (descMode)
		{
			case 1:
				if (descText) mnuSelectElement(descText);
				mnuNormalElement(descHtml);
				mnuNormalElement(descHtmlEdit);
				break;

			case 2:
				if (descText) mnuNormalElement(descText);
				mnuSelectElement(descHtml);
				mnuNormalElement(descHtmlEdit);
				break;

			case 3:
				if (descText) mnuNormalElement(descText);
				mnuNormalElement(descHtml);
				mnuSelectElement(descHtmlEdit);
				break;
		}
	}

	function btnSelect(mode)
	{
		var oldMode = descMode;
		descMode = mode;
		mnuSetElementsNormal();

		if ((oldMode == 1) && (descMode != 1))
		{
			// convert text to HTML
			var desc = getDescElement().value;

			if ((desc.indexOf('&amp;') == -1) &&
			    (desc.indexOf('&quot;') == -1) &&
			    (desc.indexOf('&lt;') == -1) &&
			    (desc.indexOf('&gt;') == -1) &&
			    (desc.indexOf('<p>') == -1) &&
			    (desc.indexOf('<i>') == -1) &&
			    (desc.indexOf('<strong>') == -1) &&
			    (desc.indexOf('<br />') == -1))
			{
				desc = desc.replace(/&/g, "&amp;");
				desc = desc.replace(/"/g, "&quot;");
				desc = desc.replace(/</g, "&lt;");
				desc = desc.replace(/>/g, "&gt;");
				desc = desc.replace(/\r\n/g, "\<br />");
				desc = desc.replace(/\n/g, "<br />");
				desc = desc.replace(/<br \/>/g, "<br />\n");
			}

			getDescElement().value = desc;
		}

		switch (mode)
		{
			case 1:
				SwitchToTextMode();
				break;
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
		var descText = document.getElementById("descText").style;
		var descHtml = document.getElementById("descHtml").style;
		var descHtmlEdit = document.getElementById("descHtmlEdit").style;

		switch (id)
		{
			case 1:
				mnuHoverElement(descText);
				break;
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
		var descText = document.getElementById("descText").style;
		var descHtml = document.getElementById("descHtml").style;
		var descHtmlEdit = document.getElementById("descHtmlEdit").style;

		switch (id)
		{
			case 1:
				mnuUnhoverElement(descText);
				break;
			case 2:
				mnuUnhoverElement(descHtml);
				break;
			case 3:
				mnuUnhoverElement(descHtmlEdit);
				break;
		}
	}
