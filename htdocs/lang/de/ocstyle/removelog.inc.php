<?php
/****************************************************************************
                                            ./lang/de/ocstyle/removelogs.inc.php
                                                            -------------------
        begin                : July 9 2004

        For license information see doc/license.txt
 ****************************************************************************/

/****************************************************************************
           
   Unicode Reminder メモ
                                                                                  
     language specific vars
        
 ****************************************************************************/

 $error_wrong_node = t('This log entry has been created on another Opencaching website. The cache can only be edited there.');
 $removed_message_end = '---';
 

function removed_log_subject($lang)
{
    global $translate;
    return $translate->t('Info: Your log entry has been removed by the cache owner.', '', basename(__FILE__), __LINE__, '', 1, $lang);
}

function removed_message_title($lang)
{
    global $translate;
    return $translate->t('The owner of the cache has written the following comment to you:', '', basename(__FILE__), __LINE__, '', 1, $lang) . "\n---";
}
