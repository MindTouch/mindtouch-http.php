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

class toArray_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function Can_get_array() {

        // arrange
        $headers = new Headers();
        $headers->addHeader('X-Foo', 'bar');
        $headers->addRawHeader('X-Foo: qux, baz');
        $headers->addHeader('Deki-Config', '12345');
        $headers->addHeader('Deki-Stats', null);
        $headers->addHeader('Deki-Database', '');
        $headers->addHeader('Deki-Database', '000');
        $headers->addHeader('Deki-Config', '67890');
        $headers->addHeader('Set-Cookie', 'authtoken=foo');
        $headers->addRawHeader('Set-Cookie: dekisession=765');
        $headers->addRawHeader('Set-Cookie: dekisettings=abc');

        // act
        $results = $headers->toArray();

        // assert
        $this->assertEquals(5, count($results));
        $this->assertArrayHasKeyValue('X-Foo', ['bar', 'qux, baz'], $results);
        $this->assertArrayHasKeyValue('Deki-Config', ['12345', '67890'], $results);
        $this->assertArrayHasKeyValue('Deki-Stats', [''], $results);
        $this->assertArrayHasKeyValue('Deki-Database', ['000'], $results);
        $this->assertArrayHasKeyValue('Set-Cookie', ['authtoken=foo', 'dekisession=765', 'dekisettings=abc'], $results);
    }

    /**
     * @test
     */
    public function Can_get_array_with_raw_header_comma_parsing_enabled() {

        // arrange
        $headers = (new Headers())->withRawHeaderCommaSeparationEnabled();
        $headers->addHeader('X-Foo', 'bar');
        $headers->addRawHeader('X-Foo: qux, baz');
        $headers->addHeader('Deki-Config', '12345');
        $headers->addHeader('Deki-Stats', null);
        $headers->addHeader('Deki-Database', '');
        $headers->addHeader('Deki-Database', '000');
        $headers->addHeader('Deki-Config', '67890');
        $headers->addHeader('Set-Cookie', 'authtoken=foo');
        $headers->addRawHeader('Set-Cookie: dekisession=765');
        $headers->addRawHeader('Set-Cookie: dekisettings=abc');

        // act
        $results = $headers->toArray();

        // assert
        $this->assertEquals(5, count($results));
        $this->assertArrayHasKeyValue('X-Foo', ['bar', 'qux', 'baz'], $results);
        $this->assertArrayHasKeyValue('Deki-Config', ['12345', '67890'], $results);
        $this->assertArrayHasKeyValue('Deki-Stats', [''], $results);
        $this->assertArrayHasKeyValue('Deki-Database', ['000'], $results);
        $this->assertArrayHasKeyValue('Set-Cookie', ['authtoken=foo', 'dekisession=765', 'dekisettings=abc'], $results);
    }
}
