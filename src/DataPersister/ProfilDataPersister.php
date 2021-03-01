<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Profil;
use Doctrine\ORM\EntityManagerInterface;

class ProfilDataPersister implements ContextAwareDataPersisterInterface
{
    private $entityManager;
    public  function __construct(EntityManagerInterface $entityManager){
        $this->entityManager= $entityManager;

    }


    public function supports($data, array $context = []): bool
    {
        // TODO: Implement supports() method.
        return $data instanceof Profil;
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
        $archive=$data->setIsArchived(true);
        $this->entityManager->persist($archive);
        $users= $data->getUsers();
      foreach ($users as $user){
         $archiveUser =$user->setIsArchived(true);
          $this->entityManager->persist($archiveUser);
      }
            $this->entityManager->flush();

    }
}
