<?php

namespace App\Component\User\Repository;

use App\Component\User\Model\UserInterface;
use Component\Resource\Repository\RepositoryInterface;

interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $email
     *
     * @return UserInterface|null
     */
    public function findOneByEmail($email);

    /**
     * @param $token
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByConfirmationToken($token);
}