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
use MindTouch\Http\ApiResult;
use MindTouch\Http\Content\ContentType;
use MindTouch\Http\Exception\ApiResultException;
use MindTouch\Http\Exception\HttpResultParserContentExceedsMaxContentLengthException;
use MindTouch\Http\Exception\HttpResultParserException;
use MindTouch\Http\Headers;
use MindTouch\Http\HttpResult;
use MindTouch\Http\Mock\MockPlug;
use MindTouch\Http\Parser\SerializedPhpArrayParser;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;
use MindTouch\Http\XUri;

class withResultErrorHandler_Test extends MindTouchHttpUnitTestCase  {

    /**
     * @test
     */
    public function Can_invoke_with_result_error_handler_exception_supression() {

        // arrange
        $uri = XUri::tryParse('test://example.com/@api/deki/pages/=foo');
        MockPlug::register(
            $this->newDefaultMockRequestMatcher(ApiPlug::METHOD_GET, $uri),
            (new ApiResult())
                ->withStatus(403)
                ->withHeaders(Headers::newFromHeaderNameValuePairs([
                    [Headers::HEADER_CONTENT_TYPE, ContentType::PHP]
                ]))
                ->withBody(serialize([
                    'error' => [
                        'message' => 'baz'
                    ]
                ]))
        );
        $plug = new ApiPlug($uri);

        // act
        $result = $plug->withResultErrorHandler(function(ApiResultException $e) {
            $result = $e->getResult();
            $this->assertEquals(403, $result->getStatus());
            $this->assertEquals('baz', $result->getError());
            return true;
        })->get();

        // assert
        $this->assertEquals(403, $result->getStatus());
    }

    /**
     * @test
     */
    public function Result_error_handler_can_handle_parser_error() {

        // arrange
        $uri = XUri::tryParse('test://example.com/@api/deki/pages/=foo');
        MockPlug::register(
            $this->newDefaultMockRequestMatcher(ApiPlug::METHOD_GET, $uri),
            (new ApiResult())
                ->withStatus(200)
                ->withHeaders(Headers::newFromHeaderNameValuePairs([
                    [Headers::HEADER_CONTENT_TYPE, ContentType::PHP],
                    [Headers::HEADER_CONTENT_LENGTH, '1000']
                ]))
                ->withBody(serialize([
                    'page' => [
                        'foo' => 'bar'
                    ]
                ]))
        );
        $plug = (new ApiPlug($uri))->withHttpResultParser((new SerializedPhpArrayParser())->withMaxContentLength(500));
        $result = null;

        // act
        $plug->withResultErrorHandler(function(HttpResultParserException $e) use (&$result) {
            $result = $e->getResult();
            return true;
        })->get();

        // assert
        $this->assertInstanceOf(HttpResult::class, $result);
    }

    /**
     * @test
     */
    public function Result_error_handler_can_handle_content_length_parser_error() {

        // arrange
        $uri = XUri::tryParse('test://example.com/@api/deki/pages/=foo');
        MockPlug::register(
            $this->newDefaultMockRequestMatcher(ApiPlug::METHOD_GET, $uri),
            (new ApiResult())
                ->withStatus(200)
                ->withHeaders(Headers::newFromHeaderNameValuePairs([
                    [Headers::HEADER_CONTENT_TYPE, ContentType::PHP],
                    [Headers::HEADER_CONTENT_LENGTH, '1000']
                ]))
                ->withBody(serialize([
                    'page' => [
                        'foo' => 'bar'
                    ]
                ]))
        );
        $plug = (new ApiPlug($uri))->withHttpResultParser((new SerializedPhpArrayParser())->withMaxContentLength(500));
        $result = null;

        // act
        $plug->withResultErrorHandler(function(HttpResultParserContentExceedsMaxContentLengthException $e) use (&$result) {
            $result = $e->getResult();
            return true;
        })->get();

        // assert
        $this->assertInstanceOf(HttpResult::class, $result);
    }
}
