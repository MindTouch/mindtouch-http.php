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
namespace MindTouch\Http\tests\HttpResult;

use MindTouch\Http\HttpResult;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;

class getCurlError_Test extends MindTouchHttpUnitTestCase  {

    /**
     * @test
     */
    public function Can_get_curl_error() {

        // arrange
        $data = [
            'status' => HttpResult::HTTP_FOUND,
            'errno' => 47,
            'error' => 'foo'
        ];
        $result = new HttpResult($data);

        // act
        $error = $result->getCurlError();

        // assert
        $this->assertEquals('foo', $error);
    }

    /**
     * @test
     */
    public function Will_get_null_if_no_curl_error() {

        // arrange
        $data = [
            'status' => HttpResult::HTTP_FOUND,
            'errno' => 0
        ];
        $result = new HttpResult($data);

        // act
        $error = $result->getCurlError();

        // assert
        $this->assertNull($error);
    }
}
