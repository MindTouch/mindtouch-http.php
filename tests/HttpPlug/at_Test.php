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
namespace MindTouch\Http\tests\HttpPlug;

use MindTouch\Http\HttpPlug;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;
use MindTouch\Http\XUri;

class at_Test extends MindTouchHttpUnitTestCase  {

    /**
     * @test
     */
    public function Can_add_single_path_segment_to_hostname() {

        // arrange
        $plug = new HttpPlug(XUri::tryParse('http://foo.com'));

        // act
        $plug = $plug->at('bar');

        // assert
        $this->assertEquals('http://foo.com/bar', $plug->getUri());
    }

    /**
     * @test
     */
    public function Can_add_multiple_path_segments_to_hostname_1() {

        // arrange
        $plug = new HttpPlug(XUri::tryParse('http://foo.com'));

        // act
        $plug = $plug->at('bar', 'qux', 'baz');

        // assert
        $this->assertEquals('http://foo.com/bar/qux/baz', $plug->getUri());
    }

    /**
     * @test
     */
    public function Can_add_multiple_path_segments_to_hostname_2() {

        // arrange
        $plug = new HttpPlug(XUri::tryParse('http://foo.com'));

        // act
        $plug = $plug->at('bar', 'qux')->at('fred', 'baz');

        // assert
        $this->assertEquals('http://foo.com/bar/qux/fred/baz', $plug->getUri());
    }

    /**
     * @test
     */
    public function Can_add_single_path_segment_to_existing_path() {

        // arrange
        $plug = new HttpPlug(XUri::tryParse('http://foo.com/qux'));

        // act
        $plug = $plug->at('bar');

        // assert
        $this->assertEquals('http://foo.com/qux/bar', $plug->getUri());
    }

    /**
     * @test
     */
    public function Can_add_multiple_path_segments_to_existing_path_1() {

        // arrange
        $plug = new HttpPlug(XUri::tryParse('http://foo.com/qux'));

        // act
        $plug = $plug->at('bar', 'qux', 'fred');

        // assert
        $this->assertEquals('http://foo.com/qux/bar/qux/fred', $plug->getUri());
    }

    /**
     * @test
     */
    public function Can_add_multiple_path_segments_to_existing_path_2() {

        // arrange
        $plug = new HttpPlug(XUri::tryParse('http://foo.com/qux'));

        // act
        $plug = $plug->at('bar', 'qux')->at('fred', 'baz');

        // assert

        /** @var HttpPlug $plug */
        $this->assertEquals('http://foo.com/qux/bar/qux/fred/baz', $plug->getUri());
    }

    /**
     * @test
     */
    public function Can_add_single_path_segment_to_existing_path_query() {

        // arrange
        $plug = new HttpPlug(XUri::tryParse('http://foo.com/qux?a=b&c=d'));

        // act
        $plug = $plug->at('bar');

        // assert
        $this->assertEquals('http://foo.com/qux/bar?a=b&c=d', $plug->getUri());
    }

    /**
     * @test
     */
    public function Can_add_multiple_path_segments_to_existing_path_query_1() {

        // arrange
        $plug = new HttpPlug(XUri::tryParse('http://foo.com/qux?a=b&c=d'));

        // act
        $plug = $plug->at('bar', 'qux', 'fred');

        // assert
        $this->assertEquals('http://foo.com/qux/bar/qux/fred?a=b&c=d', $plug->getUri());
    }

    /**
     * @test
     */
    public function Can_add_multiple_path_segments_to_existing_path_query_2() {

        // arrange
        $plug = new HttpPlug(XUri::tryParse('http://foo.com/qux?a=b&c=d'));

        // act
        $plug = $plug->at('bar', 'qux')->at('baz', 'fred');

        // assert
        $this->assertEquals('http://foo.com/qux/bar/qux/baz/fred?a=b&c=d', $plug->getUri());
    }

    /**
     * @test
     */
    public function Can_add_single_path_segment_to_existing_query() {

        // arrange
        $plug = new HttpPlug(XUri::tryParse('http://foo.com?a=b&c=d'));

        // act
        $plug = $plug->at('bar');

        // assert
        $this->assertEquals('http://foo.com/bar?a=b&c=d', $plug->getUri());
    }

    /**
     * @test
     */
    public function Can_add_multiple_path_segments_to_existing_query_1() {

        // arrange
        $plug = new HttpPlug(XUri::tryParse('http://foo.com?a=b&c=d'));

        // act
        $plug = $plug->at('bar', 'qux', 'fred');

        // assert
        $this->assertEquals('http://foo.com/bar/qux/fred?a=b&c=d', $plug->getUri());
    }

    /**
     * @test
     */
    public function Can_add_multiple_path_segments_to_existing_query_2() {

        // arrange
        $plug = new HttpPlug(XUri::tryParse('http://foo.com?a=b&c=d'));

        // act
        $plug = $plug->at('bar', 'qux')->at('foo', 'fred');

        // assert
        $this->assertEquals('http://foo.com/bar/qux/foo/fred?a=b&c=d', $plug->getUri());
    }
}
