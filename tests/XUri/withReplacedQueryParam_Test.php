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

class withReplacedQueryParam_Test extends MindTouchHttpUnitTestCase {

    /**
     * @return array
     */
    public static function param_expected_Provider() {
        return [
            ['bar', 'http://user:password@test.mindtouch.dev/?a=bar&c=d#fragment'],
            [true, 'http://user:password@test.mindtouch.dev/?a=true&c=d#fragment'],
            [false, 'http://user:password@test.mindtouch.dev/?a=false&c=d#fragment'],
            [0, 'http://user:password@test.mindtouch.dev/?a=0&c=d#fragment'],
            [-10, 'http://user:password@test.mindtouch.dev/?a=-10&c=d#fragment'],
            [new class {
                public function __toString() : string {
                    return 'fred';
                }
            }, 'http://user:password@test.mindtouch.dev/?a=fred&c=d#fragment'],
            [['qux', true, -10, 5], 'http://user:password@test.mindtouch.dev/?a=qux%2C1%2C-10%2C5&c=d#fragment'],
            [function() { return 'bazz'; }, 'http://user:password@test.mindtouch.dev/?a=bazz&c=d#fragment']
        ];
    }

    /**
     * @dataProvider param_expected_Provider
     * @test
     * @param mixed $param
     * @param string $expected
     */
    public function Can_replace_query_parameter($param, string $expected) {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/?a=b&c=d#fragment';

         // act
        $result = XUri::tryParse($uriStr)->withReplacedQueryParam('a', $param);

        // assert
        $this->assertEquals($expected, $result);
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

    /**
     * @test
     */
    public function Can_return_extended_instance() {

        // act
        $result = TestXUri::tryParse('http://user:password@test.mindtouch.dev:80/somepath?a=b&c=d&e=f#foo')->withReplacedQueryParam('a', 'qux');

        // assert
        $this->assertInstanceOf(TestXUri::class, $result);
    }
}
