<?php

namespace App\Controller\Client;

use App\Entity\Client;
use App\Manager\AssociationManager;
use App\Manager\ClientManager;
use App\Repository\AssociationRepository;
use App\Repository\ClientRepository;
use App\Utils\Enum\AssociationRole;
use App\Utils\Enum\ClientType;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use http\Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EditClientController extends AbstractController
{
    /**
     * @Route("/admin/client/edit/{!id}", name="editClient", methods={"GET", "POST"} )
     */
    public function index($id, UserPasswordHasherInterface $passwordHasher, Request $request, EntityManagerInterface $manager,
                          AssociationRepository $associationRepository, ClientRepository $clientRepository): Response
    {
        if (!$this->isGranted(SymfonyRole::SECRETAIRE)) {
            return $this->redirectToRoute('home');
        }

        $data = $request->request;
        $client = $clientRepository->find($id);
        $user = $clientRepository->findOneBy(["login" => $this->getUser()->getUserIdentifier()]);
        $member = $associationRepository->findOneBy(["member" => $client]);
        $associationManager = new AssociationManager($manager);
        $assosRoles = $associationManager->getLowerOrEqualAssociationRole($user);
        $message = "";
        $allowEdit = $this->isGranted($client->getRoles()[0]);

        if ($allowEdit && $data->count() > 0) {
            $clientManager = new ClientManager($manager);

            $clientManager->setData($client, $passwordHasher, $data->get("name"),
                $data->get("firstName"), $client->getLogin(), $data->get("password"),
                $data->get("balance"), $data->get("clientType"), $data->get("assosRoles"), $data->get("fidelityPoint"));


            // Verify the confirmity of a client and verify that the login correspond to the stored one
            if ($clientManager->verifyClient($client) && strcmp($client->getLogin(), $data->get('login'))) {
                /*
                 * If we set the ClientType Association, we need to put the client in the table Association
                 */
                if ($data->get("clientType") == ClientType::ASSOCIATION) {

                    /*
                     *  if admin changed the role of a member to another role
                     *  we have to change it too in association table
                     */
                    $this->manageMember($associationManager, $associationRepository, $client, $request->get('assosRoles'));
                }

                $clientManager->persist($client);
                if ($client->getClientType() == ClientType::ASSOCIATION) {
                    $member = $associationRepository->findOneBy(["member" => $client]);
                    if ($member->getRole() == AssociationRole::PRESIDENT) {
                        $associationManager->removeOtherPresidents($member);
                    }
                }

                return $this->redirectToRoute('menuClient', [
                    "message" => "Modification effectué avec succès"
                ]);
            }
        } else if (!$allowEdit){
            return $this->redirectToRoute('menuClient',[
                'message' => 'Vous n\'avez pas l\'autorisation de modifier ce client',
            ]);
        }

        return $this->render('client/EditClient.html.twig', [
            'assosRoles' => $assosRoles,
            'message' => $message,
            'client' => $client,
            'member' => $member,
            'allowEdit' => $allowEdit,
            'clientTypes' => ClientType::getAll()
        ]);
    }

    /**
     * @param AssociationManager $associationManager
     * @param AssociationRepository $associationRepository
     * @param Client $client
     * @param string $roleAssociation
     * @return void
     */
    private function manageMember(AssociationManager $associationManager, AssociationRepository $associationRepository, Client $client, string $roleAssociation): void
    {
        $clientMember = $associationRepository->findOneBy(['member' => $client]);

        if ($clientMember) {
            $clientMember->setRole($roleAssociation);
        } else {
            $clientMember = $associationManager->makeMember($client, $roleAssociation);
        }
        $associationManager->persist($clientMember);
    }
}