<?php

namespace App\Component\User\Security;

use App\Component\User\Model\UserInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;

/**
 * Abstracts process for manually logging in a user.
 *
 */
class LoginManager
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var UserCheckerInterface
     */
    private $userChecker;
    /**
     * @var SessionAuthenticationStrategyInterface
     */
    private $sessionStrategy;
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var RememberMeServicesInterface
     */
    private $rememberMeService;

    /**
     * LoginManager constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     * @param UserCheckerInterface $userChecker
     * @param SessionAuthenticationStrategyInterface $sessionStrategy
     * @param RequestStack $requestStack
     * @param RememberMeServicesInterface|null $rememberMeService
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        UserCheckerInterface $userChecker,
        SessionAuthenticationStrategyInterface $sessionStrategy,
        RequestStack $requestStack,
        RememberMeServicesInterface $rememberMeService = null
    )
    {
        $this->tokenStorage = $tokenStorage;
        $this->userChecker = $userChecker;
        $this->sessionStrategy = $sessionStrategy;
        $this->requestStack = $requestStack;
        $this->rememberMeService = $rememberMeService;
    }

    /**
     * {@inheritdoc}
     */
    public function logInUser($firewallName, UserInterface $user, Response $response = null)
    {
        $this->userChecker->checkPreAuth($user);
        $token = $this->createToken($firewallName, $user);

        $request = $this->requestStack->getCurrentRequest();

        if (null !== $request) {
            $this->sessionStrategy->onAuthentication($request, $token);
            if (null !== $response && null !== $this->rememberMeService) {
                $this->rememberMeService->loginSuccess($request, $response, $token);
            }
        }

        $this->tokenStorage->setToken($token);
    }

    /**
     * @param string $firewall
     * @param UserInterface $user
     *
     * @return UsernamePasswordToken
     */
    protected function createToken($firewall, UserInterface $user)
    {
        return new UsernamePasswordToken($user, $user->getPassword(), $firewall, $user->getRoles());
    }
}