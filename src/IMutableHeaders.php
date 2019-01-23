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

/**
 * Interface IMutableHeaders
 *
 * @package MindTouch\Http
 */
interface IMutableHeaders extends IHeaders {

    /**
     * Set or add values from an HTTP headers collection
     *
     * @param IHeaders $headers
     * @return void
     */
    function addHeaders(IHeaders $headers) : void;

    /**
     * Set or add value to an HTTP header
     *
     * @param string $name
     * @param string|null $value
     * @return void
     */
    function addHeader(string $name, ?string $value) : void;

    /**
     * Set or replace value on an HTTP header
     *
     * @param string $name
     * @param string|null $value
     * @return void
     */
    function setHeader(string $name, ?string $value) : void;

    /**
     * Set or add header value(s) with a raw HTTP header
     *
     * @param string $header - 'name: value, ...'
     * @return void
     */
    function addRawHeader(string $header) : void;

    /**
     * Set or replace header value(s) with a raw HTTP header
     *
     * @param string $header - 'name: value, ...'
     * @return void
     */
    function setRawHeader(string $header) : void;

    /**
     * Remove an HTTP header and all its values
     *
     * @param string $name
     * @return void
     */
    function removeHeader(string $name) : void;
}
