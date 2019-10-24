<?php

namespace DevFarm\FilesUploaderBundle\Naming;

/**
 * ConfigurableInterface.
 *
 * Allows namers to receive configuration options.
 *
 */
interface ConfigurableInterface
{
    /**
     * Injects configuration options.
     *
     * @param array $options The options
     */
    public function configure(array $options);
}
