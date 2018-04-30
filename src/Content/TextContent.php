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
     * @var string
     */
    private $contentType;

    /**
     * @param string $text
     * @param string|null $contentType - defaults to text/plain
     */
    public function __construct($text, $contentType = ContentType::TEXT) {
        $this->text = $text;
        $this->contentType = $contentType;
    }

    public function getContentType() { return $this->contentType; }

    public function toRaw() { return $this->text; }

    public function toString() { return $this->text; }

    public function __toString() { return $this->toString(); }
}
