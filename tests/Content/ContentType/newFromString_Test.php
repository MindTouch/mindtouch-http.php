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

class newFromString_Test extends MindTouchHttpUnitTestCase {

    /**
     * @return array
     */
    public static function headerLine_expected_dataProvider() : array {
        return [
            ['*/*; charset=iso-8859-1'],
            ['application/excel'],
            ['text/x-script.ksh'],
            ['image/naplps'],
            ['application/x-lotusscreencam'],
            ['x-world/x-vrml'],
            ['image/pjpeg'],
            ['video/quicktime'],
            ['application/x-vnd.ls-xpix'],
            ['application/x-sv4cpio'],
            ['audio/mpeg3'],
            ['video/x-isvideo'],
            ['drawing/x-dwf (old)'],
            ['application/x-midi'],
            ['application/x-koan'],
            ['application/x-director'],
            ['application/x-x509-ca-cert'],
            ['application/commonground'],
            ['image/x-xpixmap'],
            ['application/pdf'],
            ['video/x-isvideo;qux=bazzz; charset=latin;charset=utf-8', 'video/x-isvideo; qux=bazzz; charset=utf-8'],
            ['text/plain'],
            ['application/x-mathcad'],
            ['application/iges; baz=qux;', 'application/iges; baz=qux'],
            ['audio/x-gsm'],
            ['message/rfc822;foo=fred', 'message/rfc822; foo=fred'],
            ['text/*'],
            ['video/x-ms-asf'],
            ['application/octet-stream'],
            ['image/x-xwindowdump'],
            ['text/x-script.perl'],
            ['text/plain'],
            ['application/x-visio'],
            ['audio/make.my.funk'],
            ['application/octet-stream'],
            ['audio/voxware'],
            ['text/vnd.wap.wml'],
            ['application/x-excel'],
            ['application/x-dvi'],
            ['image/x-portable-greymap'],
            ['model/vrml'],
            ['application/x-bzip2'],
            ['application/mime'],
            ['application/x-lisp'],
            ['application/pdf'],
            ['image/naplps'],
            ['application/x-compress'],
            ['video/x-ms-asf'],
            ['video/x-gl; qux=baz'],
            ['application/x-compressed; charset=iso-8859;foo=bar', 'application/x-compressed; charset=iso-8859; foo=bar'],
            ['application/x-helpfile; boundary=something; charset=utf-8']
        ];
    }


    /**
     * @dataProvider headerLine_expected_dataProvider
     * @param string $headerLine
     * @param string|null $expected
     * @test
     */
    public function Can_return_valid_content_type_instance_from_valid_header_line(string $headerLine, string $expected = null) {

        // arrange
        if($expected === null) {
            $expected = $headerLine;
        }

        // act
        $result = ContentType::newFromString($headerLine);

        // assert
        $this->assertEquals($expected, $result->toString());
    }

    /**
     * @test
     */
    public function Can_return_null_from_invalid_header_line() {

        // act
        $result1 = ContentType::newFromString('');
        $result2 = ContentType::newFromString('image');
        $result3 = ContentType::newFromString('application; charset=utf-8');
        $result4 = ContentType::newFromString('/plain; charset=latin');
        $result5 = ContentType::newFromString('charset=iso-8859');

        // assert
        $this->assertNull($result1);
        $this->assertNull($result2);
        $this->assertNull($result3);
        $this->assertNull($result4);
        $this->assertNull($result5);
    }
}
