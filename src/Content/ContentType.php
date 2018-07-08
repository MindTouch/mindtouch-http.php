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
     * Return a new ContentType instance from an HTTP header line (ex: text/html; charset=utf-8)
     *
     * @param string $contentTypeHeaderLine
     * @return ContentType
     */
    public static function newFromContentTypeHeaderLine($contentTypeHeaderLine) {
        $parts = array_map('trim', explode(';', $contentTypeHeaderLine));
        $typeParts = array_filter(explode('/', $parts[0], 2));
        if(count($typeParts) !== 2) {
            return null;
        }
        $mainType = strtolower($typeParts[0]);
        $subType = strtolower($typeParts[1]);
        $parameters = [];
        array_shift($parts);
        foreach($parts as $part) {
            if(!StringUtil::isNullOrEmpty($part)) {
                if(strpos($part, '=') === false) {
                    $k = $part;
                    $v = null;
                } else {
                    list($k, $v) = explode('=', $part);
                }
                $parameters[$k] = $v === null ? '' : $v;
            }
        }
        return new self($mainType, $subType, $parameters);
    }

    /**
     * @var string
     */
    private $mainType;

    /**
     * @var string[]
     */
    private $parameters = [];

    /**
     * @var string
     */
    private $subType;

    /**
     * @param string $mainType - main part of content-type header line (ex: application)
     * @param string $subType - sub type of content-type header line (ex: json)
     * @param string[] $parameters - key value pairs of parameters (ex: ['charset' => 'utf-8']
     */
    public function __construct($mainType, $subType, array $parameters = []) {
        $this->mainType = $mainType;
        $this->subType = $subType;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->toString();
    }

    /**
     * @return bool
     */
    public function isJson() { return $this->subType === 'json'; }

    /**
     * @return bool
     */
    public function isXml() { return $this->subType === 'xml'; }

    /**
     * @return bool
     */
    public function isPlainText() { return $this->mainType === 'text' && $this->subType === 'plain'; }

    /**
     * Return the main part of content-type (ex: text)
     *
     * @return string
     */
    public function toMainType() { return $this->mainType; }

    /**
     * Return the sub part of content-type (ex: xml)
     *
     * @return string
     */
    public function toSubType() { return $this->subType; }

    /**
     * Return an entire content-type string with parameters (ex: application/json; charset=latin)
     *
     * @return string
     */
    public function toString() {
        $stringBuilder = ["{$this->mainType}/{$this->subType}"];
        foreach($this->parameters as $parameter => $value) {
            $stringBuilder[] = "{$parameter}={$value}";
        }
        return implode('; ', $stringBuilder);
    }
}
