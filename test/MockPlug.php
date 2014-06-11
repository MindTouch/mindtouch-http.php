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
     * @structure [ [mock] => [response], [mock] => [response] ]
     */
    private static $mocks = array();

    /**
     * @var array
     * @structure [ [0] => mock, [1] => mock, [2] => mock ]
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
     * @param MockPlugRequestSettings $RequestSettings
     * @return bool
     */
    public static function verify(MockPlugRequestSettings $RequestSettings) {
        return in_array(self::newMock($RequestSettings), self::$calls);
    }

    /**
     * Assert that all registered URI's have been called
     *
     * @return bool
     */
    public static function verifyAll() {
        foreach(self::$mocks as $id => $mock) {
            if(!in_array($id, self::$calls)) {
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
     * @param MockPlugRequestSettings $RequestSettings
     * @param MockPlugResponseSettings $ResponseSettings
     */
    public static function register(MockPlugRequestSettings $RequestSettings, MockPlugResponseSettings $ResponseSettings) {
        $mock = static::newMock($RequestSettings);
        self::$mocks[$mock] = array(
            'verb' => $RequestSettings->verb,
            'body' => $ResponseSettings->body,
            'headers' => $ResponseSettings->headers,
            'status' => $ResponseSettings->status,
            'type' => '',
            'errno' => '',
            'error' => ''
        );
        self::$registered = true;
    }

    /**
     * Get mocked response data in a format consumable by HttpPlug's invoke method
     *
     * @param MockPlugRequestSettings $RequestSettings
     * @return array - plug response data
     */
    public static function getResponse(MockPlugRequestSettings $RequestSettings) {
        $mock = static::newMock($RequestSettings);
        self::$calls[] = $mock;
        return isset(self::$mocks[$mock]) ? self::$mocks[$mock] : null;
    }

    /**
     * Reset MockPlug
     */
    public static function deregisterAll() {
        self::$mocks = array();
        self::$calls = array();
    }

    /**
     * @param MockPlugRequestSettings $RequestSettings
     * @return string
     */
    protected static function newMock(MockPlugRequestSettings $RequestSettings) {
        $params = array();

        // parse uri into components
        $uriParts = parse_url($RequestSettings->uri);
        parse_str($uriParts['query'], $params);

        // filter parameters & headers applied by dekiplug
        $params = array_diff_key($params, array_flip(self::$ignoreRequestQueryParams));
        $requestHeaders = array_diff_key($RequestSettings->headers, array_flip(self::$ignoreRequestHeaders));

        // build serialized mock string
        $key = $RequestSettings->verb . '_' . $uriParts['scheme'] . '://' . $uriParts['host'];
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
        return md5(serialize($requestHeaders) . $key . $RequestSettings->body);
    }
}
