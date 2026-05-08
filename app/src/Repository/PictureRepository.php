<?php
namespace App\Repository;

use App\Enum\Category;
use App\Entity\Picture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PictureRepository extends ServiceEntityRepository {
    public const LIMIT = 5;

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Picture::class);
    }

    public function selectPicture(int $offset, Category $category): array {
        return $this->createQueryBuilder('p')
        ->where('p.category = :category')
        ->setParameter('category', $category)
        ->orderBy('p.id', 'DESC') 
        ->setFirstResult($offset)
        ->setMaxResults(self::LIMIT)
        ->getQuery()
        ->getResult();
    }

    public function selectAllPicture(int $offset): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults(self::LIMIT)
            ->getQuery()
            ->getResult();
    }

    public function existsPicture(Category $category, string $name): bool {
        $exists = $this->createQueryBuilder('p')
        ->where('p.category = :category')
        ->andWhere('p.name = :name')
        ->setParameter('category', $category)
        ->setParameter('name', $name)
        ->setMaxResults(1)
        ->getQuery()
        ->getresult();

        if (!$exists) {
            return false;
        } else {
            return true;
        }
    }

    public function countPicture(Category $category): int {
        return $this->createQueryBuilder('p')
        ->select('COUNT(p.id)')
        ->where('p.category = :category')
        ->setParameter('category', $category)
        ->getQuery()
        ->getSingleScalarResult();
    }

    public function countAllPicture(): int {
        return $this->createQueryBuilder('p')
        ->select('COUNT(p.id)')
        ->getQuery()
        ->getSingleScalarResult();
    }
    
}