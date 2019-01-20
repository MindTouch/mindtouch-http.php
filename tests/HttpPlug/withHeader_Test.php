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
namespace MindTouch\Http\tests\HttpPlug;

use MindTouch\Http\HttpPlug;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;
use MindTouch\Http\XUri;

class withHeader_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function Can_set_header_value() {

        // arrange
        $plug = new HttpPlug(XUri::tryParse('http://foo.com'));

        // act
        $plug = $plug->withHeader('X-Foo-Bar', 'baz');

        // assert
        $this->assertEquals('baz', $plug->getHeaders()->getHeaderLine('X-Foo-Bar'));
    }

    /**
     * @test
     */
    public function Can_replace_header_value() {

        // arrange
        $plug = new HttpPlug(XUri::tryParse('http://foo.com'));

        // act
        $plug = $plug->withHeader('X-Foo-Bar', 'baz')->withHeader('X-Foo-Bar', 'fred');

        // assert
        $this->assertEquals('fred', $plug->getHeaders()->getHeaderLine('X-Foo-Bar'));
    }
}
