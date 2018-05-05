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
namespace MindTouch\Http\tests\Content\SerializedPhpArrayContent;

use MindTouch\Http\Content\ContentType;
use MindTouch\Http\Content\SerializedPhpArrayContent;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;

class newFromArray_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function Can_return_valid_instance() {

        // assert
        $array = ['foo' => ['bar' => ['bar', 'qux']]];

        // act
        $content = SerializedPhpArrayContent::newFromArray($array);

        // assert
        $this->assertInstanceOf('MindTouch\Http\Content\SerializedPhpArrayContent', $content);
        $this->assertEquals('a:1:{s:3:"foo";a:1:{s:3:"bar";a:2:{i:0;s:3:"bar";i:1;s:3:"qux";}}}', $content->toString());
        $this->assertEquals('a:1:{s:3:"foo";a:1:{s:3:"bar";a:2:{i:0;s:3:"bar";i:1;s:3:"qux";}}}', $content->toRaw());
        $this->assertEquals(ContentType::PHP, $content->getContentType());
    }
}
