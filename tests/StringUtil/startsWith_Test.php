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
namespace MindTouch\Http\tests\StringUtil;

use MindTouch\Http\StringUtil;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;

class startsWith_Test extends MindTouchHttpUnitTestCase  {

    /**
     * @test
     */
    public function String_starts_with() {

        // act
        $result = StringUtil::startsWith('foobar', 'foo');

        // assert
        $this->assertEquals(true, $result);
    }

    /**
     * @test
     */
    public function String_does_start_with() {

        // act
        $result = StringUtil::startsWith('foobar', 'gak');

        // assert
        $this->assertEquals(false, $result);
    }

    /**
     * @test
     */
    public function Case_is_strict_when_checking_starts_with() {

        // act
        $result = StringUtil::startsWith('foobar', 'FOO');

        // assert
        $this->assertEquals(false, $result);
    }
}
