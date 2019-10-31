<?php
/**
 * Created by PhpStorm.
 * User: sstienface
 * Date: 18/03/2019
 * Time: 14:13
 * Récupere dans la requete la locale envoyée par le navigateur et l'injecte dans l'objet $request
 */

namespace App\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;


class RequestListener {

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $browserLocale = strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0]);
        if(!empty($browserLocale))
        {
            $request->setLocale($browserLocale);
        }

        //TODO : Récuperer user locale en session qui prendra la priorité sur accept_language


    }

} 