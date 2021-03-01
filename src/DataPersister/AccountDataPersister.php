<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Account;
use Doctrine\ORM\EntityManagerInterface;

class AccountDataPersister implements ContextAwareDataPersisterInterface
{
    private $entityManager;
    public  function __construct(EntityManagerInterface $entityManager){
        $this->entityManager= $entityManager;

    }


    public function supports($data, array $context = []): bool
    {
        // TODO: Implement supports() method.
        return $data instanceof Account;
    }

    public function persist($data, array $context = [])
    {
        // TODO: Implement persist() method.
        $this->entityManager->persist($data);
        $this->entityManager->flush();

    }

    public function remove($data, array $context = [])
    {
        // TODO: Implement remove() method.
       $data->setIsArchived(true);
      $agency=  $data->getAgency();
      $agency->setIsArchived(true);
      $users= $agency->getUsers();
      foreach ($users as $user){
          $user->setIsArchived(true);
      }

       $this->entityManager->flush();

    }
}
