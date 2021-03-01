<?php


namespace App\Services;

use App\Entity\User;
use App\Repository\ProfilRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService
{
    private $manager;
    private $serializer;
    private $encode;
    private $profilRepo;
    private $error;
    private $encoder;
    private $userRepo;

    public function __construct( EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator,
                                 UserPasswordEncoderInterface $encode,ProfilRepository $profilRepository,ErrorService $errorService,
                                 UserPasswordEncoderInterface $passwordEncoder, UserRepository $repository){

        $this->manager=$manager;
        $this->serializer=$serializer;
        $this->encode=$encode;
        $this->profilRepo=$profilRepository;
        $this->error=$errorService;
        $this->encoder=$passwordEncoder;
        $this->userRepo= $repository;
    }
    public function addUser(Request $request){
        $dataUser= $request->request->all();

       $typeUser=$this->profilRepo->find( (int)$dataUser['profil']);

        $userObject= $this->serializer->denormalize($dataUser,User::class,true);

        $userObject->setProfil($typeUser);
        $password = $userObject->getPassword();
        $userObject -> setPassword($this->encode -> encodePassword($userObject, $password));

        $avatar= $request->files->get("avatar");
        if ($avatar){
            $avatar= fopen($avatar->getRealPath(),'rb');
            $userObject-> setAvatar($avatar);
        }

        $this->error->error($userObject);

        $this->manager->persist($userObject);
        $this->manager->flush();
        if ($avatar){
            fclose($avatar);
        }
       return $userObject;

    }

    public function putUser(Request $request, int $id){
        $dataUser= $request->request->all();
        $profil=$this->profilRepo->find($dataUser['profil']);
        $typeUser=$this->userRepo->find($id);
        if ($typeUser){
            isset($dataUser['username']) ? $typeUser->setUsername($dataUser['username']) : true;
            isset($dataUser['firstname'])? $typeUser->setFirstname($dataUser['firstname']) : true;
            isset($dataUser['lastname'])? $typeUser->setLastname($dataUser['lastname']) : true;
            isset($dataUser['address'])? $typeUser->setAddress($dataUser['address']) : true;
            isset($dataUser['phone'])? $typeUser->setPhone($dataUser['phone']) : true;
            isset($dataUser['cni'])? $typeUser->setCni($dataUser['cni']) : true;
            isset($dataUser['profil'])? $typeUser->setProfil($profil) : true;


            $avatar= $request->files->get("avatar");
            if ($avatar){
                $avatar= fopen($avatar->getRealPath(),'rb');
                $typeUser->setAvatar($avatar);
            }


            $this->manager->persist($typeUser);
            $this->manager->flush();
            if ($avatar){
                fclose($avatar);
            }


        }

        return true;
    }

}
