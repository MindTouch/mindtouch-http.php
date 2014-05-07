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

class at_Test extends PHPUnit_Framework_TestCase  {

    /**
     * @test
     */
    public function Add_single_path_segment_to_hostname() {

        // arrange
        $Plug = HttpPlug::newPlug('http://foo.com');

        // act
        $Plug = $Plug->at('bar');

        // assert
        $this->assertEquals('http://foo.com/bar', $Plug->getUri());
    }

    /**
     * @test
     */
    public function Add_multiple_path_segments_to_hostname_1() {

        // arrange
        $Plug = HttpPlug::newPlug('http://foo.com');

        // act
        $Plug = $Plug->at('bar', 'qux', 'baz');

        // assert
        $this->assertEquals('http://foo.com/bar/qux/baz', $Plug->getUri());
    }

    /**
     * @test
     */
    public function Add_multiple_path_segments_to_hostname_2() {

        // arrange
        $Plug = HttpPlug::newPlug('http://foo.com');

        // act
        $Plug = $Plug->at('bar', 'qux')->at('fred', 'baz');

        // assert

        /** @var HttpPlug $Plug */
        $this->assertEquals('http://foo.com/bar/qux/fred/baz', $Plug->getUri());
    }

    /**
     * @test
     */
    public function Add_single_path_segment_to_existing_path() {

        // arrange
        $Plug = HttpPlug::newPlug('http://foo.com/qux');

        // act
        $Plug = $Plug->at('bar');

        // assert
        $this->assertEquals('http://foo.com/qux/bar', $Plug->getUri());
    }

    /**
     * @test
     */
    public function Add_multiple_path_segments_to_existing_path_1() {

        // arrange
        $Plug = HttpPlug::newPlug('http://foo.com/qux');

        // act
        $Plug = $Plug->at('bar', 'qux', 'fred');

        // assert
        $this->assertEquals('http://foo.com/qux/bar/qux/fred', $Plug->getUri());
    }

    /**
     * @test
     */
    public function Add_multiple_path_segments_to_existing_path_2() {

        // arrange
        $Plug = HttpPlug::newPlug('http://foo.com/qux');

        // act
        $Plug = $Plug->at('bar', 'qux')->at('fred', 'baz');

        // assert

        /** @var HttpPlug $Plug */
        $this->assertEquals('http://foo.com/qux/bar/qux/fred/baz', $Plug->getUri());
    }

    /**
     * @test
     */
    public function Add_single_path_segment_to_existing_path_query() {

        // arrange
        $Plug = HttpPlug::newPlug('http://foo.com/qux?a=b&c=d');

        // act
        $Plug = $Plug->at('bar');

        // assert
        $this->assertEquals('http://foo.com/qux/bar?a=b&c=d', $Plug->getUri());
    }

    /**
     * @test
     */
    public function Add_multiple_path_segments_to_existing_path_query_1() {

        // arrange
        $Plug = HttpPlug::newPlug('http://foo.com/qux?a=b&c=d');

        // act
        $Plug = $Plug->at('bar', 'qux', 'fred');

        // assert
        $this->assertEquals('http://foo.com/qux/bar/qux/fred?a=b&c=d', $Plug->getUri());
    }

    /**
     * @test
     */
    public function Add_multiple_path_segments_to_existing_path_query_2() {

        // arrange
        $Plug = HttpPlug::newPlug('http://foo.com/qux?a=b&c=d');

        // act
        $Plug = $Plug->at('bar', 'qux')->at('baz', 'fred');

        // assert

        /** @var HttpPlug $Plug */
        $this->assertEquals('http://foo.com/qux/bar/qux/baz/fred?a=b&c=d', $Plug->getUri());
    }

    /**
     * @test
     */
    public function Add_single_path_segment_to_existing_query() {

        // arrange
        $Plug = HttpPlug::newPlug('http://foo.com?a=b&c=d');

        // act
        $Plug = $Plug->at('bar');

        // assert
        $this->assertEquals('http://foo.com/bar?a=b&c=d', $Plug->getUri());
    }

    /**
     * @test
     */
    public function Add_multiple_path_segments_to_existing_query_1() {

        // arrange
        $Plug = HttpPlug::newPlug('http://foo.com?a=b&c=d');

        // act
        $Plug = $Plug->at('bar', 'qux', 'fred');

        // assert
        $this->assertEquals('http://foo.com/bar/qux/fred?a=b&c=d', $Plug->getUri());
    }

    /**
     * @test
     */
    public function Add_multiple_path_segments_to_existing_query_2() {

        // arrange
        $Plug = HttpPlug::newPlug('http://foo.com?a=b&c=d');

        // act
        $Plug = $Plug->at('bar', 'qux')->at('foo', 'fred');

        // assert

        /** @var HttpPlug $Plug */
        $this->assertEquals('http://foo.com/bar/qux/foo/fred?a=b&c=d', $Plug->getUri());
    }
}

