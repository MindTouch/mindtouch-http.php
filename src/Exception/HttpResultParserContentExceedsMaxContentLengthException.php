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
namespace MindTouch\Http\Exception;

use MindTouch\Http\HttpResult;

/**
 * Class HttpResultParserContentExceedsMaxContentLengthException
 *
 * @package MindTouch\Http\Exception
 */
class HttpResultParserContentExceedsMaxContentLengthException extends HttpResultParserException {

    /**
     * @var int
     */
    private $resultContentLength = 0;

    /**
     * @var int
     */
    private $maxContentLength = 0;

    /**
     * @param HttpResult $result
     * @param int $resultContentLength
     * @param int $maxContentLength
     */
    public function __construct(HttpResult $result, int $resultContentLength, int $maxContentLength) {
        parent::__construct($result, 'content exceeds max content length');
        $this->resultContentLength = $resultContentLength;
        $this->maxContentLength = $maxContentLength;
    }

    /**
     * @return int
     */
    public function getResultContentLength() : int {
        return $this->resultContentLength;
    }

    /**
     * @return int
     */
    public function getMaxContentLength() : int {
        return $this->maxContentLength;
    }
}
