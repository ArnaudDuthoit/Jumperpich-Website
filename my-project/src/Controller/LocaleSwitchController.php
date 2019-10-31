<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LocaleSwitchController extends AbstractController
{
    /**
     * @Route("/switchenglish", name="switch_language_english")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function switchLanguageEnglishAction(Request $request) {
        return $this->switchLanguage('en_EN', $request);
    }

    /**
     * @Route("/switchfrench", name="switch_language_fr")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function switchLanguageGermanAction(Request $request) {
        return $this->switchLanguage('fr_FR', $request);
    }

    private function switchLanguage($locale, Request $request) {

        $this->get('session')->set('_locale', $locale);
        $request->setDefaultLocale($locale);

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);

    }
}
