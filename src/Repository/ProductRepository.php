<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Récupère les 10 premiers produits dont l'ID est strictement supérieur à une valeur donnée
     * @param int $value L'ID de référence
     * @return Product[]
     */
    public function findByIdUp(int $value): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id > :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(12)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des produits par nom ou description (moteur de recherche)
     * @param string $query Le mot-clé recherché
     * @return Product[]
     */
    public function searchEngine(string $word): array
    {
        return $this->createQueryBuilder('p')
            ->where('LOWER(p.name) LIKE :word')
            ->orWhere('LOWER(p.product_description) LIKE :word')
            ->setParameter('word', '%' . strtolower($word) . '%')
            ->getQuery()
            ->getResult();
    }
}