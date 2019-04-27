<?php declare(strict_types=1);
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
namespace MindTouch\Http\Exception;

use Exception;
use MindTouch\Http\XUri;

class HttpPlugUriHostRequiredException extends Exception {

    /**
     * @var XUri
     */
    private $uri;

    /**
     * @param XUri $uri
     */
    public function __construct(XUri $uri) {
        $this->uri = $uri;
        parent::__construct('Uri does not contain a valid hostname: ' . $uri->toString());
    }

    /**
     * @return XUri
     */
    private function getUri() : XUri {
        return $this->uri;
    }
}