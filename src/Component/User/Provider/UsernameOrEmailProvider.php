<?php

namespace App\Component\User\Provider;

class UsernameOrEmailProvider extends AbstractUserProvider
{
    /**
     * {@inheritdoc}
     */
    protected function findUser($usernameOrEmail)
    {
        if (filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)) {
            return $this->userRepository->findOneByEmail($usernameOrEmail);
        }

        return $this->userRepository->findOneBy(['usernameCanonical' => $usernameOrEmail]);
    }
}