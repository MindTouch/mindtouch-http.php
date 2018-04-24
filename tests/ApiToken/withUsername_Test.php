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
namespace MindTouch\Http\tests\HttpPlug;

use MindTouch\Http\ApiToken;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;

class withUsername_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function Can_update_user() {

        // arrange
        $token = new ApiToken('foo', 'bar');

        // act
        $token = $token->withUsername('andyv');

        // assert
        $this->assertEquals('foo_1413073249_=andyv_1b4845fe082c17fc82c0f59f0e568b619cf83aed716e7320d3c843b259defff5', $token->toHash(strtotime(' 2014-10-12T00:20:49.766Z')));
    }
}