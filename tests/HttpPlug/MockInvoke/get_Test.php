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
namespace MindTouch\Http\tests\HttpPlug\MockInvoke;

use MindTouch\Http\Content\ContentType;
use MindTouch\Http\Exception\CannotParseContentExceedsMaxContentLengthException;
use MindTouch\Http\Headers;
use MindTouch\Http\HttpPlug;
use MindTouch\Http\HttpResult;
use MindTouch\Http\Mock\MockPlug;
use MindTouch\Http\Parser\JsonParser;
use MindTouch\Http\Parser\SerializedPhpArrayParser;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;
use MindTouch\Http\XUri;

/**
 * @note mock large response content lengths
 */
class get_Test extends MindTouchHttpUnitTestCase  {

    /**
     * @test
     */
    public function Can_unserialize_json_when_max_content_length_is_not_reached() {

        // arrange
        $uri = XUri::tryParse('test://example.com/foo');
        $body = json_encode(['foo' => ['bar', 'baz']]);
        MockPlug::register(
            $this->newDefaultMockRequestMatcher(HttpPlug::METHOD_GET, $uri),
            (new HttpResult())
                ->withStatus(HttpResult::HTTP_SUCCESS)
                ->withHeaders(Headers::newFromHeaderNameValuePairs([
                    [Headers::HEADER_CONTENT_TYPE, ContentType::JSON],
                    [Headers::HEADER_CONTENT_LENGTH, strlen($body)]
                ]))
                ->withBody($body)
        );
        $plug = new HttpPlug($uri);

        // act
        $parser = (new JsonParser())->withMaxContentLength(50000);
        $result = $plug->withHttpResultParser($parser)->get();

        // assert
        $this->assertEquals(HttpResult::HTTP_SUCCESS, $result->getStatus());
        $this->assertEquals(['foo' => ['bar', 'baz']], $result->getBody()->toArray());
    }

    /**
     * @test
     */
    public function Will_throw_if_cannot_unserialize_json() {

        // arrange
        $uri = XUri::tryParse('test://example.com/foo');
        MockPlug::register(
            $this->newDefaultMockRequestMatcher(HttpPlug::METHOD_GET, $uri),
            (new HttpResult())
                ->withStatus(HttpResult::HTTP_SUCCESS)
                ->withHeaders(Headers::newFromHeaderNameValuePairs([
                    [Headers::HEADER_CONTENT_TYPE, ContentType::JSON],
                    [Headers::HEADER_CONTENT_LENGTH, 1000]
                ]))
        );
        $plug = new HttpPlug($uri);

        // act
        $exceptionThrown = false;
        try {
            $parser = (new JsonParser())->withMaxContentLength(500);
            $plug->withHttpResultParser($parser)->get();
        } catch(CannotParseContentExceedsMaxContentLengthException $e) {
            $exceptionThrown = true;
        }

        // assert
        $this->assertTrue($exceptionThrown);
    }

    /**
     * @test
     */
    public function Can_unserialize_php_when_max_content_length_is_not_reached() {

        // arrange
        $uri = XUri::tryParse('test://example.com/foo');
        $body = serialize(['foo' => ['bar', 'baz']]);
        MockPlug::register(
            $this->newDefaultMockRequestMatcher(HttpPlug::METHOD_GET, $uri),
            (new HttpResult())
                ->withStatus(HttpResult::HTTP_SUCCESS)
                ->withHeaders(Headers::newFromHeaderNameValuePairs([
                    [Headers::HEADER_CONTENT_TYPE, ContentType::PHP],
                    [Headers::HEADER_CONTENT_LENGTH, strlen($body)]
                ]))
                ->withBody($body)
        );
        $plug = new HttpPlug($uri);

        // act
        $parser = (new SerializedPhpArrayParser())->withMaxContentLength(50000);
        $result = $plug->withHttpResultParser($parser)->get();

        // assert
        $this->assertEquals(HttpResult::HTTP_SUCCESS, $result->getStatus());
        $this->assertEquals(['foo' => ['bar', 'baz']], $result->getBody()->toArray());
    }

    /**
     * @test
     */
    public function Will_throw_if_cannot_unserialize_php() {

        // arrange
        $uri = XUri::tryParse('test://example.com/foo');
        MockPlug::register(
            $this->newDefaultMockRequestMatcher(HttpPlug::METHOD_GET, $uri),
            (new HttpResult())
                ->withStatus(HttpResult::HTTP_SUCCESS)
                ->withHeaders(Headers::newFromHeaderNameValuePairs([
                    [Headers::HEADER_CONTENT_TYPE, ContentType::PHP],
                    [Headers::HEADER_CONTENT_LENGTH, 1000]
                ]))
        );
        $plug = new HttpPlug($uri);

        // act
        $exceptionThrown = false;
        try {
            $parser = (new SerializedPhpArrayParser())->withMaxContentLength(500);
            $plug->withHttpResultParser($parser)->get();
        } catch(CannotParseContentExceedsMaxContentLengthException $e) {
            $exceptionThrown = true;
        }

        // assert
        $this->assertTrue($exceptionThrown);
    }
}