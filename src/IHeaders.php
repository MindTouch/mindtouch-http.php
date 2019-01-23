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

use Iterator;

/**
 * Interface IHeaders
 *
 * @package MindTouch\Http
 */
interface IHeaders extends Iterator {

    /**
     * Retrieve comma separated values from HTTP header name
     *
     * @param string $name - case-insensitive header name
     * @return string|null
     */
    function getHeaderLine(string $name) : ?string;

    /**
     * Retrieve a collection of values or empty collection from HTTP header name
     *
     * @param string $name - case-insensitive header name
     * @return string[]
     */
    function getHeader(string $name) : array;

    /**
     * Retrieve the value of a Set-Cookie HTTP header by the cookie's case-sensitive name
     *
     * @param string $cookieName - case-sensitive cookie name
     * @return string|null
     */
    function getSetCookieHeaderLine(string $cookieName) : ?string;

    /**
     * Is HTTP header set in the collection?
     *
     * @param string $name - case-insensitive header name
     * @return bool
     */
    function hasHeader(string $name) : bool;

    /**
     * Is HTTP header collection empty?
     *
     * @return bool
     */
    function isEmpty() : bool;

    /**
     * Return a collection of raw HTTP header lines
     *
     * @return string[] - [ 'name: value, value', ... ]
     */
    function toRawHeaders() : array;

    /**
     * Return a collection of HTTP header names to comma separated values
     *
     * @return string[] - [ 'name' => 'value, value', ... ]
     */
    function toFlattenedArray() : array;

    /**
     * Return multi-dimensional HTTP header collection
     *
     * @return array - [ 'name' => ['value', ... ]
     */
    function toArray() : array;

    /**
     * Return the mutable interface of this header collection
     *
     * @return IMutableHeaders
     */
    function toMutableHeaders() : IMutableHeaders;

    /**
     * Return an new instance with the incoming HTTP headers merged with the existing HTTP headers
     *
     * @param IHeaders $headers
     * @return static
     */
    function toMergedHeaders(IHeaders $headers) : IHeaders;

    /**
     * Return an instance with raw header comma separation enabled
     *
     * @return static
     */
    function withRawHeaderCommaSeparationEnabled() : IHeaders;
}
