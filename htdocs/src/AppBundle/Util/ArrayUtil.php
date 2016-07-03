<?php

namespace AppBundle\Util;

class ArrayUtil
{
    /**
     * @param string $delimiter
     * @param string $string
     *
     * @return array
     */
    public static function trimExplode($delimiter, $string)
    {
        $result = [];
        $temp = explode($delimiter, $string);
        foreach ($temp as $value) {
            $value = trim($value);
            if ($value !== '') {
                $result[] = $value;
            }
        }

        return $result;
    }
}
