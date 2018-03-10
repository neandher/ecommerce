<?php

namespace App\Component\Resource\Repository;

use Doctrine\Common\Persistence\ObjectRepository;

interface RepositoryInterface extends ObjectRepository
{
    const ORDER_ASCENDING = 'ASC';
    const ORDER_DESCENDING = 'DESC';

    /**
     * @param array $criteria
     * @param array $sorting
     *
     * @return mixed
     */
    public function createPaginator(array $criteria = [], array $sorting = []);
}