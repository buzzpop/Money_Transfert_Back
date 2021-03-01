<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Profil;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserDataPersister implements ContextAwareDataPersisterInterface
{
    private $entityManager;
    public  function __construct(EntityManagerInterface $entityManager){
        $this->entityManager= $entityManager;

    }


    public function supports($data, array $context = []): bool
    {
        // TODO: Implement supports() method.
        return $data instanceof User;
    }

    public function persist($data, array $context = [])
    {
        // TODO: Implement persist() method.

    }

    public function remove($data, array $context = [])
    {
        // TODO: Implement remove() method.
       $data->setIsArchived(true);

            $this->entityManager->flush();

    }
}
