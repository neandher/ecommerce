<?php

namespace App\Component\User\Controller;

use App\Component\Resource\Event\FlashBagEvents;
use App\Component\User\Event\UserEvents;
use App\Component\User\Form\Type\ChangePasswordType;
use App\Component\User\Form\Type\ResettingRequestType;
use App\Component\User\Form\Type\ResettingResetType;
use App\Component\User\Model\UserInterface;
use App\Component\User\Repository\UserRepositoryInterface;
use App\Component\Resource\Controller\FlashBag;
use App\Component\User\Security\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Translation\Translator;

class UserController extends UserBaseController
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var string
     */
    private $tokenTTL;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var FlashBag
     */
    private $flashBag;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param FormFactoryInterface $formFactory
     * @param EngineInterface $templatingEngine
     * @param UserRepositoryInterface $userRepository
     * @param TokenGenerator $tokenGenerator
     * @param EventDispatcherInterface $eventDispatcher
     * @param EntityManagerInterface $em
     * @param Translator $translator
     * @param $tokenTTL
     * @param UrlGeneratorInterface $urlGenerator
     * @param FlashBag $flashBag
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        EngineInterface $templatingEngine,
        UserRepositoryInterface $userRepository,
        TokenGenerator $tokenGenerator,
        EventDispatcherInterface $eventDispatcher,
        EntityManagerInterface $em,
        Translator $translator,
        $tokenTTL,
        UrlGeneratorInterface $urlGenerator,
        FlashBag $flashBag,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->formFactory = $formFactory;
        $this->templatingEngine = $templatingEngine;
        $this->userRepository = $userRepository;
        $this->tokenGenerator = $tokenGenerator;
        $this->eventDispatcher = $eventDispatcher;
        $this->em = $em;
        $this->translator = $translator;
        $this->tokenTTL = $tokenTTL;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->tokenStorage = $tokenStorage;
    }
    
    public function resettingRequestAction(Request $request)
    {
        $formType = $this->getAppAttibute($request, 'form', ResettingRequestType::class);
        $form = $this->formFactory->create($formType);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            /** @var UserInterface $user */
            $user = $this->userRepository->findOneByEmail($data['email']);

            if (!$user) {
                $form->addError(
                    new FormError($this->translator->trans('user.resetting.request.errors.email_not_found'))
                );
            } else if ($user->isPasswordRequestNonExpired($this->tokenTTL)) {
                $form->addError(
                    new FormError(
                        $this->translator->trans('user.resetting.request.errors.password_already_requested')
                    )
                );
            }

            if (!($form->getErrors()->count() > 0)) {

                if ($user->getConfirmationToken() === null) {
                    $user->setConfirmationToken($this->tokenGenerator->generateToken());
                }

                $user->setPasswordRequestedAt(new \DateTime());

                $event = new GenericEvent($user);
                $event->setArgument('email_params', $this->getAppAttibute($request, 'email_params'));

                $this->eventDispatcher->dispatch(UserEvents::RESETTING_REQUEST_SUCCESS, $event);

                $this->em->persist($user);
                $this->em->flush();

                return new RedirectResponse($this->urlGenerator->generate($this->getAppAttibute($request, 'redirect')));
            }
        }

        return $this->templatingEngine->renderResponse($this->getAppAttibute($request, 'template'), [
                'form' => $form->createView()
            ]
        );
    }

    public function resettingResetAction(Request $request, $token)
    {
        $event = new GenericEvent();
        $event
            ->setArgument('request', $request)
            ->setArgument('repository', $this->userRepository)
            ->setArgument('tokenTTL', $this->tokenTTL);

        $this->eventDispatcher->dispatch(UserEvents::RESETTING_RESET_INITIALIZE, $event);

        $redirectRouteName = $this->getAppAttibute($request, 'redirect');

        if ($request->attributes->has('error')) {
            return new RedirectResponse($this->urlGenerator->generate($redirectRouteName));
        }

        /** @var UserInterface $user */
        $user = $event->getArgument('user');

        $form = $this->formFactory->create(ResettingResetType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->eventDispatcher->dispatch(UserEvents::RESETTING_RESET_SUCCESS, $event);

            $this->em->persist($user);
            $this->em->flush();

            $this->flashBag->newMessage(FlashBagEvents::MESSAGE_TYPE_SUCCESS, 'user.resetting.reset.success');

            $this->eventDispatcher->dispatch(UserEvents::RESETTING_RESET_COMPLETED, new GenericEvent($user));

            return new RedirectResponse($this->urlGenerator->generate($redirectRouteName));
        }

        return $this->templatingEngine->renderResponse($this->getAppAttibute($request, 'template'), [
                'form' => $form->createView(),
                'token' => $token
            ]
        );
    }

    public function changePasswordAction(Request $request)
    {
        /** @var UserInterface $user */
        $user = $this->tokenStorage->getToken()->getUser();

        $form = $this->formFactory->create(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(null);

            $this->em->persist($user);
            $this->em->flush();

            $this->flashBag->newMessage(FlashBagEvents::MESSAGE_TYPE_SUCCESS, 'user.change_password.success');

            $redirectRouteName = $this->getAppAttibute($request, 'redirect');

            return new RedirectResponse($this->urlGenerator->generate($redirectRouteName));
        }

        return $this->templatingEngine->renderResponse($this->getAppAttibute($request, 'template'), [
                'form' => $form->createView(),
            ]
        );
    }
}