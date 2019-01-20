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

class toSanitizedUri_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function Can_scrub_basic_auth_password() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/somepath?a=b&c=d#fragment';

        // act
        $result = XUri::tryParse($uriStr)->toSanitizedUri([]);

        // assert
        $this->assertEquals('http://user:###@test.mindtouch.dev/somepath?a=b&c=d#fragment', $result);
    }

    /**
     * @test
     */
    public function Can_scrub_query_params() {

        // arrange
        $uriStr = 'http://user:password@test.mindtouch.dev/somepath?a=b&c=d&e=f#fragment';

        // act
        $result = XUri::tryParse($uriStr)->toSanitizedUri(['c', 'e']);

        // assert
        $this->assertEquals('http://user:###@test.mindtouch.dev/somepath?a=b&c=%23%23%23&e=%23%23%23#fragment', $result);
    }

    /**
     * @test
     */
    public function Can_return_extended_instance() {

        // act
        $result = TestXUri::tryParse('http://user:password@test.mindtouch.dev/somepath?a=b&c=d&e=f#fragment')->toSanitizedUri(['c', 'e']);

        // assert
        $this->assertInstanceOf(TestXUri::class, $result);
    }
}
