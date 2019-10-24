<?php

namespace DevFarm\FilesUploaderBundle\Handler;

use DevFarm\FilesUploaderBundle\Exception\MappingNotFoundException;
use DevFarm\FilesUploaderBundle\Mapping\PropertyMapping;
use DevFarm\FilesUploaderBundle\Mapping\PropertyMappingFactory;
use DevFarm\FilesUploaderBundle\Storage\StorageInterface;

/**
 * Class AbstractHandler
 * @package DevFarm\FilesUploaderBundle\Handler
 */
abstract class AbstractHandler
{
    /**
     * @var PropertyMappingFactory
     */
    protected $factory;

    /**
     * @var StorageInterface
     */
    protected $storage;

    public function __construct(PropertyMappingFactory $factory, StorageInterface $storage)
    {
        $this->factory = $factory;
        $this->storage = $storage;
    }

    /**
     * @param object|array $obj
     * @param string       $fieldName
     * @param string|null  $className
     *
     * @return PropertyMapping|null
     *
     * @throws MappingNotFoundException
     */
    protected function getMapping($obj, string $fieldName, ?string $className = null): ?PropertyMapping
    {
        $mapping = $this->factory->fromField($obj, $fieldName, $className);

        if (null === $mapping) {
            throw new MappingNotFoundException(\sprintf('Mapping not found for field "%s"', $fieldName));
        }

        return $mapping;
    }
}