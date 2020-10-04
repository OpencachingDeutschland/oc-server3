/***************************************************************************
  * You can find the license in the docs directory
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
        if (document.editform.scrollposx.value + document.editform.scrollposy.value != 0)
            window.scrollTo(document.editform.scrollposx.value, document.editform.scrollposy.value);

        document.getElementById("scriptwarning").firstChild.nodeValue = "";
        document.getElementById("oldDescMode").value = descMode;
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
        if (mode != descMode)
        {
            descMode = mode;
            document.getElementById("descMode").value = descMode;
            mnuSetElementsNormal();

            switchfield = document.getElementById("switchDescMode");
            if (switchfield != null)
                switchfield.value = "1";

            saveScrollPos();
            document.editform.submit();
        }
    }

    function saveScrollPos()
    {
        if (window.pageXOffset != undefined)
        {
            document.editform.scrollposx.value = window.pageXOffset;
            document.editform.scrollposy.value = window.pageYOffset;
        }
        else
        {
            var d = document, r = d.documentElement, b = d.body;
            d.editform.scrollposx.value = r.scrollLeft || b.scrollLeft || 0;
            d.editform.scrollposy.value = r.scrollTop || b.scrollTop || 0;
        }
    }

    function btnMouseOver(id)
    {
        switch (id)
        {
            case 2:
                mnuHoverElement(document.getElementById("descHtml").style);
                break;
            case 3:
                mnuHoverElement(document.getElementById("descHtmlEdit").style);
                break;
        }
    }

    function btnMouseOut(id)
    {
        switch (id)
        {
            case 2:
                mnuUnhoverElement(document.getElementById("descHtml").style);
                break;
            case 3:
                mnuUnhoverElement(document.getElementById("descHtmlEdit").style);
                break;
        }
    }
