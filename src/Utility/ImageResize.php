<?php

namespace DevFarm\FilesUploaderBundle\Utility;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageResize
{
    private $_filePath = NULL;
    private $_imagick = NULL;
    private $_im = NULL;
    private $_library = NULL;
    private $_size = NULL;
    private $_imageType = NULL;
    private $_thumb = NULL;

    /**
     * ImageResize constructor.
     * @throws \ImagickException
     */
    public function __construct()
    {
        if (extension_loaded('imagick')) {
            $this->_imagick = new \Imagick();
            $this->_library = 'imagick';
        } else {
            $this->_library = 'gd';
        }
    }

    /**
     * @param $source
     * @return $this
     * @throws \ImagickException
     */
    public function load($source)
    {
        $this->_filePath = $source;

        if ($this->_library == 'imagick') {
            $this->_imagick->readImage($this->_filePath);
            $this->_size = $this->_imagick->getImageGeometry();
        } else {
            $this->_size = getimagesize($source);

            switch ($this->_size['mime']) {
                case 'image/jpeg':
                    $this->_im = imagecreatefromjpeg($source);
                    $this->_imageType = 'jpg';
                    break;

                case 'image/gif':
                    $this->_im = imagecreatefromgif($source);
                    $this->_imageType = 'gif';
                    break;

                case 'image/png':
                    $this->_im = imagecreatefrompng($source);
                    $this->_imageType = 'png';
                    break;

                default:
                    $this->_im = false;
                    break;
            }
        }

        return $this;
    }

    /**
     * @param null $width integer
     * @param null $height integer
     *
     * @return $this ImageResize
     */
    public function resize($width = NULL, $height = NULL)
    {
        if (!$width && !$height) {
            return $this;
        }

        if ($this->_library == 'imagick') {
            $kw = $width ? $this->_size['width'] / $width : 0;
            $kh = $height ? $this->_size['height'] / $height : 0;

            $kw = $kw > 0 ? $kw : 0;
            $kh = $kh > 0 ? $kh : 0;

            if ($kh || $kw) {
                if ($kh > $kw) {
                    $this->_imagick->thumbnailImage(0, $height);
                } else {
                    $this->_imagick->thumbnailImage($width, 0);
                }
            }
        } else {
            if (!$this->_im) {
                return $this;
            }

            $kw = $width ? $this->_size[0] / $width : 0;
            $kh = $height ? $this->_size[1] / $height : 0;

            $kw = $kw > 0 ? $kw : 0;
            $kh = $kh > 0 ? $kh : 0;

            if ($kh || $kw) {
                $k = $kh > $kw ? $kh : $kw;

                $new_width = $this->_size[0] / $k;
                $new_height = $this->_size[1] / $k;
            } else {
                $new_width = $this->_size[0];
                $new_height = $this->_size[1];
            }

            $this->_thumb = imagecreatetruecolor($new_width, $new_height);
            imagecopyresized($this->_thumb, $this->_im, 0, 0, 0, 0, $new_width, $new_height, $this->_size[0], $this->_size[1]);
            imagedestroy($this->_im);
        }

        return $this;
    }

    /**
     * @param null $width integer
     *
     * @return $this ImageResize
     */
    public function resizeSquare($width)
    {
        if ($this->_library == 'imagick') {
            $kw = $this->_size['width'] / $width;
            $kh = $this->_size['height'] / $width;

            if ($kh || $kw) {
                if ($kh < $kw) {
                    $this->_imagick->thumbnailImage(0, $width);
                } else {
                    $this->_imagick->thumbnailImage($width, 0);
                }

                $size_im = $this->_imagick->getImageGeometry();
                if ($size_im['width'] > $width) {
                    $this->_imagick->cropImage($width, $width, floor(($size_im['width'] - $width) / 2), 0);
                }

                if ($size_im['height'] > $width) {
                    $this->_imagick->cropImage($width, $width, floor(($size_im['width'] - $width) / 2), 0);
                }
            }
        } else {
            if (!$this->_im) {
                return $this;
            }

            $kw = $this->_size[0] / $width;
            $kh = $this->_size[1] / $width;

            if ($kh || $kw) {
                $k = $kh > $kw ? $kh : $kw;

                $new_width = $this->_size[0] / $k;
                $new_height = $this->_size[1] / $k;
            } else {
                $new_width = $this->_size[0];
                $new_height = $this->_size[1];
            }

            $this->_thumb = imagecreatetruecolor($new_width, $new_height);
            imagecopyresized($this->_thumb, $this->_im, 0, 0, 0, 0, $new_width, $new_height, $this->_size[0], $this->_size[1]);
            imagedestroy($this->_im);
        }

        return $this;
    }

    /**
     * @param $destination string
     *
     * @return $this ImageResize
     */
    public function save($destination)
    {
        if ($this->_library == 'imagick') {
            $this->_imagick->writeImage($destination);
        } else {
            switch ($this->_imageType) {
                case 'jpg':
                    imagejpeg($this->_thumb, $destination, 80);
                    break;

                case 'gif':
                    imagegif($this->_thumb, $destination);
                    break;

                case 'png':
                    imagepng($this->_thumb, $destination);
                    break;

                default:
                    $this->_im = false;
                    break;
            }
        }

        return $this;
    }

    /**
     * @param UploadedFile $file
     * @param null|string $uploadDir
     * @param string $name
     * @param int $width
     * @param int $height
     * @throws \ImagickException
     */
    public function createDimension(UploadedFile $file, ?string $uploadDir, string $name, int $width, int $height): void
    {
        $image = $this->load($file->getPathname());

        if ($width == $height)
            $image->resizeSquare($width);
        else
            $image->resize($width, $height);

        $dimension = $width . 'x' . $height;

        $dimensionFile = $this->buildDimensionFile($uploadDir . \DIRECTORY_SEPARATOR, $name, $dimension);
        $path = $dimensionFile->getPathname();

        if (!file_exists($uploadDir))
            @mkdir($uploadDir);

        $this->save($path);
    }

    /**
     * @param null|string $dir
     * @param string $name
     * @param string $dimension
     * @param bool $check
     * @return File
     */
    public function buildDimensionFile(?string $dir, string $name, string $dimension, bool $check = false): File
    {
        $pathInfo = \pathinfo($name);

        return new File($dir . $pathInfo['filename'] . "_$dimension." . $pathInfo['extension'], $check);
    }

    public function buildImagePathByEntityAndType($entity, $type, $extension)
    {
        if (!$entity || !$type || !$extension) return null;

        $path = DIRECTORY_SEPARATOR . 'image' . DIRECTORY_SEPARATOR;
        $id = $entity->getId();
        $entityName = implode(array_slice(explode('\\', get_class($entity)), -1));
        $path .= $entityName . DIRECTORY_SEPARATOR . 'entity' . DIRECTORY_SEPARATOR . $id . '_' . $type . '.' . $extension;

        return $path;
    }

    public function __call($name, $args)
    {
        return call_user_func_array([$this->_imagick, $name], $args);
    }
}