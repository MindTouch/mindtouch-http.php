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
namespace MindTouch\Http\Content;

use MindTouch\Http\StringUtil;

/**
 * Class ContentType
 *
 * @package MindTouch\Http\Content
 */
class ContentType {
    const JSON = 'application/json; charset=utf-8';
    const CSS = 'text/css; charset=utf-8';
    const JAVASCRIPT = 'application/javascript; charset=utf-8';
    const HTML = 'text/html; charset=utf-8';
    const XML = 'application/xml; charset=utf-8';
    const TEXT = 'text/plain; charset=utf-8';
    const STREAM = 'application/octet-stream';
    const FORM = 'multipart/form-data';
    const PHP = 'application/php; charset=utf-8';

    /**
     * @param string $type
     * @return bool
     */
    public static function isJson($type) { return StringUtil::startsWithInvariantCase($type, 'application/json') || StringUtil::startsWithInvariantCase($type, 'text/json'); }

    /**
     * @param string $type
     * @return bool
     */
    public static function isXml($type) { return StringUtil::startsWithInvariantCase($type, 'application/xml') || StringUtil::startsWithInvariantCase($type, 'text/xml'); }

    /**
     * @param string $type
     * @return bool
     */
    public static function isText($type) { return StringUtil::startsWithInvariantCase($type, 'text/plain'); }
}
