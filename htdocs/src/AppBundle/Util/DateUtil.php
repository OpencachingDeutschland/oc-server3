<?php

namespace AppBundle\Util;

use DateTime;

class DateUtil
{
    const DATE_FORMAT_MYSQL = 'Y-m-d H:i:s';

    /**
     * @param string $date
     *
     * @return \DateTime
     */
    public static function dateTimeFromMySqlFormat($date)
    {
        return DateTime::createFromFormat(self::DATE_FORMAT_MYSQL, $date);
    }
}
