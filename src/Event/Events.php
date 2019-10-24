<?php

namespace DevFarm\FilesUploaderBundle\Event;

/**
 * Contains all the events triggered by the bundle.
 *
 */
final class Events
{
    /**
     * Triggered before a file upload is handled.
     *
     * @note This event is the same for new and old entities.
     *
     * @Event("DevFarm\FilesUploaderBundle\Event\Event")
     */
    public const PRE_UPLOAD = 'devfarm_uploader.pre_upload';

    /**
     * Triggered right after a file upload is handled.
     *
     * @note This event is the same for new and old entities.
     *
     * @Event("DevFarm\FilesUploaderBundle\Event\Event")
     */
    public const POST_UPLOAD = 'devfarm_uploader.post_upload';

    /**
     * Triggered before a file is injected into an entity.
     *
     * @Event("DevFarm\FilesUploaderBundle\Event\Event")
     */
    public const PRE_INJECT = 'devfarm_uploader.pre_inject';

    /**
     * Triggered after a file is injected into an entity.
     *
     * @Event("DevFarm\FilesUploaderBundle\Event\Event")
     */
    public const POST_INJECT = 'devfarm_uploader.post_inject';

    /**
     * Triggered before a file is removed.
     *
     * @Event("DevFarm\FilesUploaderBundle\Event\Event")
     */
    public const PRE_REMOVE = 'devfarm_uploader.pre_remove';

    /**
     * Triggered after a file is removed.
     *
     * @Event("DevFarm\FilesUploaderBundle\Event\Event")
     */
    public const POST_REMOVE = 'devfarm_uploader.post_remove';
}