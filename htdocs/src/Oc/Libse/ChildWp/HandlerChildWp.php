<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

namespace Oc\Libse\ChildWp;

use Doctrine\DBAL\Connection;
use Oc\Libse\CacheNote\PresenterCacheNote;
use Oc\Libse\Coordinate\CoordinateCoordinate;
use Oc\Libse\Coordinate\TypeCoordinate;
use Oc\Libse\Language\TranslatorLanguage;

class HandlerChildWp
{
    private $childWpTypes = [];

    private $translator;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var TranslatorLanguage
     */
    private $translatorLanguage;

    public function __construct(Connection $connection, TranslatorLanguage $translatorLanguage)
    {
        global $opt;

        $this->connection = $connection;
        $this->translatorLanguage = $translatorLanguage;

        // read available types from DB
        $rs = $this->connection->fetchAll(
            "SELECT `coordinates_type`.`id`,
                    IFNULL(`trans`.`text`, `coordinates_type`.`name`) AS `name`,
                    `coordinates_type`.`image`,
                    IFNULL(`trans_pp`.`text`,
                    `coordinates_type`.`preposition`) AS `preposition`
             FROM `coordinates_type` 
             LEFT JOIN `sys_trans_text` `trans`
               ON `coordinates_type`.`trans_id`=`trans`.`trans_id`
               AND `trans`.`lang`='&1'
             LEFT JOIN `sys_trans_text` `trans_pp`
               ON `coordinates_type`.`pp_trans_id`=`trans_pp`.`trans_id`
               AND `trans_pp`.`lang`= :lang",
            [':lang' => $opt['template']['locale']]
        );

        foreach ($rs as $r) {
            $type = new TypeChildWp($r['id'], $r['name'], $r['preposition'], $r['image']);
            $this->childWpTypes[$type->getId()] = $type;
        }
    }

    public function add($cacheId, $type, $lat, $lon, $description)
    {
        $data = [
            'type' => TypeCoordinate::ChildWaypoint,
            'subtype' => $type,
            'latitude' => $lat,
            'longitude' => $lon,
            'cache_id' => $cacheId,
            'description' => $description,
        ];

        $this->connection->insert('coordinates', $data);
    }

    public function update($childId, $type, $lat, $lon, $description)
    {
        $data = [
            'subtype' => $type,
            'latitude' => $lat,
            'longitude' => $lon,
            'description' => $description,
        ];

        $this->connection->update('coordinates', $data, ['id' => $childId]);
    }

    public function getChildWp($childId)
    {
        $rs = $this->connection->fetchAssoc(
            'SELECT id, cache_id, type, subtype, latitude, longitude, description
                 FROM coordinates
                 WHERE id = :id',
            ['id' => $childId]
        );

        return $this->recordToArray($rs);
    }

    public function getChildWps($cacheId, $userId, $includeUserNote = false)
    {
        $type2 = 0;

        if ($includeUserNote) {
            $type2 = TypeCoordinate::UserNote;
        }

        $rs = $this->connection->fetchAll(
            'SELECT id, cache_id, type, subtype, latitude, longitude, description
            FROM coordinates
            WHERE cache_id = :cacheId
            AND type IN (:type1, :type2)
            AND (type=:type1 OR (user_id= :userId AND latitude!=0 AND longitude!=0))
            ORDER BY id',
            [
                'cacheId' => $cacheId,
                'type1' => TypeCoordinate::ChildWaypoint,
                'type2' => $type2,
                'userId' => $userId,
            ]
        );
        $ret = [];

        foreach ($rs as $r) {
            $ret[] = $this->recordToArray($r);
        }

        return $ret;
    }

    public function getChildWpIdAndNames()
    {
        $idAndNames = [];

        foreach ($this->childWpTypes as $type) {
            $idAndNames[$type->getId()] = $type->getName();
        }

        return $idAndNames;
    }

    public function getChildNamesAndImages()
    {
        $nameAndTypes = [];

        foreach ($this->childWpTypes as $type) {
            $nameAndTypes[$type->getName()] = $type->getImage();
        }

        return $nameAndTypes;
    }

    private function recordToArray($r)
    {
        $ret = [];

        $ret['cacheid'] = $r['cache_id'];
        $ret['childid'] = $r['id'];
        $ret['latitude'] = $r['latitude'];
        $ret['longitude'] = $r['longitude'];
        $ret['coordinate'] = new CoordinateCoordinate($ret['latitude'], $ret['longitude']);
        $ret['description'] = $r['description'];

        if ($r['type'] == TypeCoordinate::ChildWaypoint) {
            $ret['type'] = $r['subtype'];
            $type = $this->childWpTypes[$ret['type']];

            if ($type) {
                $ret['name'] = $type->getName();
                $ret['preposition'] = $type->getPreposition();
                $ret['image'] = $type->getImage();
            }
        } else {
            $ret['type'] = 0;
            $ret['name'] = $this->translatorLanguage->translate('Personal cache note');
            $ret['image'] = PresenterCacheNote::image;
        }

        return $ret;
    }

    public function delete($childId)
    {
        $this->connection->delete('coordinates', ['id' => $childId]);
    }
}
