<?php
/****************************************************************************

	Unicode Reminder メモ

	common functions for text editors

 ****************************************************************************/

	// used in both lib1 and lib2 code

	require_once('smiley.inc.php');
	require_once('OcHTMLPurifier.class.php');
	require_once('html2text.class.php');


/*
	Do all the conversions needed to process HTML or plain text editor input,
	for either storing it into the database or (when swiching modes)
	re-displaying it in another editor mode.

	oldDescMode is the mode in which the editor was running which output the $text,
	            or 0 if the text came from the database with `htm_text` = 0.

	descMode    is == descMode if the user hit the editor's "save" button, 
	            or the new mode if the user hit another mode button
*/

function processEditorInput($oldDescMode, $descMode, $text)
{
	global $opt, $smiley;

	if ($descMode != 1)
	{
		if ($oldDescMode == 1)
		{
			// mode switch from plain text to HTML editor => convert HTML special chars
			$text = nl2br(htmlspecialchars($text));
			// .. and smilies
			$text = str_replace($smiley['text'], $smiley['spaced_image'], $text);
		}
		else
		{
			// save HTML input => verify / tidy / filter
			$purifier = new OcHTMLPurifier($opt);
			$text = $purifier->purify($text);
		}
	}
	else
	{
		if ($oldDescMode == 1)
		{
			// save plain text input => convert to HTML
			$text = nl2br(htmlspecialchars($text, ENT_COMPAT, 'UTF-8'));
		}
		else
		{
			// mode switch from HTML editor to plain text => convert smilies ...
			for ($n=0; $n < count($smiley['image']); $n++)
			{
				do
				{
					$logtext0 = $text;
					$text = mb_ereg_replace("<img [^>]*?src=[^>]+?".str_replace('.','\.',$smiley['file'][$n])."[^>]+?>", "[s![".$smiley['text'][$n]."]!s]", $text);
						// the [s[ ]s] is needed to protect the spaces around the smileys
				} while ($text != $logtext0);
			}

			// ... and HTML to plain text
			$text = html2plaintext($text, $oldDescMode = 0);

			$text = str_replace(array('[s![',']!s]'), '', $text);
		}
	}

	return $text;
}


// $texthtml0 is set if the text is from cache_desc.desc or cache_logs.text
// and text_html is 0, i.e. the text was edited in the "text" editor mode.

function html2plaintext($text, $texthtml0)
{
	global $opt, $absolute_server_URI;

	if ($texthtml0)
	{
		$text = str_replace(array('<p>', "\n", "\r"), '', $text);
		$text = str_replace(array('<br />', '</p>'), "\n", $text);
		$text = html_entity_decode($text, ENT_COMPAT, 'UTF-8');
	}
	else
	{
		$h2t = new html2text($text);
		$h2t->set_base_url(isset($opt['page']['absolute_url']) ? $opt['page']['absolute_url'] : $absolute_server_URI);
		$text = $h2t->get_text();

		// remove e.g. trailing \n created from </p> by html2text
		while (substr($text,-2) == "\n\n")
			$text = substr($text, 0, strlen($text) - 1);
	}

	return $text;
}


function editorJsPath()
{
	return 'templates2/ocstyle/js/editor.js?ft=' . filemtime('templates2/ocstyle/js/editor.js');
}

?>
