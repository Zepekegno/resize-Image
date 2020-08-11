<?php

namespace zepekegno\resize_image;

/**
 * Class Resize.
 */
class Resize
{
    /**
     * height of new file.
     *
     * @var int
     */
    protected $height;

    /**
     * width of new file.
     *
     * @var int
     */
    protected $width;

    /**
     * height of old file.
     *
     * @var int
     */
    protected $oldHeight;

    /**
     * width of new file.
     *
     * @var int
     */
    protected $oldWidth;

    /**
     * file will be resize.
     *
     * @var string
     */
    protected $source;

    /**
     * Resize constructor.
     *
     * @throws \Exception
     */
    public function __construct(string $source, int $height, int $width)
    {
        $this->source = $source;
        $this->height = $height;
        $this->width = $width;
        $this->testSource($source);
    }

    /** Test if the file is an image.
     * @throws \Exception
     */
    public function testSource(string $source): ?\Exception
    {
        $this->testFileExist($source);
        if (false === getimagesize($source)) {
            throw new \Exception(sprintf('This file can\'t  be read %s', $source));
        }

        $file = getimagesize($source);
        $mimeType = mb_substr($file['mime'], 0, 6);
        if ('image/' === !$mimeType) {
            throw new \Exception(sprintf('This file must be an image %s', $source));
        }
        list($this->oldWidth, $this->oldHeight) = $file;

        return null;
    }

    /** Resize an image.
     * @throws ResizeQualityException
     */
    public function make(string $target, int $quality, bool $delete = false): ?string
    {
        $this->testQuality($this->source, $quality);

        $extension = pathinfo($this->source, PATHINFO_EXTENSION);
        $basename = pathinfo($this->source, PATHINFO_BASENAME);

        if (!file_exists($target)) {
            mkdir($target, 0777, true);
        }

        $target .= '/' . $basename;

        if (file_exists($target)) {
            if ($delete) {
                unlink($target);
            } else {
                copy($target, $target);
            }
        }
        $bool = $this->switch($extension, $this->source, $target, $quality, $this->width, $this->height);
        if (!$bool) {
            new MakeException(printf("This file %s extension {%s} is'nt supported ", $this->source, $extension));
        }

        return $target;
    }

    /** Convert an image to other format or convert and resize to other format.
     * @throws ResizeQualityException
     */
    public function convert(string $type, string $target, int $quality, bool $size = false): string
    {
        $extension = pathinfo($this->source, PATHINFO_EXTENSION);
        $filename = pathinfo($this->source, PATHINFO_FILENAME);

        if (!file_exists($target)) {
            mkdir($target, 0777, true);
        }

        $file = $target . '/' . $filename . '.' . $type;

        if (file_exists($file)) {
            $target .= '/copy_' . $filename . '.' . $type;
        } else {
            $target .= '/' . $filename . '.' . $type;
        }
        if ('jpg' === mb_strtolower($extension)) {
            $createImage = 'imagecreatefromjpeg';
        } else {
            $createImage = 'imagecreatefrom' . $extension;
        }

        $src = $createImage($this->source);

        $dest = imagecreatetruecolor($this->oldWidth, $this->oldHeight);
        imagecopy($dest, $src, 0, 0, 0, 0, $this->oldWidth, $this->oldHeight);
        if ('jpg' === mb_strtolower($type) || 'jpeg' === mb_strtolower($type)) {
            $image = $image = 'imagejpeg';
        } else {
            $image = 'image' . $type;
        }

        if ('imagepng' === $image) {
            $fileQuality = 9;
        }
        if ('imagejpeg' === $image) {
            $fileQuality = 100;
        }

        $image($dest, $target, $fileQuality);

        // if size equal true resize an image with new extension

        if ($size) {
            $dirname = pathinfo($target, PATHINFO_DIRNAME);
            $target = (new self($target, $this->width, $this->height))->make($dirname, $quality);
        }

        return $target;
    }

    /** Create a resource png for an image.
     * @return false|resource
     */
    private function createPng(string $sources, int $width, int $height)
    {
        $source = imagecreatefrompng($sources);

        $final = imagecreatetruecolor($width, $height);

        imagecopyresampled(
            $final,
            $source,
            0,
            0,
            0,
            0,
            $this->width,
            $this->height,
            $this->oldWidth,
            $this->oldHeight
        );

        return $final;
    }

    /** Create a resource jpeg|jpg for an image.
     * @return false|resource
     */
    private function createJpeg(string $sources, int $width, int $height)
    {
        $source = imagecreatefromjpeg($sources);

        $final = imagecreatetruecolor($width, $height);

        imagecopyresampled(
            $final,
            $source,
            0,
            0,
            0,
            0,
            $this->width,
            $this->height,
            $this->oldWidth,
            $this->oldHeight
        );

        return $final;
    }

    /** Create a resource gif for an image.
     * @return false|resource
     */
    private function createGif(string $sources, int $width, int $height)
    {
        $source = imagecreatefromgif($sources);

        $final = imagecreatetruecolor($width, $height);

        imagecopyresampled(
            $final,
            $source,
            0,
            0,
            0,
            0,
            $this->width,
            $this->height,
            $this->oldWidth,
            $this->oldHeight
        );

        return $final;
    }

    /**
     * Verify if file exists.
     *
     * @throws \Exception
     */
    private function testFileExist(string $file)
    {
        if (!file_exists($file)) {
            throw new \Exception(printf('No such file or directory : %s', $file));
            exit();
        }
    }

    /** Create and stored the image in the target.
     * @return bool
     */
    private function switch(string $extension, string $source, string $target, int $quality, int $width, int $height)
    {
        switch ($extension) {
            case 'png':
                $img = $this->createPng($source, $width, $height);
                imagepng($img, $target, $quality);

                return true;
                break;
            case 'jpeg':
                $img = $this->createJpeg($source, $width, $height);
                imagejpeg($img, $target, $quality);

                return true;
                break;
            case 'gif':
                $img = $this->createGif($source, $width, $height);
                imagegif($img, $target);

                return true;
                break;
            default:
                $img = $this->createJpeg($source, $width, $height);
                imagejpeg($img, $target, $quality);

                return true;
        }
    }

    /** Test the quality compression level png[0-9], jpeg|jpg[0-100].
     * @throws ResizeQualityException
     */
    private function testQuality(string $sources, int $quality)
    {
        $extension = mb_strtolower(pathinfo($sources, PATHINFO_EXTENSION));
        $compressPng = range(0, 9, 1);
        $compressJpeg = range(0, 100, 1);
        if ('png' === $extension) {
            if (!\in_array($quality, $compressPng, true)) {
                throw new ResizeQualityException(printf('compression level for png must be 0 through 9', $extension));
            }
        }

        if ('jpeg' === $extension
            || 'jpg' === $extension) {
            if (!\in_array($quality, $compressJpeg, true)) {
                throw new ResizeQualityException(printf('compression level for png must be 0 through 100', $extension));
            }
        }
    }
}
