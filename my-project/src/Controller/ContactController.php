<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use Doctrine\Common\Persistence\ObjectManager;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * List of all the messages received with the contact form
     * @Route("/admin/contact", name="messages_contact")
     * @param ContactRepository $repository
     * @param ContactRepository $contactRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(ContactRepository $repository, ContactRepository $contactRepository)
    {
        $contacts = $repository->findAllLatestMessages();

        $contactRepository->readAllFrom();

        return $this->render('contact/index.html.twig', [
            'contacts' => $contacts,
            'current_menu' => 'messages',
        ]);
    }
}
