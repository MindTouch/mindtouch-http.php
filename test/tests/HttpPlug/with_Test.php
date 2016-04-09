<?php
/**
 * MindTouch HTTP
 * Copyright (C) 2006-2016 MindTouch, Inc.
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
namespace MindTouch\Http\test\tests\HttpPlug;

use MindTouch\Http\HttpPlug;
use PHPUnit_Framework_TestCase;

class with_Test extends PHPUnit_Framework_TestCase  {

    /**
     * @test
     */
    public function Add_single_param_to_hostname() {

        // arrange
        $Plug = HttpPlug::newPlug('http://foo.com');

        // act
        $Plug = $Plug->with('a', 'b');

        // assert
        $this->assertEquals('http://foo.com/?a=b', $Plug->getUri());
    }

    /**
     * @test
     */
    public function Add_multiple_params_to_hostname() {

        // arrange
        $Plug = HttpPlug::newPlug('http://foo.com');

        // act
        $Plug = $Plug->with('a', 'b')->with('c', 'd');

        // assert

        /** @var HttpPlug $Plug */
        $this->assertEquals('http://foo.com/?a=b&c=d', $Plug->getUri());
    }

    /**
     * @test
     */
    public function Add_single_param_to_existing_path() {

        // arrange
        $Plug = HttpPlug::newPlug('http://foo.com/qux');

        // act
        $Plug = $Plug->with('a', 'b');

        // assert
        $this->assertEquals('http://foo.com/qux?a=b', $Plug->getUri());
    }

    /**
     * @test
     */
    public function Add_multiple_params_to_existing_path() {

        // arrange
        $Plug = HttpPlug::newPlug('http://foo.com/qux');

        // act
        $Plug = $Plug->with('a', 'b')->with('c', 'd');

        // assert

        /** @var HttpPlug $Plug */
        $this->assertEquals('http://foo.com/qux?a=b&c=d', $Plug->getUri());
    }

    /**
     * @test
     */
    public function Add_single_param_to_existing_path_query() {

        // arrange
        $Plug = HttpPlug::newPlug('http://foo.com/qux?a=b&c=d');

        // act
        $Plug = $Plug->with('foo', 'bar');

        // assert
        $this->assertEquals('http://foo.com/qux?a=b&c=d&foo=bar', $Plug->getUri());
    }

    /**
     * @test
     */
    public function Add_multiple_params_to_existing_path_query() {

        // arrange
        $Plug = HttpPlug::newPlug('http://foo.com/qux?a=b&c=d');

        // act
        $Plug = $Plug->with('foo', 'bar')->with('qux', 'fred');

        // assert

        /** @var HttpPlug $Plug */
        $this->assertEquals('http://foo.com/qux?a=b&c=d&foo=bar&qux=fred', $Plug->getUri());
    }

    /**
     * @test
     */
    public function Add_single_param_to_existing_query() {

        // arrange
        $Plug = HttpPlug::newPlug('http://foo.com?a=b&c=d');

        // act
        $Plug = $Plug->with('foo', 'bar');

        // assert
        $this->assertEquals('http://foo.com/?a=b&c=d&foo=bar', $Plug->getUri());
    }

    /**
     * @test
     */
    public function Add_multiple_params_to_existing_query() {

        // arrange
        $Plug = HttpPlug::newPlug('http://foo.com?a=b&c=d');

        // act
        $Plug = $Plug->with('bar', 'qux')->with('fred', 'foo');

        // assert

        /** @var HttpPlug $Plug */
        $this->assertEquals('http://foo.com/?a=b&c=d&bar=qux&fred=foo', $Plug->getUri());
    }
}

