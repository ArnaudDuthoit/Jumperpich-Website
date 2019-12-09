<?php

namespace App\Controller;



use App\Form\UserInfosType;
use App\Form\UserResetPasswordType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
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
