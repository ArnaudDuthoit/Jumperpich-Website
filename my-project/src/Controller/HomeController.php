<?php

namespace App\Controller;


use App\Repository\ProjetRepository;
use App\Repository\SoonRepository;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use ReCaptcha\ReCaptcha;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @return \Symfony\Component\HttpFoundation\Response
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
     * List of all the lastest projects published
     * @Route("/recent", name="lastest")
     * @param ProjetRepository $repository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lastest(ProjetRepository $repository)
    {

        $lastest = $repository->findAllLatest();
        $ua = htmlentities($_SERVER['HTTP_USER_AGENT'], ENT_QUOTES, 'UTF-8');
        if (preg_match('~MSIE|Internet Explorer~i', $ua) || (strpos($ua, 'Trident/7.0') !== false && strpos($ua, 'rv:11.0') !== false)) {
            return $this->render('InternetExplorer.html.twig');
        } else {

            return $this->render('home/lastest.html.twig', [
                'lastest' => $lastest
            ]);
        }
    }

    /**
     * List of all the lastest projects published
     * @Route("/popular", name="ranking")
     * @param ProjetRepository $repository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function views(ProjetRepository $repository)
    {

        $views = $repository->findViewest();

        $ua = htmlentities($_SERVER['HTTP_USER_AGENT'], ENT_QUOTES, 'UTF-8');
        if (preg_match('~MSIE|Internet Explorer~i', $ua) || (strpos($ua, 'Trident/7.0') !== false && strpos($ua, 'rv:11.0') !== false)) {
            return $this->render('InternetExplorer.html.twig');
        } else {
            return $this->render('home/ranking.html.twig', [
                'views' => $views
            ]);
        }
    }

    /**
     * Contact Form Page
     * @Route("/contact", name="contact")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $contact = new Contact;

        $GOOGLE_RECAPTCHA_SECRET = $_ENV['GOOGLE_RECAPTCHA_SECRET'];

        $recaptcha = new \ReCaptcha\ReCaptcha($GOOGLE_RECAPTCHA_SECRET);

        # Add form fields
        $form = $this->createFormBuilder($contact)
            ->add('name', TextType::class, array('label' => 'Nom', 'attr' => array('maxlength' => 255, 'class' => 'form-control', 'placeholder' => "Veuillez entrer votre nom ici", 'style' => 'margin-bottom:15px')))
            ->add('email', EmailType::class, array('label' => 'Email', 'attr' => array('maxlength' => 255, 'class' => 'form-control', 'placeholder' => "Veuillez entrer votre adresse mail ici", 'style' => 'margin-bottom:15px')))
            ->add('subject', TextType::class, array('label' => 'Objet', 'attr' => array('maxlength' => 255, 'class' => 'form-control', 'placeholder' => "Veuillez entrer l'objet de votre message ici", 'style' => 'margin-bottom:15px')))
            ->add('message', TextareaType::class, array('label' => 'Message (0/255)', 'label_attr' => array('id' => 'text'), 'attr' => array('maxlength' => 255, 'class' => 'form-control', 'placeholder' => "Veuillez entrer votre message ici")))
            ->add('captcha', CaptchaType::class, array(
                'label' => "Veuillez écrire le code de sécurité suivant",
                'attr' => array('class' => 'form-control mt-4', 'placeholder' => "Veuillez entrer le code ici"),
                'width' => 200,
                'height' => 50,
                'length' => 6,
                'background_color' => [255, 255, 255],
                'reload' => true,
                'as_url' => true
            ))
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
            $contact->setCreatedAt(new \DateTime());
            $contact->setUpdatedAt(null);
            # finally add data in database
            $sn = $this->getDoctrine()->getManager();
            $sn->persist($contact);
            $sn->flush();

            $MAILER_USERNAME = $_ENV['MAILER_USERNAME'];
            $MAILER_PASSWORD = $_ENV['MAILER_PASSWORD'];


            $transport = (new Swift_SmtpTransport('smtp.gmail.com', 465, "ssl"))#Config SwiftMailer
            ->setUsername($MAILER_USERNAME)
                ->setPassword($MAILER_PASSWORD);

            $mailer = new \Swift_Mailer($transport);

            $message = (new \Swift_Message ('Site Jumperpich.com'))#Config of the email
            ->setSubject($subject)
                ->setFrom('jumperpich59@gmail.com')
                ->setTo($email)
                ->setBody($this->renderView('home/sendemail.html.twig'), 'text/html');
            $mailer->send($message);

            return $this->render('home/contact_finish.html.twig', [
            ]);

        }

        return $this->render('home/form.html.twig', [
            'current_menu' => 'contact',
            'form' => $form->createView()]);

    }

    # get success response from recaptcha and return it to controller



        /**
     * General Data Protection Regulation page
     * @Route("/rgpd", name="RGPD")
     * @return \Symfony\Component\HttpFoundation\Response
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
