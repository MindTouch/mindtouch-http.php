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
use CURLFile;

/**
 * Class MultiPartFormDataContent
 *
 * @package MindTouch\Http\Content
 */
class MultiPartFormDataContent implements IContent {

    /**
     * @var ContentType|null
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
        $this->contentType = ContentType::newFromString(ContentType::FORM_MULTIPART);
        $this->data = $data;
    }

    public function __clone() {

        // deep copy internal data objects and arrays
        $this->contentType = unserialize(serialize($this->contentType));
        $this->data = unserialize(serialize($this->data));
        $this->files = unserialize(serialize($this->files));
    }

    public function getContentType() : ?ContentType { return $this->contentType; }

    public function toRaw() : array {
        $data = [];
        foreach($this->files as $key => $file) {
            $contentType = $file->getContentType();
            if($contentType === null) {

                // skip invalid file content types
                continue;
            }
            $data["file[{$key}]"] = new CURLFile($file->toString(), $contentType->toString());
        }
        return array_merge($this->data, $data);
    }

    public function toString() : string { return http_build_query($this->data); }

    public function __toString() : string { return $this->toString(); }

    /**
     * Return an instance with a new CurlFile as part of the form body data
     *
     * @param FileContent $content
     * @return MultiPartFormDataContent
     */
    public function withFileContent(FileContent $content) : MultiPartFormDataContent {
        $instance = clone $this;
        $instance->files[] = $content;
        return $instance;
    }
}
