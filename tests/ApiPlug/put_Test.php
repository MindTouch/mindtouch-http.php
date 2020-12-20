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
namespace MindTouch\Http\Tests\ApiPlug;

use MindTouch\Http\ApiPlug;
use MindTouch\Http\ApiResult;
use MindTouch\Http\Tests\MindTouchHttpUnitTestCase;
use modethirteen\Http\Content\ContentType;
use modethirteen\Http\Content\MultiPartFormDataContent;
use modethirteen\Http\Content\TextContent;
use modethirteen\Http\Headers;
use modethirteen\Http\Mock\MockPlug;
use modethirteen\Http\XUri;

class put_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function Can_invoke_put() : void {

        // arrange
        $uri = XUri::tryParse('test://example.com/@api/deki/pages/=foo/tags');
        MockPlug::register(
            $this->newDefaultMockRequestMatcher(ApiPlug::METHOD_POST,
                $uri->with('dream.in.verb', ApiPlug::METHOD_PUT)
            ),
            (new ApiResult())
                ->withStatus(200)
                ->withHeaders(Headers::newFromHeaderNameValuePairs([
                    ['X-Deki-Foo', 'bar'],
                    ['X-Deki-Foo', 'baz'],
                    ['X-Qux', 'fred']
                ]))
        );
        $plug = new ApiPlug($uri);

        // act
        $result = $plug->put();

        // assert
        $this->assertEquals(200, $result->getStatus());
    }

    /**
     * @test
     */
    public function Can_invoke_put_with_text_content() : void {

        // arrange
        $uri = XUri::tryParse('test://example.com/@api/deki/pages/=foo/tags');
        MockPlug::register(
            $this->newDefaultMockRequestMatcher(ApiPlug::METHOD_POST, $uri->with('dream.in.verb', ApiPlug::METHOD_PUT))
                ->withHeaders(Headers::newFromHeaderNameValuePairs([
                    [Headers::HEADER_CONTENT_TYPE, ContentType::TEXT]
                ]))
                ->withBody('qux'),
            (new ApiResult())
                ->withStatus(200)
                ->withHeaders(Headers::newFromHeaderNameValuePairs([
                    ['X-Deki-Foo', 'bar'],
                    ['X-Deki-Foo', 'baz'],
                    ['X-Qux', 'fred']
                ]))
        );
        $plug = new ApiPlug($uri);

        // act
        $result = $plug->put(new TextContent('qux'));

        // assert
        $this->assertEquals(200, $result->getStatus());
    }

    /**
     * @test
     */
    public function Can_invoke_put_with_form_data_content() : void {

        // arrange
        $uri = XUri::tryParse('test://example.com/@api/deki/pages/=foo/tags');
        MockPlug::register(
            $this->newDefaultMockRequestMatcher(ApiPlug::METHOD_POST, $uri->with('dream.in.verb', ApiPlug::METHOD_PUT))
                ->withHeaders(Headers::newFromHeaderNameValuePairs([
                    [Headers::HEADER_CONTENT_TYPE, ContentType::FORM_MULTIPART]
                ]))
                ->withBody([
                    'foo' => 'bar',
                    'baz' => 'qux'
                ]),
            (new ApiResult())
                ->withStatus(200)
                ->withHeaders(Headers::newFromHeaderNameValuePairs([
                    ['X-Deki-Foo', 'bar'],
                    ['X-Deki-Foo', 'baz'],
                    ['X-Qux', 'fred']
                ]))
        );
        $plug = new ApiPlug($uri);

        // act
        $result = $plug->put(new MultiPartFormDataContent([
            'foo' => 'bar',
            'baz' => 'qux'
        ]));

        // assert
        $this->assertEquals(200, $result->getStatus());
    }
}
