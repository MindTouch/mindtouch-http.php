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
namespace MindTouch\Http\tests\HttpResult;

use MindTouch\Http\HttpResult;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;

class isRequestError_Test extends MindTouchHttpUnitTestCase  {

    /**
     * @test
     */
    public function HTTP_500_range_is_not_server_error() {

        // arrange
        $data = ['status' => 500];
        $result = new HttpResult($data);

        // act
        $isRequestError = $result->isRequestError();

        // assert
        $this->assertFalse($isRequestError);
    }

    /**
     * @return array
     */
    public static function status_dataProvider() : array {
        return [
            [400],
            [401],
            [403],
            [404]
        ];
    }

    /**
     * @dataProvider status_dataProvider
     * @param int $status
     * @test
     */
    public function HTTP_400_range_is_request_error(int $status) {

        // arrange
        $data = ['status' => $status];
        $result = new HttpResult($data);

        // act
        $isRequestError = $result->isRequestError();

        // assert
        $this->assertTrue($isRequestError);
    }
}
