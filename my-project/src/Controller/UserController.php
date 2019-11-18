<?php

namespace App\Controller;


use App\Entity\Soon;
use App\Form\SoonFormType;
use App\Form\UserInfosType;
use App\Form\UserResetPasswordType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Projet;
use App\Form\ProjetType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Session\Session;


class UserController extends AbstractController
{

    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }


    /**
     * Display all the projects for this user
     * @Route("/admin/account", name="user.projet.index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function MyAccount()
    {
        #Get the current user logged in
        $user = $this->getUser();

        #Get all the projects published by the user

        return $this->render('user/user.html.twig', [
            'user' => $user,
            'current_menu' => 'settings'
        ]);
    }


    /**
     * Creating and publishing a new project
     * @Route("/admin/new", name="user.projet.create")
     * @param Request $request
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request, ObjectManager $manager)
    {

        $projet = new Projet();

        $form = $this->createForm(ProjetType::class, $projet);

        $form->handleRequest($request);

        if ($this->getUser()->getActive() == 1) { #if the user is active

            if ($form->isSubmitted() && $form->isValid()) {

                $user = $this->getUser();

                $projet->setUser($user);

                // if no title defined
                if($projet->getTitle() !== null)
                {
                    $manager->persist($projet);
                    $manager->flush();
                    $this->addFlash("success", " Mix publié avec succès");
                    return $this->redirectToRoute('admin.projet.index');
                }
                else {
                    $this->addFlash("warning", "Veuillez entrer un titre pour votre projet ...");
                    return $this->render('user/new.html.twig', [
                        'current_menu' => 'new',
                        'projet' => $projet,
                        'form' => $form->createView()]);
                }
            }

            return $this->render('user/new.html.twig', [
                'current_menu' => 'new',
                'projet' => $projet,
                'form' => $form->createView()
            ]);
        }

        return $this->render('user/inactif.html.twig'); #if the user is not active
    }


    /**
     * Creating and publishing a new project
     * @Route("/admin/new/soon", name="user.projet.create.soon")
     * @param Request $request
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newSoon(Request $request, ObjectManager $manager)
    {

        $projetSoon = new Soon();

        $form = $this->createForm(SoonFormType::class, $projetSoon);

        $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                    $manager->persist($projetSoon);
                    $manager->flush();
                    $this->addFlash("success", " Prochain Mix annoncé avec succès");
                    return $this->redirectToRoute('admin.projet.index');
            }

            return $this->render('user/soon.html.twig', [
                'projet' => $projetSoon,
                'form' => $form->createView()
            ]);

    }


    /**
     * The user editing his project page
     * @Route("/admin/projet/{id}", name="user.projet.edit", methods="GET|POST")
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
            return $this->redirectToRoute('user.projet.index');
        }

        return $this->render('user/edit.html.twig', [
            'projet' => $projet,
            'form' => $form->createView()
        ]);
    }


    /**
     * Delete page of the selected project
     * @Route("/admin/projet/{id}", name="user.projet.delete" , methods="DELETE")
     * @param Projet $projet
     * @param Request $request
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Projet $projet, Request $request, ObjectManager $manager)
    {


        if ($this->isCsrfTokenValid('authenticate', $request->get('_token'))) { #check if the csrf token is valid

            $manager->remove($projet); // Remove the project
            $manager->flush();
            $this->addFlash("success", " Mix supprimé avec succès");

        }

        return $this->redirectToRoute('user.projet.index');

    }

    /**
     * User new password page
     * @Route("/admin/new_pwd", name="user.new_pwd", methods="GET|POST")
     * @param Request $request
     * @param ObjectManager $manager
     * @param UserPasswordEncoderInterface $encoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function resetPassword(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->getUser();
        $form = $this->createForm(UserResetPasswordType::class, $user);
        $form->handleRequest($request);

        #get data of the form
        $old_pwd = $form['old_password']->getData();
        $new_pwd = $form['new_password']->getData();

        $checkPass = $encoder->isPasswordValid($user, $old_pwd); #encode the old password enter by the user

        if ($form->isSubmitted() && $form->isValid()) {

            if ($checkPass === true) { #if old pass enter by user corresponding with his current password in the database
                $new_pwd_encode = $encoder->encodePassword($user, $new_pwd);
                $user->setPassword($new_pwd_encode); #set the new encode password in the database
                $this->addFlash("success", " Mot de Passe modifié avec succès");
            } else {
                $this->addFlash("error", " Ancien mot de passe incorrect ");
            }
            $manager->flush();
            return $this->redirectToRoute('user.new_pwd');
        }

        return $this->render('user/reset_pwd.html.twig', [
            'user' => $user,
            'current_menu' => 'new_pwd',
            'form' => $form->createView()
        ]);
    }


    /**
     * User editing his informations (username and mail adress)
     * @Route("/admin/editprofile", name="user.editprofile", methods="GET|POST")
     * @param Request $request
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editInfo(Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();
        $form = $this->createForm(UserInfosType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
            $this->addFlash("success", " Informations modifiées avec succès");
            return $this->redirectToRoute('user.editprofile');
        }
        return $this->render('user/editprofile.html.twig', [
            'user' => $user,
            'current_menu' => 'editprofile',
            'form' => $form->createView()
        ]);
    }


    /**
     * User delete his account page
     * @Route("/admin/deleteprofile", name="user.deleteprofile", methods="GET|POST")
     * @param Request $request
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteUser(Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();
        $form = $this->createForm(UserInfosType::class, $user);
        $form->handleRequest($request);

        #remove the user and new session
        if ($form->isSubmitted() && $form->isValid()) {

            $manager->remove($user);
            $manager->flush();

            $session = $this->get('session');
            $session = new Session();
            $session->invalidate();
            return $this->redirectToRoute('home');
        }

        return $this->render('user/delete.html.twig', [
            'user' => $user,
            'current_menu' => 'deleteprofile',
            'form' => $form->createView()
        ]);
    }


}
