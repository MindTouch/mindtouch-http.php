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
namespace MindTouch\Http\tests\Content\FileContent;

use InvalidArgumentException;
use MindTouch\Http\Content\FileContent;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;

class __construct_Test extends MindTouchHttpUnitTestCase {

    /**
     * @test
     */
    public function Cannot_instantiate_file_content_with_invalid_file_path() {

        // assert
        $filePath = dirname(__FILE__) . '/foo.png';

        // act
        $exceptionThrown = false;
        try {
            new FileContent($filePath);
        } catch(InvalidArgumentException $e) {
            $exceptionThrown = true;
        }

        // assert
        $this->assertTrue($exceptionThrown);
    }
}
