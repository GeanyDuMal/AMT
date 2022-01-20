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
    public function index($id,UserPasswordHasherInterface $passwordHasher,AssociationRepository $associationRepository,Request $request,ClientTypeRepository $clientTypeRepository,AssociationRoleRepository $associationRoleRepository,ClientRepository $clientRepository,EntityManagerInterface $manager,ValidatorInterface $validator): Response
    {
        if (!$this->isGranted('ROLE_PRESIDENT')){
            return $this->redirectToRoute('home');
        }
        $data = $request->request;
        $commonFunctions=new CommonClientMethods();
        $client = $clientRepository->find($id);

        $clientManager = new ClientManager($manager);
        $assosRoles = $associationRoleRepository->findAll();
        $validationErrors="";
        $errorLoginExist= "";
        $member=$associationRepository->findOneBy(["member"=>$client]);
        if ($data->count()> 0) {
            $commonFunctions->setData($client,$request,$clientTypeRepository,$passwordHasher);
            $validationErrors = $validator->validate($client);
            if($validationErrors->count()==0){
                $ChosenClientID=$clientRepository->find($id)->getLogin();
                /*
                 *  if admin changed the role of a member to another role
                 *  we have to change it too in association table
                 * -> if admin changed the type of a member to student it is manipulated by a trigger
                 *    called deleteFromAssosIfChangedToStudent
                 */
                $this->manageMember($associationRepository,$associationRoleRepository,$client,$request,$manager,$commonFunctions);
                if(strcmp($ChosenClientID,$client->getLogin())!=0 &&
                    $clientManager->loginExists($client)){
                    $errorLoginExist="Login Existe déja";
                }
                else{
                    $clientManager->persist($client);
                    if($client->getClientType()->getName()=="Association"){
                        $member=$associationRepository->findOneBy(["member"=>$client]);
                            if($member->getRole()->getName()=="President"){
                                $commonFunctions->removeOtherPresitents($manager,$member,$clientTypeRepository,$associationRepository,$clientRepository);
                        }
                    }
                    $request->query->get("Modification avec succés");
                    return $this->redirectToRoute('client_list',["message"=>"Modification avec succés"]);

                }
            }
        }

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
     * @param CommonClientMethods $commonFunctions
     * @return void
     */
    private function manageMember(AssociationRepository $associationRepository, AssociationRoleRepository $associationRoleRepository, Client $client, Request $request, EntityManagerInterface $manager, CommonClientMethods $commonFunctions)
    {
        $data=$request->request;
        $existeDansAssos=$associationRepository->findOneBy(['member'=>$client]);
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