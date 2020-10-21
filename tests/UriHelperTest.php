<?php

namespace Rokka\Client\Tests;

use GuzzleHttp\Psr7\Uri;
use Rokka\Client\Core\Stack;
use Rokka\Client\Core\StackOperation;
use Rokka\Client\Core\StackUri;
use Rokka\Client\UriHelper;

class UriHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return array
     */
    public function provideAddOptionsToUri()
    {
        return [
            ['', 'options-dpr-2', 'o-dpr-2'],
            ['', 'o-dpr-2', 'options-dpr-2', false],
            ['', 'options-dpr-2--v-w-300', 'o-dpr-2--v-w-300'],
            ['', 'o-dpr-2--variables-w-300', 'options-dpr-2--variables-w-300', false],
            ['', 'variables-w-300', 'variables-w-300', false],
            ['', 'v-w-300', 'v-w-300'],

            ['', ['options' => ['dpr' => 2]], 'o-dpr-2'],
            ['resize-width-100', ['options' => ['dpr' => 2]], 'resize-width-100--o-dpr-2'],
            ['resize-width-100', ['options' => ['dpr' => 2]], 'resize-width-100--options-dpr-2', false],

            ['resize-width-100', ['operations' => [['name' => 'resize', 'options' => ['upscale' => 'false']]]], 'resize-upscale-false-width-100'],
            ['resize-width-100', ['operations' => [['name' => 'resize', 'options' => ['upscale' => false]]]], 'resize-upscale-false-width-100'],
            ['resize-width-100', 'resize-upscale-false', 'resize-upscale-false-width-100'],

            ['resize-width-100', ['options' => ['dpr' => 2], 'operations' => [new StackOperation('resize', ['width' => 200])]], 'resize-width-200--o-dpr-2'],
            ['resize-width-100', ['options' => ['dpr' => 2], 'operations' => [['name' => 'resize', 'options' => ['width' => 200]]]], 'resize-width-200--o-dpr-2'],

            ['resize-width-100/options-dpr-3', 'options-dpr-2', 'resize-width-100--o-dpr-2'],
            ['resize-width-100/o-dpr-3', 'options-dpr-2', 'resize-width-100--o-dpr-2'],
            ['resize-width-100/options-dpr-3', ['options' => ['dpr' => 2]], 'resize-width-100--o-dpr-2'],
            ['resize-width-100/options-dpr-3/variables-w-3', ['options' => ['dpr' => 2]], 'resize-width-100--o-dpr-2--v-w-3'],
            ['resize-width-100/options-dpr-3/v-w-3', ['options' => ['dpr' => 2]], 'resize-width-100--o-dpr-2--v-w-3'],

            ['resize-width-100/options-dpr-3', '', 'resize-width-100--o-dpr-3'],

            ['resize-width-100--resize-width-200', 'resize-width-300', 'resize-width-300--resize-width-300'],
            ['resize-width-100/resize-width-200', 'resize-width-300', 'resize-width-300'],
            ['resize-width-100', 'options-dpr-2--resize-width-200', 'resize-width-200--o-dpr-2'],
            ['resize-width-100--resize-width-200', 'options-dpr-2', 'resize-width-100--resize-width-200--o-dpr-2'],
            ['resize-width-100--options-autoformat-true-dpr-3', 'options-dpr-2', 'resize-width-100--o-autoformat-true-dpr-2'],
            ['resize-width-100--resize-width-300--options-autoformat-true-dpr-3', 'resize-height-200', 'resize-height-200-width-100--resize-height-200-width-300--o-autoformat-true-dpr-3'],
            ['resize-width-100--resize-width-300--options-autoformat-true-dpr-3', 'resize-height-200-width-200', 'resize-height-200-width-200--resize-height-200-width-200--o-autoformat-true-dpr-3'],
            ['resize-width-100--options-autoformat-true', 'options-dpr-2', 'resize-width-100--o-autoformat-true-dpr-2'],
            ['resize-width-100--options-autoformat-true-dpr-3', 'options-dpr-2', 'resize-width-100--o-autoformat-true-dpr-2'],
            ['resize-width-100--options-autoformat-true-dpr-3', 'o-dpr-2', 'resize-width-100--o-autoformat-true-dpr-2'],

            ['resize-width-100--options-autoformat-true--v-w-300', 'v-w-200', 'resize-width-100--o-autoformat-true--v-w-200'],
        ];
    }

    /**
     * @return array
     */
    public function provideGetSrcSetUrl()
    {
        return [
            ['2x', null, 'https://test.rokka.io/stackname/o-dpr-2/b537639e539efcc3df4459ef87c5963aa5079ca6.jpg'],
            ['3x', null, 'https://test.rokka.io/stackname/o-dpr-3/b537639e539efcc3df4459ef87c5963aa5079ca6.jpg'],
            ['300w', null, 'https://test.rokka.io/stackname/resize-width-300/b537639e539efcc3df4459ef87c5963aa5079ca6.jpg'],
            ['300w', '2x', 'https://test.rokka.io/stackname/resize-width-150--o-dpr-2/b537639e539efcc3df4459ef87c5963aa5079ca6.jpg'],
            ['300w', '3x', 'https://test.rokka.io/stackname/resize-width-100--o-dpr-3/b537639e539efcc3df4459ef87c5963aa5079ca6.jpg'],
            ['2x', 'options-jpg.quality-50', 'https://test.rokka.io/stackname/o-dpr-2-jpg.quality-50/b537639e539efcc3df4459ef87c5963aa5079ca6.jpg'],
            ['300w', 'options-jpg.quality-50', 'https://test.rokka.io/stackname/resize-width-300--o-jpg.quality-50/b537639e539efcc3df4459ef87c5963aa5079ca6.jpg'],

            ['300w', 'options-jpg.quality-50-dpr-2', 'https://test.rokka.io/stackname/resize-width-150--o-dpr-2-jpg.quality-50/b537639e539efcc3df4459ef87c5963aa5079ca6.jpg'],
            ['300w', 'options-jpg.quality-50-dpr-2--resize-width-200', 'https://test.rokka.io/stackname/resize-width-200--o-dpr-2-jpg.quality-50/b537639e539efcc3df4459ef87c5963aa5079ca6.jpg'],
            ['300w', 'options-jpg.quality-50--resize-width-200', 'https://test.rokka.io/stackname/resize-width-200--o-jpg.quality-50/b537639e539efcc3df4459ef87c5963aa5079ca6.jpg'],

            ['300w', 'options-jpg.quality-50--resize-width-200', 'https://test.rokka.io/stackname/resize-width-200--o-jpg.quality-50/b537639e539efcc3df4459ef87c5963aa5079ca6.jpg', false],

            ['300w', 'v-w-200--options-jpg.quality-50', 'https://test.rokka.io/stackname/o-jpg.quality-50--v-w-200/b537639e539efcc3df4459ef87c5963aa5079ca6.jpg', false],
            ['300w', 'v-w-200', 'https://test.rokka.io/stackname/v-w-200/b537639e539efcc3df4459ef87c5963aa5079ca6.jpg', false],
        ];
    }

    public function provideDecomposeUri()
    {
        return [
            ['', ['optionsUrl' => '', 'optionsArray' => []]],
            ['/', ['optionsUrl' => '', 'optionsArray' => []]],
            ['options-jpg.quality-90/', ['optionsUrl' => 'o-jpg.quality-90', 'optionsArray' => ['jpg.quality' => '90']]],
            ['options-jpg.quality-90/options-dpr-2/', ['optionsUrl' => 'o-dpr-2-jpg.quality-90', 'optionsArray' => ['jpg.quality' => '90', 'dpr' => '2']]],
            ['options-jpg.quality-90/options-jpg.quality-40/', ['optionsUrl' => 'o-jpg.quality-40', 'optionsArray' => ['jpg.quality' => '40']]],
            ['options-jpg.quality-90--options-jpg.quality-40/', ['optionsUrl' => 'o-jpg.quality-40', 'optionsArray' => ['jpg.quality' => '40']]],
            ['resize-width-100//', ['optionsUrl' => 'resize-width-100', 'optionsArray' => []]],
            ['//options-jpg.quality-90//', ['optionsUrl' => 'o-jpg.quality-90', 'optionsArray' => ['jpg.quality' => '90']]],
            ['//options-jpg.quality-90/', ['optionsUrl' => 'o-jpg.quality-90', 'optionsArray' => ['jpg.quality' => '90']]],
            ['--options/', ['optionsUrl' => '', 'optionsArray' => []]],
            ['options--/', ['optionsUrl' => '', 'optionsArray' => []]],
            ['--/', ['optionsUrl' => '', 'optionsArray' => []]],
            ['resize-width-100--options-dpr-2/', ['optionsUrl' => 'resize-width-100--o-dpr-2', 'optionsArray' => ['dpr' => '2']]],
            ['resize-width-100--blur--resize-width-200--options-dpr-2/', ['optionsUrl' => 'resize-width-100--blur--resize-width-200--o-dpr-2', 'optionsArray' => ['dpr' => '2']]],
            ['resize-width-100--blur--resize-width-200--options-dpr-2/resize-width-300/', ['optionsUrl' => 'resize-width-300--blur--resize-width-300--o-dpr-2', 'optionsArray' => ['dpr' => 2]]],
        ];
    }

    /**
     * @dataProvider provideDecomposeUri
     *
     * @param $option
     * @param $expectedOptions
     */
    public function testDecomposeUri($option, $expectedOptions)
    {
        $stacks = [
            'noop',
            'dynamic',
            '5e05e0',
        ];

        $hashes = [
            'b537639e539efcc3df4459ef87c5963aa5079ca6',
            'b53763',
            '-some/remot-e/path.jpeg-',
            '-remote/image.jpg%3Fwidth=100-',
            '-some/rem-ote/image-',
        ];

        foreach ($stacks as $stack) {
            $this->twoRoutesTest($hashes, $stack, $option, $expectedOptions);
        }
    }

    /**
     * @dataProvider provideAddOptionsToUri
     *
     * @param string       $inputUrl
     * @param string|array $options
     * @param string       $expected
     * @param mixed        $shortNames
     */
    public function testAddOptionsToUri($inputUrl, $options, $expected, $shortNames = true)
    {
        $this->assertSame('https://test.rokka.io/stackname/'.$expected.'/b53763.jpg', UriHelper::addOptionsToUriString('https://test.rokka.io/stackname/'.$inputUrl.'/b53763.jpg', $options, $shortNames));
    }

    public function testGetDynamicStackFromStackObject()
    {
        $stack = new Stack(null, 'dynamic');

        $stack->addStackOperation(new StackOperation('resize', ['width' => 200, 'height' => 200]));
        $stack->addStackOperation(new StackOperation('rotate', ['angle' => 45]));
        $stack->setStackOptions(['jpg.quality' => 80]);
        $stack->setStackVariables(['w' => 300]);
        $stack->addStackOption('webp.quality', 80);
        $this->assertEquals('dynamic/resize-height-200-width-200--rotate-angle-45--o-jpg.quality-80-webp.quality-80--v-w-300', $stack->getDynamicUriString());
    }

    /**
     * @dataProvider provideGetSrcSetUrl
     *
     * @param string      $size
     * @param string|null $custom
     * @param string      $expected
     * @param mixed       $setWidthInUrl
     */
    public function testGetSrcSetUrl($size, $custom, $expected, $setWidthInUrl = true)
    {
        $inputUrl = 'https://test.rokka.io/stackname/b537639e539efcc3df4459ef87c5963aa5079ca6.jpg';
        $this->assertSame($expected, (string) UriHelper::getSrcSetUrlString($inputUrl, $size, $custom, $setWidthInUrl));
    }

    public function testSignUrl() {
        $this->assertEquals("/dynamic/abcdef.jpg?sig=c819798233635f29", (string)UriHelper::signUrl('/dynamic/abcdef.jpg', "abcdef"));
        //without leading slash, should return the same sig
        $this->assertEquals("dynamic/abcdef.jpg?sig=c819798233635f29", (string)UriHelper::signUrl('dynamic/abcdef.jpg', "abcdef"));

        // time limited signature
        $this->assertEquals(
            "dynamic/abcdef.jpg?sigopts=IntcInVudGlsXCI6XCIyMDUwLTAyLTA4VDA4OjA1OjAwKzAwOjAwXCJ9Ig%3D%3D&sig=d438a4ff962e311c",
            (string)UriHelper::signUrl('dynamic/abcdef.jpg', "abcdef", new \DateTime("2050-02-08T08:03:00"))
        );
        // time limited signature a minute later should return the same
        $this->assertEquals(
            "dynamic/abcdef.jpg?sigopts=IntcInVudGlsXCI6XCIyMDUwLTAyLTA4VDA4OjA1OjAwKzAwOjAwXCJ9Ig%3D%3D&sig=d438a4ff962e311c",
            (string)UriHelper::signUrl('dynamic/abcdef.jpg', "abcdef", new \DateTime("2050-02-08T08:04:00"))
        );
        // but 2 minutes later again different
        $this->assertEquals(
            "dynamic/abcdef.jpg?sigopts=IntcInVudGlsXCI6XCIyMDUwLTAyLTA4VDA4OjEwOjAwKzAwOjAwXCJ9Ig%3D%3D&sig=24626a9a07f116a7",
            (string)UriHelper::signUrl('dynamic/abcdef.jpg', "abcdef", new \DateTime("2050-02-08T08:06:00"))
        );

    }


    private function twoRoutesTest(array $hashes, $stack, $option, array $expectedOptions)
    {
        foreach ($hashes as $hash) {
            $this->singleRouteTest($stack.'/'.$option.$hash.'.jpeg', $hash, $expectedOptions, '', $stack);
            $this->singleRouteTest($stack.'/'.$option.$hash.'/5e05d0.jpeg', $hash, $expectedOptions, '5e05d0', $stack);
        }

        return $hash;
    }

    private function singleRouteTest($testUrl, $hash, array $expectedOptions, $filename, $stack)
    {
        $testUrl = 'https://test.rokka.io/'.$testUrl;
        $testUri = new Uri($testUrl);

        try {
            $components = UriHelper::decomposeUri($testUri);
        } catch (\InvalidArgumentException $e) {
            $this->fail('Arguments were not valid for url '.$testUrl.'.'.$e->getMessage());
        }
        /** @var StackUri $cStack */
        $cStack = $components['stack'];
        $expectedStackUri = trim($cStack->getName().'/'.$expectedOptions['optionsUrl'], '/');
        $this->assertEquals($expectedStackUri, $cStack->getStackUriString(), 'getStackUri was not as expected for url '.$testUrl);
        $this->assertEquals($hash, $components['hash'], 'Hash was not expected for url '.$testUrl);
        $this->assertEquals($filename, (string) $components['filename'], 'Filename was not as expected for url '.$testUrl);
        $this->assertEquals('jpeg', $components['format'], 'Format was not as expected for url '.$testUrl);
        $this->assertEquals($expectedOptions['optionsArray'], $cStack->getStackOptions(), 'Options were not as expected for url '.$testUrl);
        //test back with composeUri
        if ($filename) {
            $filename = '/'.$filename;
        }
        if ($expectedOptions['optionsUrl']) {
            $expectedOptions['optionsUrl'] = $expectedOptions['optionsUrl'].'/';
        }
        $this->assertEquals('https://test.rokka.io/'.$stack.'/'.$expectedOptions['optionsUrl'].$hash.$filename.'.jpeg', (string) UriHelper::composeUri($components, $testUri));
    }

}
