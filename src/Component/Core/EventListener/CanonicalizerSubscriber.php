<?php

namespace App\Component\Core\EventListener;

use App\Component\Core\Model\CustomerInterface;
use App\Component\User\Model\UserInterface;
use App\Component\User\Canonicalizer\Canonicalizer;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class CanonicalizerSubscriber implements EventSubscriber
{
    /**
     * @var Canonicalizer
     */
    private $canonicalizer;

    public function __construct(Canonicalizer $canonicalizer)
    {
        $this->canonicalizer = $canonicalizer;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'preUpdate'
        );
    }

    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $this->canonicalize($eventArgs);
    }

    public function preUpdate(LifecycleEventArgs $eventArgs)
    {
        $this->canonicalize($eventArgs);
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function canonicalize(LifecycleEventArgs $event)
    {
        $item = $event->getEntity();

        if ($item instanceof CustomerInterface) {
            $item->setEmailCanonical($this->canonicalizer->canonicalize($item->getEmail()));
        } elseif ($item instanceof UserInterface) {
            $item->setUsernameCanonical($this->canonicalizer->canonicalize($item->getUsername()));
            $item->setEmailCanonical($this->canonicalizer->canonicalize($item->getEmail()));
        }
    }
}