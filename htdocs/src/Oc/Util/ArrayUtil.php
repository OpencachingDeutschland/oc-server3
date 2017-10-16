<?php

namespace Oc\Util;

class ArrayUtil
{
    /**
     * Explodes the given $string by $delimiter, trims all values and only adds non empty values to the result array.
     *
     * @param string $delimiter
     * @param string $string
     *
     * @return array
     */
    public static function explodeTrim($delimiter, $string)
    {
        $result = [];
        $values = explode($delimiter, $string);

        foreach ($values as $value) {
            $value = trim($value);
            if ($value !== '') {
                $result[] = $value;
            }
        }

        return $result;
    }

    /**
     * Implodes an array of strings in a human language matter.
     *
     * With default values: ['Foo', 'bar', 'baz'] => 'Foo, bar and baz'
     *
     * @param array $pieces
     * @param string $conjunction
     * @param string $glue
     *
     * @return mixed|string
     */
    public static function humanImplode(array $pieces, $conjunction = 'and', $glue = ',')
    {
        $lastElement = array_pop($pieces);

        if (!empty($pieces)) {
            return $lastElement;
        }

        return sprintf(
            '%s %s %s',
            implode($glue . ' ', $pieces),
            $conjunction,
            $lastElement
        );
    }
}
