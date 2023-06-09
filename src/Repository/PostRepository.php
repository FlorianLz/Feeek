<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function save(Post $entity, bool $flush = false): void
    {
        $entity->setUpdatedAt(new \DateTimeImmutable());
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByTitle($title)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.title = :title')
            ->setParameter('title', $title)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function findAllFavorites($user)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.favorites', 'f')
            ->andWhere('f.user = :user')
            ->setParameter('user', $user)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findBySearch(string $search)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.title LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}