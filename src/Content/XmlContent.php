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

use MindTouch\XArray\XArray;

/**
 * Class XmlContent
 *
 * @package MindTouch\Http\Content
 */
class XmlContent implements IContent {

    /**
     * Return an instance from an XML encoded array
     *
     * @param array $xml
     * @return static
     */
    public static function newFromArray(array $xml) { return new static((new XArray($xml))->toXml()); }

    /**
     * @var string
     */
    private $xml;

    /**
     * @param string $xml
     */
    public function __construct($xml) {
        $this->xml = $xml;
    }

    public function getContentType() { return ContentType::XML; }

    public function toRaw() { return $this->xml; }

    public function toString() { return $this->xml; }

    public function __toString() { return $this->toString(); }
}
