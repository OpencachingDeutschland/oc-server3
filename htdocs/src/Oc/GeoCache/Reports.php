<?php

namespace Oc\GeoCache;

use Doctrine\DBAL\Connection;
use Oc\Util\DbalConnection;

class Reports
{
    /**
     * @var DbalConnection
     */
    private $connection;

    /**
     * @param DbalConnection $connection
     */
    public function __construct(DbalConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param array $wpCodes
     * @return array
     */
    public function getReportStatus(array $wpCodes)
    {
        $query = $this->connection->getQueryBuilder()
            ->select('DISTINCT(wp_oc)')
            ->from('caches', 'c')
            ->innerJoin('c', 'cache_reports', 'cr', 'cr.cacheid = c.cache_id')
            ->where('wp_oc IN (:wpCodes)')
            ->andWhere('cr.status IN (:status)')
            ->setParameter(':wpCodes', $wpCodes, Connection::PARAM_STR_ARRAY)
            ->setParameter(':status', [1, 2], Connection::PARAM_INT_ARRAY);

        $statement = $query->execute();

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
