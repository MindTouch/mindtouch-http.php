<?php
/**
 * MindTouch API PHP Client
 * Copyright (C) 2006-2016 MindTouch, Inc.
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
namespace MindTouch\ApiClient\test\tests\ApiPlug;

use MindTouch\ApiClient\ApiPlug;
use MindTouch\ApiClient\test\MockPlug;
use MindTouch\ApiClient\test\MockRequest;
use MindTouch\ApiClient\test\MockResponse;
use PHPUnit_Framework_TestCase;

class get_Test extends PHPUnit_Framework_TestCase  {

    public function setUp() {
        MockPlug::ignoreRequestHeader(ApiPlug::HEADER_CONTENT_LENGTH);
        MockPlug::ignoreRequestQueryParam('dream_out_format');
    }

    public function tearDown() {
        MockPlug::deregisterAll();
    }

    /**
     * @test
     */
    public function Can_invoke_get() {

        // arrange
        $uri = 'http://example.com/@api/deki/pages/=foo';
        MockPlug::register(
            MockRequest::newMockRequest(ApiPlug::VERB_GET, $uri, []),
            MockResponse::newMockResponse(ApiPlug::HTTPSUCCESS, [], ['page'])
        );
        $Plug = ApiPlug::newPlug($uri);

        // act
        $Result = $Plug->get();

        // assert
        $this->assertEquals(200, $Result->getStatus());
        $this->assertEquals('page', $Result->getVal('body'));
    }
}
