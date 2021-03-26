<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Repository\ComissionsRepository;
use App\Repository\TransactionRepository;
use App\Services\TaxeCalculatorService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Client;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class TransactionsController extends AbstractController
{
    private $taxeCalculatorService;
    private $frais;
    public  function  __construct(TaxeCalculatorService $taxeCalculatorService){
        $this->taxeCalculatorService= $taxeCalculatorService;
    }


    /**
     * @Route("/api/calculfrais/{amount}",methods={"GET"})
     * @return JsonResponse
     */

    public function calculFraisAgence (int $amount){


            $this->frais=  $this->taxeCalculatorService->Taxe($amount);

            return  $this->json(['frais'=>$this->frais]);
    }

    /**
     * @Route("/api/transaction/{code}/retrait/clients",methods={"GET"})
     * @param string $code
     * @param TransactionRepository $transactionRepository
     * @return JsonResponse
     */

    public function getClients (string $code, TransactionRepository $transactionRepository){
        $transaction = $transactionRepository->findOneBy(["transactionCode"=> $code]);
        if(!$transaction){
            return $this->json('le code de transaction est invalide',403);
        }
        return  $this->json($transaction,200,[],['groups'=>'clients']);
    }

    /**
     * @Route("/api/user/{amount}/taxe",methods={"GET"})
     * @param int $amount
     * @return JsonResponse
     */

    public function getTaxe (int $amount, TaxeCalculatorService $taxe){
        if ($amount<0 || $amount > 5000000){
            return $this->json("le montant doit etre positif et inferieur a 5000000",200);
        }

        $taxe= floor($taxe->Taxe($amount));
        return $this->json(["taxe"=>$taxe],200);
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
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param ComissionsRepository $comissionsRepository
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManagerInterface $manager
     * @return JsonResponse
     * @throws ExceptionInterface
     */

    public function depot (Request $request, SerializerInterface $serializer,
                           ComissionsRepository $comissionsRepository,
                           TokenStorageInterface $tokenStorage,
                           EntityManagerInterface $manager): JsonResponse
    {
        $adminAgence= $tokenStorage->getToken()->getUser();
        $account= $adminAgence->getAgency()->getAccount();
        if ($account->getBalance() < 5000){
            return $this->json("low account",403);
        }

        $code=$this->code();
        $comissions= $comissionsRepository->findAll();
        $comissions=$comissions[0];
        $data=$request->getContent();
        $dataTab= $serializer->decode($data,'json');
        if ($account->getBalance() < $dataTab['amount'] ){
            return $this->json("insufficient amount",403);
        }
        $dataObject= $serializer->denormalize($dataTab, Transaction::class,true);
        $clientD= $serializer->denormalize($dataTab['clientD'], Client::class,true);
        $clientR= $serializer->denormalize($dataTab['clientR'], Client::class,true);
        $taxe= $this->taxeCalculatorService->Taxe($dataObject->getAmount());
        $dataObject->setDepositDate(new DateTime());
        $dataObject->setTaxes($taxe);
        $dataObject->setStateTaxe($taxe * $comissions->getState()/100);
        $dataObject->setSystemTaxe($taxe * $comissions->getApplicationSystem()/100);
        $dataObject->setShippingTaxe($taxe * $comissions->getDepositOperator()/100);
        $dataObject->setWithdrawalTaxe($taxe * $comissions->getWithdrawalOperator()/100);
        $dataObject->setTransactionCode($code);
        $account->setBalance(($account->getBalance()+  $dataObject->getShippingTaxe()) - $dataObject->getAmount());
        $dataObject->setAccountDepot($account);
        $dataObject->setUserDepot($adminAgence);
        $manager->persist($clientD);
        $manager->persist($clientR);
        $dataObject->setClientDepot($clientD);
        $dataObject->setClientRetrait($clientR);
        $manager->persist($dataObject);
        $manager->flush();

        return $this->json($dataObject,200,[],["groups"=>"print"]);

    }

    /**
     * @Route("/api/admin/transactions/retrait_client", name="retrait",methods={"PUT"})
     * @Security("is_granted('ROLE_AdminAgence') or is_granted('ROLE_UserAgence')",message="permission non accodée")
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManagerInterface $manager
     * @param TransactionRepository $transactionRepository
     * @return JsonResponse
     */
    public function retrait (Request $request, SerializerInterface $serializer,
                           TokenStorageInterface $tokenStorage,
                           EntityManagerInterface $manager, TransactionRepository $transactionRepository)
    {

        $data = $request->getContent();
        $dataTab = $serializer->decode($data,'json');
        $transaction = $transactionRepository->findOneBy(["transactionCode"=>$dataTab['transactionCode']]);
        if( !$transaction){
            return $this->json('withdrawal code is not valid',403);
        }
        elseif ( $transaction->getWithdrawalDate() != null ){
            return $this->json(date_format( $transaction->getWithdrawalDate(),'Y-m-d'),403);
        }
        $adminAgence = $tokenStorage->getToken()->getUser();
        $account = $adminAgence->getAgency()->getAccount();
        if($account->getBalance() < $transaction->getAmount()){
            return $this->json('impossible', 403);
        }
        $transaction->setWithdrawalDate(new DateTime());
        $account->setBalance($account->getBalance() + $transaction->getAmount() + $transaction->getWithdrawalTaxe());
        $transaction->getClientRetrait()->setCni($dataTab['cni']);
        $transaction->setUserRetrait($adminAgence);
        $transaction->setAccountRetrait($account);
        $manager->flush();
        return $this->json($transaction,200,[],["groups"=>"print"]);
    }

    /**
     * @Route("/api/admin/transactions/cancelled", name="cancel",methods={"PUT"})
     * @Security("is_granted('ROLE_AdminAgence') or is_granted('ROLE_UserAgence')",message="permission non accodée")
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManagerInterface $manager
     * @param TransactionRepository $transactionRepository
     * @return JsonResponse
     */

    public function cancel (Request $request, SerializerInterface $serializer,
                             TokenStorageInterface $tokenStorage,
                             EntityManagerInterface $manager, TransactionRepository $transactionRepository)
    {
        $data = $request->getContent();
        $dataTab = $serializer->decode($data,'json');
        $transaction = $transactionRepository->findOneBy(["transactionCode"=>$dataTab['transactionCode']]);
        if(!$transaction){
            return $this->json('withdrawal code is not valid',403);
        }
        if ($transaction->getWithdrawalDate() != null ){
            return $this->json('the deposit has already been withdrawn',403);
        }
        $transaction->setCancellationDate(new DateTime());
        $transactionAccount= $transaction->getAccountDepot();
        $transactionAccount->setBalance($transactionAccount->getBalance() + $transaction->getAmount());
        $adminAgence= $tokenStorage->getToken()->getUser();
        $transaction->setUserRetrait($adminAgence);
        $manager->flush();
        return $this->json('Transaction has been withdrawn', 200);

    }

    /**
     * @Route("/api/admin/transactions/commissions/depot", methods={"GET"})
     * @Security("is_granted('ROLE_AdminAgence') or is_granted('ROLE_UserAgence')",message="permission non accodée")
     * @param TokenStorageInterface $tokenStorage
     * @param TransactionRepository $transactionRepository
     * @return JsonResponse
     */

    public function getCommissionsDepot(TokenStorageInterface $tokenStorage,
                                        TransactionRepository $transactionRepository)
    {

        $user = $tokenStorage->getToken()->getUser();
        $agenceId=$user->getAgency()->getId();
        $commission = $transactionRepository->getCommissionDepot($agenceId);
        return $this->json($commission);

    }

    /**
     * @Route("/api/admin/transactions/commissions/retrait", methods={"GET"})
     * @Security("is_granted('ROLE_AdminAgence') or is_granted('ROLE_UserAgence')",message="permission non accodée")
     * @param TokenStorageInterface $tokenStorage
     * @param TransactionRepository $transactionRepository
     * @return JsonResponse
     */

    public function getCommissionsRetrait(TokenStorageInterface $tokenStorage, TransactionRepository $transactionRepository)
    {


        $user = $tokenStorage->getToken()->getUser();
        $agenceId=$user->getAgency()->getId();
        $commission = $transactionRepository->getCommissionRetrait($agenceId);
        return $this->json($commission);




    }

    /**
     * @Route("/api/admin/transactions/user/depot", methods={"GET"})
     * @Security("is_granted('ROLE_AdminAgence') or is_granted('ROLE_UserAgence')",message="permission non accodée")
     * @param TokenStorageInterface $tokenStorage
     * @param TransactionRepository $transactionRepository
     * @return JsonResponse
     */

    public function getTransDepotByUser(TokenStorageInterface $tokenStorage, TransactionRepository $transactionRepository)
    {


        $user = $tokenStorage->getToken()->getUser();
        $userId=$user->getId();

        $transactions = $transactionRepository->getTransDepotByUser($userId);
        return $this->json($transactions);

    }
    /**
     * @Route("/api/admin/transactions/user/retrait", methods={"GET"})
     * @Security("is_granted('ROLE_AdminAgence') or is_granted('ROLE_UserAgence')",message="permission non accodée")
     * @param TokenStorageInterface $tokenStorage
     * @param TransactionRepository $transactionRepository
     * @return JsonResponse
     */

    public function getTransRetraitByUser(TokenStorageInterface $tokenStorage, TransactionRepository $transactionRepository)
    {


        $user = $tokenStorage->getToken()->getUser();
        $userId=$user->getId();

        $transactions = $transactionRepository->getTransRetraitByUser($userId);
        return $this->json($transactions);
    }

    /**
     * @Route("/api/admin/transactions/agence", methods={"GET"})
     * @Security("is_granted('ROLE_AdminAgence')",message="permission non accodée")
     * @param TokenStorageInterface $tokenStorage
     * @param TransactionRepository $transactionRepository
     * @return JsonResponse
     */

    public function transactionAgence(TokenStorageInterface $tokenStorage, TransactionRepository $transactionRepository)
    {


        $user = $tokenStorage->getToken()->getUser();
        $idAgence=$user->getAgency()->getId();
        $t= $transactionRepository->transactionAgence($idAgence);
        dd($t);
        return $this->json($transactions);




    }
}
