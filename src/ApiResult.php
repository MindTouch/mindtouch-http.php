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
namespace MindTouch\Http;

use MindTouch\XArray\XArray;

/**
 * Class ApiResult - wraps MindTouch API results with accessors
 * @package MindTouch\Http
 */
class ApiResult extends XArray {

    /**
     * Return the value of a Set-Cookie header by the cookie's name
     *
     * @param string $name
     * @return string|null
     */
    public function getSetCookieHeader($name) {
        $headers = $this->getHeaders('Set-Cookie');
        if(!is_null($headers)) {
            if(!is_array($headers)) {
                $headers = [$headers];
            }
            foreach($headers as $header) {
                if(strpos($header, $name) === 0) {
                    return $header;
                }
            }
        }
        return null;
    }

    /**
     * @param string $name
     * @param string|null $default
     * @return array|null
     */
    public function getHeaders($name, $default = null) {
        if(!isset($this->array['headers'][$name])) {
            return $default;
        }
        $headers = $this->array['headers'][$name];
        if(!is_array($headers)) {
            $headers = [$headers];
        }
        return $headers;
    }

    /**
     * @param string $name
     * @param string|null $default
     * @return string|null
     */
    public function getHeader($name, $default = null) { return $this->getVal('headers/' . $name, $default); }

    /**
     * @param int $return
     * @return int
     */
    public function getStatus($return = 0) { return isset($this->array['status']) ? $this->array['status'] : $return; }

    /**
     * @param string $return
     * @return string
     */
    public function getUri($return = '') { return isset($this->array['uri']) ? $this->array['uri'] : $return; }

    /**
     * If there was a connection problem or internal curl error this will be true
     * @return bool
     */
    public function isCurlError() { return $this->array['errno'] > 0; }

    /**
     * @param int $status
     * @return bool
     */
    public function is($status) { return $this->getStatus() === $status; }

    /**
     * @return bool
     */
    public function isSuccess() {
        $status = $this->getStatus();
        return $status >= 200 && $status < 300;
    }

    /**
     * @return bool
     */
    public function isServerError() {
        $status = $this->getStatus();
        return $status >= 500 && $status < 600;
    }
    /**
     * @param string $key
     * @return string
     */
    public function getXml($key = '') {
        if($key == '') {
            return $this->toXml();
        }
        $val = $this->getVal($key, null);
        $XArray = new XArray($val);
        return $XArray->toXml();
    }

    /**
     * @return array|string|null
     */
    public function getError() {

        // formatted API error
        $error = $this->getVal('body/error/message');
        if($error !== null) {
            return $error;
        }

        // exception API error
        $error = $this->getVal('body/exception/message');
        if($error !== null) {
            return $error;
        }

        // other API error
        $error = $this->getVal('body');
        if($error !== null) {
            return $error;
        }

        // curl error
        return (isset($this->array['error'])) ? $this->array['error'] : null;
    }

    /**
     * @return string
     */
    public function getException() {
        $exception = $this->getVal('body/error/exception');
        return $exception !== null ? $exception : null;
    }

    /**
     * @return bool
     * @throws ApiResultException
     */
    public function handleResponse() {

        // a plug response was not returned
        if(!is_array($this->array)) {
            return false;
        }

        // 503: Service not Available usually means host has crashed
        if($this->getStatus() === 503) {
            throw new ApiResultException($this);
        }

        // 200-level is good
        if($this->isSuccess()) {
            return true;
        }
        return false;
    }
}
