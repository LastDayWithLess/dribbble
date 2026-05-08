<?php
namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, User::class);
    }

    public function existsUser(string $email): bool {
        $exists = $this->createQueryBuilder('u')
        ->where('u.email = :email')
        ->setParameter('email', $email)
        ->setMaxResults(1)
        ->getQuery()
        ->getResult();

        if (!$exists) {
            return false;
        } else {
            return true;
        }
    }
}