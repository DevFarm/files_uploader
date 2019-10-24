<?php

namespace DevFarm\FilesUploaderBundle\Naming;

use DevFarm\FilesUploaderBundle\Mapping\PropertyMapping;

interface NamerInterface
{
    public function name($object, PropertyMapping $mapping): string;
}