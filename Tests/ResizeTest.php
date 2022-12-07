<?php

namespace Zepekegno\Tests;

use Exception;
use Zepekegno\Resize;
use Zepekegno\ResizeQualityException;

class ResizeTest extends \PHPUnit\Framework\TestCase
{
    // Test if the file is  an image
    public function testSourceIfImage()
    {
        $source = './tmp/logique.pdf';
        $this->expectException(Exception::class);
        new Resize($source, 50, 50);
    }


    // Test resize image
    public function testResizeImage()
    {
        $source = './tmp/twiter.png';
        $image = new Resize($source, 50, 50);
        $target = './tmp/final';
        $image->make($target, 9);
        list($w, $h) = getimagesize($target . '/twiter.png');
        $this->assertEquals([50, 50], [$w, $h]);
    }

    public function testResizeImageAndDeleteIfFIleExist()
    {
        $source = './tmp/twiter.png';
        $image = new Resize($source, 50, 50);
        $target = './tmp/final';
        $image->make($target, 9,true);
        list($w, $h) = getimagesize($target . '/twiter.png');
        $this->assertEquals([50, 50], [$w, $h]);
    }


    // Test resize image
    public function testResizeImageGif()
    {
        $source = './tmp/1010.gif';
        $image = new Resize($source, 50, 50);
        $target = './tmp/final';
        $image->make($target);
        list($w, $h) = getimagesize($target . '/1010.gif');
        $this->assertEquals([50, 50], [$w, $h]);
    }

    // Test quality if it interval required
    public function testPngQualityFailWithException()
    {
        $source1 = './tmp/fmi.png';
        $image1 = new Resize($source1, 50, 50);
        $target = './tmp/final';
        $this->expectException(ResizeQualityException::class);
        $image1->make($target, 15);
    }

    // Test quality if it interval required
    public function testJpegQualityFailWithException()
    {
        $source2 = './tmp/iphone.jpg';
        $image2 = new Resize($source2, 50, 50);
        $target = './tmp/final';
        $this->expectException(ResizeQualityException::class);
        $image2->make($target, 200);
    }

    public function testConvertAndDeleteOldFileAndCreateNew()
    {
        $source = './tmp/fmi.png';
        $image = new Resize($source, 50, 50);
        $convert = $image->convert(type:'jpeg', target:'./tmp/final',quality: 100,old:true);
        $this->assertFileEquals($convert, './tmp/final/fmi.jpeg',"The old file is deleted and new would created");
    }

    public function testConvertAndNotDeleteOldFile()
    {
        $source = './tmp/fmi.png';
        $image = new Resize($source, 50, 50);
        $convert = $image->convert(type:'jpeg', target:'./tmp/final',quality: 100);
        $this->assertFileEquals($convert, './tmp/final/fmi.jpeg');
    }

    public function testConvertSaveToAnotherNameAndNotDelete()
    {
        $source = './tmp/fmi.png';
        $image = new Resize($source, 50, 50);
        $convert = $image->convert(type:'jpeg', target:'./tmp/final',quality: 100,name:'my-file');
        $this->assertFileEquals($convert, './tmp/final/fmi.jpeg');
    }

    // Test for conversion the image to other format
    public function testConvertPngToJpeg()
    {
        $source = './tmp/fmi.png';
        $image = new Resize($source, 50, 50);
        $convert = $image->convert(type:'jpeg', target:'./tmp/final',quality: 100);
        $this->assertFileEquals($convert, './tmp/final/fmi.jpeg');
    }

    // Test for conversion the image to other format
    public function testConvertPngToJpg()
    {
        $source = './tmp/fmi.png';
        $image = new Resize($source, 50, 50);
        $convert = $image->convert('jpg', './tmp/final', 100);
        $this->assertFileExists($convert);
    }

    public function testConvertJpgToPng()
    {
        $source = './tmp/diapo2.jpg';
        $image = new Resize($source, 50, 50);
        $convert = $image->convert('png', './tmp/final', 9);
        $this->assertFileExists($convert);
    }

    public function testConvertJpgToJpeg()
    {
        $source = './tmp/diapo2.jpg';
        $image = new Resize($source, 50, 50);
        $convert = $image->convert('jpeg', './tmp/final', 9);
        $this->assertFileExists($convert);
    }

    //Test for verify file exit, if is true it will add a suffix copy_ to filename
    public function testConvertWithSuffixCp()
    {
        $source = './tmp/twiter.png';
        $image = new Resize($source, 50, 50);
        $convert = $image->convert('jpeg', './tmp/final', 100);
        $this->assertFileExists($convert);
    }

    public function testConvertAndResize(){
        $source = './tmp/t.jpg';
        $image = new Resize($source, 50, 50);
        $convert = $image->convert(type:'jpeg', target: './tmp/final', quality: 100, isResizable: true);
        $this->assertFileExists($convert);
    }
}