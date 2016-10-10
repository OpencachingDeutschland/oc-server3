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

    /**
     * @param array $pieces
     * @param string $conjunction
     * @param string $glue
     *
     * @return mixed|string
     */
    public static function humanLangImplode(array $pieces, $conjunction = 'and', $glue = ',')
    {
        $lastElement = array_pop($pieces);
        if (!empty($pieces)) {
            return implode($glue . ' ', $pieces) . ' ' . $conjunction . ' ' . $lastElement;
        }

        return $lastElement;
    }
}
