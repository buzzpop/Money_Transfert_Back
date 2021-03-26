<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Deposit;
use App\Entity\Profil;
use App\Entity\User;
use App\Repository\AccountRepository;
use App\Repository\AgencyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

class DepositDataPersister implements ContextAwareDataPersisterInterface
{
    private $entityManager;
    private $request;
    private $serializer;
    private $token;
    private $agence;
    public  function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack,
                                 SerializerInterface $serializer,AgencyRepository $agencyRepository, TokenStorageInterface $tokenStorage){
        $this->entityManager= $entityManager;
        $this->request= $requestStack;
        $this->serializer= $serializer;
        $this->agence= $agencyRepository;
        $this->token= $tokenStorage;

    }


    public function supports($data, array $context = []): bool
    {
        // TODO: Implement supports() method.
        return $data instanceof Deposit;
    }

    public function persist($data, array $context = [])
    {
        // TODO: Implement persist() method.

        if (isset($context['collection_operation_name'])){
            $content= $this->request->getCurrentRequest()->getContent();
            $content= $this->serializer->decode($content, 'json');

           $agence= $this->agence->find($content['idA']);
           $account=$agence->getAccount();
           if ($account->getBalance()> 50000){
               return new JsonResponse('impossible',403);
           }
           $account->setBalance($account->getBalance() + $content['amount']);
           $object= $this->serializer->denormalize($content, Deposit::class, true);
           $object->setAccount($account);
           $user= $this->token->getToken()->getUser();
           $object->setUser($user);
           $this->entityManager->persist($object);
            $this->entityManager->flush();
        }
        return new JsonResponse($object,200);

    }

    public function remove($data, array $context = [])
    {
        // TODO: Implement remove() method.
       $data->setIsArchived(true);

            $this->entityManager->flush();

    }
}
