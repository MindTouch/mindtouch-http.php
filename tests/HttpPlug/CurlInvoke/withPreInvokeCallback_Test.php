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
namespace MindTouch\Http\tests\HttpPlug\CurlInvoke;

use MindTouch\Http\Headers;
use MindTouch\Http\HttpPlug;
use MindTouch\Http\IMutableHeaders;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;
use MindTouch\Http\XUri;

class withPreInvokeCallback_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function Can_execute_callback_and_mutate_request_before_invocation() {

        // arrange
        $plug = $this->newHttpBinPlug();

        // act
        $result = $plug
            ->withPreInvokeCallback(function(&$method, XUri &$uri, IMutableHeaders $headers) {
                $method = HttpPlug::METHOD_GET;
                $uri = $uri->at('anything');
                $headers->setHeader('X-Callback-Header', 'baz');
            })
            ->post();

        // assert
        $this->assertAllMockPlugMocksCalled();
        $this->assertEquals(200, $result->getStatus());
        $this->assertEquals(HttpPlug::METHOD_GET, $result->getBody()->getVal('method'));
        $this->assertEquals('baz', $result->getBody()->getVal('headers/X-Callback-Header'));
    }

    /**
     * @test
     */
    public function Can_execute_callback_and_mutate_request_credentials_before_invocation() {

        // arrange
        $plug = $this->newHttpBinPlug()
            ->at('anything')
            ->withCredentials('foo', 'bar');
        $preInvokeAuthorizationHeaderValue = null;

        // act
        $result = $plug
            ->withPreInvokeCallback(function($method, XUri $uri, IMutableHeaders $headers) use (&$preInvokeAuthorizationHeaderValue) {
                $preInvokeAuthorizationHeaderValue = $headers->getHeaderLine(Headers::HEADER_AUTHORIZATION);
                $headers->setHeader(Headers::HEADER_AUTHORIZATION, 'Basic ' . base64_encode('foo:fred'));
            })
            ->get();

        // assert
        $this->assertAllMockPlugMocksCalled();
        $this->assertEquals(200, $result->getStatus());
        $this->assertEquals(HttpPlug::METHOD_GET, $result->getBody()->getVal('method'));
        $this->assertEquals('Basic Zm9vOmJhcg==', $preInvokeAuthorizationHeaderValue);
        $this->assertEquals('Basic Zm9vOmZyZWQ=', $result->getBody()->getVal('headers/Authorization'));
    }
}
