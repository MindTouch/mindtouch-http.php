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
namespace MindTouch\Http\tests\Content\SerializedPhpArrayContent;

use MindTouch\Http\Content\ContentType;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;

class toMainType_Test extends MindTouchHttpUnitTestCase {

    /**
     * @return array
     */
    public static function headerLine_expected_dataProvider() : array {
        return [
            ['*/*; charset=iso-8859-1', '*'],
            ['application/excel', 'application'],
            ['text/x-script.ksh', 'text'],
            ['image/naplps', 'image'],
            ['application/x-lotusscreencam', 'application'],
            ['x-world/x-vrml', 'x-world'],
            ['image/pjpeg', 'image'],
            ['video/quicktime', 'video'],
            ['application/x-vnd.ls-xpix', 'application'],
            ['application/x-sv4cpio', 'application'],
            ['audio/mpeg3', 'audio'],
            ['video/x-isvideo', 'video'],
            ['drawing/x-dwf (old)', 'drawing'],
            ['application/x-midi', 'application'],
            ['application/x-koan', 'application'],
            ['application/x-director', 'application'],
            ['application/x-x509-ca-cert', 'application'],
            ['application/commonground', 'application'],
            ['image/x-xpixmap', 'image'],
            ['application/pdf', 'application'],
            ['video/x-isvideo;qux=bazzz; charset=latin;charset=utf-8', 'video'],
            ['text/plain', 'text'],
            ['application/x-mathcad', 'application'],
            ['application/iges; baz=qux;', 'application'],
            ['audio/x-gsm', 'audio'],
            ['message/rfc822;foo=fred', 'message'],
            ['text/*', 'text'],
            ['video/x-ms-asf', 'video'],
            ['application/octet-stream', 'application'],
            ['image/x-xwindowdump', 'image'],
            ['text/x-script.perl', 'text'],
            ['text/plain', 'text'],
            ['application/x-visio', 'application'],
            ['audio/make.my.funk', 'audio'],
            ['application/octet-stream', 'application'],
            ['audio/voxware', 'audio'],
            ['text/vnd.wap.wml', 'text'],
            ['application/x-excel', 'application'],
            ['application/x-dvi', 'application'],
            ['image/x-portable-greymap', 'image'],
            ['model/vrml', 'model'],
            ['application/x-bzip2', 'application'],
            ['application/mime', 'application'],
            ['application/x-lisp', 'application'],
            ['application/pdf', 'application'],
            ['image/naplps', 'image'],
            ['application/x-compress', 'application'],
            ['video/x-ms-asf', 'video'],
            ['video/x-gl; qux=baz', 'video'],
            ['application/x-compressed; charset=iso-8859;foo=bar', 'application'],
            ['application/x-helpfile; boundary=something; charset=utf-8', 'application']
        ];
    }


    /**
     * @dataProvider headerLine_expected_dataProvider
     * @param string $headerLine
     * @param string $expected
     * @test
     */
    public function Can_return_main_type(string $headerLine, string $expected) {

        // arrange
        $contentType = ContentType::newFromString($headerLine);

        // act
        $result = $contentType->toMainType();

        // assert
        $this->assertEquals($expected, $result);
    }
}
