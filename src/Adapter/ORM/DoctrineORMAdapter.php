<?php

namespace DevFarm\FilesUploaderBundle\Adapter\ORM;

use DevFarm\FilesUploaderBundle\Adapter\AdapterInterface;
use Doctrine\ORM\EntityManager;

/**
 * DoctrineORMAdapter.
 *
 */
class DoctrineORMAdapter implements AdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public function getObjectFromArgs($event)
    {
        return $event->getEntity();
    }

    /**
     * {@inheritdoc}
     */
    public function recomputeChangeSet($event): void
    {
        $object = $this->getObjectFromArgs($event);

        /** @var EntityManager $em */
        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();
        $metadata = $em->getClassMetadata(\get_class($object));
        $uow->recomputeSingleEntityChangeSet($metadata, $object);
    }
}
