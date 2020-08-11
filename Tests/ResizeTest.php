<?php

namespace zepekegno\tests;

use zepekegno\resize_image\Resize;

class ResizeTest extends \PHPUnit\Framework\TestCase
{
    // Test if the file is  an image
    public function testSourceIfImage()
    {
        $source = './tmp/logique.pdf';
        $this->expectException(\Exception::class);
        new Resize($source, 150, 150);
    }

    // Test resize image
    public function testResizeImage()
    {
        $source = './tmp/twiter.png';
        $image = new Resize($source, 150, 150);
        $target = './tmp/final';
        $file = $image->make($target, 9);
        list($w, $h) = getimagesize($file);
        $this->assertSame([150, 150], [$w, $h]);
    }

    // Test quality if it interval required
    public function testResizeImageQualityFailWithException()
    {
        $source1 = './tmp/fmi.png';
        $source2 = './tmp/iphone.jpg';
        $image1 = new Resize($source1, 150, 150);
        $image2 = new Resize($source2, 220, 150);
        $target = './tmp/final';
        $this->expectException(\Exception::class);
        $image1->make($target, 15);
        $this->expectException(\Exception::class);
        $image2->make($target, 200);
    }

    // Test for conversion the image to other format
    public function testConvert()
    {
        $source1 = './tmp/fmi.png';
        $source2 = './tmp/diapo2.jpg';
        $image1 = new Resize($source1, 150, 150);
        $image2 = new Resize($source2, 150, 150);
        $convert = $image1->convert('jpeg', './tmp/final', 100);
        $convert1 = $image2->convert('png', './tmp/final', 9);
        $this->assertContains($convert, ['./tmp/final/fmi.jpeg', './tmp/final/copy_fmi.jpeg']);
        $this->assertContains($convert1, ['./tmp/final/diapo2.png', './tmp/final/copy_diapo2.png']);
    }

    //Test for verify file exit, if is true it will add a suffix copy_ to filename
    public function testConvertWithSuffixCopyImage()
    {
        $source1 = './tmp/twiter.png';
        $image1 = new Resize($source1, 150, 150);
        $convert = $image1->convert('jpeg', './tmp/final', 100);
        $this->assertFileExists($convert);
    }

    // test for convert image and resize it
    public function testConvertWithNewWidthAnHeight()
    {
        $source1 = './Tmp/twiter.png';
        $image1 = new Resize($source1, 150, 150);
        $convert = $image1->convert('jpeg', './tmp/final', 100, true);
        list($w, $h) = getimagesize($convert);
        $this->assertSame([150, 150], [$w, $h]);
    }
}
