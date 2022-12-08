<?php

namespace Zepekegno;
use \Exception;
use \GdImage;
use Zepekegno\Exception\ReadableException;
use Zepekegno\Exception\TypeException;
use Zepekegno\ResizeInterface;
use Zepekegno\ResizeQualityException;

/**
 * Class Resize.
 * @author Moussa Traoré <moussatraore158@gmail.com>
 * @copyright 2022 zepekegno224
 */
class Resize implements ResizeInterface
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
     * Path of final image
     * @var null|string
     */
    protected string $file;

   
    /**
     * @param string $source
     * @param int|null $height=0
     * @param int|null $width=0
     * @throws null|Exception|TypeException
     */
    public function __construct(string $source, ?int $height=0, ?int $width=0)
    {
        $this->source = $source;
        $this->height = $height;
        $this->width = $width;
        $this->testFileExist($source);
        $this->testSource($source);
    }

    /** Test if the file is an image.
     * @param string source
     * @throws null|Exception|TypeException
     */
    public function testSource(string $source)
    {
        if (false === getimagesize($source)) {
            throw new ReadableException(sprintf('An error occur'));
        }

        $file = getimagesize($source);
        $mimeType = substr($file['mime'], 0, 6);
        if ('image/' === !$mimeType) {
            throw new TypeException(sprintf('This file must be an image %s', $source));
        }
        list($this->oldWidth, $this->oldHeight) = $file;
    }

    
    /**
     * Apply resizing of an image
     * @param string $target
     * @param mixed $quality
     * @param bool $delete
     * @throws ResizeQualityException
     * @return string|null
     */
    public function make(string $target, mixed $quality = -1, $delete=false): ?string
    {
       if($quality != -1) $this->testQuality($this->source, $quality);

        $extension = pathinfo($this->source, PATHINFO_EXTENSION);
        $basename = pathinfo($this->source, PATHINFO_BASENAME);

        if (!file_exists($target)) {
            mkdir($target, 0777, true);
        }

        
        $file = $target.'/' . $basename;

        if (file_exists($file) && !$delete) {
            $file = $target .DIRECTORY_SEPARATOR .'cpr-'.uniqid().'-'. $basename;
        }

        if(file_exists($file) && $delete){
            unlink($file);
        }

        $this->matchType($extension, $this->source, $file, $this->width, $this->height,$quality);

        return $file;
      
    }

    
    /** Convert an image to other format or 
     * convert and resize to other format.
     * @throws ResizeQualityException
     * @param string $type
     * @param string $target
     * @param int|null $quality
     * @param bool $isResizable
     * @param string|null $name
     * @param bool $old
     * 
     * @return string|null
     */
    public function convert(string $type, string $target, ?int $quality = null ,bool $isResizable = false,?string $name = null,bool $old = false): ?string
    {
        $extension = pathinfo($this->source, PATHINFO_EXTENSION);
        $filename = pathinfo($this->source, PATHINFO_FILENAME);

        if ($extension == 'gif')
            throw new TypeException('Sorry convert for Gif not supported');

        if (!file_exists($target)) {
            mkdir($target, 0777, true);
        }

        $file = $target . '/' . $filename . '.' . $type;

        $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename.'.' .$type;

        if (file_exists($file) && $old) {
            copy($file, $tmp);
            unlink($file);
        } 

        if (file_exists($file) && !$old) {
            copy($file, $tmp);
            $file =  $target . DIRECTORY_SEPARATOR.'cp-'.$filename . '.' . $type;
            $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR.'cp-'.$filename.'.' .$type;
        } 

        if (file_exists($file) && !$old && $name != null) {
            copy($file, $tmp);
            $file =  $target . '/' . $name. '.' . $type;
            $tmp = sys_get_temp_dir(). DIRECTORY_SEPARATOR.$name.'.'.$type;
        } 
        
        $createImage =  ('jpg' === strtolower($extension)) ?'imagecreatefromjpeg':'imagecreatefrom' . $extension;

        $src = $createImage($this->source);

        $dest = imagecreatetruecolor($this->oldWidth, $this->oldHeight);

        imagecopy($dest, $src, 0, 0, 0, 0, $this->oldWidth, $this->oldHeight);

        $image = ('jpg' === strtolower($type) || 'jpeg' === strtolower($type)) ? 'imagejpeg' : 'image' . $type;

        $fileQuality =  ('imagepng' === $image) ? $fileQuality = 9 :
                        $fileQuality = 100;

        $image($dest, $tmp, $fileQuality);

        

        if($isResizable){
            $this->source = $tmp;
            $file = $this->make($target, $quality);
            return $this->file =  $file;
        }

        copy($tmp, $file);
        unlink($tmp);
        return $this->file =  $file;
    }

  
    /**
     * @param string $file
     * @throws Exception
     */
    private function testFileExist(string $file)
    {
        if (!file_exists($file)) {
            throw new Exception(printf('No such file or directory : %s', $file));
        }
    }

    /**
     * @param string $extension
     * @param string $source
     * @param string $target
     * @param int $width
     * @param int $height
     * @param int|null $quality
     * @throws TypeException
     * 
     * @return void
     */
    public function matchType(string $extension, string $source,string $target, int $width, int $height, int $quality=-1) : void{

        match($extension){
            'png'=> $this->png($source,$target,$width,$height,$quality),
            'gif'=> $this->gif($source,$target,$width,$height),
            'jpeg'=>  $this->jpeg($source,$target,$width,$height,$quality),
            'jpg'=>  $this->jpeg($source,$target,$width,$height,$quality),
            'default'=>
                throw new TypeException(printf("This file %s extension {%s} is'nt supported ", 
                                        $this->source, $extension))

        };
    }

    /**
     * @param string $sources
     * @param string $target
     * @param int $width
     * @param int $height
     * @param int $quality
     * 
     * @return void
     */
    public function png(string $sources, string $target, int $width, int $height, int $quality):void{
        $source = imagecreatefrompng($sources);

        $final = imagecreatetruecolor($width, $height);

        $this->imageSampled($final,$source);

         imagepng($final, $target, $quality);

        $this->file = $target;
    }

    /**
     * @param string $sources
     * @param string $target
     * @param int $width
     * @param int $height
     * 
     * @return void
     */
    public function gif(string $sources, string $target, int $width, int $height):void{
        $source = imagecreatefromgif($sources);

        $final = imagecreatetruecolor($width, $height);

        $this->imageSampled($final,$source);

        imagegif($final, $target);

        $this->file = $target;
    }

   
   
    /**
     * @param string $sources
     * @param string $target
     * @param int $width
     * @param int $height
     * @param int $quality
     * 
     * @return void
     */
    protected function jpeg(string $sources, string $target, int $width, int $height, int $quality): void{

        $source = imagecreatefromjpeg($sources);

        $final = imagecreatetruecolor($width, $height);

        $this->imageSampled($final,$source);

        imagejpeg($final, $target, $quality);
        
        $this->file = $target;
    }

    /**
     * @param mixed $final
     * @param mixed $source
     * 
     * @return bool
     */
    private function imageSampled($final,$source) :bool{
        return imagecopyresampled(
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
    }

 
    /** 
     * Test the quality compression level png[0-9], jpeg|jpg[0-100].
     * @throws ResizeQualityException
     * 
     * @param string $sources
     * @param int $quality
     * 
     * @return void
     */
    private function testQuality(string $sources, int $quality):void
    {
        $extension = strtolower(pathinfo($sources, PATHINFO_EXTENSION));
        $compressPng = range(1, 9, 1);
        $compressJpeg = range(1, 100, 1);
    
        if (('png' === $extension) && !in_array($quality,$compressPng) ) {
            throw new ResizeQualityException(printf('compression level for %s must be 0 through 9', $extension));
        }

        if (('jpeg' === $extension
            || 'jpg' === $extension) && !in_array($quality, $compressJpeg)) {
                throw new ResizeQualityException(printf('compression level for %s must be 0 through 100', $extension));
        }
    }
}
