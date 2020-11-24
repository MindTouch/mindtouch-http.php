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
namespace MindTouch\Http\Tests\ApiToken;

use MindTouch\Http\ApiToken;
use MindTouch\Http\Tests\MindTouchHttpUnitTestCase;

class withUserId_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function Can_update_user() {

        // arrange
        $token = new ApiToken('foo', 'bar');

        // act
        $token = $token->withUserId(123);

        // assert
        $this->assertEquals('foo_1413073249_123_06fbb9f1900f25333d3ea92b1f0074890d349e469b38e1ed6605e475da313200', $token->toSignature(strtotime(' 2014-10-12T00:20:49.766Z')));
    }
}
