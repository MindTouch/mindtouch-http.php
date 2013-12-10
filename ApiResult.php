<?php
/*
 * MindTouch API Client
 * Copyright (C) 2006-2013 MindTouch, Inc.
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
namespace MindTouch\ApiClient;

/**
 * Class ApiResult - wraps MindTouch API results with accessors
 * @package MindTouch\ApiClient
 */
class ApiResult extends HttpResult {

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
     * @return bool
     * @throws CannotLoadCurrentSiteException
     */
    public function handleResponse() {

        // a plug response was not returned
        if(!is_array($this->array)) {
            return false;
        }

        // 503: Service not Available usually means host has crashed
        if($this->getStatus() === 503) {
            throw new CannotLoadCurrentSiteException($this);
        }

        // 200-level is good
        if($this->isSuccess()) {
            return true;
        }
        return false;
    }
}
