<?php

namespace App\Component\User\Security;

use App\Component\User\Model\UserInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserImpersonator
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var string
     */
    private $sessionTokenParameter;

    /**
     * UserImpersonator constructor.
     *
     * @param Session $session
     * @param $firewallContextName
     */
    public function __construct(Session $session, $firewallContextName)
    {
        $this->session = $session;
        $this->sessionTokenParameter = sprintf('_security_%s', $firewallContextName);
    }

    public function impersonate(UserInterface $user)
    {
        $token = new UsernamePasswordToken($user, $user->getPassword(), $this->sessionTokenParameter, $user->getRoles());
        
        $this->session->set($this->sessionTokenParameter, serialize($token));
        $this->session->save();
    }
}