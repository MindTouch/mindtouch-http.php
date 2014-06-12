<?php
/*
 * MindTouch
 * Copyright (c) 2006-2012 MindTouch Inc.
 * http://mindtouch.com
 *
 * This file and accompanying files are licensed under the
 * MindTouch Master Subscription Agreement (MSA).
 *
 * At any time, you shall not, directly or indirectly: (i) sublicense,
 * resell, rent, lease, distribute, market, commercialize or otherwise
 * transfer rights or usage to: (a) the Software, (b) any modified version
 * or derivative work of the Software created by you or for you, or (c)
 * MindTouch Open Source (which includes all non-supported versions of
 * MindTouch-developed software), for any purpose including timesharing or
 * service bureau purposes; (ii) remove or alter any copyright, trademark
 * or proprietary notice in the Software; (iii) transfer, use or export the
 * Software in violation of any applicable laws or regulations of any
 * government or governmental agency; (iv) use or run on any of your
 * hardware, or have deployed for use, any production version of MindTouch
 * Open Source; (v) use any of the Support Services, Error corrections,
 * Updates or Upgrades, for the MindTouch Open Source software or for any
 * Server for which Support Services are not then purchased as provided
 * hereunder; or (vi) reverse engineer, decompile or modify any encrypted
 * or encoded portion of the Software.
 *
 * A complete copy of the MSA is available at http://www.mindtouch.com/msa
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
        return isset(self::$responses[$id]) ? $responses[$id] : null;
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
        $params = array();

        // parse uri into components
        $uriParts = parse_url($Request->uri);
        parse_str($uriParts['query'], $params);

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
