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
    
    public static function removeTreeKeys(array $keys, array &$array): void
    {
        if (!$keys) {
            return;
        }
        elseif (count($keys) === 1) {
            unset($array[$keys[0]]);
            return;
        }
        else {
            $key = array_shift($keys);

            if (isset($array[$key])) {
                self::removeTreeKeys($keys, $array[$key]);
            }
        }
    }

    public static function flatten(array $array): array
    {
        $result = [];
        
        array_walk_recursive(
            $array, 
            function($a) use (&$result) {
                $result[] = $a;
            }
        );
        
        return $result;
    }
}