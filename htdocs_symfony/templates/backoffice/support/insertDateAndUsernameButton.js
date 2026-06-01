<script type="text/javascript">
    function addDateAndNameToTextField(textfieldname, usercountry, userlang, username = '---') {
    var textfield = document.getElementById(textfieldname);
    var today = new Date();
    var date = today.toLocaleString(userlang + '-' + usercountry, {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'});

    textfield.value = date + ' ' + username + ':\n\n\n' + textfield.value;
    textfield.focus();
    textfield.selectionStart = 0;
    textfield.selectionEnd = 0;
}
</script>
