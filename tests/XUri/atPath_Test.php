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

class atPath_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function Can_add_path() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/?a=b&c=d#fragment';

        // act
        $result = XUri::tryParse($uriStr)->atPath('foo/bar');

        // assert
        $this->assertEquals('http://user:password@test.mindtouch.dev/foo/bar?a=b&c=d#fragment', $result);
    }

    /**
     * @test
     */
    public function Can_add_query() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/?a=b&c=d#fragment';

        // act
        $result = XUri::tryParse($uriStr)->atPath('foo/bar?z=x&y=z');

        // assert
        $this->assertEquals('http://user:password@test.mindtouch.dev/foo/bar?a=b&c=d&z=x&y=z#fragment', $result);
    }

    /**
     * @test
     */
    public function Can_add_query_if_no_existing_query() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/#fragment';

        // act
        $result = XUri::tryParse($uriStr)->atPath('foo/bar?z=x&y=z');

        // assert
        $this->assertEquals('http://user:password@test.mindtouch.dev/foo/bar?z=x&y=z#fragment', $result);
    }

    /**
     * @test
     */
    public function Can_replace_fragment() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/?a=b&c=d#fragment';

        // act
        $result = XUri::tryParse($uriStr)->atPath('foo/bar?z=x&y=z#mouse');

        // assert
        $this->assertEquals('http://user:password@test.mindtouch.dev/foo/bar?a=b&c=d&z=x&y=z#mouse', $result);
    }

    /**
     * @test
     */
    public function Can_add_path_with_prepended_slash() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/?a=b&c=d#fragment';

        // act
        $result = XUri::tryParse($uriStr)->atPath('/foo/bar?z=x&y=z#mouse');

        // assert
        $this->assertEquals('http://user:password@test.mindtouch.dev/foo/bar?a=b&c=d&z=x&y=z#mouse', $result);
    }

    /**
     * @test
     */
    public function Can_add_path_with_appended_slash() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/?a=b&c=d#fragment';

        // act
        $result = XUri::tryParse($uriStr)->atPath('foo/bar/?z=x&y=z#mouse');

        // assert
        $this->assertEquals('http://user:password@test.mindtouch.dev/foo/bar?a=b&c=d&z=x&y=z#mouse', $result);
    }

    /**
     * @test
     */
    public function Can_add_path_with_colon() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/?a=b&c=d#fragment';

        // act
        $result = XUri::tryParse($uriStr)->atPath('Special:foo/bar');

        // assert
        $this->assertEquals('http://user:password@test.mindtouch.dev/Special:foo/bar?a=b&c=d#fragment', $result);
    }
}
