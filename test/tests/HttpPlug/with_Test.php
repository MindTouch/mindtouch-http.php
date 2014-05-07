<?php
/*
 * MindTouch
 * Copyright (c) 2006-2012 MindTouch Inc.
 * http://mindtouch.com
 *
 * This file and accompanying files are licensed under the
 * MindTouch Master Subscription Agreement (MSA).
 *
 * At any time, you shall not, directly or indirectly: (i) sublicense,
 * resell, rent, lease, distribute, market, commercialize or otherwise
 * transfer rights or usage to: (a) the Software, (b) any modified version
 * or derivative work of the Software created by you or for you, or (c)
 * MindTouch Open Source (which includes all non-supported versions of
 * MindTouch-developed software), for any purpose including timesharing or
 * service bureau purposes; (ii) remove or alter any copyright, trademark
 * or proprietary notice in the Software; (iii) transfer, use or export the
 * Software in violation of any applicable laws or regulations of any
 * government or governmental agency; (iv) use or run on any of your
 * hardware, or have deployed for use, any production version of MindTouch
 * Open Source; (v) use any of the Support Services, Error corrections,
 * Updates or Upgrades, for the MindTouch Open Source software or for any
 * Server for which Support Services are not then purchased as provided
 * hereunder; or (vi) reverse engineer, decompile or modify any encrypted
 * or encoded portion of the Software.
 *
 * A complete copy of the MSA is available at http://www.mindtouch.com/msa
 */
namespace MindTouch\ApiClient\test\tests\HttpPlug;

use MindTouch\ApiClient\HttpPlug;
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

