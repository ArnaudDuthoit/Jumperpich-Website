<?php

namespace App\Controller;


use App\Repository\ProjetRepository;
use App\Repository\SoonRepository;
use DateTime;
use Exception;
use ReCaptcha\ReCaptcha;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Contact;
use Swift_SmtpTransport;


class HomeController extends AbstractController
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
     * Home Page
     * @Route("/", name="home")
     * @param ProjetRepository $repository
     * @param SoonRepository $soonRepository
     * @return Response
     */
    public function index(ProjetRepository $repository, SoonRepository $soonRepository)
    {


        $soon = $soonRepository->findAll();

        $last = $repository->findLatest();
        $views = $repository->findViewest();

        $ua = htmlentities($_SERVER['HTTP_USER_AGENT'], ENT_QUOTES, 'UTF-8');
        if (preg_match('~MSIE|Internet Explorer~i', $ua) || (strpos($ua, 'Trident/7.0') !== false && strpos($ua, 'rv:11.0') !== false)) {
            return $this->render('InternetExplorer.html.twig');
        } else {
            return $this->render('home/home.html.twig', [
                'last' => $last,
                'views' => $views,
                'soon' => $soon,
                'current_menu' => 'home'
            ]);
        }

    }


    /**
     * Contact Form Page
     * @Route("/contact", name="contact")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function createAction(Request $request)
    {
        $contact = new Contact;

        $GOOGLE_RECAPTCHA_SECRET = $_ENV['GOOGLE_RECAPTCHA_SECRET'];

        $recaptcha = new ReCaptcha($GOOGLE_RECAPTCHA_SECRET);

        # Add form fields
        $form = $this->createFormBuilder($contact)
            ->add('name', TextType::class, array('label' => 'Nom', 'attr' => array('maxlength' => 255, 'class' => 'form-control', 'placeholder' => "Veuillez entrer votre nom ici", 'style' => 'margin-bottom:15px')))
            ->add('email', EmailType::class, array('label' => 'Email', 'attr' => array('maxlength' => 255, 'class' => 'form-control', 'placeholder' => "Veuillez entrer votre adresse mail ici", 'style' => 'margin-bottom:15px')))
            ->add('subject', TextType::class, array('label' => 'Objet', 'attr' => array('maxlength' => 255, 'class' => 'form-control', 'placeholder' => "Veuillez entrer l'objet de votre message ici", 'style' => 'margin-bottom:15px')))
            ->add('message', TextareaType::class, array('label' => 'Message (0/255)', 'label_attr' => array('id' => 'text'), 'attr' => array('maxlength' => 255, 'class' => 'form-control', 'placeholder' => "Veuillez entrer votre message ici")))
            ->add('Save', SubmitType::class, array('label' => 'Envoyer', 'attr' => array('class' => 'btn btn__custom', 'style' => 'margin-top:15px')))
            ->getForm();
        # Handle form response
        $form->handleRequest($request);

        $recaptchaToken = $request->request->get('recaptchaToken');
        $resp = $recaptcha->verify($recaptchaToken);

        if ($form->isSubmitted() && $form->isValid() & $resp->isSuccess()) { #Get Data for all the inputs form
            $name = $form['name']->getData();
            $email = $form['email']->getData();
            $subject = $form['subject']->getData();
            $message = $form['message']->getData();

            # set form data
            $contact->setName($name);
            $contact->setEmail($email);
            $contact->setSubject($subject);
            $contact->setMessage($message);
            $contact->setCreatedAt(new DateTime());
            $contact->setUpdatedAt(null);
            # finally add data in database
            $sn = $this->getDoctrine()->getManager();
            $sn->persist($contact);
            $sn->flush();

            $MAILER_USERNAME = $_ENV['MAILER_USERNAME'];
            $MAILER_PASSWORD = $_ENV['MAILER_PASSWORD'];


            $transport = (new Swift_SmtpTransport('mail.infomaniak.com', 587, "TLS"))#Config SwiftMailer
            ->setUsername($MAILER_USERNAME)
                ->setPassword($MAILER_PASSWORD);

            $mailer = new Swift_Mailer($transport);

            $message = (new Swift_Message ('Re:'.$subject))#Config of the email
                ->setFrom(['contact@jumperpich.com' => 'Jumperpich'])
                ->setTo($email)
                ->setBody($this->renderView('home/sendemail.html.twig', [
                    'name' => $name
                ]), 'text/html');
            $mailer->send($message);

            $message_2 = (new Swift_Message ('Nouveau Message'))
            ->setFrom(['contact@jumperpich.com' => 'Jumperpich.com'])
                ->setTo('contact@jumperpich.com')
                ->setBody('Nouveau Message sur le site Jumperpich.com', 'text/html');
            $mailer->send($message_2);


            $this->addFlash("success", "Formulaire envoyé avec succès !");

            return $this->redirectToRoute('contact');

        }

        return $this->render('home/form.html.twig', [
            'current_menu' => 'contact',
            'form' => $form->createView()]);

    }

    # get success response from recaptcha and return it to controller



        /**
     * General Data Protection Regulation page
     * @Route("/rgpd", name="RGPD")
     * @return Response
     */
    public function RGPD()
    {
        return $this->render('home/RGPD.html.twig');
    }

    /**
     * The terms and conditions page
     * @Route ("/mentions", name="mentions")
     */
    public function mentions()
    {
        return $this->render('home/mentions.html.twig');
    }

    /**
     * Q&A page
     * @Route ("/faq", name="faq")
     */
    public function faq()
    {
        return $this->render('home/faq.html.twig');
    }

    /**
     * Update page
     * @Route ("/update", name="update")
     */
    public function update()
    {
        return $this->render('home/update.html.twig');
    }

}
