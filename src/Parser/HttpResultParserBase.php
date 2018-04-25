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
namespace MindTouch\Http\Parser;

use MindTouch\Http\Exception\CannotParseContentExceedsMaxContentLengthException;
use MindTouch\Http\Headers;
use MindTouch\Http\HttpResult;

/**
 * Class HttpResultParserBase
 *
 * @package MindTouch\Http\Parser
 */
abstract class HttpResultParserBase {

    /**
     * @var int|null
     */
    protected $maxContentLength = null;

    /**
     * @param HttpResult $result
     * @throws CannotParseContentExceedsMaxContentLengthException
     */
    protected function validateContentLength(HttpResult $result) {
        if(!is_int($this->maxContentLength)) {
            return;
        }
        if(intval($result->getHeaders()->getHeaderLine(Headers::HEADER_CONTENT_LENGTH)) > $this->maxContentLength) {
            throw new CannotParseContentExceedsMaxContentLengthException($result);
        }
    }
}
