<?php

namespace DevFarm\FilesUploaderBundle\Event;

use Symfony\Contracts\EventDispatcher\Event as ContractEvent;
use DevFarm\FilesUploaderBundle\Mapping\PropertyMapping;

/**
 * Base class for upload events.
 *
 * Class Event
 * @package DevFarm\FilesUploaderBundle\Event
 */
class Event extends ContractEvent
{
    protected $object;

    protected $mapping;

    public function __construct($object, PropertyMapping $mapping)
    {
        $this->object = $object;
        $this->mapping = $mapping;
    }

    /**
     * Accessor to the object being manipulated.
     *
     * @return object
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Accessor to the mapping used to manipulate the object.
     *
     * @return PropertyMapping
     */
    public function getMapping(): PropertyMapping
    {
        return $this->mapping;
    }
}