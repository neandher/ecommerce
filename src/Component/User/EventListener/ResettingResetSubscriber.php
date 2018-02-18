<?php

namespace App\Component\User\EventListener;

use App\Component\Resource\Event\FlashBagEvents;
use App\Component\User\Event\UserEvents;
use App\Component\User\Model\UserInterface;
use App\Component\User\Repository\UserRepositoryInterface;
use App\Component\Resource\Controller\FlashBag;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ResettingResetSubscriber implements EventSubscriberInterface
{
    /**
     * @var FlashBag
     */
    private $flashBag;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * ResettingResetSubscriber constructor.
     *
     * @param FlashBag $flashBag
     * @param UrlGeneratorInterface $router
     */
    public function __construct(
        FlashBag $flashBag,
        UrlGeneratorInterface $router
    )
    {
        $this->flashBag = $flashBag;
        $this->router = $router;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            UserEvents::RESETTING_RESET_INITIALIZE => 'onResettingResetInitialize',
            UserEvents::RESETTING_RESET_SUCCESS => 'onResettingResetSuccess',
        );
    }

    public function onResettingResetInitialize(GenericEvent $event)
    {
        $request = $event->getArgument('request');
        $token = $request->attributes->get('token');

        /** @var UserRepositoryInterface $repository */
        $repository = $event->getArgument('repository');
        $user = $repository->findOneByConfirmationToken($token);

        $tokenTTL = $event->getArgument('tokenTTL');

        if (!$user) {

            $this->flashBag->newMessage(
                FlashBagEvents::MESSAGE_TYPE_ERROR,
                'user.resetting.reset.errors.invalid_token'
            );

            $request->attributes->add(['error' => 'true']);

        } elseif (!$user->isPasswordRequestNonExpired($tokenTTL)) {

            $this->flashBag->newMessage(
                FlashBagEvents::MESSAGE_TYPE_ERROR,
                'user.resetting.reset.errors.expired_token'
            );

            $request->attributes->add(['error' => 'true']);

        } else {
            $event->setArgument('user', $user);
        }
    }

    public function onResettingResetSuccess(GenericEvent $event)
    {
        /** @var UserInterface $user */
        $user = $event->getArgument('user');

        $user->setPasswordRequestedAt(null)
            ->setConfirmationToken(null);
    }
}