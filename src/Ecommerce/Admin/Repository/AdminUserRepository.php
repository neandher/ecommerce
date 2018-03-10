<?php

namespace App\Ecommerce\Admin\Repository;

use App\Component\Resource\Repository\EntityRepository;
use App\Component\Resource\Util\Pagination;
use App\Component\User\Repository\UserRepositoryInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * AdminUserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AdminUserRepository extends EntityRepository implements UserRepositoryInterface
{
    public function findOneByEmail($email)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.emailCanonical = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByConfirmationToken($token)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.confirmationToken = :token')->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

    protected function queryLatest(Pagination $pagination)
    {
        $routeParams = $pagination->getRouteParams();

        $qb = $this->createQueryBuilder('admin');

        if (isset($routeParams['search'])) {
            $qb->andWhere(
                $qb->expr()->like(
                    $qb->expr()->concat('admin.firstName', $qb->expr()->concat($qb->expr()->literal(' '), 'admin.lastName')),
                    ':search'
                )
            )->setParameter('search', '%' . $routeParams['search'] . '%');
        }

        $qb = $this->addOrderingQueryBuilder($qb, $routeParams);

        return $qb->getQuery();
    }

    public function findLatest(Pagination $pagination)
    {
        $routeParams = $pagination->getRouteParams();

        $paginator = new Pagerfanta(new DoctrineORMAdapter($this->queryLatest($pagination), false, false));
        $paginator->setMaxPerPage($routeParams['num_items']);
        $paginator->setCurrentPage($routeParams['page']);

        return $paginator;
    }
}
