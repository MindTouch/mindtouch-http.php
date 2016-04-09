<?php
/**
 * MindTouch API PHP Client
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
namespace MindTouch\ApiClient\test;

use MindTouch\ApiClient\HttpPlug;

/**
 * Class MockRequest
 *
 * Object for configuring a MockPlug request to mock or verify
 *
 * @package MindTouch\ApiClient\test
 */
class MockRequest {

    /**
     * @param string $verb
     * @param string $uri
     * @param array $headers
     * @param string|null $body
     * @return MockRequest
     */
    public static function newMockRequest($verb, $uri, array $headers, $body = null) {
        $Request = new self();
        $Request->verb = $verb;
        $Request->uri = $uri;
        $Request->headers = $headers;
        $Request->body = $body;
        return $Request;
    }

    /**
     * @var string
     */
    public $verb = HttpPlug::VERB_GET;

    /**
     * @var string
     */
    public $uri;

    /**
     * @var array - [ ["header"] => "value" ]
     */
    public $headers = array();

    /**
     * body is only relevant for PUT or POST requests
     *
     * @var string
     */
    public $body;
}
