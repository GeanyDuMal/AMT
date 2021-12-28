<?php

namespace App\Controller\Client;
use App\Entity\AssociationRole;
use App\Entity\Client;
use App\Manager\ClientManager;
use App\Repository\AssociationRepository;
use App\Repository\AssociationRoleRepository;
use App\Repository\ClientRepository;
use App\Repository\ClientTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EditClientController extends AbstractController
{
    /**
     * @Route("/admin/client/edit/{id}", name="edit_client",methods={"GET", "POST"} )
     */
    public function index(UserPasswordHasherInterface $passwordHasher,AssociationRepository $associationRepository,Request $request,ClientTypeRepository $clientTypeRepository,AssociationRoleRepository $associationRoleRepository,ClientRepository $clientRepository,$id,EntityManagerInterface $manager,ValidatorInterface $validator): Response
    {
        if (!$this->isGranted('ROLE_PRESIDENT')){
            return $this->redirectToRoute('home');
        }
        $data = $request->request;
        $commonFunctions=new CommonFunctions();
        $client = $clientRepository->find($id);

        $clientManager = new ClientManager($manager);
        $assosRoles = $associationRoleRepository->findAll();
        $validationErrors="";
        $errorLoginExist= "";

        if ($data->count()> 0) {
            $commonFunctions->setData($client,$request,$clientTypeRepository,$passwordHasher);
            $validationErrors = $validator->validate($client);
            if($validationErrors->count()==0){
                $ChosenClientID=$clientRepository->find($id)->getLogin();
                $this->manageMember($associationRepository,$associationRoleRepository,$client,$request,$manager,$commonFunctions);
                if(strcmp($ChosenClientID,$client->getLogin())!=0 &&
                    $clientManager->loginExists($client)){
                    $errorLoginExist="Login Existe déja";
                }
                else{
                    $clientManager->persist($client);
                    $request->query->get("Modification avec succés");
                    return $this->redirectToRoute('client_list_message',["message"=>"Modification avec succés"]);

                }
            }
        }
        $member=$associationRepository->findOneBy(["member"=>$client]);
        return $this->render('client/EditModalClient.html.twig',
            [
                'assosRoles' => $assosRoles,
                'validationErrors' => $validationErrors,
                'errorLoginExist' => $errorLoginExist,
                'client' => $client,
                'member' => $member
            ]
        );
    }

    /**
     * @param AssociationRepository $associationRepository
     * @param AssociationRoleRepository $associationRoleRepository
     * @param Client $client
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param CommonFunctions $commonFunctions
     * @return void
     */
    private function manageMember(AssociationRepository $associationRepository, AssociationRoleRepository $associationRoleRepository, Client $client, Request $request, EntityManagerInterface $manager, CommonFunctions $commonFunctions)
    {
        $data=$request->request;
        $existeDansAssos=$associationRepository->findOneBy(['member'=>$client]);
        /*
         * if the admin changed the type of the client from association to etudiant
         * then we have to remove this client from association
         * else if he only changed his role
         * we have to change it too in association table
         * */
        if($client->getClientType()->getName()=="Etudiant"){
            if($existeDansAssos){
                $manager->remove($existeDansAssos);
                $manager->flush();
            }
        }else{
            if($existeDansAssos){
                $existeDansAssos->setRole($associationRoleRepository->findOneBy(["name"=>$data->get('assosRoles')]));
            }
            else{
                $existeDansAssos=$commonFunctions->makeMember($client,$associationRoleRepository,$request);
            }
            $manager->persist($existeDansAssos);
            $manager->flush();
        }
    }
}