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
namespace MindTouch\Http\tests\Mock\MockPlug;

use MindTouch\Http\Headers;
use MindTouch\Http\HttpPlug;
use MindTouch\Http\Mock\MockRequestMatcher;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;
use MindTouch\Http\XUri;

class toArray_Test extends MindTouchHttpUnitTestCase  {

    /**
     * @test
     */
    public function Can_get_array() {

        // arrange
        $matcher = (new MockRequestMatcher(HttpPlug::METHOD_POST, XUri::tryParse('http://example.com')))
            ->withHeaders(Headers::newFromHeaderNameValuePairs([
                ['X-Qux', 'foo']
            ]))
            ->withBody('bar');

        // act
        $result = $matcher->toArray();

        // assert
        $this->assertEquals([
            'method' => 'POST',
            'uri' => 'http://example.com',
            'headers' => [
                'X-Qux' => 'foo'
            ],
            'body' => 'bar'
        ], $result);
    }
}
