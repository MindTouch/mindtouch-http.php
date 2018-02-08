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
namespace MindTouch\Http\Mock;

/**
 * Class MockPlug
 *
 * A global HttpPlug interceptor for testing
 *
 * @package MindTouch\Http\test
 */
class MockPlug {

    /**
     * @var bool
     */
    public static $registered = false;

    /**
     * @var string[]
     */
    private static $ignoreRequestQueryParams = [];

    /**
     * @var string[]
     */
    private static $ignoreRequestHeaders = [];

    /**
     * @var object[]
     * @structure [ [id] => { Request: MockRequest, Response: MockResponse, verify: bool }, ... ]
     */
    private static $mocks = [];

    /**
     * @var MockRequest[]
     * @structure [ [id] => MockRequest, ... ]
     */
    private static $calls = [];

    /**
     * @var string[]
     * @structure [ id, ... ]
     */
    private static $matches = [];

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
    public static function verify(MockRequest $Request) { return isset(self::$calls[self::newMockId($Request)]); }

    /**
     * Assert that all registered URI's have been called
     *
     * @return bool
     */
    public static function verifyAll() {
        foreach(self::$mocks as $id => $Mock) {
            if($Mock->verify && !isset(self::$calls[$id])) {
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
     * @param bool $verify - verify when all registered uri calls are checked
     */
    public static function register(MockRequest $Request, MockResponse $Response, $verify = true) {
        self::$mocks[self::newMockId($Request)] = (object) [
            'Request' => $Request,
            'Response' => $Response,
            'verify' => $verify
        ];
        self::$registered = true;
    }

    /**
     * Get mocked response data
     *
     * @param MockRequest $Request
     * @return MockResponse|null
     */
    public static function getResponse(MockRequest $Request) {
        $id = self::newMockId($Request);
        $Response = isset(self::$mocks[$id]) ? self::$mocks[$id]->Response : null;
        self::$calls[$id] = $Request;
        if($Response !== null) {
            self::$matches[] = $id;
        }
        return $Response;
    }

    /**
     * Get collection of attempted http calls
     *
     * @return MockRequest[]
     */
    public static function getCalls() { return self::$calls; }

    /**
     * Get collection of attempted http calls with normalized data for reporting
     *
     * @return MockRequest[]
     */
    public static function getNormalizedCalls() {
        $calls = [];
        foreach(self::$calls as $id => $MockRequest) {
            $NewMockRequest = clone $MockRequest;
            $NewMockRequest->uri = self::newNormalizedUri($MockRequest->uri);
            $NewMockRequest->headers = self::newNormalizedHeaders($MockRequest->headers);

            // magic property is used only for reporting
            $NewMockRequest->matched = in_array($id, self::$matches);
            $calls[$id] = $NewMockRequest;
        }
        return $calls;
    }

    /**
     * @structure [ ['MockRequest' => MockRequest, 'MockResponse' => MockResponse'], ... ]
     * @return array
     */
    public static function getMocks() {
        $mocks = [];
        foreach(self::$mocks as $id => $Mock) {
            $mocks[$id] = [
                'MockRequest' => $Mock->Request,
                'MockResponse' => $Mock->Response
            ];
        }
        return $mocks;
    }

    /**
     * Get a collection of mocked http requests with normalized data for reporting
     *
     * @structure [ ['MockRequest' => MockRequest, 'MockResponse' => MockResponse'], ... ]
     * @return array
     */
    public static function getNormalizedMocks() {
        $mocks = [];
        foreach(self::$mocks as $id => $Mock) {
            $MockRequest = clone $Mock->Request;

            /** @var MockRequest $MockRequest */
            $MockRequest->uri = self::newNormalizedUri($MockRequest->uri);
            $MockRequest->headers = self::newNormalizedHeaders($MockRequest->headers);

            // magic property is used only for reporting
            $MockRequest->optional = !$Mock->verify;
            $mocks[$id] = [
                'MockRequest' => $MockRequest,
                'MockResponse' => $Mock->Response
            ];
        }
        return $mocks;
    }

    /**
     * Reset MockPlug
     */
    public static function deregisterAll() {
        self::$mocks = [];
        self::$calls = [];
    }

    /**
     * @param MockRequest $Request
     * @return string
     */
    protected static function newMockId(MockRequest $Request) {
        $requestHeaders = self::newNormalizedHeaders($Request->headers);
        $uri = self::newNormalizedUri($Request->uri);
        return md5(serialize($requestHeaders) . "{$Request->verb}_{$uri}{$Request->body}");
    }

    /**
     * @param string $uri
     * @return string
     */
    private static function newNormalizedUri($uri) {
        $params = [];

        // parse uri into components
        $uriParts = parse_url($uri);
        if(isset($uriParts['query'])) {
            parse_str($uriParts['query'], $params);
        }

        // filter parameters applied by Plug
        $params = array_diff_key($params, array_flip(self::$ignoreRequestQueryParams));

        // rebuild uri
        $uri = $uriParts['scheme'] . '://' . $uriParts['host'];
        if(isset($uriParts['port'])) {
            $uri .= ':' . $uriParts['port'];
        }
        if(isset($uriParts['path'])) {
            $uri .= $uriParts['path'];
        }
        asort($params);
        if(!empty($params)) {
            $uri .= '?' . http_build_query($params);
        }
        return $uri;
    }

    private static function newNormalizedHeaders(array $headers) {

        // filter headers applied by Plug
        $headers = array_diff_key($headers, array_flip(self::$ignoreRequestHeaders));

        // rebuild headers
        ksort($headers);
        return $headers;
    }
}