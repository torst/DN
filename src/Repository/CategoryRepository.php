<?php

namespace App\Repository;

use App\Entity\Category;
use App\Utils\Constant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function getPublicatedCategories()
    {
        $query = $this->_em->getRepository(Category::class)->createQueryBuilder('c')
            ->where('c.isPublicated = 1')
            ->orderBy('c.tri','ASC')
            ->getQuery()->getResult()
        ;

        return $query;
    }

    public function getCategoryArticles($id)
    {
        $query = $this->_em
            ->createQuery('
                SELECT a 
                FROM App\\Entity\\Article a  
                INNER JOIN App\\Entity\\Category cat 
                WITH a.category = cat.categoryId 
                WHERE cat.categoryId = :id
                AND a.isPublished = 1
                ORDER BY a.tri ASC
            ')
            ->setParameter('id',$id)
        ;

        return $query->getResult();
    }

    public function getPaginatedArticles(int $page = 1, int $id, int $nbArticles) : Pagerfanta
    {

        $query = $this->_em
            ->createQuery('
                SELECT a 
                FROM App\\Entity\\Article a  
                INNER JOIN App\\Entity\\Category cat 
                WITH a.category = cat.categoryId 
                WHERE cat.categoryId = :id
                AND a.isPublished = 1
                ORDER BY a.tri ASC
            ')
            ->setParameter('id',$id)
        ;

        return $this->createPaginator($query, $page, $nbArticles);

    }

    private function createPaginator(Query $query, int $page, $nbArticles): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage($nbArticles);
        $paginator->setCurrentPage($page);

        return $paginator;
    }
}
