<?php declare(strict_types=1);
/**
 * MindTouch HTTP
 * Copyright (C) 2006-2018 MindTouch, Inc.
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
namespace MindTouch\Http;

class QueryParams implements IQueryParams {

    /**
     * @var string[]
     */
    private $params = [];

    /**
     * @var mixed - current key
     */
    private $key;

    /**
     * @var string[] - list of keys in the map
     */
    private $keys = [];

    public function __toString() : string {
        return $this->toString();
    }

    public function current() : string {
        return $this->params[$this->key];
    }

    public function key() : string {
        return StringUtil::stringify($this->key);
    }

    public function next() : void {
        $this->key = next($this->keys);
    }

    public function rewind() : void {
        $this->key = reset($this->keys);
    }

    public function valid() : bool {
        return $this->key !== false;
    }

    public function get(string $key) : ?string {
        return isset($this->params[$key]) ? $this->params[$key] : null;
    }

    public function set(string $key, ?string $value) : void {
        if($value === null) {
            unset($this->params[$key]);
        } else {
            $this->params[$key] = $value;
        }
        $this->keys = array_keys($this->params);
        $this->rewind();
    }

    public function toArray() : array {
        return $this->params;
    }

    public function toString() : string {
        return http_build_query($this->params);
    }
}