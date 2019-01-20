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

class with_Test extends MindTouchHttpUnitTestCase  {

    /**
     * @test
     */
    public function Add_single_param_to_hostname() {

        // arrange
        $plug = new HttpPlug(XUri::tryParse('http://foo.com'));

        // act
        $plug = $plug->with('a', 'b');

        // assert
        $this->assertEquals('http://foo.com?a=b', $plug->getUri());
    }

    /**
     * @test
     */
    public function Add_multiple_params_to_hostname() {

        // arrange
        $plug = new HttpPlug(XUri::tryParse('http://foo.com'));

        // act
        $plug = $plug->with('a', 'b')->with('c', 'd');

        // assert

        /** @var HttpPlug $plug */
        $this->assertEquals('http://foo.com?a=b&c=d', $plug->getUri());
    }

    /**
     * @test
     */
    public function Add_single_param_to_existing_path() {

        // arrange
        $plug = new HttpPlug(XUri::tryParse('http://foo.com/qux'));

        // act
        $plug = $plug->with('a', 'b');

        // assert
        $this->assertEquals('http://foo.com/qux?a=b', $plug->getUri());
    }

    /**
     * @test
     */
    public function Add_multiple_params_to_existing_path() {

        // arrange
        $plug = new HttpPlug(XUri::tryParse('http://foo.com/qux'));

        // act
        $plug = $plug->with('a', 'b')->with('c', 'd');

        // assert

        /** @var HttpPlug $plug */
        $this->assertEquals('http://foo.com/qux?a=b&c=d', $plug->getUri());
    }

    /**
     * @test
     */
    public function Add_single_param_to_existing_path_query() {

        // arrange
        $plug = new HttpPlug(XUri::tryParse('http://foo.com/qux?a=b&c=d'));

        // act
        $plug = $plug->with('foo', 'bar');

        // assert
        $this->assertEquals('http://foo.com/qux?a=b&c=d&foo=bar', $plug->getUri());
    }

    /**
     * @test
     */
    public function Add_multiple_params_to_existing_path_query() {

        // arrange
        $plug = new HttpPlug(XUri::tryParse('http://foo.com/qux?a=b&c=d'));

        // act
        $plug = $plug->with('foo', 'bar')->with('qux', 'fred');

        // assert

        /** @var HttpPlug $plug */
        $this->assertEquals('http://foo.com/qux?a=b&c=d&foo=bar&qux=fred', $plug->getUri());
    }

    /**
     * @test
     */
    public function Add_single_param_to_existing_query() {

        // arrange
        $plug = new HttpPlug(XUri::tryParse('http://foo.com?a=b&c=d'));

        // act
        $plug = $plug->with('foo', 'bar');

        // assert
        $this->assertEquals('http://foo.com?a=b&c=d&foo=bar', $plug->getUri());
    }

    /**
     * @test
     */
    public function Add_multiple_params_to_existing_query() {

        // arrange
        $plug = new HttpPlug(XUri::tryParse('http://foo.com?a=b&c=d'));

        // act
        $plug = $plug->with('bar', 'qux')->with('fred', 'foo');

        // assert
        $this->assertEquals('http://foo.com?a=b&c=d&bar=qux&fred=foo', $plug->getUri());
    }
}

