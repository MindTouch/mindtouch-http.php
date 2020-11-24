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
use MindTouch\Http\IApiToken;
use MindTouch\Http\Tests\MindTouchHttpUnitTestCase;
use modethirteen\Http\Headers;
use modethirteen\Http\Mock\MockPlug;
use modethirteen\Http\XUri;

class withApiToken_Test extends MindTouchHttpUnitTestCase  {

    /**
     * @test
     */
    public function Can_invoke_with_api_token() {

        // arrange
        $uri = XUri::tryParse('test://example.com/@api/deki/pages/=foo');
        $token = $this->newMock(IApiToken::class);
        $token->expects($this->once())
            ->method('toSignature')
            ->will($this->returnValue('foo'));
        MockPlug::register(
            $this->newDefaultMockRequestMatcher(ApiPlug::METHOD_GET, $uri)
                ->withHeaders(Headers::newFromHeaderNameValuePairs([
                    ['X-Deki-Token', 'foo']
                ])),
            (new ApiResult())->withStatus(200)
        );
        $plug = new ApiPlug($uri);

        // act
        /** @var IApiToken $token */
        $result = $plug->withApiToken($token)->get();

        // assert
        $this->assertEquals(200, $result->getStatus());
    }
}
