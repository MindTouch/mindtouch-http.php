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

use MindTouch\Http\HttpResult;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;

class withPostInvokeCallback_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function Can_execute_callback_and_mutate_result_after_invocation() {

        // arrange
        $plug = $this->newHttpBinPlug()->at('anything');

        // act
        $result = $plug
            ->withPostInvokeCallback(function(HttpResult $result) {
                $result->setVal('headers/X-Callback-Header', ['foo']);
            })
            ->get();

        // assert
        $this->assertAllMockPlugMocksCalled();
        $this->assertEquals(200, $result->getStatus());
        $this->assertEquals('foo', $result->getHeaders()->getHeaderLine('X-Callback-Header'));
    }

    /**
     * @test
     */
    public function Can_execute_callback_after_request_info_has_been_added_and_parsers_have_run() {

        // arrange
        $plug = $this->newHttpBinPlug()->at('anything');
        $request = null;
        $body = null;

        // act
        $result = $plug
            ->withPostInvokeCallback(function(HttpResult $result) use (&$request, &$body) {
                $request = $result->getVal('request');
                $body = $result->getVal('body');
            })
            ->get();

        // assert
        $this->assertAllMockPlugMocksCalled();
        $this->assertEquals(200, $result->getStatus());
        $this->assertTrue(is_array($request));
        $this->assertTrue(is_array($body));
    }
}
