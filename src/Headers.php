<?php
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

use InvalidArgumentException;

/**
 * Class Headers
 *
 * @package MindTouch\Http
 */
class Headers implements IMutableHeaders {
    const HEADER_CONTENT_TYPE = 'Content-Type';
    const HEADER_AUTHORIZATION = 'Authorization';
    const HEADER_CONTENT_LENGTH = 'Content-Length';
    const HEADER_SET_COOKIE = 'Set-Cookie';

    /**
     * Header names that should not have multiple values
     *
     * @var string[]
     */
    protected static $singleValueHeaders = [

        // https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.17
        'Content-Type',

        // https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.30
        'Location'
    ];

    /**
     * Header names that are sent as separate headers, not a single header with multiple values
     *
     * @var string[]
     */
    protected static $multipleNameValuePairHeaders = [

        /**
         * http://tools.ietf.org/html/rfc6265: Origin servers SHOULD NOT fold multiple Set-Cookie header fields into a single header field. The usual
         * mechanism for folding HTTP headers fields (i.e., as defined in [RFC2616]) might change the semantics of the Set-Cookie header field because
         * the %x2C (",") character is used by Set-Cookie in a way that conflicts with such folding.
         */
        'Set-Cookie'
    ];

    /**
     * Return an instance from a name/value pair structure: [ [ name, value ], [ name, value ], ...]
     *
     * @param string[][] $pairs
     * @return self
     */
    public static function newFromHeaderNameValuePairs(array $pairs) {
        $headers = new Headers();
        foreach($pairs as $pair) {
            if(!isset($pair[0])) {
                throw new InvalidArgumentException('Invalid name/value pair structure');
            }
            $value = isset($pair[1]) ? $pair[1] : null;
            $headers->addHeader($pair[0], $value);
        }
        return $headers;
    }

    /**
     * Return a raw header string
     *
     * @param string $name
     * @param string|null $value
     * @return string
     */
    private static function newRawHeader($name, $value = null) { return !StringUtil::isNullOrEmpty($value) ? "{$name}: {$value}" : "{$name}:"; }

    /**
     * Retrieve HTTP header name in capitalized, hyphenated format (Content-Type, Set-Cookie, ...)
     *
     * @param string $name
     * @return string
     */
    private static function getFormattedHeaderName($name) { return implode('-', array_map('ucfirst', explode('-', strtolower($name)))); }

    /**
     * @var array
     * @structure name => [ value, ... ]
     */
    private $headers = [];

    /**
     * @var array - list of keys in the map
     */
    private $names = [];

    /**
     * @var mixed - current key
     */
    private $name;

    public function getHeaderLine($name) {
        $values = $this->getHeader(self::getFormattedHeaderName($name));
        return !empty($values) ? implode(', ', $values) : null;
    }

    public function getHeader($name) {
        $name = self::getFormattedHeaderName($name);
        return isset($this->headers[$name]) ? $this->headers[$name] : [];
    }

    public function getSetCookieHeaderLine($cookieName) {
        $headers = $this->getHeader('Set-Cookie');
        if(empty($headers)) {
            return null;
        }
        foreach($headers as $header) {
            if(strpos($header, $cookieName) === 0) {
                return $header;
            }
        }
        return null;
    }

    public function addHeaders(IHeaders $headers) {
        foreach($headers as $name => $values) {
            foreach($values as $value) {
                $this->addHeader($name, $value);
            }
        }
    }

    public function addHeader($name, $value) {
        $values = StringUtil::isNullOrEmpty($value) ? [] : [trim($value)];
        $name = self::getFormattedHeaderName($name);
        $this->set($name, $values, false);
    }

    public function setHeader($name, $value) {
        $values = StringUtil::isNullOrEmpty($value) ? [] : [trim($value)];
        $name = self::getFormattedHeaderName($name);
        $this->set($name, $values, true);
    }

    public function addRawHeader($header) { $this->setRawHeaderHelper($header, false); }

    public function setRawHeader($header) { $this->setRawHeaderHelper($header, true); }

    public function hasHeader($name) { return isset($this->headers[self::getFormattedHeaderName($name)]); }

    public function removeHeader($name) { unset($this->headers[self::getFormattedHeaderName($name)]); }

    public function isEmpty() { return empty($this->headers); }

    public function rewind() { $this->name = reset($this->names); }

    public function key() { return $this->name; }

    public function current() { return $this->headers[$this->name]; }

    public function next() { $this->name = next($this->names); }

    public function valid() { return $this->name !== false; }

    public function toRawHeaders() {
        $headers = [];
        foreach($this as $name => $values) {
            if(in_array($name, static::$multipleNameValuePairHeaders)) {
                foreach($values as $value) {
                    $headers[] = self::newRawHeader($name, $value);
                }
            } else {
                $headers[] = self::newRawHeader($name,  implode(', ', $values));
            }
        }
        return $headers;
    }

    public function toFlattenedArray() {
        $headers = [];
        foreach($this as $name => $values) {
            $headers[$name] = implode(', ', $values);
        }
        return $headers;
    }

    public function toArray() { return $this->headers; }

    public function toMutableHeaders() { return $this; }

    public function toMergedHeaders(IHeaders $headers) {
        $instance = clone $this;
        $instance->addHeaders($headers);
        return $instance;
    }

    /**
     * @param string $name
     * @param string[] $values
     * @param bool $overwrite
     */
    protected function set($name, $values, $overwrite) {
        if(in_array($name, static::$singleValueHeaders)) {

            // enforce headers that can only hold one value
            if(!empty($values)) {
                $values = [$values[0]];
            }
            $overwrite = true;
        }
        $this->headers[$name] = isset($this->headers[$name])
            ? ($overwrite ? $values : array_merge($this->headers[$name], $values))
            : $values;

        // rebuild map and move iterator back to first element
        $values = array_values(array_filter($this->headers[$name], 'strlen'));
        if(empty($values)) {
            $values = [''];
        }
        $this->headers[$name] = $values;
        $this->names = array_keys($this->headers);
        $this->rewind();
    }

    /**
     * @param string $header
     * @param bool $overwrite
     */
    protected function setRawHeaderHelper($header, $overwrite) {
        if(strpos($header, ':') === false) {
            throw new InvalidArgumentException('Invalid HTTP header: ' . $header);
        }
        list($name, $value) = explode(':', $header, 2);
        $name = self::getFormattedHeaderName($name);
        $values = in_array($name, static::$multipleNameValuePairHeaders)
            ? [trim($value)]

            // split multiple header values
            : array_filter(array_map('trim', explode(',', $value)), 'strlen');
        $this->set($name, $values, $overwrite);
    }
}
