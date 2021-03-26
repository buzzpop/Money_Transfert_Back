<?php

namespace App\Controller;

use App\Repository\DepositRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DepositController extends AbstractController
{
    /**
     * @Route("/api/admin/lastdepot", methods={"GET"})
     * @Security("is_granted('ROLE_AdminAgence') or is_granted('ROLE_UserAgence') or is_granted('ROLE_AdminSystem')",message="permission non accodée")
     */

    public function getLastDepot(TokenStorageInterface $tokenStorage,
                                        DepositRepository $depot)
    {

        $user = $tokenStorage->getToken()->getUser();
        $id=$user->getId();
        $lastDepot= $depot->getLastDepot($id);
        return $this->json($lastDepot[0]);

    }
    /**
     * @Route("/api/admin/depot/{id}/canceled", methods={"PUT"})
     * @Security("is_granted('ROLE_AdminAgence') or is_granted('ROLE_UserAgence') or is_granted('ROLE_AdminSystem')",message="permission non accodée")
     */

    public function cancelled(int $id,DepositRepository $depot,EntityManagerInterface $manager)
    {
        $lastDepot= $depot->find($id);
        $lastDepot->setCancelled(true);
        $account= $lastDepot->getAccount();
        $account->setBalance($account->getBalance() - $lastDepot->getAmount());
        $manager->flush();
        return $this->json('success');

    }
}
