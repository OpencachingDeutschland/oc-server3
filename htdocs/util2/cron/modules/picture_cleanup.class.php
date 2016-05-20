<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Delete duplicate log pictures (produced e.g. by Ocprop)
 ***************************************************************************/

checkJob(new picture_cleanup());

class picture_cleanup
{
    public $name = 'picture_cleanup';
    public $interval = 86400;

    public function run()
    {
        $rsDuplicatePic = sql(
            'SELECT `object_id`, `title`
             FROM `pictures`
             WHERE `object_type`=1
             GROUP BY `object_id`, `title`
             HAVING COUNT(*) > 1'
        );

        while ($rDuplicatePic = sql_fetch_assoc($rsDuplicatePic)) {
            $rsInstances = sql(
                " SELECT `pictures`.`id` `picid`, `cache_logs`.`cache_id` `cache_id`
                 FROM `pictures`
                 LEFT JOIN `cache_logs` ON `cache_logs`.`id` = `pictures`.`object_id`
                 WHERE `pictures`.`object_type`=1 AND `pictures`.`object_id`='&1' AND `pictures`.`title`='&2'
                 ORDER BY `pictures`.`date_created`",
                $rDuplicatePic['object_id'],
                $rDuplicatePic['title']
            );

            $instances = sql_fetch_assoc_table($rsInstances);
            foreach ($instances as &$instance) {
                $instance['pic'] = new picture($instance['picid']);
                $instance['filesize'] = @filesize($instance['pic']->getFilename());
            }

            for ($n = 1; $n < count($instances); ++ $n) {
                if ($instances[$n]['filesize'] !== false) {// ensure that pic is stored locally
                    for ($nn = $n - 1; $nn >= 0; -- $nn) {
                        if ($instances[$nn]['filesize'] === $instances[$n]['filesize']) {
                            if (file_get_contents($instances[$nn]['pic']->getFilename())
                                == file_get_contents($instances[$n]['pic']->getFilename())
                            ) {
                                $picture = $instances[$n]['pic'];
                                echo
                                    'deleting duplicate picture '
                                    . $picture->getPictureId() . ' ("' . $picture->getTitle() . '")'
                                    . ' from log ' . $rDuplicatePic['object_id']
                                    . ' of cache ' . $instances[$n]['cache_id'] . "\n";
                                $picture->delete(false);
                                $instances[$n]['filesize'] = false;
                                break;
                            }
                        }
                    }
                }
            }
        }

        sql_free_result($rsDuplicatePic);
    }
}
