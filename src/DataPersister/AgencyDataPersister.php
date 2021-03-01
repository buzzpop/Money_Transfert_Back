<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Account;
use App\Entity\Agency;
use App\Entity\Deposit;
use App\Entity\Profil;
use App\Entity\User;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

class AgencyDataPersister implements ContextAwareDataPersisterInterface
{
    private $entityManager;
    private $request;
    private $serializer;
    private $token;
    private $account;
    public  function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack,
                                 SerializerInterface $serializer,AccountRepository $accountRepository, TokenStorageInterface $tokenStorage){
        $this->entityManager= $entityManager;
        $this->request= $requestStack;
        $this->serializer= $serializer;
        $this->account= $accountRepository;
        $this->token= $tokenStorage;

    }


    public function supports($data, array $context = []): bool
    {
        // TODO: Implement supports() method.
        return $data instanceof Agency;
    }

    public function persist($data, array $context = [])
    {
        // TODO: Implement persist() method.

        if (isset($context['collection_operation_name'])){
            $content= $this->request->getCurrentRequest()->getContent();
            $content= $this->serializer->decode($content, 'json');
           $account= $this->serializer->denormalize($content['account'], Account::class, true);
           $agency= $this->serializer->denormalize($content, Agency::class, true);
            $this->entityManager->persist($account);
            $agency->setAccount($account);
           $this->entityManager->persist($agency);
            $this->entityManager->flush();
        }
        if (isset($context['item_operation_name'])){
            $this->entityManager->persist($data);
            $this->entityManager->flush();
        }

    }

    public function remove($data, array $context = [])
    {
        // TODO: Implement remove() method.
        $userId= $this->request->getCurrentRequest()->get('idU');
        $users= $data->getUsers();

        foreach ($users as $user){
            if ($user->getId()== $userId){
                $user->setIsArchived(true);
                $data->removeUser($user);
            }
        }

            $this->entityManager->flush();

    }
}
