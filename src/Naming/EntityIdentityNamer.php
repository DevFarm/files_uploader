<?php

namespace DevFarm\FilesUploaderBundle\Naming;


use DevFarm\FilesUploaderBundle\Mapping\PropertyMapping;

class EntityIdentityNamer implements DirectoryNamerInterface
{
    public function directoryName($object, PropertyMapping $mapping): ?string
    {
        return $object->getId();
    }
}