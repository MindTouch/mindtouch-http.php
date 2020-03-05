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
namespace MindTouch\Http\tests\ApiPlug;

use MindTouch\Http\ApiPlug;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;
use MindTouch\Http\XUri;

class at_Test extends MindTouchHttpUnitTestCase  {

    /**
     * @test
     */
    public function Can_add_single_path_segment_to_hostname() {

        // arrange
        $plug = new ApiPlug(XUri::tryParse('http://foo.com'));

        // act
        $plug = $plug->at('bar');

        // assert
        $this->assertEquals('http://foo.com/bar?dream.out.format=php', $plug->getUri());
    }

    /**
     * @test
     */
    public function Can_add_multiple_path_segments_to_hostname_1() {

        // arrange
        $plug = new ApiPlug(XUri::tryParse('http://foo.com'));
        $object = new class {
            public function __toString() {
                return 'xyz';
            }
        };
        $func = function() {
            return 'asdf';
        };

        // act
        $plug = $plug->at('bar', 'qux', 'baz', false, 321, $object, $func);

        // assert
        $this->assertEquals('http://foo.com/bar/qux/baz/false/321/xyz/asdf?dream.out.format=php', $plug->getUri());
    }

    /**
     * @test
     */
    public function Can_add_multiple_path_segments_to_hostname_2() {

        // arrange
        $plug = new ApiPlug(XUri::tryParse('http://foo.com'));

        // act
        $plug = $plug->at('bar', 'qux')->at('fred', 'baz');

        // assert
        $this->assertEquals('http://foo.com/bar/qux/fred/baz?dream.out.format=php', $plug->getUri());
    }

    /**
     * @test
     */
    public function Can_add_single_path_segment_to_existing_path() {

        // arrange
        $plug = new ApiPlug(XUri::tryParse('http://foo.com/qux'));

        // act
        $plug = $plug->at('bar');

        // assert
        $this->assertEquals('http://foo.com/qux/bar?dream.out.format=php', $plug->getUri());
    }

    /**
     * @test
     */
    public function Can_add_multiple_path_segments_to_existing_path_1() {

        // arrange
        $plug = new ApiPlug(XUri::tryParse('http://foo.com/qux'));

        // act
        $plug = $plug->at('bar', 'qux', 'fred');

        // assert
        $this->assertEquals('http://foo.com/qux/bar/qux/fred?dream.out.format=php', $plug->getUri());
    }

    /**
     * @test
     */
    public function Can_add_multiple_path_segments_to_existing_path_2() {

        // arrange
        $plug = new ApiPlug(XUri::tryParse('http://foo.com/qux'));

        // act
        $plug = $plug->at('bar', 'qux')->at('fred', 'baz');

        // assert

        /** @var ApiPlug $plug */
        $this->assertEquals('http://foo.com/qux/bar/qux/fred/baz?dream.out.format=php', $plug->getUri());
    }

    /**
     * @test
     */
    public function Can_add_single_path_segment_to_existing_path_query() {

        // arrange
        $plug = new ApiPlug(XUri::tryParse('http://foo.com/qux?a=b&c=d'));

        // act
        $plug = $plug->at('bar');

        // assert
        $this->assertEquals('http://foo.com/qux/bar?a=b&c=d&dream.out.format=php', $plug->getUri());
    }

    /**
     * @test
     */
    public function Can_add_multiple_path_segments_to_existing_path_query_1() {

        // arrange
        $plug = new ApiPlug(XUri::tryParse('http://foo.com/qux?a=b&c=d'));

        // act
        $plug = $plug->at('bar', 'qux', 'fred');

        // assert
        $this->assertEquals('http://foo.com/qux/bar/qux/fred?a=b&c=d&dream.out.format=php', $plug->getUri());
    }

    /**
     * @test
     */
    public function Can_add_multiple_path_segments_to_existing_path_query_2() {

        // arrange
        $plug = new ApiPlug(XUri::tryParse('http://foo.com/qux?a=b&c=d'));

        // act
        $plug = $plug->at('bar', 'qux')->at('baz', 'fred');

        // assert
        $this->assertEquals('http://foo.com/qux/bar/qux/baz/fred?a=b&c=d&dream.out.format=php', $plug->getUri());
    }

    /**
     * @test
     */
    public function Can_add_single_path_segment_to_existing_query() {

        // arrange
        $plug = new ApiPlug(XUri::tryParse('http://foo.com?a=b&c=d'));

        // act
        $plug = $plug->at('bar');

        // assert
        $this->assertEquals('http://foo.com/bar?a=b&c=d&dream.out.format=php', $plug->getUri());
    }

    /**
     * @test
     */
    public function Can_add_multiple_path_segments_to_existing_query_1() {

        // arrange
        $plug = new ApiPlug(XUri::tryParse('http://foo.com?a=b&c=d'));

        // act
        $plug = $plug->at('bar', 'qux', 'fred');

        // assert
        $this->assertEquals('http://foo.com/bar/qux/fred?a=b&c=d&dream.out.format=php', $plug->getUri());
    }

    /**
     * @test
     */
    public function Can_add_multiple_path_segments_to_existing_query_2() {

        // arrange
        $plug = new ApiPlug(XUri::tryParse('http://foo.com?a=b&c=d'));

        // act
        $plug = $plug->at('bar', 'qux')->at('foo', 'fred');

        // assert
        $this->assertEquals('http://foo.com/bar/qux/foo/fred?a=b&c=d&dream.out.format=php', $plug->getUri());
    }

    /**
     * @test
     */
    public function Can_add_double_urlencoded_segments() {

        // arrange
        $plug = new ApiPlug(XUri::tryParse('http://foo.com/@api/deki?strict=true'));

        // act
        $plug = $plug->at('pages', '=some/page')->at('tags', '=tag!name')->at('properties', 'chars$!');

        // assert
        $this->assertEquals('http://foo.com/@api/deki/pages/=some%252Fpage/tags/=tag%2521name/properties/chars%2524%2521?strict=true&dream.out.format=php', $plug->getUri());
    }

    /**
     * @test
     */
    public function Can_add_non_urlencoded_segments() {

        // arrange
        $plug = new ApiPlug(XUri::tryParse('http://foo.com/@api/deki?strict=true'));

        // act
        $plug = $plug->at('pages', '=some/page', 'files,subpages')->at('123', 'children,siblings');

        // assert
        $this->assertEquals('http://foo.com/@api/deki/pages/=some%252Fpage/files,subpages/123/children,siblings?strict=true&dream.out.format=php', $plug->getUri());
    }

    /**
     * @test
     */
    public function Can_add_guid_segment() {

        // arrange
        $plug = new ApiPlug(XUri::tryParse('http://foo.com/@api/deki?strict=true'));

        // act
        $plug = $plug->at('pages', ':123', 'info');

        // assert
        $this->assertEquals('http://foo.com/@api/deki/pages/:123/info?strict=true&dream.out.format=php', $plug->getUri());
    }
}
