<?php
/****************************************************************************
											./lang/de/ocstyle/removedesc.tpl.php
															-------------------
		begin                : July 7 2004

		For license information see doc/license.txt
 ****************************************************************************/

/****************************************************************************
	      
   Unicode Reminder メモ
                                   				                                
	 remove a cache description
		
		desclang_name
		cachename
		cacheid_urlencode
		desclang_urlencode
		
 ****************************************************************************/
?>
<div class="content2-pagetitle"><img src="lang/de/ocstyle/images/description/22x22-description.png" style="align: left; margin-right: 10px;" width="22" height="22" alt="" />{t}delete this cache-description{/t} ({desclang_name})</div>

<p><br />{t}Do you really want to delete this  description of your cache &quot;{cachename}&quot;{/t}</p>
<p><br /><a href="removedesc.php?cacheid={cacheid_urlencode}&desclang={desclang_urlencode}&commit=1">{t}Yes, delete cache description{/t}</a></p>