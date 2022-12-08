<?php

namespace Zepekegno\Tests;

use DirectoryIterator;
use Exception;
use PHPUnit\Framework\TestCase;
use Zepekegno\Resize;
use Zepekegno\ResizeQualityException;

class ResizeTest extends TestCase
{
    
  public static function setUpBeforeClass(): void
  {

        self::removeDir(__DIR__ . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'final');
       
  }

  private static function removeDir($path){

    if(file_exists($path)){
        $dir = $dir = new DirectoryIterator($path);
        foreach ($dir as $fileinfo) {
            if ($fileinfo->isFile() || $fileinfo->isLink()) {
                unlink($fileinfo->getPathName());
            }
        }
    }
  }
   
    private function getFile(string $file){
       return __DIR__.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.$file;
    }

    // Test if the file is  an image
    public function testSourceIfImage()
    {
        $source = $this->getFile('logique.pdf');
        $this->expectException(Exception::class);
        new Resize($source, 50, 50);
    }


    // Test resize image
    public function testResizeImage()
    {
        $source = $this->getFile('twiter.png');
        $image = new Resize($source, 50, 50);
        $target = $this->getFile('final');
        $image->make($target, 9);
        list($w, $h) = getimagesize($target . '/twiter.png');
        $this->assertEquals([50, 50], [$w, $h]);
    }

    public function testResizeImageAndDeleteIfFIleExist()
    {
        $source = $this->getFile('twiter.png');
        $image = new Resize($source, 50, 50);
        $target = $this->getFile('final');
        $image->make($target, 9,true);
        list($w, $h) = getimagesize($target . '/twiter.png');
        $this->assertEquals([50, 50], [$w, $h]);
    }


    // Test resize image
    public function testResizeImageGif()
    {
        $source = $this->getFile('1010.gif');
        $image = new Resize($source, 50, 50);
        $target = $this->getFile('final');
        $image->make($target);
        list($w, $h) = getimagesize($target . '/1010.gif');
        $this->assertEquals([50, 50], [$w, $h]);
    }

    // Test quality if it interval required
    public function testPngQualityFailWithException()
    {
        $source1 = $this->getFile('fmi.png');
        $image1 = new Resize($source1, 50, 50);
        $target = $this->getFile('final');
        $this->expectException(ResizeQualityException::class);
        $image1->make($target, 15);
    }

    // Test quality if it interval required
    public function testJpegQualityFailWithException()
    {
        $source2 = $this->getFile('iphone.jpg');
        $image2 = new Resize($source2, 50, 50);
        $target = $this->getFile('final');
        $this->expectException(ResizeQualityException::class);
        $image2->make($target, 200);
    }

    public function testConvertAndDeleteOldFileAndCreateNew()
    {
        $source =$this->getFile('fmi.png');
        $image = new Resize($source, 50, 50);
        $target = $this->getFile('final');
        $convert = $image->convert(type:'jpeg', target: $target,quality: 100,old:true);
        $this->assertFileEquals($convert, $target.'/fmi.jpeg');
    }

    public function testConvertAndNotDeleteOldFile()
    {
        $source = $this->getFile('fmi.png');
        $image = new Resize($source, 50, 50);
        $target = $this->getFile('final');
        $convert = $image->convert(type:'jpeg', target:$target,quality: 100);
        $this->assertFileEquals($convert, $target.'/fmi.jpeg');
    }

    public function testConvertSaveToAnotherNameAndNotDelete()
    {
        $source = $this->getFile('fmi.png');
        $image = new Resize($source, 50, 50);
        $target = $this->getFile('final');
        $convert = $image->convert(type:'jpeg', target:$target,quality: 100,name:'my-file');
        $this->assertFileEquals($convert, $target.'/fmi.jpeg');
    }

    // Test for conversion the image to other format
    public function testConvertPngToJpeg()
    {
        $source = $this->getFile('fmi.png');
        $image = new Resize($source, 50, 50);
        $target = $this->getFile('final');
        $convert = $image->convert(type:'jpeg', target:$target,quality: 100);
        $this->assertFileEquals($convert, $target.'/fmi.jpeg');
    }

    // Test for conversion the image to other format
    public function testConvertPngToJpg()
    {
        $source = $this->getFile('fmi.png');
        $image = new Resize($source, 50, 50);
        $target = $this->getFile('final');
        $convert = $image->convert(type:'jpg', target:$target, quality: 100);
        $this->assertFileExists($convert);
    }

    public function testConvertJpgToPng()
    {
        $source = $this->getFile('diapo2.jpg');
        $image = new Resize($source, 50, 50);
        $target = $this->getFile('final');
        $convert = $image->convert(type:'png', target:$target, quality: 9);
        $this->assertFileExists($convert);
    }

    public function testConvertJpgToJpeg()
    {
        $source = $this->getFile('diapo2.jpg');
        $image = new Resize($source, 50, 50);
        $target = $this->getFile('final');
        $convert = $image->convert(type:'jpeg', target:$target,quality: 9);
        $this->assertFileExists($convert);
    }

    //Test for verify file exit, if is true it will add a suffix copy_ to filename
    public function testConvertWithSuffixCp()
    {
        $source = $this->getFile('twiter.png');
        $image = new Resize($source, 50, 50);
        $target = $this->getFile('final');
        $convert = $image->convert(type:'jpeg', target:$target,quality: 100);
        $this->assertFileExists($convert);
    }

    public function testConvertAndResize(){
        $source = $this->getFile('t.jpg');
        $image = new Resize($source, 50, 50);
        $target = $this->getFile('final');
        $convert = $image->convert(type:'jpeg', target: $target, quality: 100, isResizable: true);
        $this->assertFileExists($convert);
    }
}