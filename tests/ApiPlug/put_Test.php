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
namespace MindTouch\Http\tests\ApiPlug;

use MindTouch\Http\ApiPlug;
use MindTouch\Http\ApiResult;
use MindTouch\Http\Content\ContentType;
use MindTouch\Http\Content\FormDataContent;
use MindTouch\Http\Content\TextContent;
use MindTouch\Http\Headers;
use MindTouch\Http\Mock\MockPlug;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;
use MindTouch\Http\XUri;

class put_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function Can_invoke_put() {

        // arrange
        $uri = XUri::tryParse('test://example.com/@api/deki/pages/=foo/tags');
        MockPlug::register(
            $this->newDefaultMockRequestMatcher(ApiPlug::METHOD_POST,
                $uri->with('dream.in.verb', ApiPlug::METHOD_PUT)
            ),
            (new ApiResult())
                ->withStatus(ApiResult::HTTP_SUCCESS)
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
        $this->assertEquals(ApiResult::HTTP_SUCCESS, $result->getStatus());
    }

    /**
     * @test
     */
    public function Can_invoke_put_with_text_content() {

        // arrange
        $uri = XUri::tryParse('test://example.com/@api/deki/pages/=foo/tags');
        MockPlug::register(
            $this->newDefaultMockRequestMatcher(ApiPlug::METHOD_POST, $uri->with('dream.in.verb', ApiPlug::METHOD_PUT))
                ->withHeaders(Headers::newFromHeaderNameValuePairs([
                    [Headers::HEADER_CONTENT_TYPE, ContentType::TEXT]
                ]))
                ->withBody('qux'),
            (new ApiResult())
                ->withStatus(ApiResult::HTTP_SUCCESS)
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
        $this->assertEquals(ApiResult::HTTP_SUCCESS, $result->getStatus());
    }

    /**
     * @test
     */
    public function Can_invoke_put_with_form_data_content() {

        // arrange
        $uri = XUri::tryParse('test://example.com/@api/deki/pages/=foo/tags');
        MockPlug::register(
            $this->newDefaultMockRequestMatcher(ApiPlug::METHOD_POST, $uri->with('dream.in.verb', ApiPlug::METHOD_PUT))
                ->withHeaders(Headers::newFromHeaderNameValuePairs([
                    [Headers::HEADER_CONTENT_TYPE, ContentType::FORM]
                ]))
                ->withBody([
                    'foo' => 'bar',
                    'baz' => 'qux'
                ]),
            (new ApiResult())
                ->withStatus(ApiResult::HTTP_SUCCESS)
                ->withHeaders(Headers::newFromHeaderNameValuePairs([
                    ['X-Deki-Foo', 'bar'],
                    ['X-Deki-Foo', 'baz'],
                    ['X-Qux', 'fred']
                ]))
        );
        $plug = new ApiPlug($uri);

        // act
        $result = $plug->put(new FormDataContent([
            'foo' => 'bar',
            'baz' => 'qux'
        ]));

        // assert
        $this->assertEquals(ApiResult::HTTP_SUCCESS, $result->getStatus());
    }
}
