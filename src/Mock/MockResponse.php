<?php
/**
 * MindTouch HTTP
 * Copyright (C) 2006-2016 MindTouch, Inc.
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
namespace MindTouch\Http\mock;

use MindTouch\Http\HttpPlug;

/**
 * Class MockResponse
 *
 * Object for configuring a MockPlug mock response
 *
 * @package MindTouch\Http\test
 */
class MockResponse {

    /**
     * @param int $status
     * @param array $headers
     * @param mixed $body
     * @return MockResponse
     */
    public static function newMockResponse($status, array $headers, $body) {
        $Response = new self();
        $Response->status = $status;
        $Response->headers = $headers;
        $Response->body = $body;
        return $Response;
    }

    /**
     * @var int
     */
    public $status = HttpPlug::HTTPSUCCESS;

    /**
     * @var array - [ ["header"] => "value" ]
     */
    public $headers = array();

    /**
     * @var mixed
     */
    public $body;
}
