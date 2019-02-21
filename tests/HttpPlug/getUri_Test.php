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

class getUri_Test extends MindTouchHttpUnitTestCase  {

    /**
     * @test
     */
    public function Can_get_uri_with_credentials() {

        // arrange
        $plug = (new HttpPlug(XUri::tryParse('http://foo.com/bar/baz?a=b&c=d')))
            ->withCredentials('aaa', 'bbb');

        // act
        $result = $plug->getUri(true);

        // assert
        $this->assertEquals('http://aaa:bbb@foo.com/bar/baz?a=b&c=d', $result->toString());
    }

    /**
     * @test
     */
    public function Can_get_uri_with_username_credential() {

        // arrange
        $plug = (new HttpPlug(XUri::tryParse('http://foo.com/bar/baz?a=b&c=d')))
            ->withCredentials('aaa', null);

        // act
        $result = $plug->getUri(true);

        // assert
        $this->assertEquals('http://aaa:@foo.com/bar/baz?a=b&c=d', $result->toString());
    }

    /**
     * @test
     */
    public function Can_get_uri_without_credentials() {

        // arrange
        $plug = (new HttpPlug(XUri::tryParse('http://foo.com/bar/baz?a=b&c=d')))
            ->withCredentials('aaa', 'bbb');

        // act
        $result = $plug->getUri();

        // assert
        $this->assertEquals('http://foo.com/bar/baz?a=b&c=d', $result);
    }
}
