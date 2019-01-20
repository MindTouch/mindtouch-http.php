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

class addHeader_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function Can_set_multiple_values() {

        // arrange
        $headers = new Headers();

        // act
        $headers->addHeader('qux', 'foo');
        $headers->addHeader('qux', 'bar');
        $headers->addHeader('qux', 'baz');

        // assert
        $this->assertArrayHasKeyValue('Qux', ['foo', 'bar', 'baz'], $headers->toArray());
    }

    /**
     * @test
     */
    public function Can_set_empty_value() {

        // arrange
        $headers = new Headers();

        // act
        $headers->addHeader('qux', '');

        // assert
        $this->assertArrayHasKeyValue('Qux', [''], $headers->toArray());
    }

    /**
     * @test
     */
    public function Can_only_set_a_single_empty_value() {

        // arrange
        $headers = new Headers();

        // act
        $headers->addHeader('qux', '');
        $headers->addHeader('qux', '');
        $headers->addHeader('qux', '');

        // assert
        $this->assertArrayHasKeyValue('Qux', [''], $headers->toArray());
    }

    /**
     * @test
     */
    public function Can_set_single_value_only_header_values() {

        // arrange
        $headers = new Headers();

        // act
        $headers->addHeader('Content-Type', 'application/xml');
        $headers->addHeader('Content-Type', 'application/json');
        $headers->addHeader('Location', 'https://example.com');
        $headers->addHeader('Location', 'http://bar.example.com');

        // assert
        $this->assertArrayHasKeyValue('Content-Type', ['application/json'], $headers->toArray());
        $this->assertArrayHasKeyValue('Location', ['http://bar.example.com'], $headers->toArray());
    }
}
