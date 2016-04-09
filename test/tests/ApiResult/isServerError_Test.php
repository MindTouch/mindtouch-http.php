<?php
/**
 * MindTouch API PHP Client
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
namespace MindTouch\ApiClient\test\tests\ApiResult;

use MindTouch\ApiClient\ApiResult;
use PHPUnit_Framework_TestCase;

class isServerError_Test extends PHPUnit_Framework_TestCase  {

    /**
     * @test
     */
    public function HTTP_400_range_is_not_server_error() {

        // arrange
        $data = ['status' => 400];
        $ApiResult = new ApiResult($data);

        // act
        $isServerError = $ApiResult->isServerError();

        // assert
        $this->assertFalse($isServerError);
    }

    /**
     * @test
     */
    public function HTTP_500_range_is_server_error_1() {

        // arrange
        $data = ['status' => 503];
        $ApiResult = new ApiResult($data);

        // act
        $isServerError = $ApiResult->isServerError();

        // assert
        $this->assertTrue($isServerError);
    }

    /**
     * @test
     */
    public function HTTP_500_range_is_server_error_2() {

        // arrange
        $data = ['status' => 500];
        $ApiResult = new ApiResult($data);

        // act
        $isServerError = $ApiResult->isServerError();

        // assert
        $this->assertTrue($isServerError);
    }
}
