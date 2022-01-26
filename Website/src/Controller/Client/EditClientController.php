<?php

namespace App\Controller\Client;

use App\Entity\AssociationRole;
use App\Entity\Client;
use App\Manager\AssociationManager;
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
    public function index($id, UserPasswordHasherInterface $passwordHasher, Request $request, EntityManagerInterface $manager, 
                          ValidatorInterface $validator, AssociationRepository $associationRepository, ClientTypeRepository $clientTypeRepository,
                          AssociationRoleRepository $associationRoleRepository, ClientRepository $clientRepository): Response
    {
        if (!$this->isGranted('ROLE_PRESIDENT')){
            return $this->redirectToRoute('home');
        }

        $data = $request->request;
        $client = $clientRepository->find($id);
        $assosRoles = $associationRoleRepository->findAll();
        $validationErrors = "";
        $errorLoginExist = "";
        $member = $associationRepository->findOneBy(["member" => $client]);

        if ($data->count() > 0){
            $clientManager = new ClientManager($manager);
            $associationManager = new AssociationManager($manager);

            $clientManager->setData($client, $clientTypeRepository, $passwordHasher, $data->get("name"),
                $data->get("firstName"), $data->get("login"), $data->get("password"),
                $data->get("balance"), $data->get("assosRoles"),  $data->get("clientType"));

            $validationErrors = $validator->validate($client);

            if($validationErrors->count() == 0){
                $ChosenClientLogin = $clientRepository->find($id)->getLogin();

                /*
                 * If we set the ClientType Association, we need to put the client in the table Association
                 */
                if ($data->get("clientType") == "Association"){
                    $roleName = $request->get('assosRoles');
                    $roleAssociation = $associationRoleRepository->findOneBy(["name" => $roleName]);

                    /*
                     *  if admin changed the role of a member to another role
                     *  we have to change it too in association table
                     * -> if admin changed the type of a member to student it is manipulated by a trigger
                     *    called deleteFromAssosIfChangedToStudent
                     */
                    $this->manageMember($manager, $clientManager, $associationRepository, $client, $roleAssociation);
                }

                if(strcmp($ChosenClientLogin,$client->getLogin()) != 0 && $clientManager->loginExists($client)){
                    $errorLoginExist="Login Existe déja";
                }
                else{
                    $clientManager->persist($client);
                    if($client->getClientType()->getName() == "Association"){
                        $member = $associationRepository->findOneBy(["member"=>$client]);
                        if($member->getRole()->getName() == "President"){
                            $associationManager->removeOtherPresidents($manager, $member, $clientTypeRepository, $clientRepository);
                        }
                    }
                    return $this->redirectToRoute('client_list',["message"=>"Modification avec succés"]);

                }
            }
        }

        return $this->render('client/EditModalClient.html.twig', [
                'assosRoles' => $assosRoles,
                'validationErrors' => $validationErrors,
                'errorLoginExist' => $errorLoginExist,
                'client' => $client,
                'member' => $member
            ]
        );
    }

    /**
     * @param EntityManagerInterface $manager
     * @param ClientManager $clientManager
     * @param AssociationRepository $associationRepository
     * @param Client $client
     * @param AssociationRole $roleAssociation
     * @return void
     */
    private function manageMember(EntityManagerInterface $manager, ClientManager $clientManager, AssociationRepository $associationRepository, Client $client, AssociationRole $roleAssociation)
    {
        $clientMember = $associationRepository->findOneBy(['member' => $client]);

        if($clientMember){
            $clientMember->setRole($roleAssociation);
        }
        else{
            $clientMember = $clientManager->makeMember($client, $roleAssociation);
        }
        $manager->persist($clientMember);
        $manager->flush();
    }
}