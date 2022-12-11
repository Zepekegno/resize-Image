# Resize an Image
![example branch parameter](https://github.com/zepekegno224/resize-image/actions/workflows/ci.yml/badge.svg?branch=master)
[![codecov](https://codecov.io/gh/zepekegno224/resize-image/branch/master/graph/badge.svg?token=5YUQBHCHSU)](https://codecov.io/gh/zepekegno224/resize-image)
[![Coverage Status](https://coveralls.io/repos/github/zepekegno224/resize-image/badge.svg?branch=master)](https://coveralls.io/github/zepekegno224/resize-image?branch=master)
[![stable](http://badges.github.io/stability-badges/dist/stable.svg)](http://github.com/badges/stability-badges)
![GitHub all releases](https://img.shields.io/github/downloads/zepekegno224/resize-image/total?style=social)

 This Library will help you to convert easily and image into other format. this library use GD driver.
 PHP version supported *8
 What we can do with this library ?
 - We can convert an image into another format.
 - We can resize an image to another format.
 - We can convert and resize an image to another format simultaneously.

 Format supported for resizing : PNG, JPEG, JPG, GIF
 Format supported for conversion : PNG, JPEG, JPG

```php

//Resize an image

// Example with png
$source = 'image.png';

$resizeImage = new zepekegno\Resize($source,50,50);


/**
 * If file exist a copy of this file will be created with suffix cpr
*/
$resizeImage->make(target:'tmp/final',quality:9);

//if delete is true will delete this file and create a new file
$resizeImage->make(target:'tmp/final',quality:9,delete:true);

// Example with Gif

$source = 'image.gif';

$resizeImage = new zepekegno\Resize($source,50,50);

/**
 * If file exist a copy of this file will be created with suffix cpr
*/
$resizeImage->make('tmp/final');

//if delete is true will delete this file and create a new file
$resizeImage->make(target:'tmp/final',delete:true);

//Convert an image to other format 
/**
 * convert image png to jpeg
 * Return the path of image
*/
$source = 'image.png';
$convertImage = new zepekegno\Resize(source:$source,height:50,width:50);
$convertImage->convert(type:'jpeg',target:'tmp/final',quality:100,isResizable:false);

/**
 * Convert png to jpeg and resize it
 * if we want to resize, past isResizable to true
 * Return the path of image
*/
$source = 'image.png';
$convertImage = new zepekegno\Resize(source:$source,height:50,width:50);
$img = $convertImage->convert(type:'jpeg',target:'tmp/final',quality:100,isResizable:true);

// Generate image
$image = "<img src=\"{$img}\"/>";


```

ResizeImage are constructed with this parameters

- $source **string**, the filename 

- $width **int**, the new width of the file

- $height **int**, the new height of the file


The make method which needs the following parameters

- $target **string**, the directory where the file will be store 

- $quality **int**, the quality of the image will be use for png [0-9], jpeg or png [0-100], gif are not need

- $delete **bool*, Optional, true delete the file if exist else it will be copied into a new file prepend with **cpr*

The convert method which needs the following parameters

- $type **string**, the format would be convert the image 

- $target **string**, the directory where the file will be store 

- $quality **int**, the quality of the image will be use for png [0-9], jpeg or png [0-100], gif are not need

- $isResizable **bool**, Optional, true convert and resize the image, false convert only the image

The support conversion for Gif is'nt available 
