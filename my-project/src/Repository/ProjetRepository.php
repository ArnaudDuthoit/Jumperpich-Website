<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Projet;
use App\Entity\ProjetSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;


/**
 * @method Projet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Projet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Projet[]    findAll()
 * @method Projet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjetRepository extends ServiceEntityRepository
{
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    public function __construct(RegistryInterface $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Projet::class);
        $this->paginator = $paginator;
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


    /**
     * @param SearchData $search
     * @return PaginationInterface
     */
    public function findSearch(SearchData $search): PaginationInterface
    {

        $query = $this->getSearchQuery($search)->getQuery();

        return $this->paginator->paginate(
            $query,
            $search->page,
        15
        );
    }

    private function getSearchQuery(SearchData $search):QueryBuilder
    {
        $query = $this
            ->createQueryBuilder('p')
            ->orderBy('p.created_at', 'DESC')
            ->select('tag', 'p')
            ->join('p.tags', 'tag');


        if (!empty($search->q)){
            $query = $query
                ->andWhere('p.title LIKE :q')
                ->orWhere('p.description LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }


        if(!empty($search->Tags)){
            $query = $query
                ->andWhere('tag.id IN (:Tags)')
                ->setParameter('Tags', $search->Tags);
        }

        return $query;
    }


}
