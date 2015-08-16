<?php
  if (isset($_REQUEST['lang']))
    $lang = $_REQUEST['lang'];
  else
    $lang = 'de';
?>

tinyMCE_GZ.init({
	plugins : 'advhr,contextmenu,emotions,insertdatetime,paste,table',
	themes : 'advanced',
	languages : '<?php echo $lang; ?>',
	disk_cache : true,
	debug : false
});
tinyMCE.init({
	mode : "textareas",
	theme : "advanced",

	plugins : "advhr,contextmenu,emotions,insertdatetime,paste,table",

	theme_advanced_buttons1 : "cut,copy,paste,pasteword,pastetext,removeformat,separator,undo,redo,separator,link,unlink,image,separator,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "bold,italic,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,bullist,numlist,separator,insertdate,inserttime,separator,forecolor,backcolor,charmap,emotions",
	theme_advanced_buttons3 : "visualaid,tablecontrols,separator,advhr",

	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_path_location : "bottom",
	plugin_insertdate_dateFormat : "%Y-%m-%d",
	plugin_insertdate_timeFormat : "%H:%M:%S",
	file_browser_callback : "",

	theme_advanced_resize_horizontal : false,
	theme_advanced_resizing : true,
	editor_deselector : "mceNoEditor",
	language : "<?php echo $lang; ?>",
	preformatted : true,
	remove_linebreaks : false,
	oninit : "postEditorInit",

	content_css : "resource2/tinymce/config/content.css"
});

