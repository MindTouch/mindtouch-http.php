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

use MindTouch\Http\Headers;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;

class getHeaderLine_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function Can_get_header_line_by_case_insensitive_name() {

        // arrange
        $headers = new Headers();
        $headers->addHeader('X-Foo-BAZ', 'bar');

        // act
        $result = $headers->getHeaderLine('x-foo-BaZ');

        // assert
        $this->assertEquals('bar', $result);
    }

    /**
     * @test
     */
    public function Can_get_header_line_with_multiple_values_by_case_insensitive_name() {

        // arrange
        $headers = new Headers();
        $headers->addHeader('X-Foo-BAZ', 'bar');
        $headers->addHeader('X-foo-baz', 'qux');

        // act
        $result = $headers->getHeaderLine('x-foo-BaZ');

        // assert
        $this->assertEquals('bar, qux', $result);
    }

    /**
     * @test
     */
    public function Can_get_header_line_from_single_value_headers() {

        // arrange
        $headers = new Headers();
        $headers->addHeader('Content-Type', 'application/xml');
        $headers->addHeader('Content-Type', 'application/json');
        $headers->addHeader('Location', 'https://example.com');
        $headers->addHeader('Location', 'http://bar.example.com');

        // act
        $result1 = $headers->getHeaderLine('Content-Type');
        $result2 = $headers->getHeaderLine('Location');

        // assert
        $this->assertEquals('application/json', $result1);
        $this->assertEquals('http://bar.example.com', $result2);
    }
}
