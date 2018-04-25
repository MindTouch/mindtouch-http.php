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
namespace MindTouch\Http\tests\ApiPlug;

use MindTouch\Http\ApiPlug;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;
use MindTouch\Http\XUri;

class atRaw_Test extends MindTouchHttpUnitTestCase  {

    /**
     * @test
     */
    public function Can_add_non_urlencoded_segments() {

        // arrange
        $plug = new ApiPlug(XUri::tryParse('http://foo.com'));

        // act
        $plug = $plug->atRaw('@api')->at('deki');

        // assert
        $this->assertEquals('http://foo.com/@api/deki?dream.out.format=php', $plug->getUri());
    }
}
