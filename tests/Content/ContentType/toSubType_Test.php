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

class toSubType_Test extends MindTouchHttpUnitTestCase {

    /**
     * @return array
     */
    public static function headerLine_expected_dataProvider() : array {
        return [
            ['*/*; charset=iso-8859-1', '*'],
            ['application/excel', 'excel'],
            ['text/x-script.ksh', 'x-script.ksh'],
            ['image/naplps', 'naplps'],
            ['application/x-lotusscreencam', 'x-lotusscreencam'],
            ['x-world/x-vrml', 'x-vrml'],
            ['image/pjpeg', 'pjpeg'],
            ['video/quicktime', 'quicktime'],
            ['application/x-vnd.ls-xpix', 'x-vnd.ls-xpix'],
            ['application/x-sv4cpio', 'x-sv4cpio'],
            ['audio/mpeg3', 'mpeg3'],
            ['video/x-isvideo', 'x-isvideo'],
            ['drawing/x-dwf (old)', 'x-dwf (old)'],
            ['application/x-midi', 'x-midi'],
            ['application/x-koan', 'x-koan'],
            ['application/x-director', 'x-director'],
            ['application/x-x509-ca-cert', 'x-x509-ca-cert'],
            ['application/commonground', 'commonground'],
            ['image/x-xpixmap', 'x-xpixmap'],
            ['application/pdf', 'pdf'],
            ['video/x-isvideo;qux=bazzz; charset=latin;charset=utf-8', 'x-isvideo'],
            ['text/plain', 'plain'],
            ['application/x-mathcad', 'x-mathcad'],
            ['application/iges; baz=qux;', 'iges'],
            ['audio/x-gsm', 'x-gsm'],
            ['message/rfc822;foo=fred', 'rfc822'],
            ['text/*', '*'],
            ['video/x-ms-asf', 'x-ms-asf'],
            ['application/octet-stream', 'octet-stream'],
            ['image/x-xwindowdump', 'x-xwindowdump'],
            ['text/x-script.perl', 'x-script.perl'],
            ['text/plain', 'plain'],
            ['application/x-visio', 'x-visio'],
            ['audio/make.my.funk', 'make.my.funk'],
            ['application/octet-stream', 'octet-stream'],
            ['audio/voxware', 'voxware'],
            ['text/vnd.wap.wml', 'vnd.wap.wml'],
            ['application/x-excel', 'x-excel'],
            ['application/x-dvi', 'x-dvi'],
            ['image/x-portable-greymap', 'x-portable-greymap'],
            ['model/vrml', 'vrml'],
            ['application/x-bzip2', 'x-bzip2'],
            ['application/mime', 'mime'],
            ['application/x-lisp', 'x-lisp'],
            ['application/pdf', 'pdf'],
            ['image/naplps', 'naplps'],
            ['application/x-compress', 'x-compress'],
            ['video/x-ms-asf', 'x-ms-asf'],
            ['video/x-gl; qux=baz', 'x-gl'],
            ['application/x-compressed; charset=iso-8859;foo=bar', 'x-compressed'],
            ['application/x-helpfile; boundary=something; charset=utf-8', 'x-helpfile']
        ];
    }


    /**
     * @dataProvider headerLine_expected_dataProvider
     * @param string $headerLine
     * @param string $expected
     * @test
     */
    public function Can_return_sub_type(string $headerLine, string $expected) {

        // arrange
        $contentType = ContentType::newFromString($headerLine);

        // act
        $result = $contentType->toSubType();

        // assert
        $this->assertEquals($expected, $result);
    }
}