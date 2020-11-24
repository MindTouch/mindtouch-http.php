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
namespace MindTouch\Http\Tests;

use modethirteen\Http\Headers;
use modethirteen\Http\Mock\MockRequestMatcher;
use modethirteen\Http\XUri;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MindTouchHttpUnitTestCase extends TestCase {

    public function setUp() {
        parent::setUp();
        MockRequestMatcher::setIgnoredHeaderNames([
            Headers::HEADER_CONTENT_LENGTH
        ]);
        MockRequestMatcher::setIgnoredQueryParamNames([
            'dream.out.format',
            'dream_out_format'
        ]);
    }

    /**
     * @param string $class
     * @return MockObject
     */
    protected function newMock(string $class) : MockObject {
        return $this->getMockBuilder($class)
            ->setMethods(get_class_methods($class))
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @param string $method
     * @param XUri $uri
     * @return MockRequestMatcher
     */
    protected function newDefaultMockRequestMatcher(string $method, XUri $uri) : MockRequestMatcher {
        return new MockRequestMatcher($method, $uri);
    }
}
