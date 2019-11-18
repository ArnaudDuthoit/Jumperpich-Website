<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Projet;

use App\Entity\Soon;
use App\Form\ProjetType;
use App\Repository\ProjetRepository;
use App\Repository\SoonRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AdminProjetController extends AbstractController
{
    /**
     * @var ProjetRepository
     */
    private $repository;

    /**
     * @var SoonRepository
     */
    private $soonrepository;

    public function __construct(ProjetRepository $repository , SoonRepository $soonRepository)
    {
        $this->repository = $repository;
        $this->soonrepository = $soonRepository;

    }


    /**
     * Admin Home Page
     * @Route("/admin", name="admin.projet.index")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {

        // $projets = $this->repository->AllOrderRecent();

        $soon = $this->soonrepository->findAll();

        #find and paginate all the project with search criteria
        $projets = $paginator->paginate(
            $this->repository->AllOrderRecent(),
            $request->query->getInt('page', 1), #Start page
            9 #number of projects per page
        );

        return $this->render('admin_projet/index.html.twig',[
                'soon' => $soon,
                'projets' => $projets,
                'current_menu' => 'administration'
            ]
        );

    }

    /**
     * Admin Edit Page CRUD
     * @Route("/admin/mix/{id}", name="admin.projet.edit", methods="GET|POST")
     * @param Projet $projet
     * @param Request $request
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit(Projet $projet, Request $request, ObjectManager $manager)
    {

        $form = $this->createForm(ProjetType::class, $projet);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
            $this->addFlash("success", " Mix modifié avec succès");
            return $this->redirectToRoute('admin.projet.index');
        }

        return $this->render('admin_projet/edit.html.twig', [
            'projet' => $projet,
            'form' => $form->createView()
        ]);
    }

    /**
     * Delete selected project
     * @Route("admin/mix/{id}", name="admin.projet.delete" , methods="DELETE")
     * @param Projet $projet
     * @param Request $request
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Projet $projet, Request $request, ObjectManager $manager)
    {

        if ($this->isCsrfTokenValid('authenticate', $request->get('_token'))) { // check if csrf token is valid

            $manager->remove($projet); // Remove the project
            $manager->flush();
            $this->addFlash("success", " Mix supprimé avec succès");

        }

        return $this->redirectToRoute('admin.projet.index');

    }


    /**
     * Delete selected project
     * @Route("admin/mix/soon/{id}", name="admin.projet.soon.delete" , methods="DELETE")
     * @param Soon $soon
     * @param Request $request
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteSoon(Soon $soon, Request $request, ObjectManager $manager)
    {

        if ($this->isCsrfTokenValid('authenticate', $request->get('_token'))) { // check if csrf token is valid

            $manager->remove($soon); // Remove the project
            $manager->flush();
            $this->addFlash("success", " Annonce supprimée avec succès");

        }

        return $this->redirectToRoute('admin.projet.index');

    }


    /**
     * Delete selected project
     * @Route("admin/message/{id}", name="admin.message.delete" , methods="DELETE")
     * @param Contact $contact
     * @param Request $request
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteMessage(Contact $contact, Request $request, ObjectManager $manager)
    {


        if ($this->isCsrfTokenValid('authenticate', $request->get('_token'))) { // check if csrf token is valid

            $manager->remove($contact); // Remove the message
            $manager->flush();
            $this->addFlash("success", " Message supprimé avec succès");

        }

        return $this->redirectToRoute('messages_contact');

    }

    /**
     * Stats basic
     * @Route("/admin/basic_charts", name="admin.charts")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function Basic_charts()
    {

        return $this->render('admin_projet/charts.html.twig', [
            'current_menu' => 'charts'
            ]);

    }

}
