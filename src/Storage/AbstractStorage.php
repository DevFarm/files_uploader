<?php

namespace DevFarm\FilesUploaderBundle\Storage;

use DevFarm\FilesUploaderBundle\Utility\ImageResize;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use DevFarm\FilesUploaderBundle\Exception\MappingNotFoundException;
use DevFarm\FilesUploaderBundle\Mapping\PropertyMapping;
use DevFarm\FilesUploaderBundle\Mapping\PropertyMappingFactory;

/**
 * FileSystemStorage.
 *
 */
abstract class AbstractStorage implements StorageInterface
{
    /**
     * @var PropertyMappingFactory
     */
    protected $factory;

    protected $imageResize;

    public function __construct(PropertyMappingFactory $factory, ImageResize $imageResize)
    {
        $this->factory = $factory;
        $this->imageResize = $imageResize;
    }

    abstract protected function doUpload(PropertyMapping $mapping, UploadedFile $file, ?string $dir, string $name);

    public function upload($obj, PropertyMapping $mapping): void
    {
        $file = $mapping->getFile($obj);

        if (null === $file || !($file instanceof UploadedFile)) {
            throw new \LogicException('No uploadable file found');
        }

        $name = $mapping->getUploadName($obj);
        $mapping->setFileName($obj, $name);

        $mapping->writeProperty($obj, 'size', $file->getSize());
        $mapping->writeProperty($obj, 'mimeType', $file->getMimeType());
        $mapping->writeProperty($obj, 'originalName', $file->getClientOriginalName());

        $dir = $mapping->getUploadDir($obj);

        $this->doUpload($mapping, $file, $dir, $name);
    }

    abstract protected function doRemove(PropertyMapping $mapping, ?string $dir, string $name): ?bool;

    public function remove($obj, PropertyMapping $mapping): ?bool
    {
        $name = $mapping->getFileName($obj);

        if (empty($name)) {
            return false;
        }

        return $this->doRemove($mapping, $mapping->getUploadDir($obj), $name);
    }

    /**
     * Do resolve path.
     *
     * @param PropertyMapping $mapping  The mapping representing the field
     * @param string          $dir      The directory in which the file is uploaded
     * @param string          $name     The file name
     * @param bool            $relative Whether the path should be relative or absolute
     *
     * @return string
     */
    abstract protected function doResolvePath(PropertyMapping $mapping, ?string $dir, string $name, ?bool $relative = false): string;

    public function resolvePath($obj, string $fieldName, ?string $className = null, ?bool $relative = false): ?string
    {
        [$mapping, $filename] = $this->getFilename($obj, $fieldName, $className);

        if (empty($filename)) {
            return null;
        }

        return $this->doResolvePath($mapping, $mapping->getUploadDir($obj), $filename, $relative);
    }

    public function resolveUri($obj, string $fieldName, ?string $className = null, string $dimension = null): ?string
    {
        [$mapping, $filename] = $this->getFilename($obj, $fieldName, $className);

        if (empty($filename)) {
            return null;
        }

        $dir = $mapping->getUploadDir($obj);
        $path = !empty($dir) ? $dir.'/'.$filename : $filename;

        return $mapping->getUriPrefix().'/'.$path;
    }

    public function resolveStream($obj, string $fieldName, ?string $className = null)
    {
        $path = $this->resolvePath($obj, $fieldName, $className);

        if (empty($path)) {
            return null;
        }

        return \fopen($path, 'rb');
    }

    /**
     * note: extension point.
     *
     * @param             $obj
     * @param string      $fieldName
     * @param string|null $className
     *
     * @return array
     *
     * @throws MappingNotFoundException
     * @throws \RuntimeException
     * @throws \DevFarm\FilesUploaderBundle\Exception\NotUploadableException
     */
    protected function getFilename($obj, string $fieldName, ?string $className = null): array
    {
        $mapping = $this->factory->fromField($obj, $fieldName, $className);

        if (null === $mapping) {
            throw new MappingNotFoundException(\sprintf('Mapping not found for field "%s"', $fieldName));
        }

        return [$mapping, $mapping->getFileName($obj)];
    }
}