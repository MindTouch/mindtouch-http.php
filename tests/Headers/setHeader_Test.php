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

    /**
     * @test
     */
    public function Can_set_non_string_type_header() {

        // arrange
        $headers = new Headers();

        // act
        $headers->setHeader('bar', true);
        $headers->setHeader('fred', false);
        $headers->setHeader('baz', 0);
        $headers->setHeader('qux', -10);
        $headers->setHeader('bazz', new class {
            public function __toString() : string {
                return 'zzz';
            }
        });
        $headers->setHeader('fredd', ['qux', true, -10, 5]);
        $headers->setHeader('barr', function() { return 'bazzzzz'; });

        // assert
        $this->assertArrayHasKeyValue('Bar', ['true'], $headers->toArray());
        $this->assertArrayHasKeyValue('Fred', ['false'], $headers->toArray());
        $this->assertArrayHasKeyValue('Baz', ['0'], $headers->toArray());
        $this->assertArrayHasKeyValue('Qux', ['-10'], $headers->toArray());
        $this->assertArrayHasKeyValue('Bazz', ['zzz'], $headers->toArray());
        $this->assertArrayHasKeyValue('Fredd', ['qux','true','-10','5'], $headers->toArray());
        $this->assertArrayHasKeyValue('Barr', 'bazzzzz', $headers->toArray());
    }
}
