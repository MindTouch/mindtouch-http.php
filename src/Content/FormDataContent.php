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
use CURLFile;

/**
 * Class FormDataContent
 *
 * @package MindTouch\Http\Content
 */
class FormDataContent implements IContent {

    /**
     * @var ContentType
     */
    private $contentType;

    /**
     * @var string[]
     */
    private $data;

    /**
     * @var FileContent[]
     */
    private $files = [];

    /**
     * @param string[] $data - name/value pairs of form data
     */
    public function __construct(array $data) {
        $this->contentType = ContentType::newFromString(ContentType::FORM);
        $this->data = $data;
    }

    public function __clone() {

        // deep copy internal data objects and arrays
        $this->contentType = unserialize(serialize($this->contentType));
        $this->data = unserialize(serialize($this->data));
        $this->files = unserialize(serialize($this->files));
    }

    public function getContentType() { return $this->contentType; }

    public function toRaw() {
        $data = [];
        foreach($this->files as $key => $file) {
            $data["file[{$key}]"] = new CURLFile($file->toString(), $file->getContentType()->toString());
        }
        return array_merge($this->data, $data);
    }

    public function toString() { return http_build_query($this->data); }

    public function __toString() { return $this->toString(); }

    /**
     * Return an instance with a new CurlFile as part of the form body data
     *
     * @param FileContent $content
     * @return FormDataContent
     */
    public function withFileContent(FileContent $content) {
        $instance = clone $this;
        $instance->files[] = $content;
        return $instance;
    }
}
