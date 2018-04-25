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

class setHeader_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function Can_set_value() {

        // arrange
        $headers = new Headers();

        // act
        $headers->setHeader('qux', 'foo');

        // assert
        $this->assertArrayHasKeyValue('Qux', ['foo'], $headers->toArray());
    }

    /**
     * @test
     */
    public function Can_replace_values() {

        // arrange
        $headers = new Headers();

        // act
        $headers->setHeader('qux', 'foo');
        $headers->addHeader('qux', 'bar');
        $headers->setHeader('qux', 'baz');

        // assert
        $this->assertArrayHasKeyValue('Qux', ['baz'], $headers->toArray());
    }

    /**
     * @test
     */
    public function Can_set_empty_value() {

        // arrange
        $headers = new Headers();

        // act
        $headers->setHeader('qux', '');

        // assert
        $this->assertArrayHasKeyValue('Qux', [''], $headers->toArray());
    }
}
