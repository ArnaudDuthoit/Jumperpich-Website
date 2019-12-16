<?php

namespace App\Controller;


use App\Data\SearchData;
use App\Entity\Contact;
use App\Entity\Projet;
use App\Entity\ProjetSearch;
use App\Entity\Tag;
use App\Form\ProjetSearchType;
use App\Form\SearchForm;
use App\Repository\ProjetRepository;
use App\Repository\TagRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Handler\DownloadHandler;

class ProjetController extends AbstractController
{
    /**
     * @var ProjetRepository
     */
    private $repository;


    public function __construct(ProjetRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Index page of all the projects (with research function)
     * @Route("/api/getmix", name="getmix")
     * @param Request $request
     * @return string
     */
    public function getMixbyName(Request $request)
    {

        $name = $request->query->get('name');

        $projets = $this->repository->findMixbyName($name);

        return $this->render('projet/ajax.html.twig', [

            'projets' => $projets,

        ]);

    }

    /**
     * Index page of all the projects (with research function)
     * @Route("/api/getmixtag", name="getmixtag")
     * @param Request $request
     * @return string
     */
    public function getMixbyTags(Request $request)
    {

        $tag = $request->query->get('tag');

        $projets = $this->repository->findMixbyTags($tag);

        return $this->render('projet/ajax.html.twig', [

            'projets' => $projets,

        ]);

    }

    /**
     * Index page of all the projects (with research function)
     * @Route("/mixes", name="projet.index")
     * @param ProjetRepository $repository
     * @param Request $request
     * @return Response
     */
    public function index(ProjetRepository $repository, Request $request)
    {


        $data = new SearchData();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchForm::class, $data);

        $form->handleRequest($request);

        $projets = $repository->findSearch($data);

        return $this->render('projet/index.html.twig', [
            'projets' => $projets,
            'form' =>$form->createView()
        ]);


    }

    /**
     * Page with all the details of the project
     * @Route("/mix/{slug}-{id}", name="projet.show", requirements={"slug": "[a-z0-9\-]*"})
     * @param Projet $projet
     * @param string $slug
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function show(Projet $projet, string $slug, ObjectManager $manager)

    {

        if ($projet->getSlug() !== $slug) {
            return $this->redirectToRoute('projet.show', [
                'id' => $projet->getId(),
                'slug' => $projet->getSlug()
            ], 301);
        }

        $views = $projet->getViews();
        $projet->setViews($views + 1);
        $manager->persist($projet);
        $manager->flush();


        return $this->render('projet/show.html.twig', [
            'projet' => $projet,
        ]);
    }

    /**
     * Download Count
     * @Route("/download/mp3/{mp3filename}", name="downloadcount")
     * @param Projet $projet
     * @param Request $request
     * @param DownloadHandler $downloadHandler
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function download(Projet $projet, Request $request, DownloadHandler $downloadHandler)

    {

        $mp3filename = $projet->getMp3filename();

        $entityManager = $this->getDoctrine()->getManager();
        $projet = $entityManager->getRepository(Projet::class)->findOneBy(['mp3filename' => $mp3filename]);

        $download = $projet->getDownloadCount();
        $projet->setDownloadCount($download + 1);
        $entityManager->persist($projet);
        $entityManager->flush();

        return $downloadHandler->downloadObject($projet, $fileField = 'mp3File');


    }

}