<?php
namespace marcosiino\pipeflow\Utils\Helpers;

use marcosiino\pipeflow\Exceptions\StageConfigurationException;

class Helpers
{
    /**
     * Gets the specified array item at the given key path by traversing the array using the keys in the path.
     *
     * @param array $array An associative array
     * @param string $path - The dot-separated key path of the field to get (e.g. "key1" to get the field at the key "key1", "key1.key2" to get the field at the key "key2" of the field at the key "key1")
     * @return mixed
     */
    static function getArrayItemAtPath($array, $path)
    {
        //print_r($array);
        $keys = explode('.', $path);
        foreach ($keys as $key) {
            if (is_array($array) && array_key_exists($key, $array)) {
                //print("key $key: $array[$key]\n");
                $array = $array[$key];
            } else {
                //print("key $key: null\n");
                return null;
            }
        }
        return $array;
    }
}