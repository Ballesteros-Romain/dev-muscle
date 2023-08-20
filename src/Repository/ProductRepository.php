<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findProductPaginated(int $page, string $slug, int $limit = 6): array
    {
        $limit = abs($limit);
        $result = [];

        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('c','p')
            ->from('App\Entity\Product', 'p')
            ->join('p.categorie', 'c')
            ->where("c.slug = '$slug'")
            ->setMaxResults($limit)
            ->setFirstResult(($page * $limit) - $limit);

            $paginator = new Paginator($query);
            $data = $paginator->getQuery()->getResult();

            // on verifie qu'on a des données
            if(empty($data)){
                return $result;
            }

            // on calcule le nombre de pages
            // $pages = ceil($paginator->count() / $limit);

            // on va remplir le tableau
            // $result['data'] = $data;
            // $result['pages']= $pages;
            // $result['page'] = $page;
            // $result['limit'] = $limit;
            return $result;
    }

//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}