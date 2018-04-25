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

/**
 * Interface IMutableHeaders
 *
 * @package MindTouch\Http
 */
interface IMutableHeaders extends IHeaders {

    /**
     * Set or add value to a header
     *
     * @param string $name
     * @param string $value
     */
    function addHeader($name, $value);

    /**
     * Set or replace value on a header
     *
     * @param string $name
     * @param string $value
     */
    function setHeader($name, $value);

    /**
     * Set or add header value(s) with a raw header
     *
     * @param string $header - 'name: value, ...'
     */
    function addRawHeader($header);

    /**
     * Set or replace header value(s) with a raw header
     *
     * @param string $header - 'name: value, ...'
     */
    function setRawHeader($header);

    /**
     * Remove a header and all its values
     *
     * @param string $name
     */
    function removeHeader($name);
}
