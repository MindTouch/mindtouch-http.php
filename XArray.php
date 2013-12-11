<?php
/*
 * MindTouch API PHP Client
 * Copyright (C) 2006-2013 MindTouch, Inc.
 * www.mindtouch.com  oss@mindtouch.com
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace MindTouch\ApiClient;

/**
 * Class XArray - fetching array values and retrieving xml converted arrays
 * @package MindTouch\ApiClient
 */
class XArray {

    protected $array = array();

    /*
     * @param array &$array - reference to the array to be accessed
     */
    public function __construct(&$array = null) {
        $this->array = ($array != null) ? $array : array();
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setVal($key, $value = null) { $this->setValHelper($this->array, $key, $value); }

    /**
     * @param array $array
     * @param string $key
     * @param mixed $value
     */
    private function setValHelper(&$array, $key, $value) {
        $keys = explode('/', $key);
        $count = count($keys);
        $i = 0;
        foreach($keys as $key) {
            $i++;

            //last value
            if($i == $count) {
                if($value === null) {
                    unset($array[$key]);
                    return;
                }
                if(!isset($array[$key]) || is_string($array[$key])) {
                    $array[$key] = $value;
                }

                // if you're attempting to write a string value to a key that is an array, this operation will fail
            } else {
                if(!isset($array[$key])) {
                    $array[$key] = array();
                }
            }
            if(is_string($array[$key])) {
                return;
            }
            $array = &$array[$key];
        }
    }

    /***
     * Given an array $array, will try to find $key, which is delimited by /
     * if $key itself is an array of multiple values which has a key of '0', will return the first value
     * this is useful for getting stuff back from the api and to avoid the "cannot use string offset as array" error,
     * see http://www.zend.com/forums/index.php?S=ab6bd42e992e7497c9b0ba4a33b01dd9&t=msg&th=1556
     *
     * @param string $key - the array path to return, i.e. /pages/content
     * @param mixed $default - if the key is not found, this value will be returned
     * @return mixed
     */
    public function getVal($key = '', $default = null) {
        $array = $this->array;
        if($key == '') {
            return $array;
        }
        $keys = explode('/', $key);
        $count = count($keys);
        $i = 0;
        foreach($keys as $k => $val) {
            $i++;
            if($val == '') {
                continue;
            }
            if(isset($array[$val]) && !is_array($array[$val])) {
                if($array[$val] != null && $i == $count) {
                    return $array[$val];
                }
                return $default;
            }
            if(isset($array[$val])) {
                $array = $array[$val];
            } else {
                return $default;
            }
            if(is_array($array) && key($array) == '0') {
                $array = current($array);
            }
        }
        return $array;
    }

    public function getAll($key = '', $default = null) {
        $array = $this->array;
        if($key == '') {
            return $array;
        }
        $keys = explode('/', $key);
        $count = count($keys);
        $i = 0;
        foreach($keys as $val) {
            $i++;
            if($val == '') {
                continue;
            }
            if(!isset($array[$val])) {
                return $default;
            }
            if(!is_array($array[$val])) {
                return array($array[$val]);
            }
            $array = $array[$val];
            if($i == $count) {
                if(key($array) != '0') {
                    $array = array($array);
                }
            }
        }
        return $array;
    }

    /**
     * Helper for encoding a PHP arrays into XML
     *
     * @param string $outer - optional output tag, used for recursion
     * @return string - xml representation of the array
     */
    public function toXml($outer = null) {
        $result = '';
        foreach($this->array as $key => $value) {
            if(strncmp($key, '@', 1) == 0) {

                // skip attributes
            } else {
                $tag = $outer ? $outer : $key;
                if(is_array($value) && (count($value) > 0) && isset($value[0])) {

                    // numeric array found => child nodes
                    $XArray = new XArray($value);
                    $result .= $XArray->toXml($key);
                    unset($XArray);
                } else {
                    if(is_array($value)) {

                        // attribute list found
                        $attrs = '';
                        foreach($value as $attr_key => $attr_value) {
                            if(strncmp($attr_key, '@', 1) == 0) {
                                $attrs .= ' ' . substr($attr_key, 1) . '="' . htmlspecialchars($attr_value) . '"';
                            }
                        }
                        $XArray = new XArray($value);
                        $result .= '<' . $tag . $attrs . '>' . $XArray->toXml() . '</' . $tag . '>';
                        unset($XArray);
                    } else {
                        if($tag != '#text') {
                            $result .= '<' . $tag . '>' . $value . '</' . $tag . '>';
                        } else {
                            $result .= htmlspecialchars($value);
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Accessor for the array
     * @return array
     */
    public function toArray() {
        return $this->array;
    }
}
