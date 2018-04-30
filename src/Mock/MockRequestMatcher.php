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
namespace MindTouch\Http\Mock;

use InvalidArgumentException;
use MindTouch\Http\Content\IContent;
use MindTouch\Http\Headers;
use MindTouch\Http\HttpPlug;
use MindTouch\Http\IHeaders;
use MindTouch\Http\XUri;

/**
 * Class MockRequest
 *
 * Object for configuring a MockPlug request to mock or verify; file uploads not supported
 *
 * @package MindTouch\Http\Mock
 */
class MockRequestMatcher {

    /**
     * @var string[]
     */
    private static $ignoredQueryParamNames = [];

    /**
     * @var string[]
     */
    private static $ignoredHeaderNames = [];

    /**
     * Set query param names to ignore during matching
     *
     * @param string[] $names
     */
    public static function setIgnoredQueryParamNames(array $names) { self::$ignoredQueryParamNames = $names; }

    /**
     * Set HTTP header names to ignore during matching
     *
     * @param string[] $names
     */
    public static function setIgnoredHeaderNames(array $names) { self::$ignoredHeaderNames = $names; }

    /**
     * @var string
     */
    private $method = HttpPlug::METHOD_GET;

    /**
     * @var XUri
     */
    private $uri;

    /**
     * @var IHeaders
     */
    private $headers;

    /**
     * @var string|null
     */
    private $body;

    /**
     * @param string $method
     * @param XUri $uri
     */
    public function __construct($method, XUri $uri) {
        $this->method = $method;
        $this->uri = $uri;
        $this->headers = new Headers();
    }

    /**
     * Return an instance with the specified HTTP headers.
     *
     * @param IHeaders $headers
     * @return MockRequestMatcher
     */
    public function withHeaders(IHeaders $headers) {
        $request = clone $this;
        $request->headers = $headers;
        return $request;
    }

    /**
     * Return an instance with the specified body string
     *
     * @param string|string[]|null $body - array body is assumed to be form fields and will be encoded to a string
     * @return MockRequestMatcher
     */
    public function withBody($body) {
        if(!is_string($body) && !is_array($body) && $body !== null) {
            throw new InvalidArgumentException('Body value must be string, array, or null');
        }
        if(is_array($body)) {
            $body = http_build_query($body);
        }
        $request = clone $this;
        $request->body = $body;
        return $request;
    }

    /**
     * Return an instance with the specified content. Method will set a body and content-type depending on
     * the value of the content object
     *
     * @param IContent $content
     * @return MockRequestMatcher
     */
    public function withContent(IContent $content) {
        $request = clone $this;
        $request->headers->setHeader(Headers::HEADER_CONTENT_TYPE, $content->getContentType());
        $request->body = $content->toString();
        return $request;
    }

    /**
     * Retrieve denormalized matcher uri
     *
     * @return XUri
     */
    public function getUri() { return $this->uri; }

    /**
     * Retrieve id to match mock results to matcher
     *
     * @return string
     */
    public function getMatcherId() {
        $uri = $this->newNormalizedUriString();
        $headers = $this->newNormalizedHeaderStrings();
        return md5(serialize($headers) . "{$this->method}{$uri}{$this->body}");
    }

    /**
     * @return array
     */
    public function toArray() {
        return [
            'method' => $this->method,
            'uri' => $this->uri->toString(),
            'headers' => $this->headers->toFlattenedArray(),
            'body' => $this->body !== null ? $this->body : ''
        ];
    }

    /**
     * @return array
     */
    public function toNormalizedArray() {
        return [
            'method' => $this->method,
            'uri' => $this->newNormalizedUriString(),
            'headers' => $this->newNormalizedHeaderStrings(),
            'body' => $this->body !== null ? $this->body : ''
        ];
    }

    /**
     * @return string
     */
    private function newNormalizedUriString() {
        $params = [];

        // parse uri into components
        $data = parse_url($this->uri);
        if(isset($data['query'])) {
            parse_str($data['query'], $params);
        }

        // filter parameters applied by Plug
        $params = array_diff_key($params, array_flip(self::$ignoredQueryParamNames));

        // rebuild uri
        $uri = $data['scheme'] . '://' . $data['host'];
        if(isset($data['port'])) {
            $uri .= ':' . $data['port'];
        }
        if(isset($data['path'])) {
            $uri .= $data['path'];
        }
        asort($params);
        if(!empty($params)) {
            $uri .= '?' . http_build_query($params);
        }
        return $uri;
    }

    /**
     * @return string[]
     */
    private function newNormalizedHeaderStrings() {
        $headers = $this->headers->toFlattenedArray();
        $headers = array_diff_key($headers, array_flip(self::$ignoredHeaderNames));

        // rebuild headers
        ksort($headers);
        return $headers;
    }
}
