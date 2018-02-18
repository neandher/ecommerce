<?php

namespace App\Component\User\Provider;

use App\Component\User\Repository\UserRepositoryInterface;
use App\Component\User\Canonicalizer\Canonicalizer;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

abstract class AbstractUserProvider implements UserProviderInterface
{
    /**
     * @var string
     */
    protected $supportedUserClass = UserInterface::class;

    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * @var Canonicalizer
     */
    protected $canonicalizer;

    /**
     * @param string $supportedUserClass FQCN
     * @param UserRepositoryInterface $userRepository
     * @param Canonicalizer $canonicalizer
     */
    public function __construct(
        $supportedUserClass,
        UserRepositoryInterface $userRepository,
        Canonicalizer $canonicalizer
    )
    {
        $this->supportedUserClass = $supportedUserClass;
        $this->userRepository = $userRepository;
        $this->canonicalizer = $canonicalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $username = $this->canonicalizer->canonicalize($username);
        $user = $this->findUser($username);

        if (null === $user) {
            throw new UsernameNotFoundException(
                sprintf('Username "%s" does not exist.', $username)
            );
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }
        if (null === $reloadedUser = $this->userRepository->find($user->getId())) {
            throw new UsernameNotFoundException(
                sprintf('User with ID "%d" could not be refreshed.', $user->getId())
            );
        }

        return $reloadedUser;
    }

    /**
     * {@inheritdoc}
     */
    abstract protected function findUser($uniqueIdentifier);

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $this->supportedUserClass === $class || is_subclass_of($class, $this->supportedUserClass);
    }
}