<?php

namespace DevFarm\FilesUploaderBundle\EventListener\Doctrine;

use Doctrine\Common\EventSubscriber;
use DevFarm\FilesUploaderBundle\Adapter\AdapterInterface;
use DevFarm\FilesUploaderBundle\Handler\UploadHandler;
use DevFarm\FilesUploaderBundle\Metadata\MetadataReader;
use DevFarm\FilesUploaderBundle\Utility\ClassUtils;

/**
 * BaseListener.
 *
 */
abstract class BaseListener implements EventSubscriber
{
    /**
     * @var string
     */
    protected $mapping;

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var MetadataReader
     */
    protected $metadata;

    /**
     * @var UploadHandler
     */
    protected $handler;

    public function __construct(string $mapping, AdapterInterface $adapter, MetadataReader $metadata, UploadHandler $handler)
    {
        $this->mapping = $mapping;
        $this->adapter = $adapter;
        $this->metadata = $metadata;
        $this->handler = $handler;
    }

    /**
     * Checks if the given object is uploadable using the current mapping.
     *
     * @param object $object The object to test
     *
     * @return bool
     */
    protected function isUploadable($object): bool
    {
        return $this->metadata->isUploadable(ClassUtils::getClass($object), $this->mapping);
    }

    /**
     * Returns a list of uploadable fields for the given object and mapping.
     *
     * @param object $object The object to use
     *
     * @return array|string[] A list of field names
     *
     * @throws \DevFarm\FilesUploaderBundle\Exception\MappingNotFoundException
     */
    protected function getUploadableFields($object): array
    {
        $fields = $this->metadata->getUploadableFields(ClassUtils::getClass($object), $this->mapping);

        return \array_map(function ($data) {
            return $data['propertyName'];
        }, $fields);
    }
}
