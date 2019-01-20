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
namespace MindTouch\Http\tests\Mock\MockRequestMatcher;

use MindTouch\Http\Headers;
use MindTouch\Http\HttpPlug;
use MindTouch\Http\Mock\MockRequestMatcher;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;
use MindTouch\Http\XUri;

class __clone_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function Can_deep_copy() {

        // arrange
        $matcher = (new MockRequestMatcher(HttpPlug::METHOD_GET, XUri::newFromString('https://example.com/foo')))
            ->withHeaders(Headers::newFromHeaderNameValuePairs([
                ['baz', 'qux']
            ]))
            ->withBody('foo');
        $uri = $matcher->getUri();
        $headers = $matcher->getHeaders();

        // act
        $cloned = clone $matcher;
        $clonedUri = $cloned->getUri();
        $clonedHeaders = $cloned->getHeaders();

        // assert
        $this->assertNotSame($uri, $clonedUri);
        $this->assertNotSame($headers, $clonedHeaders);
    }
}
