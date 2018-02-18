<?php

namespace App\Component\User\EventListener;

use App\Component\User\Event\UserEvents;
use App\Component\User\Security\LoginManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\Exception\AccountStatusException;

class AuthenticationSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoginManager
     */
    private $loginManager;

    /**
     * @var string
     */
    private $firewallName;

    /**
     * AuthenticationListener constructor.
     *
     * @param LoginManager $loginManager
     * @param string $firewallName
     */
    public function __construct(LoginManager $loginManager, $firewallName)
    {
        $this->loginManager = $loginManager;
        $this->firewallName = $firewallName;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            UserEvents::REGISTRATION_CONFIRMED => 'authenticate',
            UserEvents::RESETTING_RESET_COMPLETED => 'authenticate'
        );
    }

    /**
     * @param GenericEvent $event
     * @param string $eventName
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function authenticate(GenericEvent $event, $eventName, EventDispatcherInterface $eventDispatcher)
    {
        try {
            $this->loginManager->logInUser($this->firewallName, $event->getSubject());

            $eventDispatcher->dispatch(
                UserEvents::SECURITY_IMPLICIT_LOGIN,
                new GenericEvent($event->getSubject())
            );

        } catch (AccountStatusException $ex) {
            // We simply do not authenticate users which do not pass the user
            // checker (not enabled, expired, etc.).
        }
    }

}