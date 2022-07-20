<?php

namespace App\Controller;

use App\Entity\Client;
use App\Manager\AssociationManager;
use App\Manager\ClientManager;
use App\Manager\PostManager;
use App\Repository\AssociationRepository;
use App\Repository\ClientRepository;
use App\Repository\PostRepository;
use App\Utils\Enum\AssociationRole;
use App\Utils\Enum\ClientType;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ManagementController extends AbstractController
{
    /**
     * @Route("/management/", name="management")
     */
    public function index(EntityManagerInterface $manager, Request $request, ClientRepository $clientRepository,
        AssociationRepository $associationRepository, PostRepository $postRepository): Response
    {
        if (!$this->isGranted(SymfonyRole::PRESIDENT)){
            return $this->redirectToRoute('home');
        }

        $inputParameterBag = $request->request;
        $message = null;

        /**
         * Nettoyage des cotisants
         */
        if ($inputParameterBag->get("clearCotisant") != ""){
            $clientManager = new ClientManager($manager);
            $listCotisant = $clientRepository->findBy(["clientType" => ClientType::COTISANT]);

            foreach ($listCotisant as $cotisant){
                $cotisant->setClientType(ClientType::ETUDIANT);
                $clientManager->persist($cotisant);
            }

            $message = "Les cotisants ont été purgés.";
        }

        /**
         * Nettoyage de l'association
         */
        if ($inputParameterBag->get("clearAssociation") != ""){
            $clientManager = new ClientManager($manager);
            $listTypeAssociation = $clientRepository->findBy(["clientType" => ClientType::ASSOCIATION]);

            $president = $associationRepository->findOneBy(["role" => AssociationRole::PRESIDENT])->getMember();

            foreach ($listTypeAssociation as $client){
                if ($client !== $president){
                    $client->setClientType(ClientType::ETUDIANT);

                    $clientManager->persist($client);
                }
            }

            $message = "L'association a été purgée.";
        }

        /**
         * Nettoyage des anciens clients
         */
        if ($inputParameterBag->get("clearOldClient") != ""){

        }

        /**
         * Nettoyage des anciens produits
         */
        if ($inputParameterBag->get("clearProduct") != ""){

        }

        /**
         * Nettoyage des anciennes commandes sans client
         */
        if ($inputParameterBag->get("clearOrder") != ""){

        }

        /**
         * Nettoyage des 3 posts les plus anciens
         */
        if ($inputParameterBag->get("clearPost") != ""){
            $listPost = $postRepository->findBy([], ["id" => "ASC"]);
            $postManager = new PostManager($manager);

            for ($i = 0; $i < 3; $i++){
                $post = $listPost[$i];
                $postManager->remove($post);
            }

            $message = "Les 3 derniers post ont été supprimés.";
        }

        return $this->render('management/management.html.twig', [
            "message" => $message
        ]);
    }
}
