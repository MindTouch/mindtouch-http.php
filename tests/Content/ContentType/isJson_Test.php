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
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;

class isJson_Test extends MindTouchHttpUnitTestCase {

    /**
     * @return array
     */
    public static function headerLine_expected_dataProvider() {
        return [
            ['application/x-latex', false],
            ['image/x-cmu-raster', false],
            ['application/base64', false],
            ['application/vnd.hp-hpgl', false],
            ['text/xml', false],
            ['application/wordperfect', false],
            ['image/pjpeg', false],
            ['application/json', true],
            ['audio/make', false],
            ['image/naplps', false],
            ['application/freeloader', false],
            ['application/octet-stream', false],
            ['text/json', true],
            ['application/xml', false],
            ['text/plain', false]
        ];
    }


    /**
     * @dataProvider headerLine_expected_dataProvider
     * @param string $headerLine
     * @param bool $expected
     * @test
     */
    public function Can_check_if_content_type_is_json($headerLine, $expected) {

        // arrange
        $contentType = ContentType::newFromContentTypeHeaderLine($headerLine);

        // act
        $result = $contentType->isJson();

        // assert
        $this->assertEquals($expected, $result);
    }
}
