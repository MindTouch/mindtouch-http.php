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

/**
 * Class MockPlug
 *
 * A global HttpPlug interceptor for testing
 *
 * @package MindTouch\ApiClient\test
 */
class MockPlug {

    /**
     * @var bool
     */
    public static $registered = false;

    /**
     * @var array
     */
    private static $ignoreRequestQueryParams = array();

    /**
     * @var array
     */
    private static $ignoreRequestHeaders = array();

    /**
     * @var array
     * @structure [ [id] => MockResponse, [id] => MockResponse ]
     */
    private static $responses = array();

    /**
     * @var array
     * @structure [ [id] => MockRequest, [id] => MockRequest ]
     */
    private static $calls = array();

    /**
     * Ignore URI query param when matching requests to mocked responses
     *
     * @param string $param
     */
    public static function ignoreRequestQueryParam($param) { self::$ignoreRequestQueryParams[] = $param; }

    /**
     * Ignore HTTP Header name when matching requests to mocked responses
     *
     * @param string $header
     */
    public static function ignoreRequestHeader($header) { self::$ignoreRequestHeaders[] = $header; }

    /**
     * Assert that call to URI has been made
     *
     * @param MockRequest $Request
     * @return bool
     */
    public static function verify(MockRequest $Request) {
        return isset(self::$calls[self::newMockId($Request)]);
    }

    /**
     * Assert that all registered URI's have been called
     *
     * @return bool
     */
    public static function verifyAll() {
        foreach(self::$responses as $id => $Response) {
            if(!isset(self::$calls[$id])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Assert that at least one call attempt was made
     *
     * @return bool
     */
    public static function verifyCalled() { return !empty(self::$calls); }

    /**
     * New request and response to mock
     *
     * @param MockRequest $Request
     * @param MockResponse $Response
     */
    public static function register(MockRequest $Request, MockResponse $Response) {
        self::$responses[self::newMockId($Request)] = $Response;
        self::$registered = true;
    }

    /**
     * Get mocked response data
     *
     * @param MockRequest $Request
     * @return MockResponse
     */
    public static function getResponse(MockRequest $Request) {
        $id = self::newMockId($Request);
        self::$calls[$id] = $Request;
        return isset(self::$responses[$id]) ? self::$responses[$id] : null;
    }

    /**
     * Get collection of attempted http calls
     *
     * @return array
     */
    public static function getCalls() { return self::$calls; }

    /**
     * Reset MockPlug
     */
    public static function deregisterAll() {
        self::$responses = array();
        self::$calls = array();
    }

    /**
     * @param MockRequest $Request
     * @return string
     */
    protected static function newMockId(MockRequest $Request) {
        $params = [];

        // parse uri into components
        $uriParts = parse_url($Request->uri);
        if(isset($uriParts['query'])) {
            parse_str($uriParts['query'], $params);
        }
     
        // filter parameters & headers applied by dekiplug
        $params = array_diff_key($params, array_flip(self::$ignoreRequestQueryParams));
        $requestHeaders = array_diff_key($Request->headers, array_flip(self::$ignoreRequestHeaders));

        // build serialized mock string
        $key = $Request->verb . '_' . $uriParts['scheme'] . '://' . $uriParts['host'];
        if(isset($uriParts['port'])) {
            $key .= ':' . $uriParts['port'];
        }
        if(isset($uriParts['path'])) {
            $key .= $uriParts['path'];
        }
        asort($params);
        if(!empty($params)) {
            $key .= '?' . http_build_query($params);
        }
        ksort($requestHeaders);
        return md5(serialize($requestHeaders) . $key . $Request->body);
    }
}
