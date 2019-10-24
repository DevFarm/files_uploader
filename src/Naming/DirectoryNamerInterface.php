<?php

namespace DevFarm\FilesUploaderBundle\Naming;

use DevFarm\FilesUploaderBundle\Mapping\PropertyMapping;

interface DirectoryNamerInterface
{
    public function directoryName($object, PropertyMapping $mapping): ?string;
}