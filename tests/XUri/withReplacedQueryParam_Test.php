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
namespace MindTouch\Http\tests\XUri;

use MindTouch\Http\XUri;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;

class withReplacedQueryParam_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function Can_replace_query_parameter() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/?a=b&c=d#fragment';

         // act
        $result = XUri::tryParse($uriStr)->withReplacedQueryParam('a', 'e');

        // assert
        $this->assertEquals('http://user:password@test.mindtouch.dev/?a=e&c=d#fragment', $result);
    }

    /**
     * @test
     */
    public function Can_remove_query_parameter() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/?a=b&c=d#fragment';

         // act
        $result = XUri::tryParse($uriStr)->withReplacedQueryParam('a', null);

        // assert
        $this->assertEquals('http://user:password@test.mindtouch.dev/?c=d#fragment', $result);
    }

    /**
     * @test
     */
    public function Will_do_nothing_when_query_parameter_not_set() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/?a=b&c=d#fragment';

         // act
        $result = XUri::tryParse($uriStr)->withReplacedQueryParam('f', 'g');

        // assert
        $this->assertEquals('http://user:password@test.mindtouch.dev/?a=b&c=d#fragment', $result);
    }
}
