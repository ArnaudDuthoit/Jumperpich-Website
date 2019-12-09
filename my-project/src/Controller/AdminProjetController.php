<?php

namespace App\Controller;

use App\Repository\ProjetRepository;
use App\Repository\SoonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
     * Stats basic
     * @Route("/admin/basic_charts", name="admin.charts")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function Basic_charts()
    {

        return $this->render('admin_projet/charts.html.twig');

    }

}
