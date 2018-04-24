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
namespace MindTouch\Http\tests\Headers;

use MindTouch\Http\Headers;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;

class addRawHeader_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function Can_set_header() {

        // arrange
        $headers = new Headers();

        // act
        $headers->addRawHeader('X-Foo-bar: qux fred quxx');

        // assert
        $this->assertArrayHasKeyValue('X-Foo-Bar', ['qux fred quxx'], $headers->toArray());
    }

    /**
     * @test
     */
    public function Can_set_multi_value_header() {

        // arrange
        $headers = new Headers();

        // act
        $headers->addRawHeader('X-Foo-bar: qux, fred, quxx; foo');

        // assert
        $this->assertArrayHasKeyValue('X-Foo-Bar', ['qux', 'fred', 'quxx; foo'], $headers->toArray());
    }

    /**
     * @test
     */
    public function Can_add_header() {

        // arrange
        $headers = new Headers();

        // act
        $headers->addRawHeader('X-Foo-bar: qux, fred, quxx; foo');
        $headers->addRawHeader('X-Foo-bar: a, b');

        // assert
        $this->assertArrayHasKeyValue('X-Foo-Bar', ['qux', 'fred', 'quxx; foo', 'a', 'b'], $headers->toArray());
    }

    /**
     * @test
     */
    public function Can_set_empty_value() {

        // arrange
        $headers = new Headers();

        // act
        $headers->addRawHeader('X-Foo-bar:');

        // assert
        $this->assertArrayHasKeyValue('X-Foo-Bar', [''], $headers->toArray());
    }

    /**
     * @test
     */
    public function Can_only_set_a_single_empty_value() {

        // arrange
        $headers = new Headers();

        // act
        $headers->addRawHeader('X-Foo-bar:');
        $headers->addRawHeader('X-Foo-bar:');
        $headers->addRawHeader('X-Foo-bar:');

        // assert
        $this->assertArrayHasKeyValue('X-Foo-Bar', [''], $headers->toArray());
    }

    /**
     * @test
     */
    public function Can_set_single_value_only_header_values() {

        // arrange
        $headers = new Headers();

        // act
        $headers->addRawHeader('Content-Type: application/xml, application/json');
        $headers->addRawHeader('Location: https://example.com/foo, http://bar.example.com');

        // assert
        $headers = $headers->toArray();
        $this->assertArrayHasKeyValue('Content-Type', ['application/xml'], $headers);
        $this->assertArrayHasKeyValue('Location', ['https://example.com/foo'], $headers);
    }
}
