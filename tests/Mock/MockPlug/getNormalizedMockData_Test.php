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
namespace MindTouch\Http\tests\Mock\MockPlug;

use MindTouch\Http\Headers;
use MindTouch\Http\HttpPlug;
use MindTouch\Http\HttpResult;
use MindTouch\Http\Mock\MockPlug;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;
use MindTouch\Http\XUri;

class getNormalizedMockData_Test extends MindTouchHttpUnitTestCase  {

    /**
     * @test
     */
    public function Can_get_normalized_call_data() {

        // arrange
        $uri1 = XUri::tryParse('test://example.com/@api/deki/pages/=foo');
        MockPlug::register(
            $this->newDefaultMockRequestMatcher(HttpPlug::METHOD_GET, $uri1)
                ->withHeaders(Headers::newFromHeaderNameValuePairs([
                    ['X-Foo', 'bar'],
                    ['X-Baz', 'qux']
                ])),
            (new HttpResult())->withStatus(HttpResult::HTTP_SUCCESS)
        );
        $uri2 = XUri::tryParse('test://example.com/@api/deki/pages/=bar/contents');
        MockPlug::register(
            $this->newDefaultMockRequestMatcher(HttpPlug::METHOD_POST, $uri2)
                ->withHeaders(Headers::newFromHeaderNameValuePairs([
                    ['X-Qux', 'foo']
                ]))
                ->withBody('string'),
            (new HttpResult())
                ->withStatus(HttpResult::HTTP_SUCCESS)
                ->withHeaders(Headers::newFromHeaderNameValuePairs([
                    ['Set-Cookie', 'dekisession=abc']
                ])),
            false
        );

        // act
        $result = MockPlug::getNormalizedMockData();

        // assert
        $expected = [
            'b924455559efcad7b3f5b8f2f515e4ab' => [
                'request' => [
                    'method' => 'GET',
                    'uri' => 'test://example.com/@api/deki/pages/=foo',
                    'headers' => [
                        'X-Baz' => 'qux',
                        'X-Foo' => 'bar'
                    ],
                    'body' => '',
                    'optional' => false
                ],
                'result' => [
                    'status' => 200
                ]
            ],
            '0a12d1b8a66091561d0e7d55edb2b4d3' => [
                'request' => [
                    'method' => 'POST',
                    'uri' => 'test://example.com/@api/deki/pages/=bar/contents',
                    'headers' => [
                        'X-Qux' => 'foo'
                    ],
                    'body' => 'string',
                    'optional' => true
                ],
                'result' => [
                    'status' => 200,
                    'headers' => [
                        'Set-Cookie' => [
                            'dekisession=abc'
                        ]
                    ]
                ]
            ]
        ];
        $this->assertEquals($expected, $result);
    }
}