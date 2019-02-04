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

class withQueryParam_Test extends MindTouchHttpUnitTestCase {

    /**
     * @return array
     */
    public static function param_expected_Provider() {
        return [
            ['bar', 'http://user:password@test.mindtouch.dev/?a=b&c=d&foo=bar#fragment'],
            [true, 'http://user:password@test.mindtouch.dev/?a=b&c=d&foo=true#fragment'],
            [false, 'http://user:password@test.mindtouch.dev/?a=b&c=d&foo=false#fragment'],
            [0, 'http://user:password@test.mindtouch.dev/?a=b&c=d&foo=0#fragment'],
            [-10, 'http://user:password@test.mindtouch.dev/?a=b&c=d&foo=-10#fragment'],
            [new class {
                public function __toString() : string {
                    return 'fred';
                }
            }, 'http://user:password@test.mindtouch.dev/?a=b&c=d&foo=fred#fragment'],
            [['qux', true, -10, 5], 'http://user:password@test.mindtouch.dev/?a=b&c=d&foo=qux%2C1%2C-10%2C5#fragment'],
            [function() { return 'bazz'; }, 'http://user:password@test.mindtouch.dev/?a=b&c=d&foo=bazz#fragment']
        ];
    }

    /**
     * @dataProvider param_expected_Provider
     * @test
     * @param mixed $param
     * @param string $expected
     */
    public function With_add_query_parameter($param, string $expected) {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/?a=b&c=d#fragment';

         // act
        $result = XUri::tryParse($uriStr)->withQueryParam('foo', $param);

        // assert
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function Can_add_query_parameter_after_removing_it() {
        $xuri = XUri::tryParse('http://test.mindtouch.dev/?mt-f1=true');
        $result = $xuri->withoutQueryParam('mt-f1')->withQueryParam('mt-f1', 'true');
        $this->assertEquals('http://test.mindtouch.dev/?mt-f1=true', $result->toString());
    }

    /**
     * @test
     */
    public function Can_return_extended_instance() {

        // act
        $result = TestXUri::tryParse('http://user:password@test.mindtouch.dev:80/somepath?a=b&c=d&e=f#foo')->withQueryParam('foo', 'bar');

        // assert
        $this->assertInstanceOf(TestXUri::class, $result);
    }
}
