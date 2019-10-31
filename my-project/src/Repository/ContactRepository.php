<?php

namespace App\Repository;

use App\Entity\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Contact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contact[]    findAll()
 * @method Contact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Contact::class);
    }


    /**
     * Get all the messages of the ContactForm page and order it by most recent
     * @return array
     */
    public function findAllLatestMessages(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Set all the messages of the current conversation with a read_at date
     * @return mixed
     */
    public function readAllFrom()
    {

        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->update(Contact::class, 'm')
            ->set('m.updatedAt', ':now')
            ->setParameter('now', new \DateTime());

        $query = $queryBuilder->getQuery();
        // echo $query->getDQL(), "\n";
        $query->execute();
        return $queryBuilder;
    }

    /**
     * Get the total of unread messages
     */
    public function CountUnreadCount()
    {

        $dql = $this->getEntityManager()
            ->createQuery('SELECT a.id from App:Contact a 
                                where a.updatedAt is null')
            ->getResult(Query::HYDRATE_ARRAY);
        return $dql;
    }
}
