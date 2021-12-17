<?php

namespace App\Util;

class ArrayUtil
{
	public static function setTreeBranch(array $keys, $value, array &$array): void
    {
        if (!$keys) {
            return;
        }
        elseif (count($keys) === 1) {
            $array[$keys[0]] = $value;
            return;
        }
        else {
            $key = array_shift($keys);

            if (!isset($array[$key])) {
                $array[$key] = [];
            }

            self::setTreeBranch($keys, $value, $array[$key]);
        }
    }
}