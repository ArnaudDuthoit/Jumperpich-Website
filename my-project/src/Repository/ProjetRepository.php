<?php

namespace App\Repository;

use App\Entity\Projet;
use App\Entity\ProjetSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;


/**
 * @method Projet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Projet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Projet[]    findAll()
 * @method Projet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjetRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Projet::class);
    }


    /**
     * @param $name
     * @return Query
     */
    public function findMixbyName($name)
    {

        $query = $this->createQueryBuilder('p');

        return $query = $query
            ->where('p.title LIKE :search')
            ->orWhere('p.description LIKE :search')
            ->orderBy('p.created_at', 'DESC')
            #Add some joker at the beginning and a the end for more results
            ->setParameter('search', '%' . $name . '%')
            ->getQuery()
            ->getResult();

    }


    /**
     * @param $tag
     * @return Query
     */
    public function findMixbyTags($tag)
    {

        $query = $this->createQueryBuilder('p')
                 ->orderBy('p.created_at', 'DESC');

        if ($tag != null) {

            return $query = $query
                ->andWhere(":tag MEMBER OF p.tags")
                ->setParameter("tag", $tag)
                ->getQuery()
                ->getResult();

        } else {
            return $query = $query
                ->getQuery()
                ->getResult();
        }

    }


    /**
     * @param ProjetSearch $search
     * @return Query
     */
    public function findAllActive(ProjetSearch $search)
    {
        $query = $this->createQueryBuilder('p')
            ->orderBy('p.created_at', 'DESC');

        if ($search->getProjectname()) { #Put the input search in the query.
            $query = $query
                ->where('p.title LIKE :search')
                #Add some joker at the beginning and a the end for more results
                ->setParameter('search', '%' . $search->getProjectname() . '%');
        }
        if ($search->getTags()->count() > 0) { #if the user select at least one tag

            foreach ($search->getTags() as $tag) {
                $query = $query
                    ->andWhere(":tag MEMBER OF p.tags")
                    ->setParameter("tag", $tag);
            }
        }


        return $query->getQuery();
    }

    /**
     * Get all the projects order by date and with a max result of 8
     * @return Projet[]
     */
    public function findLatest(): array
    {

        return $this->createQueryBuilder('p')
            ->orderBy('p.created_at', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get all the projects order by date and with a max result of 8
     * @return Projet[]
     */
    public function AllOrderRecent(): array
    {

        return $this->createQueryBuilder('p')
            ->orderBy('p.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }


    /**
     * Get all the projects order by date with no results limit
     * @return Projet[]
     */
    public function findAllLatest(): array
    {

        return $this->createQueryBuilder('p')
            ->orderBy('p.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get all the projects order by date and with a max result of 8
     * @return Projet[]
     */
    public function findViewest(): array
    {

        return $this->createQueryBuilder('p')
            ->orderBy('p.views', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();
    }


}
