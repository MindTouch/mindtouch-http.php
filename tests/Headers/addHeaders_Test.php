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

class addHeaders_Test extends MindTouchHttpUnitTestCase {

       /**
     * @test
     */
    public function Can_add_headers_collection_to_existing_headers() {

        // arrange
        $headers = new Headers();
        $headers->addHeader('X-Foo', 'bar');
        $headers->addHeader('X-Foo', 'qux');
        $headers->addHeader('Deki-Config', '12345');
        $headers->addHeader('Deki-Stats', null);
        $headers->addHeader('Deki-Database', '');
        $headers->addHeader('Deki-Database', '000');
        $headers->addHeader('Deki-Config', '67890');
        $headers->addHeader('Set-Cookie', 'dekisession=765');
        $headers->addHeader('Set-Cookie', 'authtoken=123');
        $incomingHeaders = new Headers();
        $incomingHeaders->addHeader('X-Foo', 'bazz');
        $incomingHeaders->addHeader('Deki-Stats', '999');
        $incomingHeaders->addHeader('Deki-Stats', '888');
        $incomingHeaders->addHeader('Deki-Frontend', 'web');

        // act
        $headers->addHeaders($incomingHeaders);

        // assert
        $this->assertEquals([
            'X-Foo' => ['bar', 'qux', 'bazz'],
            'Deki-Config' => ['12345', '67890'],
            'Deki-Stats' => ['999', '888'],
            'Deki-Database' => ['000'],
            'Set-Cookie' => ['dekisession=765', 'authtoken=123'],
            'Deki-Frontend' => ['web']
        ], $headers->toArray());
    }
}
