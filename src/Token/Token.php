<?php


namespace App\Token;


use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class Token
{
    public function updateJwtData(JWTCreatedEvent $event)
    {
        // On récupère l'utilisateur
        $user = $event->getUser();

        // On enrichit le data du Token
        $data = $event->getData();

        if ($user->getProfil()->getLibelle()=="AdminAgence" || $user->getProfil()->getLibelle()=="UserAgence" ){
            $data['id'] = $user->getAgency()->getAccount()->getId();
            $data['agencyName'] = $user->getAgency()->getAgencyName();
        }
        $data['idUser'] = $user->getId();

        $event->setData($data);
    }
}
