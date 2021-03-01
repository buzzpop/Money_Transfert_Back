<?php


namespace App\Services;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ErrorService
{
    private $validator;
    private $serializer;
    public function __construct(ValidatorInterface $validator,SerializerInterface $serializer){
        $this->validator=$validator;
        $this->serializer=$serializer;
    }
    public function error($tabError){
        $errors= $this->validator->validate( $tabError);
        if (count($errors)>0){

            foreach ($errors as $error){
                $tabE['erreur: ']=$error->getMessage();
            }
            $tabE= $this->serializer->encode($tabE,'json');

          return new JsonResponse($tabE,Response::HTTP_FORBIDDEN);
        }

        return true;
    }

}
