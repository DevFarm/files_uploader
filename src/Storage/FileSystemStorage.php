<?php

namespace DevFarm\FilesUploaderBundle\Storage;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use DevFarm\FilesUploaderBundle\Mapping\PropertyMapping;

/**
 * FileSystemStorage.
 *
 */
class FileSystemStorage extends AbstractStorage
{
    protected function doUpload(PropertyMapping $mapping, UploadedFile $file, ?string $dir, string $name)
    {
        $uploadDir = $mapping->getUploadDestination().\DIRECTORY_SEPARATOR.$dir;

        if (false !== \strpos($file->getMimeType(), 'image/') && 'image/svg+xml' !== $file->getMimeType()) {

            $dimensions = $this->parseDimensions($mapping->getDimensionsPropertyName());

            foreach ($dimensions as $dimension) {
                $width = $dimension[0];
                $height = $dimension[1];

                $this->imageResize->createDimension($file, $uploadDir, $name, $width, $height);
            }
        }

        if (!file_exists($uploadDir))
            @mkdir($uploadDir);


        $file = $file->move($uploadDir, $name);

        try {
            $this->imageResize->load($file->getPathname());
            $this->imageResize->save($file->getPathname());
        } catch (\Exception $e) {
            $this->doRemove($mapping, $dir, $name);
            return false;
        }

        return $file;
    }

    protected function doRemove(PropertyMapping $mapping, ?string $dir, string $name): ?bool
    {
        $file = $this->doResolvePath($mapping, $dir, $name);

        $dimensions = $this->parseDimensions($mapping->getDimensionsPropertyName());

        $uploadDir = $mapping->getUploadDestination().\DIRECTORY_SEPARATOR.$dir;

        foreach ($dimensions as $dimension) {
            $width = $dimension[0];
            $height = $dimension[1];
            $dimension = $width.'x'.$height;

            $dimensionFile = $this->imageResize->buildDimensionFile($uploadDir . \DIRECTORY_SEPARATOR, $name, $dimension);
            $path = $dimensionFile->getPathname();
            if (\file_exists($path))
                \unlink($path);
        }

        return \file_exists($file) ? \unlink($file) : false;
    }

    protected function doResolvePath(PropertyMapping $mapping, ?string $dir, string $name, ?bool $relative = false): string
    {
        $path = !empty($dir) ? $dir.\DIRECTORY_SEPARATOR.$name : $name;

        if ($relative) {
            return $path;
        }

        return $mapping->getUploadDestination().\DIRECTORY_SEPARATOR.$path;
    }

    public function resolveUri($obj, string $mappingName, string $className = null, string $dimension = null): ?string
    {
        [$mapping, $name] = $this->getFilename($obj, $mappingName, $className);

        if (empty($name)) {
            return null;
        }

        $uploadDir = $this->convertWindowsDirectorySeparator($mapping->getUploadDir($obj));
        $uploadDir = empty($uploadDir) ? '' : $uploadDir.'/';


        if (null !== $dimension) {
            $dir = $mapping->getUriPrefix() . \DIRECTORY_SEPARATOR . $uploadDir;

            return $this->imageResize->buildDimensionFile($dir, $name, $dimension);
        }

        return \sprintf('%s/%s', $mapping->getUriPrefix(), $uploadDir.$name);
    }

    private function convertWindowsDirectorySeparator(string $string): string
    {
        return \str_replace('\\', '/', $string);
    }

    private function parseDimensions(array $dimensions): array
    {
        return \array_map(function ($dimension) {
            return \explode('x', $dimension);
        }, $dimensions);
    }
}