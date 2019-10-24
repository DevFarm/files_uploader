<?php

namespace DevFarm\FilesUploaderBundle\Adapter;

/**
 * AdapterInterface.
 *
 */
interface AdapterInterface
{
    /**
     * Gets the mapped object from an event.
     *
     * @param object $event The event
     *
     * @return object The mapped object
     */
    public function getObjectFromArgs($event);

    /**
     * Recomputes the change set for the object.
     *
     * @param object $event The event
     */
    public function recomputeChangeSet($event);
}