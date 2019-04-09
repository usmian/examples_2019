<?php

namespace App\Repository;

use App\Entity\Module;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ModuleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Module::class);
    }

    public function findWithRoles()
    {
        $qb = $this->createQueryBuilder('m')
            ->addSelect('r')
            ->leftJoin('m.roles', 'r');

        return $qb->getQuery()
            ->getArrayResult();
    }

}
