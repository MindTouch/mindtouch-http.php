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

use InvalidArgumentException;
use MindTouch\Http\XUri;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;

class withQuery_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function Can_set_query_string() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/#fragment';

        // act
        $result = XUri::tryParse($uriStr)->withQuery('a=b&c=d');

        // assert
        $this->assertEquals('http://user:password@test.mindtouch.dev/?a=b&c=d#fragment', $result);
    }

    /**
     * @test
     */
    public function Can_replace_query_string() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/?a=b&c=d#fragment';

        // act
        $result = XUri::tryParse($uriStr)->withQuery('foo=bar');

        // assert
        $this->assertEquals('http://user:password@test.mindtouch.dev/?foo=bar#fragment', $result);
    }

    /**
     * @test
     */
    public function Cannot_include_question_mark_symbol_in_query_string() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/?a=b&c=d#fragment';

        // ac
        $exceptionThrown = false;
        try {
            XUri::tryParse($uriStr)->withQuery('?foo=bar');
        } catch(InvalidArgumentException $e) {
            $exceptionThrown = true;
        }

        // assert
        $this->assertTrue($exceptionThrown);
    }
}
