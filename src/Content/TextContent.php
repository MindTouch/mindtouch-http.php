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
namespace MindTouch\Http\Content;

/**
 * Class TextContent
 *
 * @package MindTouch\Http\Content
 */
class TextContent implements IContent {

    /**
     * @var string
     */
    private $text;

    /**
     * @var ContentType
     */
    private $contentType;

    /**
     * @param string $text
     * @param ContentType|null $contentType - defaults to text/plain
     */
    public function __construct(string $text, ContentType $contentType = null) {
        if($contentType === null) {
            $contentType = ContentType::newFromString(ContentType::TEXT);
        }
        $this->contentType = $contentType;
        $this->text = $text;
    }

    public function __clone() {

        // deep copy internal data objects and arrays
        $this->contentType = unserialize(serialize($this->contentType));
    }

    public function getContentType() : ContentType { return $this->contentType; }

    public function toRaw() : string { return $this->text; }

    public function toString() : string { return $this->text; }

    public function __toString() : string { return $this->toString(); }
}
