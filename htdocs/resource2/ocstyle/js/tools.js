/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Javascript toolbox
 ***************************************************************************/


	// detect client size and scoll position on all browsers
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


	// detect document height in all browsers
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
