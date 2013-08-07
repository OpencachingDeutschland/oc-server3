/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Javascript toolbox; browser-idependend functions
 ***************************************************************************/


	// detect client size and scoll position
	//
	// based on http://www.ajaxschmiede.de/javascript/fenstergrose-und-scrollposition-in-javascript-auslesen/

	function getClientWidth()
	{
		if (typeof(window.innerWidth) == 'number')
			return window.innerWidth;  // Non-IE
		else
			return Math.max(document.documentElement.clientWidth, document.body.clientWidth);  // IE
	}

	function getClientHeight()
	{
		if (typeof(window.innerHeight) == 'number')
			return window.innerHeight;  // Non-IE
		else
			return Math.max(document.documentElement.clientHeight, document.body.clientHeight);  // IE
	}

	function getScrollX()
	{
		if (typeof(window.pageXOffset) == 'number')
			return window.pageXOffset;  // Non-IE
		else
			return Math.max(document.body.scrollLeft, document.documentElement.scrollLeft);  // IE
	}

	function getScrollY()
	{
		if (typeof(window.pageYOffset) == 'number')
			return window.pageYOffset;  // Non-IE
		else
			return Math.max(document.body.scrollTop, document.documentElement.scrollTop);  // IE
	}


	// detect document height
	//
	// from http://james.padolsey.com/javascript/get-document-height-cross-browser/

	function getDocHeight()
	{
		var D = document;
		return Math.max(
			D.body.scrollHeight, D.documentElement.scrollHeight,
			D.body.offsetHeight, D.documentElement.offsetHeight,
			D.body.clientHeight, D.documentElement.clientHeight
		);
	}


	// detect if the user scolled to the document bottom

	function scrolledToBottom(tolerance)
	{
		// alert(getScrollY() + " " + getClientHeight() + " " + getDocHeight());
		return getScrollY() + getClientHeight() + tolerance >= getDocHeight();
	}


	// create object for XMLHttp-Requests, i.e. for retreiving web pages or
	// other XML pages via HTTP

	function createXMLHttp()
	{
		if (typeof XMLHttpRequest != 'undefined')
			return new XMLHttpRequest();
		else if (window.ActiveXObject)
		{
			var avers = ["Microsoft.XmlHttp", "MSXML2.XmlHttp","MSXML2.XmlHttp.3.0", "MSXML2.XmlHttp.4.0","MSXML2.XmlHttp.5.0"];
			for (var i = avers.length -1; i >= 0; i--)
			{
				try
				{
					httpObj = new ActiveXObject(avers[i]);
					return httpObj;
				}
				catch(e)
				{
				}
			}
		}
		return null;
	}
