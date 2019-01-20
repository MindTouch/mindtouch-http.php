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
namespace MindTouch\Http\tests\XUri;

use MindTouch\Http\tests\MindTouchHttpUnitTestCase;
use MindTouch\Http\XUri;

class at_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function Can_add_path_segments() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/?a=b&c=d#fragment';

         // act
        $result = XUri::tryParse($uriStr)->at('foo', 'bar', 'baz');

        // assert
        $this->assertEquals('http://user:password@test.mindtouch.dev/foo/bar/baz?a=b&c=d#fragment', $result);
    }

    /**
     * @test
     */
    public function Can_add_path_string() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/?a=b&c=d#fragment';

         // act
        $result = XUri::tryParse($uriStr)->at('foo/bar/baz');

        // assert
        $this->assertEquals('http://user:password@test.mindtouch.dev/foo/bar/baz?a=b&c=d#fragment', $result);
    }

    /**
     * @test
     */
    public function Can_preserve_slashes() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev';

        // act
        $result = XUri::tryParse($uriStr)->atPath('a/b/c')->at('foo', 'bar', 'baz');

        // assert
        $this->assertEquals('http://user:password@test.mindtouch.dev/a/b/c/foo/bar/baz', $result);
    }

    /**
     * @test
     */
    public function Will_do_nothing_if_empty_path_segments() {

            // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/a/b/c';

        // act
        $result = XUri::tryParse($uriStr)->at();

        // assert
        $this->assertEquals('http://user:password@test.mindtouch.dev/a/b/c', $result);
    }
}
