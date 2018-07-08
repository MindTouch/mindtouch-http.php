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
namespace MindTouch\Http;

use MindTouch\Http\Content\ContentType;
use MindTouch\Http\Content\IContent;
use MindTouch\XArray\XArray;

/**
 * Class HttpResult - wraps http result with accessors
 *
 * @package MindTouch\Http
 */
class HttpResult extends XArray {

    public function __clone() {

        // deep copy internal data objects and arrays
        $this->array = unserialize(serialize($this->array));
    }


    /**
     * Return an instance with the added request information
     *
     * @param string $method
     * @param XUri $uri
     * @param IHeaders $headers
     * @param int $start - curl start timestamp
     * @param int $end - curl stop timestamp
     * @return static
     */
    public function withRequestInfo($method, XUri $uri, IHeaders $headers, $start, $end) {
        $result = clone $this;
        $result->array['request'] = [
            'method' => $method,
            'uri' => $uri->toString(),
            'headers' => $headers->toArray(),
            'start' => $start,
            'end' => $end
        ];
        return $result;
    }

    /**
     * Return an instance with the specified HTTP status code
     *
     * @param int $status
     * @return static
     */
    public function withStatus($status) {
        $result = clone $this;
        $result->array['status'] = $status;
        return $result;
    }

    /**
     * Return an instance with the specified HTTP headers
     *
     * @param IHeaders $headers
     * @return static
     */
    public function withHeaders(IHeaders $headers) {
        $result = clone $this;
        $result->array['headers'] = $headers->toArray();
        return $result;
    }

    /**
     * Return an instance with the specified body
     *
     * @param string|array $body
     * @return static
     */
    public function withBody($body) {
        $result = clone $this;
        $result->array['body'] = $body;
        return $result;
    }

    /**
     * Return an instance with the specified result content body and content type
     *
     * @param  $content
     * @return static
     */
    public function withContent(IContent $content) {
        $result = clone $this;
        $result->array['body'] = $content->toString();
        $result->array['type'] = $content->getContentType();
        return $result;
    }

    /**
     * Retrieve the HTTP response status code
     *
     * @return int
     */
    public function getStatus() { return isset($this->array['status']) ? $this->array['status'] : 0; }

    /**
     * Retrieve the HTTP response content type
     *
     * @return ContentType|null - returns null if not set or invalid content type
     */
    public function getContentType() { return isset($this->array['type']) ? ContentType::newFromContentTypeHeaderLine($this->array['type']) : null; }

    /**
     * Retrieve an instance of HTTP response headers
     *
     * @return IHeaders
     */
    public function getHeaders() {
        $headers = new Headers();
        if(empty($this->getVal('headers'))) {
            return $headers;
        }
        foreach($this->getVal('headers') as $name => $values) {
            foreach($values as $value) {
                $headers->addHeader($name, $value);
            }
        }
        return $headers;
    }

    /**
     * Return a XArray representation of the HTTP response body (unparsed HttpResult will return ['body' => string])
     *
     * @return XArray
     */
    public function getBody() {
        $body = $this->getVal('body');
        if(!is_array($body)) {
            $body = ['body' => $body];
        }
        return new XArray($body);
    }

    /**
     * Retrieve an XML string representation of a result value, or the entire result
     *
     * @param string|null $key - result key name (ex: body/content), empty returns entire result as xml (aliases HttpResult::toXml)
     * @return string
     */
    public function getXml($key = null) {
        if(StringUtil::isNullOrEmpty($key)) {
            return "<result>{$this->toXml()}</result>";
        }
        $value = $this->getVal($key, null);
        $XArray = new XArray($value);
        return $XArray->toXml();
    }

    /**
     * What is the HTTP response status code?
     *
     * @param int $status
     * @return bool
     */
    public function is($status) { return $this->getStatus() === $status; }

    /**
     * Is the response status code in the HTTP 2xx range?
     *
     * @return bool
     */
    public function isSuccess() {
        $status = $this->getStatus();
        return $status >= 200 && $status < 300;
    }

    /**
     * Is the response status code in the HTTP 3xx range?
     *
     * @return bool
     */
    public function isRedirect() {
        $status = $this->getStatus();
        return $status >= 300 && $status < 400;
    }

    /**
     * Did the request fail due to a server problem?
     *
     * @return bool
     */
    public function isServerError() {
        $status = $this->getStatus();
        return $status >= 500 && $status < 600;
    }

    /**
     * Did the request fail due to a request problem?
     *
     * @return bool
     */
    public function isRequestError() {
        $status = $this->getStatus();
        return $status >= 400 && $status < 500;
    }

       /**
     * Is there a connection problem or internal curl error?
     *
     * @return bool
     */
    public function isCurlError() { return $this->array['errno'] > 0; }

    /**
     * Get curl internal error message
     *
     * @return string|null
     */
    public function getCurlError() { return (isset($this->array['error'])) ? $this->array['error'] : null; }
}
