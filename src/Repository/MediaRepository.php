<?php

// src/Repository/MediaRepository.php

namespace App\Repository;

use App\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Media::class);
    }

    public function findByFiletype(string $filetype): array
    {
        if(strstr($filetype, 'videos')){
            return $this->createQueryBuilder('m')
                ->where('m.filetype = :filetype1 OR m.filetype = :filetype2')
                ->setParameter('filetype1', 'video')
                ->setParameter('filetype2', 'clip')
                ->orderBy('m.dateUploaded', 'ASC')
                ->setMaxResults(6)
                ->getQuery()
                ->getResult()
                ;
        }else{
            return $this->createQueryBuilder('m')
                ->where('m.filetype = :filetype')
                ->setParameter('filetype', $filetype)
                ->orderBy('m.dateUploaded', 'DESC')
                ->setMaxResults(6)
                ->getQuery()
                ->getResult();
        }
    }

    public function findByObjects(string $filetype): array
    {
        if(strstr($filetype, 'videos')){
            return $this->createQueryBuilder('m')
                ->where('m.filetype = :filetype1')
                ->setParameter('filetype1', 'unity_model')
                ->orderBy('m.dateUploaded', 'ASC')
                ->setMaxResults(6)
                ->getQuery()
                ->getResult()
                ;
        }else{
            return $this->createQueryBuilder('m')
                ->where('m.filetype = :filetype')
                ->setParameter('filetype', $filetype)
                ->orderBy('m.dateUploaded', 'DESC')
                ->getQuery()
                ->getResult();
        }
    }

    public function findByFilename(string $filetype): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.filetype = :filetype')
            ->setParameter('filename', $filetype)
            ->orderBy('m.dateUploaded', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();
    }

    public function findByViews(): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.filetype = :filetype1 OR m.filetype = :filetype2 OR m.filetype = :filetype3 OR m.filetype = :filetype4')
            ->setParameter('filetype1', 'video')
            ->setParameter('filetype2', 'clip')
            ->setParameter('filetype3', 'audio')
            ->setParameter('filetype4', 'image')
            ->orderBy('m.dateUploaded', 'DESC')
            ->setMaxResults(8)
            ->getQuery()
            ->getResult()
            ;
    }

    public function deleteVideo(int $id): void
    {
        $qb = $this->createQueryBuilder('m');
        $qb->delete()
            ->where('m.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }

    public function findTrashItems($uid)
    {
        $qb = $this->createQueryBuilder('m');

        $qb->join('m.user', 'u')
            ->where('u.id = :uid')
            ->andWhere('m.filetype = :filetype')
            ->setParameter('uid', $uid)
            ->setParameter('filetype', 'trash')
            ->orderBy('m.date_uploaded', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findImages($uid)
    {
        return $this->createQueryBuilder('m')
            ->where('m.uid = :uid')
            ->andWhere('m.filetype = :filetype')
            ->setParameter('uid', $uid)
            ->setParameter('filetype', 'image')
            ->orderBy('m.date_uploaded', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAudio($uid)
    {
        return $this->createQueryBuilder('m')
            ->where('m.uid = :uid')
            ->andWhere('m.filetype = :filetype')
            ->setParameter('uid', $uid)
            ->setParameter('filetype', 'audio')
            ->orderBy('m.date_uploaded', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByUid($uid)
    {
        try {
            return $this->createQueryBuilder('m')
                ->where('m.uid = :uid')
                ->andWhere('m.filetype = :filetype1 OR m.filetype = :filetype2')
                ->orderBy('m.date_uploaded', 'DESC')
                ->setParameters([
                    'uid' => $uid,
                    'filetype1' => 'video',
                    'filetype2' => 'clip',
                ])
                ->getQuery()
                ->getResult();
        } catch (\Exception $e) {
            print $e->getMessage();
            return [];
        }
    }
}
