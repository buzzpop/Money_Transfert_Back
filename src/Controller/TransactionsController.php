<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Repository\ComissionsRepository;
use App\Repository\TransactionRepository;
use App\Services\TaxeCalculatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Client;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class TransactionsController extends AbstractController
{
    private $taxeCalculatorService;
    public  function  __construct(TaxeCalculatorService $taxeCalculatorService){
        $this->taxeCalculatorService= $taxeCalculatorService;
    }

    function code(){
        $chars = '0123456789';
        $string = '';
        for($i=0; $i<9; $i++){

            $string .= $chars[rand(0, strlen($chars)-1)];
            if ($i==2 || $i==5){
                $string.='-';
            }
        }
        return $string;
    }

    /**
     * @Route(
     * name="depot",
     * path="/api/admin/transactions/depot_client",
     * methods={"POST"},
     * defaults={
     * "_controller"="\app\Controller\TransactionController::depot",
     * "_api_resource_class"=Client::class,
     * "_api_collection_operation_name"="depot"
     * }
     * )
     *
     * @Security("is_granted('ROLE_AdminAgence') or is_granted('ROLE_UserAgence')",message="permission non accodée")
     */

    public function depot (Request $request, SerializerInterface $serializer,
                           ComissionsRepository $comissionsRepository,
                           TokenStorageInterface $tokenStorage,
                           EntityManagerInterface $manager)
    {
        $adminAgence= $tokenStorage->getToken()->getUser();
        $account= $adminAgence->getAgency()->getAccount();
        if ($account->getBalance() < 5000){
            return $this->json("le depot ne peut pas etre effectué car le solde du compte est inferieur à 5000",403);
        }

        $code=$this->code();
        $comissions= $comissionsRepository->findAll();
        $comissions=$comissions[0];
        $data=$request->getContent();
        $dataTab= $serializer->decode($data,'json');
        if ($account->getBalance() < $dataTab['amount'] ){
            return $this->json("le solde est inferieur au montant demandé",403);
        }
        $dataObject= $serializer->denormalize($dataTab, Transaction::class,true);
        $clientD= $serializer->denormalize($dataTab['clientD'], Client::class,true);
        $clientR= $serializer->denormalize($dataTab['clientR'], Client::class,true);
        $taxe= $this->taxeCalculatorService->Taxe($dataObject->getAmount());
        $dataObject->setDepositDate(new \DateTime());
        $dataObject->setTaxes($taxe);
        $dataObject->setStateTaxe($taxe * $comissions->getState()/100);
        $dataObject->setSystemTaxe($taxe * $comissions->getApplicationSystem()/100);
        $dataObject->setShippingTaxe($taxe * $comissions->getDepositOperator()/100);
        $dataObject->setWithdrawalTaxe($taxe * $comissions->getWithdrawalOperator()/100);
        $dataObject->setTransactionCode($code);
        $account->setBalance($account->getBalance() - $dataObject->getAmount());
        $dataObject->setAccount($account);
        $dataObject->setUserDepot($adminAgence);
        $manager->persist($clientD);
        $manager->persist($clientR);
        $dataObject->setClientDepot($clientD);
        $dataObject->setClientRetrait($clientR);
        $manager->persist($dataObject);
        $manager->flush();

        return $this->json(['Transaction Details'=>$dataObject],200,[],["groups"=>"print"]);

    }




    /**
     * @Route("/api/admin/transactions/retrait_client", name="retrait",methods={"PUT"})
     * @Security("is_granted('ROLE_AdminAgence') or is_granted('ROLE_UserAgence')",message="permission non accodée")
     */
    public function retrait (Request $request, SerializerInterface $serializer,
                           TokenStorageInterface $tokenStorage,
                           EntityManagerInterface $manager, TransactionRepository $transactionRepository)
    {

        $data = $request->getContent();
        $dataTab = $serializer->decode($data,'json');
        $transaction = $transactionRepository->findOneBy(["transactionCode"=>$dataTab['transactionCode']]);
        if($transaction->getWithdrawalDate() != null || !$transaction){
            return $this->json('withdrawal code is not valid',403);
        }
        $adminAgence = $tokenStorage->getToken()->getUser();
        $account = $adminAgence->getAgency()->getAccount();
        if($account->getBalance() < $transaction->getAmount()){
            return $this->json('withdrawal not possible', 403);
        }
        $transaction->setWithdrawalDate(new \DateTime());
        $account->setBalance($account->getBalance() + $transaction->getAmount());
        $transaction->getClientRetrait()->setCni($dataTab['cni']);
        $transaction->setUserRetrait($adminAgence);
        $manager->flush();
        return $this->json($transaction,200,[],["groups"=>"print"]);
    }
}
