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
namespace MindTouch\Http\tests\HttpPlug\CurlInvoke;

use MindTouch\Http\Content\ContentType;
use MindTouch\Http\Content\TextContent;
use MindTouch\Http\HttpResult;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;

class withAutoRedirects_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function Can_follow_redirects_by_default() {

        // arrange
        $plug = $this->newHttpBinPlug()->at('redirect', '1');

        // act
        $result = $plug->get();

        // assert
        $this->assertEquals(HttpResult::HTTP_SUCCESS, $result->getStatus());
        $this->assertEquals('https://httpbin.org/redirect/1', $result->getVal('request/uri'));
        $this->assertEquals('https://httpbin.org/get', $result->getBody()->getVal('url'));
        $headers = $result->getAll('rawheaders');
        $this->assertContains('HTTP/1.1 302 FOUND', $headers);
        $this->assertContains('Location: /get', $headers);
    }

    /**
     * @test
     */
    public function Can_follow_redirect_by_method() {

           // arrange
        $plug = $this->newHttpBinPlug()->at('redirect-to')
            ->with('url', 'https://httpbin.org/post')
            ->with('status_code', '307');

        // act
        $result = $plug->post(new TextContent('foo'));

        // assert
        $this->assertEquals(HttpResult::HTTP_SUCCESS, $result->getStatus());
        $body = $result->getBody();
        $this->assertEquals('https://httpbin.org/post', $body->getVal('url'));
        $this->assertEquals(ContentType::TEXT, $body->getVal('headers/Content-Type'));
        $this->assertEquals('3', $body->getVal('headers/Content-Length'));
        $this->assertEquals('foo', $body->getVal('data'));
    }

    /**
     * @test
     */
    public function Can_set_max_auto_redirects() {

        // arrange
        $plug = $this->newHttpBinPlug()->at('redirect', '3');

        // act
        $result = $plug->withAutoRedirects(2)->get();

        // assert
        $this->assertEquals(HttpResult::HTTP_FOUND, $result->getStatus());
        $this->assertEquals(47, $result->getVal('errno'));
        $this->assertEquals('/get', $result->getHeaders()->getHeaderLine('Location'));
    }

    /**
     * @test
     */
    public function Can_disable_auto_redirect() {

        // arrange
        $plug = $this->newHttpBinPlug()->at('redirect', '1');

        // act
        $result = $plug->withAutoRedirects(0)->get();

        // assert
        $this->assertEquals(HttpResult::HTTP_FOUND, $result->getStatus());
        $this->assertEquals(0, $result->getVal('errno'));
        $this->assertEquals('/get', $result->getHeaders()->getHeaderLine('Location'));
    }
}
