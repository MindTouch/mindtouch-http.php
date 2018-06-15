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
namespace MindTouch\Http\Exception;

use Exception;
use MindTouch\Http\HttpResult;

/**
 * Class ApiResultException
 *
 * @package MindTouch\Http\Exception
 */
class HttpResultParserException extends Exception {

    /**
     * @var HttpResult
     */
    private $result;

    /**
     * @param HttpResult $result
     * @param string $reason
     */
    public function __construct(HttpResult $result, $reason) {
        parent::__construct('Cannot parse result content: ' . $reason);
        $this->result = $result;
    }

    /**
     * @return HttpResult
     */
    public function getResult() { return $this->result; }
}
