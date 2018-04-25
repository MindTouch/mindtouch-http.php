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
namespace MindTouch\Http\tests\HttpPlug\CurlInvoke;

use MindTouch\Http\Content\ContentType;
use MindTouch\Http\Content\FileContent;
use MindTouch\Http\Content\FormDataContent;
use MindTouch\Http\Content\JsonContent;
use MindTouch\Http\Content\TextContent;
use MindTouch\Http\Content\XmlContent;
use MindTouch\Http\HttpPlug;
use MindTouch\Http\tests\MindTouchHttpUnitTestCase;

class post_Test extends MindTouchHttpUnitTestCase  {

    /**
     * @test
     */
    public function Can_invoke_post() {

        // arrange
        $plug = $this->newHttpBinPlug()->at('anything');

        // act
        $result = $plug->post();

        // assert
        $this->assertEquals(200, $result->getStatus());
        $this->assertEquals(HttpPlug::METHOD_POST, $result->getBody()->getVal('method'));
    }

    /**
     * @test
     */
    public function Can_invoke_post_with_text_contet() {

        // arrange
        $plug = $this->newHttpBinPlug()->at('anything');

        // act
        $result = $plug->post(new TextContent('foo'));

        // assert
        $this->assertEquals(200, $result->getStatus());
        $body = $result->getBody();
        $this->assertEquals(ContentType::TEXT, $body->getVal('headers/Content-Type'));
        $this->assertEquals('3', $body->getVal('headers/Content-Length'));
        $this->assertEquals('foo', $body->getVal('data'));
    }

    /**
     * @test
     */
    public function Can_invoke_post_with_empty_content() {

        // arrange
        $plug = $this->newHttpBinPlug()->at('anything');

        // act
        $result = $plug->post(new TextContent(''));

        // assert
        $this->assertEquals(200, $result->getStatus());
        $body = $result->getBody();
        $this->assertEquals(ContentType::TEXT, $body->getVal('headers/Content-Type'));
        $this->assertEquals('0', $body->getVal('headers/Content-Length'));
        $this->assertEquals('', $body->getVal('data'));
    }

    /**
     * @test
     */
    public function Can_invoke_post_with_file_content() {

        // arrange
        $plug = $this->newHttpBinPlug()->at('anything');
        $filePath = dirname(__FILE__) . '/file.png';

        // act
        $result = $plug->post(new FileContent($filePath));

        // assert
        $this->assertEquals(200, $result->getStatus());
        $body = $result->getBody();
        $this->assertStringStartsWith('image/png; charset=binary', $body->getVal('headers/Content-Type'));
        $this->assertStringStartsWith('data:application/octet-stream;base64,', $body->getVal('data'));
        $this->assertEquals('100-continue', $body->getVal('headers/Expect'));

        // content-length when posting files is inconsistent across different versions of php and curl
        //$this->assertEquals('1881', $body->getVal('headers/Content-Length'));
    }

    /**
     * @test
     */
    public function Can_invoke_post_with_form_data_content() {

        // arrange
        $plug = $this->newHttpBinPlug()->at('anything');

        // act
        $result = $plug->post(new FormDataContent([
            'foo' => 'bar',
            'baz' => 'qux'
        ]));

        // assert
        $this->assertEquals(200, $result->getStatus());
        $body = $result->getBody();
        $this->assertStringStartsWith('multipart/form-data', $body->getVal('headers/Content-Type'));
        $this->assertEquals('236', $body->getVal('headers/Content-Length'));
        $this->assertEquals('100-continue', $body->getVal('headers/Expect'));
        $this->assertEquals(['foo' => 'bar', 'baz' => 'qux'], $body->getVal('form'));
    }

    /**
     * @test
     */
    public function Can_invoke_post_with_json_content() {

        // arrange
        $plug = $this->newHttpBinPlug()->at('anything');

        // act
        $result = $plug->post(JsonContent::newFromArray([
            'foo' => 'bar',
            'baz' => [
                'qux',
                'quxx',
                'fred'
            ]
        ]));

        // assert
        $this->assertEquals(200, $result->getStatus());
        $body = $result->getBody();
        $this->assertEquals(ContentType::JSON, $body->getVal('headers/Content-Type'));
        $this->assertEquals('41', $body->getVal('headers/Content-Length'));
        $this->assertEquals('{"foo":"bar","baz":["qux","quxx","fred"]}', $body->getVal('data'));
    }

    /**
     * @test
     */
    public function Can_invoke_post_with_xml_content() {

        // arrange
        $plug = $this->newHttpBinPlug()->at('anything');

        // act
        $result = $plug->post(XmlContent::newFromArray([
            'foo' => [
                '@id' => '123',
                '@baz' => 'qux',
                'baz' => [
                    '#text' => 'fred',
                    '@foo' => 'abc'
                ]
            ]
        ]));

        // assert
        $this->assertEquals(200, $result->getStatus());
        $body = $result->getBody();
        $this->assertEquals(ContentType::XML, $body->getVal('headers/Content-Type'));
        $this->assertEquals('55', $body->getVal('headers/Content-Length'));
        $this->assertEquals('<foo id="123" baz="qux"><baz foo="abc">fred</baz></foo>', $body->getVal('data'));
    }
}
