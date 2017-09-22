<?php
/***************************************************************************
 * for license information see LICENSE.md
 *
 * updates full text search index
 ***************************************************************************/

include __DIR__ . '/../../../lib2/search/ftsearch.inc.php';

checkJob(new SearchIndex());

class SearchIndex
{
    public $name = 'search_index';
    public $interval = 0;

    public function run()
    {
        // ftsearch_refresh() will roughly process 5-50 search_index_times entries
        // per second. In normal Opencaching.de operation, we expect just 1 changed
        // text item per cronjob run.
        //
        // Let's allow 20 per run. This will yield a timely indexing even in peak
        // activity situations, and still limit the cronjob runtime to a few seconds.

        // If there is some very huge batch outstanding, or for some special index
        // rebuild, we will leave it up to fill_search_index.php. This avoids
        // duplicate word counting when both run concurrently (though the word
        // counts are uncritical an so far not used at all).

        if (sql_value('SELECT COUNT(*) FROM `search_index_times`', 0) < 2000) {
            ftsearch_refresh(20);
        }
    }
}
