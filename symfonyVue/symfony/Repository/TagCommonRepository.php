<?php

namespace App\Repository;

use App\Entity\TagCommon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TagCommon|null find($id, $lockMode = null, $lockVersion = null)
 * @method TagCommon|null findOneBy(array $criteria, array $orderBy = null)
 * @method TagCommon[]    findAll()
 * @method TagCommon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagCommonRepository extends ServiceEntityRepository
{
    /**
     * TagCommonRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TagCommon::class);
    }

    /**
     * @todo привести в порядок
     * @param array $criteria
     * @return mixed
     */
    public function findByType(array $criteria)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.is_archive = :archive')
            ->andWhere('t.type = :type')
            ->setParameter('archive', $criteria['isArchive'])
            ->setParameter('type', $criteria['type'])
            ->orderBy('t.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**@todo привести в порядок
     * @param array $criteria
     * @return mixed
     */
    public function findByClientAndType(array $criteria)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.is_archive = 0')
            ->andWhere('t.type = :type')
            ->andWhere('t.contragents = :client')
            ->setParameter('client', $criteria['client'])
            ->setParameter('type', $criteria['type'])
            ->orderBy('t.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**@todo привести в порядок
     * @param $key
     * @return mixed
     */
    public function filterByKeyArray($key)
    {
        $qb = $this->createQueryBuilder('tg')
            ->select([
                'tg.id',
                'tg.name AS text',
                'tg.type_name as type_text'
            ])
            ->andWhere('UPPER(tg.name) LIKE UPPER(:key)')
            ->andWhere('tg.is_archive = 0')
            ->setParameter('key', '%' . $key . '%')
            ->orderBy('tg.name', 'ASC')
            ->addOrderBy('tg.type_name', 'ASC')
            ->getQuery();

        return $qb->getArrayResult();
    }
}
