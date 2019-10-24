<?php

namespace DevFarm\FilesUploaderBundle\Injector;

use DevFarm\FilesUploaderBundle\Mapping\PropertyMapping;

/**
 * FileInjectorInterface.
 *
 */
interface FileInjectorInterface
{
    /**
     * Injects the uploadable field of the specified object and mapping.
     *
     * The field is populated with a \Symfony\Component\HttpFoundation\File\File
     * instance.
     *
     * @param object          $obj     The object
     * @param PropertyMapping $mapping The mapping representing the field to populate
     */
    public function injectFile($obj, PropertyMapping $mapping): void;
}
