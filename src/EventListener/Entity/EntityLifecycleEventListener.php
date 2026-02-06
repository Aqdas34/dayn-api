<?php

namespace App\EventListener\Entity;

use App\Util\DateTimeUtils;
use App\Util\UidUtils;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::prePersist)]
class EntityLifecycleEventListener
{
    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if (method_exists($entity, 'setUid')) {
            $entity->setUid(UidUtils::generateUid());
        }

        $now = DateTimeUtils::getDateTimeNow();
        if (method_exists($entity, 'setCreatedAt')) {
            $entity->setCreatedAt($now);
        }
        if (method_exists($entity, 'setLastModifiedAt')) {
            $entity->setLastModifiedAt($now);
        }
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (method_exists($entity, 'setLastModifiedAt')) {
            $entity->setLastModifiedAt(DateTimeUtils::getDateTimeNow());
        }
    }
}