<?php

namespace App\Repository;

use App\Entity\Client;
use Assert\Assert;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function findManyBy(string $name = null, array $orderBy = [], int $limit = null, int $offset = null): array
    {
        $expr = $this->getEntityManager()->getExpressionBuilder();

        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('client')
            ->from(Client::class, 'client');

        if ($limit) {
            $queryBuilder->setMaxResults($limit);
        }

        if ($offset) {
            $queryBuilder->setFirstResult($offset);
        }

        foreach ($orderBy as $key => $value) {
            Assert::that($value)->inArray(['ASC', 'DESC']);
            $queryBuilder->addOrderBy(sprintf('client.%s', $key), $value);
        }

        if ($name) {
            $queryBuilder
                ->andWhere($expr->like('LOWER(client.name)', 'LOWER(:name)'))
                ->setParameter('name', '%' . $name . '%');
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function getTotal(): int
    {
        return (int) $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(client)')
            ->from(Client::class, 'client')
            ->getQuery()->getSingleScalarResult();
    }
}
