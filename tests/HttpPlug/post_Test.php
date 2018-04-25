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

use InvalidArgumentException;
use MindTouch\Http\HttpPlug;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;
use MindTouch\Http\XUri;

class post_Test extends MindTouchHttpUnitTestCase  {

    /**
     * @test
     */
    public function Cannot_invoke_post_with_non_content_object() {

        // arrange
        $uri = XUri::tryParse('https://example.com/foo');
        $plug = new HttpPlug($uri);

        // act
        $exceptionThrown = false;
        try {
            $plug->post('foo');
        } catch(InvalidArgumentException $e) {
            $exceptionThrown = true;
        }

        // assert
        $this->assertTrue($exceptionThrown);
    }
}
