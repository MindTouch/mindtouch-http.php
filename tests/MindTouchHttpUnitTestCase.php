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
namespace MindTouch\Http\tests;

use MindTouch\Http\Content\FileContent;
use MindTouch\Http\Content\JsonContent;
use MindTouch\Http\Content\TextContent;
use MindTouch\Http\Content\XmlContent;
use MindTouch\Http\Headers;
use MindTouch\Http\HttpPlug;
use MindTouch\Http\Mock\MockPlug;
use MindTouch\Http\Mock\MockRequestMatcher;
use MindTouch\Http\Parser\JsonParser;
use MindTouch\Http\StringUtil;
use MindTouch\Http\XUri;
use MindTouch\XArray\XArray;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

class MindTouchHttpUnitTestCase extends PHPUnit_Framework_TestCase {

    #region Test data providers

    /**
     * @return array
     */
    public static function content_dataProvider() {
        return [
            'text' => [new TextContent('foo')],
            'json' => [JsonContent::newFromArray(['foo' => ['bar', 'baz']])],
            'xml' => [XmlContent::newFromArray(['foo' => ['bar' => ['@id' => 'qux']]])]
        ];
    }

    #endregion

    public function setUp() {
        parent::setUp();
        MockRequestMatcher::setIgnoredHeaderNames([
            Headers::HEADER_CONTENT_LENGTH
        ]);
        MockRequestMatcher::setIgnoredQueryParamNames([
            'dream.out.format',
            'dream_out_format'
        ]);
    }

    public function tearDown() {
        parent::tearDown();
        MockRequestMatcher::setIgnoredHeaderNames([]);
        MockRequestMatcher::setIgnoredQueryParamNames([]);
        MockPlug::deregisterAll();
    }

    /**
     * @param string $class
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function newMock($class) {
        return $this->getMockBuilder($class)
            ->setMethods(get_class_methods($class))
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @param string $method
     * @param XUri $uri
     * @return MockRequestMatcher
     */
    protected function newDefaultMockRequestMatcher($method, XUri $uri) { return new MockRequestMatcher($method, $uri); }

    /**
     * Return a new HttpPlug instance configured for httpbin.org
     *
     * @return HttpPlug
     */
    protected function newHttpBinPlug() {
        $baseuri = getenv('HTTPBIN_BASEURI');
        if($baseuri === false || StringUtil::isNullOrEmpty($baseuri)) {
            $baseuri = 'https://httpbin.org';
        }
        $uri = XUri::tryParse($baseuri);
        return (new HttpPlug($uri))->withHttpResultParser(new JsonParser());
    }

    /**
     * Assert that all registered mock plug invocations were called
     */
    protected function assertAllMockPlugMocksCalled() {
        if(!MockPlug::verifyAll()) {
            $this->fail('Failed asserting that all MockPlug mocks were called');
        }
    }

    /**
     * Assert that an array contains a specified key with the specified value
     *
     * @param string $key
     * @param mixed $expected
     * @param array $array
     */
    protected function assertArrayHasKeyValue($key, $expected, $array) {
        $X = new XArray($array);
        $isExpectingNumericArray = is_array($expected) && key_exists(0, $expected);
        $this->assertEquals($expected, $isExpectingNumericArray ? $X->getAll($key) : $X->getVal($key));
    }
}
