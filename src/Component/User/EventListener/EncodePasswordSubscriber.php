<?php

namespace App\Component\User\EventListener;

use App\Component\User\Model\UserInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class EncodePasswordSubscriber implements EventSubscriber
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
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
        $entity = $eventArgs->getEntity();

        if (!$this->isUser($entity)) {
            return;
        }

        $this->encodePassword($entity);
    }

    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();

        if (!$this->isUser($entity)) {
            return;
        }

        $this->encodePassword($entity);
    }

    private function encodePassword(UserInterface $user)
    {
        $plainPassword = $user->getPlainPassword();

        if (!is_null($plainPassword)) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $plainPassword));
        }
    }

    private function isUser($entity)
    {
        return $entity instanceof UserInterface;
    }
}