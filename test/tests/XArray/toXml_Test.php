<?php
/*
 * MindTouch
 * Copyright (c) 2006-2014 MindTouch Inc.
 * http://mindtouch.com
 *
 * This file and accompanying files are licensed under the
 * MindTouch Master Subscription Agreement (MSA).
 *
 * At any time, you shall not, directly or indirectly: (i) sublicense,
 * resell, rent, lease, distribute, market, commercialize or otherwise
 * transfer rights or usage to: (a) the Software, (b) any modified version
 * or derivative work of the Software created by you or for you, or (c)
 * MindTouch Open Source (which includes all non-supported versions of
 * MindTouch-developed software), for any purpose including timesharing or
 * service bureau purposes; (ii) remove or alter any copyright, trademark
 * or proprietary notice in the Software; (iii) transfer, use or export the
 * Software in violation of any applicable laws or regulations of any
 * government or governmental agency; (iv) use or run on any of your
 * hardware, or have deployed for use, any production version of MindTouch
 * Open Source; (v) use any of the Support Services, Error corrections,
 * Updates or Upgrades, for the MindTouch Open Source software or for any
 * Server for which Support Services are not then purchased as provided
 * hereunder; or (vi) reverse engineer, decompile or modify any encrypted
 * or encoded portion of the Software.
 *
 * A complete copy of the MSA is available at http://www.mindtouch.com/msa
 */
namespace MindTouch\ApiClient\test\tests\XArray;

use MindTouch\ApiClient\XArray;
use PHPUnit_Framework_TestCase;

class with_Test extends PHPUnit_Framework_TestCase  {

    /**
     * @test
     */
    public function Simple_array_with_attributes_and_text() {

        // arrange
        $source = array('p' => array('@attr1' => 'val1', '@attr2' => 'val2', '#text' => 'text'));
        $Array = new XArray($source);

        // act
        $xml = $Array->toXml();

        // assert
        $this->assertEquals('<p attr1="val1" attr2="val2">text</p>', $xml, 'XML output was incorrect');
    }

    /**
     * @test
     */
    public function Nested_array_with_attributes_and_text() {

        // arrange
        $source = array('div' => array('@attr1' => 'val1', '@attr2' => 'val2', '#text' => 'text',
            'p' => array(
                '@attr4' => 'val4',
                '@attr5' => 'val5',
                '#text' => 'text2'
            )));
        $Array = new XArray($source);

        // act
        $xml = $Array->toXml();

        // assert
        $this->assertEquals('<div attr1="val1" attr2="val2">text<p attr4="val4" attr5="val5">text2</p></div>', $xml, 'XML output was incorrect');
    }

    /**
     * @test
     * Checks that the following characters get escaped:
     * ', ", <, >, &
     */
    public function Attributes_with_special_characters_are_encoded() {

        // arrange
        $source = array('p' => array('@attr1"&\'<>' => 'val1', '#text' => 'text'));
        $Array = new XArray($source);

        // act
        $xml = $Array->toXml();

        // assert
        $this->assertEquals('<p attr1&quot;&amp;&#039;&lt;&gt;="val1">text</p>', $xml, 'XML output was incorrect');
    }

    /**
     * @test
     * Checks that the following characters get escaped:
     * ', ", <, >, &
     */
    public function Attribute_values_with_special_characters_are_encoded() {

        // arrange
        $source = array('p' => array('@attr1' => 'val1"&\'<>', '#text' => 'text'));
        $Array = new XArray($source);

        // act
        $xml = $Array->toXml();

        // assert
        $this->assertEquals('<p attr1="val1&quot;&amp;&#039;&lt;&gt;">text</p>', $xml, 'XML output was incorrect');
    }

    /**
     * @test
     * Checks that the following characters get escaped:
     * ', ", <, >, &
     */
    public function Text_with_special_characters_are_encoded() {

        // arrange
        $source = array('p' => array('#text' => 'text"&\'<>'));
        $Array = new XArray($source);

        // act
        $xml = $Array->toXml();

        // assert
        $this->assertEquals('<p>text&quot;&amp;&#039;&lt;&gt;</p>', $xml, 'XML output was incorrect');
    }

    /**
     * @test
     * Checks that the following characters get escaped:
     * ', ", <, >, &
     */
    public function Xml_tags_with_special_characters_are_encoded() {

        // arrange
        $source = array('p"&\'<>' => array('#text' => 'text'));
        $Array = new XArray($source);

        // act
        $xml = $Array->toXml();

        // assert
        $this->assertEquals('<p&quot;&amp;&#039;&lt;&gt;>text</p&quot;&amp;&#039;&lt;&gt;>', $xml, 'XML output was incorrect');
    }

    /**
     * @test
     * Checks that the following characters get escaped:
     * ', ", <, >, &
     */
    public function Xml_values_with_special_characters_are_encoded() {

        // arrange
        $source = array('p' => 'val"&\'<>');
        $Array = new XArray($source);

        // act
        $xml = $Array->toXml();

        // assert
        $this->assertEquals('<p>val&quot;&amp;&#039;&lt;&gt;</p>', $xml, 'XML output was incorrect');
    }
}
