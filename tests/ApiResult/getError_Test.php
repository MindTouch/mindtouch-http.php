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
namespace MindTouch\Http\Tests\ApiResult;

use MindTouch\Http\ApiResult;
use MindTouch\Http\Tests\MindTouchHttpUnitTestCase;

class getError_Test extends MindTouchHttpUnitTestCase {

    public static function body_expected_dataProvider() : array {
        return [
            [['error' => ['message' => 'foo']], 'foo'],
            [['exception' => ['message' => 'bar']], 'bar'],
            [['foo' => ['qux' => ['@id' => '123']]], '<foo><qux id="123"></qux></foo>'],
            ['<error>qux</error>', '<error>qux</error>']
        ];
    }

    /**
     * @dataProvider body_expected_dataProvider
     * @test
     */
    public function Can_get_error_from_body(string|array $body, string $expected) {

        // arrange
        $data = [
            'status' => 500,
            'error' => '',
            'errno' => 0,
            'body' => $body
        ];
        $result = new ApiResult($data);

        // act
        $result = $result->getError();

        // assert
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function Can_get_curl_error() : void {

        // arrange
        $data = [
            'errno' => 50,
            'error' => 'baz',
            'status' => 0
        ];
        $result = new ApiResult($data);

        // act
        $result = $result->getError();

        // assert
        $this->assertEquals('baz', $result);
    }

    /**
     * @test
     */
    public function Will_get_null_if_no_error() : void {

        // arrange
        $result = new ApiResult([]);

        // act
        $result = $result->getError();

        // assert
        $this->assertNull($result);
    }
}
