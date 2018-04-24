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
namespace MindTouch\Http\tests\ApiPlug;

use MindTouch\Http\ApiPlug;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;

class urlEncode_Test extends MindTouchHttpUnitTestCase  {

    /**
     * @see ApiPlug::urlEncode()
     * @test
     */
    public function Can_url_encode_with_special_trailing_dot_handling() {

        // act
        $result = ApiPlug::urlEncode('abc/123/765?!/.');

        // assert
        $this->assertEquals('abc%2F123%2F765%3F%21%2F%2E', $result);
    }

    /**
     * @test
     */
    public function Can_double_url_encode() {

        // act
        $result = ApiPlug::urlEncode('abc/123/765?!/.', true);

        // assert
        $this->assertEquals('abc%252F123%252F765%253F%2521%252F%252E', $result);
    }
}
