<?php
/*
 * MindTouch API PHP Client
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

use Exception;

class ApiResultException extends Exception {
    protected $Result;

    /**
     * @param ApiResult $Result
     */
    public function __construct($Result) {
        $this->Result = $Result;
        $error = $Result->getError();
        if(is_array($error)) {

            // exception must be a string
            $error = json_encode($error);
        }
        parent::__construct($error != null ? $error : 'unknown');
    }

    /**
     * @return ApiResult
     */
    public function getResult() { return $this->Result; }

    /**
     * @return string
     */
    public function getRequestUri() { return $this->Result->getVal('request/uri'); }

    /**
     * @return string
     */
    public function getRequestVerb() { return $this->Result->getVal('request/verb'); }

    /**
     * @return string
     */
    public function getType() { return $this->Result->getException(); }
}
