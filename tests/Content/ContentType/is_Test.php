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
namespace MindTouch\Http\tests\Content\ContentType;

use MindTouch\Http\Content\ContentType;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;

class is_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function Can_match_content_type_with_parameters() {

        // arrange
        $contentType1 = ContentType::newFromString(ContentType::XML);
        $contentType2 = ContentType::newFromString(ContentType::XML);

        // act
        $result1 = $contentType1->is($contentType2, true);
        $result2 = $contentType2->is($contentType1, true);

        // assert
        $this->assertTrue($result1);
        $this->assertTrue($result2);
    }

    /**
     * @test
     */
    public function Can_match_content_type_without_parameters() {

        // arrange
        $contentType1 = ContentType::newFromString('text/html; charset=latin');
        $contentType2 = ContentType::newFromString('text/html; charset=utf-8');

        // act
        $result1 = $contentType1->is($contentType2);
        $result2 = $contentType2->is($contentType1);

        // assert
        $this->assertTrue($result1);
        $this->assertTrue($result2);
    }

    /**
     * @test
     */
    public function Can_fail_if_content_type_not_matched_with_parameters() {

        // arrange
        $contentType1 = ContentType::newFromString('text/html; charset=utf-8');
        $contentType2 = ContentType::newFromString('text/css; charset=utf-8');

        // act
        $result1 = $contentType1->is($contentType2);
        $result2 = $contentType2->is($contentType1);

        // assert
        $this->assertFalse($result1);
        $this->assertFalse($result2);
    }

    /**
     * @test
     */
    public function Can_fail_if_content_type_not_matched_without_parameters() {

        // arrange
        $contentType1 = ContentType::newFromString('text/html; charset=utf-8');
        $contentType2 = ContentType::newFromString('text/html; charset=latin');

        // act
        $result1 = $contentType1->is($contentType2, true);
        $result2 = $contentType2->is($contentType1, true);

        // assert
        $this->assertFalse($result1);
        $this->assertFalse($result2);
    }
}
