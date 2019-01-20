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
namespace MindTouch\Http\tests\Headers;

use InvalidArgumentException;
use MindTouch\Http\Headers;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;

class newFromHeaderNameValuePairs_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function Can_return_headers_from_pairs() {

        // act
        $headers = Headers::newFromHeaderNameValuePairs([
            ['x-foo', 'bar'],
            ['x-FOO', 'baz'],
            ['x-qux-quxx', 'fred']
        ]);

        // assert
        $this->assertEquals([
            'X-Foo' => ['bar', 'baz'],
            'X-Qux-Quxx' => ['fred']
        ], $headers->toArray());
    }

    /**
     * @test
     */
    public function Cannot_return_headers_from_pairs_if_invalid_structure() {

        // act
        $exceptionThrown = false;
        try {
            Headers::newFromHeaderNameValuePairs([
                ['x-foo', 'bar'],
                ['baz' => 'foo'],
                ['x-qux-quxx', 'fred']
            ]);
        } catch(InvalidArgumentException $e) {
            $exceptionThrown = true;
        }

        // assert
        $this->assertTrue($exceptionThrown);
    }
}
