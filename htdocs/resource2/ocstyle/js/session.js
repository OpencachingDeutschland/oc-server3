/***************************************************************************
 *  Opencaching PHP-Session timeout reminder implementation
 *
 *  You can find the license in the docs directory of the CVS repository
 *
 *  Unicode Reminder メモ
 ***************************************************************************/
var nSessionTimeout = 30; // in minutes (default 30)
nSessionTimeout -= 3;     // tolerance

var bSetTimoutWarning = false;

function initSessionTimeout()
{
	var d = document.getElementById('sessionTimout');
	d.firstChild.nodeValue = nSessionTimeout;

	var oSessionCheck = self.setInterval("checkSessionTimeout()", 60000);
}

function checkSessionTimeout()
{
	var d = document.getElementById('sessionTimout');
	d.firstChild.nodeValue = nSessionTimeout;
	nSessionTimeout = nSessionTimeout - 1;

	if (nSessionTimeout < 10 && bSetTimoutWarning==false)
	{
		document.getElementById('sessionWarn').style.display = 'block';
		bSetTimoutWarning = true;
	}

	if (nSessionTimeout == 0)
	{
		self.location = 'login.php?action=logout';
		self.clearInterval(oSessionCheck);
	}
}

function cancelSessionTimeout()
{
	alert(sSessionId);
}