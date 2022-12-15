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
use MindTouch\Http\Exception\ApiResultException;
use MindTouch\Http\Tests\MindTouchHttpUnitTestCase;
use modethirteen\Http\Content\ContentType;
use modethirteen\Http\Headers;
use modethirteen\Http\Mock\MockPlug;
use modethirteen\Http\XUri;

class withoutResultErrorHandler_Test extends MindTouchHttpUnitTestCase  {

    /**
     * @test
     */
    public function Can_remove_result_error_handler_exception_supression_before_invocation() : void {

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
        $exceptionThrown = false;
        try {
            $plug->withResultErrorHandler(fn(): bool => true)->withoutResultErrorHandler()->get();

        } /** @noinspection PhpRedundantCatchClauseInspection */
        catch(ApiResultException $e) {
            $exceptionThrown = true;
            $result = $e->getResult();
            $this->assertEquals(403, $result->getStatus());
            $this->assertEquals('baz', $result->getError());
        }

        // assert
        $this->assertTrue($exceptionThrown);
    }
}
