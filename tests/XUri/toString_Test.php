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

use MindTouch\Http\tests\MindTouchHttpUnitTestCase;
use MindTouch\Http\XUri;

class toString_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function Does_not_removes_trailing_slash_from_path_if_it_was_included() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/somepath/?a=b&c=d#fragment';

        // act
        $result = XUri::tryParse($uriStr)->toString();

        // assert
        $this->assertEquals('http://user:password@test.mindtouch.dev/somepath/?a=b&c=d#fragment', $result);
    }

    /**
     * @test
     */
    public function Does_not_remove_trailing_slash_from_homepage_path_if_it_was_included_1() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/?a=b&c=d#fragment';

        // act
        $result = XUri::tryParse($uriStr)->toString();

        // assert
        $this->assertEquals('http://user:password@test.mindtouch.dev/?a=b&c=d#fragment', $result);
    }

    /**
     * @test
     */
    public function Does_not_remove_trailing_slash_from_homepage_path_if_it_was_included_2() {

        // arrange
        $uriStr = 'http://test.mindtouch.dev/';

        // act
        $result = XUri::tryParse($uriStr)->toString();

        // assert
        $this->assertEquals('http://test.mindtouch.dev/', $result);
    }

    /**
     * @test
     */
    public function Does_not_add_trailing_slash_from_path_if_it_was_not_included() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/somepath?a=b&c=d#fragment';

        // act
        $result = XUri::tryParse($uriStr)->toString();

        // assert
        $this->assertEquals('http://user:password@test.mindtouch.dev/somepath?a=b&c=d#fragment', $result);
    }

    /**
     * @test
     */
    public function Does_not_add_trailing_slash_to_homepage_path_if_it_was_not_included_1() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/?a=b&c=d#fragment';

        // act
        $result = XUri::tryParse($uriStr)->toString();

        // assert
        $this->assertEquals('http://user:password@test.mindtouch.dev/?a=b&c=d#fragment', $result);
    }

    /**
     * @test
     */
    public function Does_not_add_trailing_slash_to_homepage_path_if_it_was_not_included_2() {

        // arrange
        $uriStr = 'http://test.mindtouch.dev/';

        // act
        $result = XUri::tryParse($uriStr)->toString();

        // assert
        $this->assertEquals('http://test.mindtouch.dev/', $result);
    }
}
