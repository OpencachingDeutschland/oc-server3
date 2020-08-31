// We need this until refacuring

// Used @ myhome.tpl
function toggle_archived()
{
    var archived = document.getElementsByName("row_archived");
    var show;
    if (archived[0].style.display == "none")
    {
        show="";
        document.getElementById("show_archived").style.display = "none";
        document.getElementById("hide_archived").style.display = "";
    }
    else
    {
        show="none";
        document.getElementById("hide_archived").style.display = "none";
        document.getElementById("show_archived").style.display = "";
    }
    for (var i=0; i<archived.length; i++)
        archived[i].style.display = show;

    var dCookieExp = new Date(2049, 12, 31);
    document.cookie = "ocprofilearchived=" + show + ";expires=" + dCookieExp.toUTCString();
}

function myHomeLoad()
{
    enl_init();

    var archived = document.getElementsByName("row_archived");
    if (archived.length > 0)  // is 0 for MSIE due to getElementsByName() bug
    {
        var sCookieContent = document.cookie.split(";");
        for (var nIndex = 0; nIndex < sCookieContent.length; nIndex++)
        {
            var sCookieValue = sCookieContent[nIndex].split("=");
            if (sCookieValue[0].replace(/^\s+/,'') == "ocprofilearchived" && sCookieValue[1] == "none")
                toggle_archived();
        }
        document.getElementById("toggle_archived_option").style.display = "";
    }
}
