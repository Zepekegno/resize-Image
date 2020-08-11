# Resize an Image

[![Build Status](https://travis-ci.com/Zepekegno/Resize_Image.svg?branch=master)](https://travis-ci.com/Zepekegno/Resize_Image)
[![Coverage Status](https://coveralls.io/repos/github/Zepekegno/Resize-Image/badge.svg?branch=master)](https://coveralls.io/github/Zepekegno/Resize-Image?branch=master)

 This resize class resize a PNG, JPG, JPEG, GIF image to a desired size using GD driver

```php

//Resize an image
$source = 'image.png';
$resizeImage = new zepekegno\resize_image\Resize($source,150,150);

//if delete is false and file exist no delete it, it will be copy with old filename 
$resizeImage->make('tmp/final',9);

//if delete is true and file exist delete it and create filename
$resizeImage->make('tmp/final',9,true);

//Convert an image to other format 
$source = 'image.png';
$convertImage = new zepekegno\resize_image\Resize($source,150,150);
$convertImage->convert('jpeg','tmp/final',100,false);

//Convert an image to other format and resize this
$source = 'image.png';
$convertImage = new zepekegno\resize_image\Resize($source,150,150);
$img = $convertImage->convert('jpeg','tmp/final',100,true);

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

- $delete **bool*, Optional, true delete the file if exist else it will be copy

The convert method which needs the following parameters

- $type **string**, the format would be convert the image 

- $target **string**, the directory where the file will be store 

- $quality **int**, the quality of the image will be use for png [0-9], jpeg or png [0-100], gif are not need

- $size **bool**, Optional, true convert and resize the image, false convert only the image