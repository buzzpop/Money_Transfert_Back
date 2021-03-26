<?php

namespace App\Controller;

use App\Services\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class UserController extends AbstractController
{
    private $userService;

    public function __construct(UserService $userService){
        $this->userService= $userService;
    }


    /**
     * @Route(
     * name="add_user",
     * path="/api/admin/users",
     * methods={"POST"},
     * defaults={
     * "_controller"="\app\Controller\UserController::addUser",
     * "_api_resource_class"=User::class,
     * "_api_collection_operation_name"="add_user"
     * }
     * )
     */
    public function addUser(Request $request)
    {
            $this->userService->addUser($request);
            return $this->json("Utilisateur Ajouté",200);

    }

    /**
     * @Route(
     * name="put_user",
     * path="/api/admin/users/{id}",
     * methods={"PUT"},
     * defaults={
     * "_controller"="\app\Controller\UserController::putUser",
     * "_api_resource_class"=User::class,
     * "_api_item_operation_name"="put_user"
     * }
     * )
     * @Security("is_granted('ROLE_AdminSystem') or is_granted('ROLE_AdminAgence') or is_granted('ROLE_UserAgence')",message="permission non accodée")

     */

    public function putUser(Request $request, int $id)
    {
            $this->userService->putUser($request, $id);

            return $this->json("Utilisateur modifié",200);
    }


}
