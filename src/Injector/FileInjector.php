<?php

namespace DevFarm\FilesUploaderBundle\Injector;

use Symfony\Component\HttpFoundation\File\File;
use DevFarm\FilesUploaderBundle\Mapping\PropertyMapping;
use DevFarm\FilesUploaderBundle\Storage\StorageInterface;

/**
 * FileInjector.
 *
 */
class FileInjector implements FileInjectorInterface
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function injectFile($obj, PropertyMapping $mapping): void
    {
        $path = $this->storage->resolvePath($obj, $mapping->getFilePropertyName());

        if (null !== $path) {
            $mapping->setFile($obj, new File($path, false));
        }
    }
}
