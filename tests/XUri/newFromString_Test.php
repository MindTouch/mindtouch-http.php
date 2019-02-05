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

use MindTouch\Http\Exception\MalformedUriException;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;
use MindTouch\Http\XUri;

class newFromString_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function XUri_roundtrip_test_1() {

        // arrange
        $uriStr = 'http://test.mindtouch.dev/';

        // act
        $result = XUri::newFromString($uriStr);

        // assert
        $this->assertEquals($uriStr, $result);
    }

    /**
     * @test
     */
    public function XUri_roundtrip_test_2() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/?a=b&c=d#fragment';

        // act
        $result = XUri::newFromString($uriStr);

        // assert
        $this->assertEquals($uriStr, $result);
    }

    /**
     * @test
     */
    public function XUri_roundtrip_test_3() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/?a=b&c=d#fragment';

        // act
        $result = XUri::newFromString($uriStr);

        // assert
        $this->assertEquals($uriStr, $result);
    }

    /**
     * @test
     * @expectedException \MindTouch\Http\Exception\MalformedUriException
     */
    public function Invalid_uri_throws_exception() {

        // arrange
        $uriStr = 'totally_invalid_string';

        // act
        XUri::newFromString($uriStr);
    }

    /**
     * @test
     * @expectedException \MindTouch\Http\Exception\MalformedUriException
     */
    public function Valid_uri_must_have_scheme() {

        // arrange
        $uriStr = 'test.mindtouch.dev/?a=b&c=d#fragment';

        // act
        XUri::newFromString($uriStr);
    }

    /**
     * @test
     * @expectedException \MindTouch\Http\Exception\MalformedUriException
     */
    public function Valid_uri_must_have_valid_port() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev:12322342332423/?a=b&c=d#fragment';

        // act
        XUri::newFromString($uriStr);
    }

    /**
     * @test
     */
    public function Can_return_extended_instance() {

        // act
        $result = TestXUri::newFromString('http://user:password@test.mindtouch.dev/?a=b&c=d#fragment');

        // assert
        $this->assertInstanceOf(TestXUri::class, $result);
    }
}
