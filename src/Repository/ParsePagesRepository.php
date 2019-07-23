<?php

namespace App\Repository;

use App\Entity\ParsePages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\EntityManagerInterface;


/**
 * @method ParsePages|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParsePages|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParsePages[]    findAll()
 * @method ParsePages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParsePagesRepository extends ServiceEntityRepository
{
    /**
     *  EntityManagerInterface
     */
    protected $entityManager;


    public function __construct(RegistryInterface $registry, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct($registry, ParsePages::class);
    }

    /**
     * @param array $data
     * @param int $pageCount
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function saveOrUpdateParsePages(array $data, int $pageCount)
    {
        $i = 1;
        var_dump('saveOrUpdateParsePages');
        foreach ($data as $item) {
            if ($i <= $pageCount || $pageCount == 0) {
                $i++;
                $parse = $this->findOneByUrl($item['link']);
                if (!is_null($parse)){
                    $parse->setUrl($item['link']);
                    $parse->setCountImages($item['count_images']);
                    $parse->setProcessingSpeed($item['processing_speed']);
                    $this->entityManager->flush();
                }else{
                    $parse = new ParsePages();
                    $parse->setUrl($item['link']);
                    $parse->setCountImages($item['count_images']);
                    $parse->setProcessingSpeed($item['processing_speed']);
                    $this->entityManager->persist($parse);
                    $this->entityManager->flush();
                }
            }
        }
        return true;
    }

    /**
     * @param $value
     * @return ParsePages|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByUrl($value): ?ParsePages
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.url = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
