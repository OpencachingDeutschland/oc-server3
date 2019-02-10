<?php
/***************************************************************************
 * for license information see LICENSE.md
 *  Import new data from geokrety.org.
 *  See util2/geokrety for check and repair functions.
 *  See discussion in http://redmine.opencaching.de/issues/18.
 ***************************************************************************/

checkJob(new Geokrety());

class Geokrety
{
    public $name = 'geokrety';
    public $interval = 900;

    public function run(): void
    {
        global $opt;

        if ($opt['cron']['geokrety']['run']) {
            $xmlfile = $this->loadXML();
            if ($xmlfile === false) {
                return;
            }

            $this->importXML($xmlfile);
            if (!$opt['cron']['geokrety']['xml_archive']) {
                $this->removeXML($xmlfile);
            }
        }
    }

    /* get file from XML interface
     * and return path of saved xml
     * or false on error
     */
    public function loadXML()
    {
        global $opt;

        if (!@mkdir(__DIR__ . '/../../../var/cache2/geokrety')) {
            // die('can\'t create geogrety cache dir');
        }
        $path = __DIR__ . '/../../../var/cache2/geokrety/import-' . date('Ymd-His') . '.xml';

        // Changed default-value for getSysConfig() from '2005-01-01 00:00:00' to 'NOW - 9d 12h'
        // to safely stay in api-limit, even when client and server are in different time zones.
        $modifiedSince = strtotime(
            getSysConfig('geokrety_lastupdate', date($opt['db']['dateformat'], time() - 60 * 60 * 24 * 9.5))
        );
        if (!@copy('https://geokrety.org/export.php?modifiedsince=' . date('YmdHis', $modifiedSince - 1), $path)) {
            return false;
        }

        return $path;
    }

    /**
     * @param string $file
     */
    public function removeXML($file): void
    {
        @unlink($file);
    }

    /**
     * @param string $file
     */
    public function importXML($file): void
    {
        global $opt;

        $xr = new XMLReader();
        if (!$xr->open($file)) {
            $xr->close();

            return;
        }

        $xr->read();
        if ($xr->nodeType != XMLReader::ELEMENT) {
            echo 'error: First element expected, aborted' . "\n";

            return;
        }
        if ($xr->name != 'gkxml') {
            echo 'error: First element not valid, aborted' . "\n";

            return;
        }

        $startUpdate = $xr->getAttribute('date');
        if ($startUpdate === '') {
            echo 'error: Date attribute not valid, aborted' . "\n";

            return;
        }
//      is probably noot needed
//        while ($xr->read() && !($xr->name == 'geokret' || $xr->name == 'moves')) {}

        $nRecordsCount = 0;
        do {
            if ($xr->nodeType == XMLReader::ELEMENT) {
                $element = $xr->expand();
                switch ($xr->name) {
                    case 'geokret':
                        $this->importGeoKret($element);
                        break;
                    case 'moves':
                        $this->importMove($element);
                        break;
                }

                $nRecordsCount++;
            }
        } while ($xr->next());

        $xr->close();

        setSysConfig('geokrety_lastupdate', date($opt['db']['dateformat'], strtotime($startUpdate)));
    }

    /**
     * @param DOMNode $element
     */
    public function importGeoKret($element): void
    {
        global $opt;

        $id = $element->getAttribute('id');

        $name = html_entity_decode($this->getNodeValue($element, 'name'));
        if ($name === '') {
            return;
        }

        $userId = $this->getNodeAttribute($element, 'owner', 'id') + 0;
        $username = $this->getNodeValue($element, 'owner');
        $this->checkUser($userId, $username);

        $typeId = $this->getNodeAttribute($element, 'type', 'id') + 0;
        $typename = $this->getNodeValue($element, 'type');
        $this->checkGeoKretType($typeId, $typename);

        $description = html_entity_decode($this->getNodeValue($element, 'description'));
        $dateCreated = strtotime($this->getNodeValue($element, 'datecreated'));

        $distanceTravelled = $this->getNodeValue($element, 'distancetravelled') + 0;
        $state = $this->getNodeValue($element, 'state') + 0;

        $longitude = $this->getNodeAttribute($element, 'position', 'longitude') + 0;
        $latitude = $this->getNodeAttribute($element, 'position', 'latitude') + 0;

        sql(
            "INSERT INTO `gk_item`
                    (`id`, `name`, `description`, `userid`, `datecreated`,
                    `distancetravelled`, `latitude`, `longitude`, `typeid`, `stateid`)
            VALUES ('&1', '&2', '&3', '&4', '&5', '&6', '&7', '&8', '&9', '&10')
            ON DUPLICATE KEY UPDATE
            `name`='&2', `description`='&3', `userid`='&4', `datecreated`='&5', `distancetravelled`='&6',
            `latitude`='&7', `longitude`='&8', `typeid`='&9', `stateid`='&10'",
            $id,
            $name,
            $description,
            $userId,
            date($opt['db']['dateformat'], $dateCreated),
            $distanceTravelled,
            $latitude,
            $longitude,
            $typeId,
            $state
        );

        // Deleting and inserting item-waypoints if they have not changed will
        // update caches.meta_last_modified -> caches.okapi_syncbase and thus trigger
        // OKAPI changelog actions. This probably can be ignored as OKAPI will verify
        // if data has really changed.

        // update associated waypoints
        /**
         * This does not work properly, because geokret.waypoints does NOT contain the
         * current location of the Kret but something like the last cache where it was logged.
         * Evaluating the 'state' fielt might help, but for now, we import waypoint data
         * from the moves instead.
         * sql("DELETE FROM `gk_item_waypoint` WHERE id='&1'", $id);
         * $waypoints = $element->getElementsByTagName('waypoints');
         * if ($waypoints->length > 0)
         * {
         * $wpItems = $waypoints->item(0)->getElementsByTagName('waypoint');
         * for ($i = 0; $i < $wpItems->length; $i++)
         * {
         * $wp = $wpItems->item($i)->nodeValue;
         * if ($wp != '')
         * sql("INSERT INTO `gk_item_waypoint`
         * (`id`, `wp`)
         * VALUES
         * ('&1', '&2')",
         * $id, $wp);
         * }
         * }
         */
    }

    /**
     * @param DOMNode $element
     */
    public function importMove($element): void
    {
        global $opt;

        $id = $element->getAttribute('id') + 0;

        $gkId = $this->getNodeAttribute($element, 'geokret', 'id') + 0;
        if (sql_value("SELECT COUNT(*) FROM `gk_item` WHERE `id`='&1'", 0, $gkId) == 0) {
            return;
        }

        $latitude = $this->getNodeAttribute($element, 'position', 'latitude') + 0;
        $longitude = $this->getNodeAttribute($element, 'position', 'longitude') + 0;

        $dateLogged = strtotime($this->getNodeAttribute($element, 'date', 'logged'));
        $dateMoved = strtotime($this->getNodeAttribute($element, 'date', 'moved'));
        $userId = $this->getNodeAttribute($element, 'user', 'id') + 0;
        $username = $this->getNodeValue($element, 'user');
        $this->checkUser($userId, $username);

        $comment = html_entity_decode($this->getNodeValue($element, 'comment'));
        $logTypeId = $this->getNodeAttribute($element, 'logtype', 'id') + 0;
        $logTypeName = $this->getNodeValue($element, 'logtype');
        $this->checkMoveType($logTypeId, $logTypeName);

        sql(
            "INSERT INTO `gk_move`
                (`id`, `itemid`, `latitude`, `longitude`, `datemoved`, `datelogged`, `userid`, `comment`, `logtypeid`)
            VALUES ('&1', '&2', '&3', '&4', '&5', '&6', '&7', '&8', '&9')
            ON DUPLICATE KEY UPDATE
                `itemid`='&2', `latitude`='&3', `longitude`='&4', `datemoved`='&5',
                `datelogged`='&6', `userid`='&7', `comment`='&8', `logtypeid`='&9'",
            $id,
            $gkId,
            $latitude,
            $longitude,
            date($opt['db']['dateformat'], $dateMoved),
            date($opt['db']['dateformat'], $dateLogged),
            $userId,
            $comment,
            $logTypeId
        );

        sql("DELETE FROM `gk_move_waypoint` WHERE id='&1'", $id);

        // update associated waypoints
        $waypoints = $element->getElementsByTagName('waypoints');
        if ($waypoints->length > 0) {
            $wpItems = $waypoints->item(0)->getElementsByTagName('waypoint');
            for ($i = 0; $i < $wpItems->length; $i++) {
                $wp = mb_trim($wpItems->item($i)->nodeValue);
                if ($wp != '') {
                    sql("INSERT INTO `gk_move_waypoint` (`id`, `wp`) VALUES ('&1', '&2')", $id, $wp);
                }
            }
        }

        // update the current gk-waypoints based on the last move
        sql("DELETE FROM `gk_item_waypoint` WHERE `id`='&1'", $gkId);
        $rs = sql(
            "
                SELECT `id`,`logtypeid` FROM `gk_move`
                WHERE `itemid`='&1' AND `logtypeid`!=2
                ORDER BY `datemoved` DESC LIMIT 1",
            $gkId
        );
        $r = sql_fetch_assoc($rs);
        sql_free_result($rs);
        if ($r === false) {
            return;
        }

        if ($r['logtypeid'] == 0 /* dropped */ || $r['logtypeid'] == 3 /* seen in */) {
            sql(
                "
                INSERT INTO `gk_item_waypoint` (`id`, `wp`)
                SELECT '&1' AS `id`, `wp`
                FROM `gk_move_waypoint`
                WHERE `id`='&2' AND `wp`!=''",
                $gkId,
                $r['id']
            ); // "late log" bugfix: replaced $id parameter by $r['id']
        }
    }

    /**
     * @param int $id
     * @param $name
     */
    public function checkGeoKretType($id, $name): void
    {
        sql(
            "INSERT INTO `gk_item_type` (`id`, `name`) VALUES ('&1', '&2') ON DUPLICATE KEY UPDATE `name`='&2'",
            $id,
            $name
        );
    }

    /**
     * @param int $id
     * @param $name
     */
    public function checkUser($id, $name): void
    {
        if ($id === 0) {
            return;
        }

        sql("INSERT INTO `gk_user` (`id`, `name`) VALUES ('&1', '&2') ON DUPLICATE KEY UPDATE `name`='&2'", $id, $name);
    }

    /**
     * @param int $id
     * @param $name
     */
    public function checkMoveType($id, $name): void
    {
        sql(
            "INSERT INTO `gk_move_type` (`id`, `name`) VALUES ('&1', '&2') ON DUPLICATE KEY UPDATE `name`='&2'",
            $id,
            $name
        );
    }

    /**
     * @param $domNode
     * @param string $element
     * @return string
     */
    public function getNodeValue(&$domNode, $element)
    {
        $subNode = $domNode->getElementsByTagName($element);
        if ($subNode->length < 1) {
            return '';
        }

        return $subNode->item(0)->nodeValue;
    }

    /**
     * @param string $element
     * @param string $attr
     * @param & $domNode
     * @return string
     */
    public function getNodeAttribute(&$domNode, $element, $attr)
    {
        $subNode = $domNode->getElementsByTagName($element);
        if ($subNode->length < 1) {
            return '';
        }

        return $subNode->item(0)->getAttribute($attr);
    }
}
