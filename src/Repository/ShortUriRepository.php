<?php

namespace App\Repository;

use App\Entity\ShortUri;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShortUri>
 *
 * @method ShortUri|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShortUri|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShortUri[]    findAll()
 * @method ShortUri[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShortUriRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShortUri::class);
    }

    public static function crc32(string $string): string
    {
        $result = crc32($string);

        if ($result > 0x7FFFFFFF) {
            $result = -(0xFFFFFFFF - $result + 1);
        }

        return $result;
    }

    public function generateShortUri(): string
    {
        do {
            $uri = '~' . static::randomString(ShortUri::DEFAULT_LENGTH);
            $uriCrc32 = static::crc32($uri);

            $rows = $this->findByShortUriCrc($uriCrc32);
            if (empty($rows)) {
                break;
            }
        } while (true);

        return $uri;
    }

    public function save(ShortUri $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ShortUri $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return ShortUri[] Returns an array of ShortUri objects
     */
    public function findByUriCrc(string $crc32): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.uri_crc = :value')
            ->setParameter('value', $crc32)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return ShortUri[] Returns an array of ShortUri objects
     */
    public function findByShortUriCrc(string $crc32): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.short_uri_crc = :value')
            ->setParameter('value', $crc32)
            ->getQuery()
            ->getResult();
    }

    protected static function randomString(int $length): string
    {
        $result = '';

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);

        for ($idx = 0; $idx < $length; $idx++) {
            $result .= $characters[random_int(0, $charactersLength-1)];
        }

        return $result;
    }

//    /**
//     * @return ShortUri[] Returns an array of ShortUri objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ShortUri
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
