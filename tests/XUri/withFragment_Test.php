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

class withFragment_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function Can_set_fragment() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/?a=b&c=d';

        // act
        $result = XUri::tryParse($uriStr)->withFragment('foo');

        // assert
        $this->assertEquals('http://user:password@test.mindtouch.dev/?a=b&c=d#foo', $result);
    }

    /**
     * @test
     */
    public function Can_change_fragment() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/?a=b&c=d#foo';

        // act
        $result = XUri::tryParse($uriStr)->withFragment('bar');

        // assert
        $this->assertEquals('http://user:password@test.mindtouch.dev/?a=b&c=d#bar', $result);
    }

     /**
     * @test
     */
    public function Can_remove_fragment() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/?a=b&c=d';

        // act
        $result = XUri::tryParse($uriStr)->withFragment(null);

        // assert
        $this->assertEquals('http://user:password@test.mindtouch.dev/?a=b&c=d', $result);
    }
}
