<?php
/**
 * Created by PhpStorm.
 * User: arnau
 * Date: 26/04/2019
 * Time: 09:28
 */

namespace App\Services;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;


class NewMessages extends ContactRepository
{
    public $val;


    /**
     * NewMessages constructor.
     * @param RegistryInterface $registry
     * @param \Symfony\Component\Security\Core\Security $security
     */
    public function __construct(RegistryInterface $registry,\Symfony\Component\Security\Core\Security $security)
    {
        parent::__construct($registry, Contact::class);

    }

    public function getCountMessages()
    {

        return  $this->CountUnreadCount();
    }
}