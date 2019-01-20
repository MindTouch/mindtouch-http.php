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
namespace MindTouch\Http\tests\XUri;

use MindTouch\Http\tests\MindTouchHttpUnitTestCase;
use MindTouch\Http\XUri;

class isAbsoluteUrl_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function Is_absolute_url() {

        // act
        $result = XUri::isAbsoluteUrl('https://foo.example.com/baz');

        // assert
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function Is_not_absolute_url() {

        // act
        $result = XUri::isValidUrl('/baz');

        // assert
        $this->assertFalse($result);
    }
}
