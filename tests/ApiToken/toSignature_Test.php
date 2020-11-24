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

class toSignature_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function Can_convert_to_signature() {

        // arrange
        $token = new ApiToken('foo', 'bar');

        // act
        $signature = $token->toSignature();

        // assert
        $this->assertNotEmpty($signature);
    }

    /**
     * @test
     */
    public function Can_convert_to_signature_by_timestamp() {

        // arrange
        $token = new ApiToken('foo', 'bar');

        // act
        $signature = $token->toSignature(strtotime(' 2014-10-12T00:20:49.766Z'));

        // assert
        $this->assertEquals('foo_1413073249_2_8e158d680647ce83fef7c05fd189b537f9834e298734efe2c6b383540c1a67b7', $signature);
    }
}
