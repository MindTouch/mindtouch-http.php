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

/**
 * Class ApiResult - wraps http result with MindTouch API specific accessors
 *
 * @package MindTouch\Http
 */
class ApiResult extends HttpResult {

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
     * Return an error message, or an xml representation of the HTTP response body.
     *
     * @return string|null
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

        // curl error
        $error = $this->getCurlError();
        if($error !== null) {
            return $error;
        }

        // the error message must be in the body if nowhere else
        $error = $this->getVal('body');
        if($error !== null) {
            if(is_array($error)) {
                return $this->getXml('body');
            }
            return $error;
        }
        return null;
    }

    /**
     * Return the API exception type name.
     *
     * @return string|null
     */
    public function getException() {
        $exception = $this->getVal('body/error/exception');
        return $exception !== null ? $exception : null;
    }
}
