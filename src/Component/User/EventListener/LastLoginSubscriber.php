<?php

namespace App\Component\User\EventListener;

use App\Component\User\Event\UserEvents;
use App\Component\User\Model\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class LastLoginSubscriber implements EventSubscriberInterface
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return array(
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
            UserEvents::SECURITY_IMPLICIT_LOGIN => 'onSecurityInteractiveLogin'
        );
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        $this->updateUserLastLogin($user);
    }

    /**
     * @param GenericEvent $event
     */
    public function onSecurityInteractiveLogin(GenericEvent $event)
    {
        $user = $event->getSubject();
        $this->updateUserLastLogin($user);
    }

    /**
     * @param UserInterface $user
     */
    protected function updateUserLastLogin(UserInterface $user)
    {
        if ($user instanceof UserInterface) {
            $user->setLastLoginAt(new \DateTime());
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }
}