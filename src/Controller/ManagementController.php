<?php

namespace App\Controller;

use App\Manager\ClientManager;
use App\Manager\OrderedManager;
use App\Manager\PostManager;
use App\Manager\ProductManager;
use App\Repository\AssociationRepository;
use App\Repository\ClientRepository;
use App\Repository\OrderedRepository;
use App\Repository\PostRepository;
use App\Repository\ProductRepository;
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
     * @Route("/management/", name="menuManagement")
     */
    public function index(EntityManagerInterface $manager, Request $request, ClientRepository $clientRepository,
        AssociationRepository $associationRepository, PostRepository $postRepository, OrderedRepository $orderedRepository,
        ProductRepository $productRepository): Response
    {
        if (!$this->isGranted(SymfonyRole::PRESIDENT)){
            return $this->redirectToRoute('home');
        }

        $inputParameterBag = $request->request;
        $message = null;

        /**
         * Purge des cotisants
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
         * Purge de l'association
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
         * Purge des anciens clients
         */
        if ($inputParameterBag->get("clearOldClient") != ""){
            $listClient = $clientRepository->findClientWithoutOrderedTwoYears();
            $clientManager = new ClientManager($manager);

            foreach ($listClient as $client){
                $clientManager->remove($client);
            }

            $message = "Les clients sans commandes de moins de 2 ans ont été supprimés";
        }

        /**
         * Purge des anciens produits
         */
        if ($inputParameterBag->get("clearProduct") != ""){
            $listProduct = $productRepository->findProductEmptyWithoutCommandOneYear();
            $productManager = new ProductManager($manager);

            foreach ($listProduct as $product){
                $productManager->remove($product);
            }

            $message = "Les produits sans commandes de moins de 1 an dont le stock est vide ont été supprimés";
        }

        /**
         * Purge des anciennes commandes sans client
         */
        if ($inputParameterBag->get("clearOrder") != ""){
            $listOrdered = $orderedRepository->findOrderWithoutClientTwoYearsOld();
            $orderedManager = new OrderedManager($manager);

            foreach ($listOrdered as $ordered){
                $orderedManager->remove($ordered);
            }

            $message = "Les commandes de plus de 2 ans sans client ont été supprimées.";
        }

        /**
         * Purge des 3 posts les plus anciens
         */
        if ($inputParameterBag->get("clearPost") != ""){
            $listPost = $postRepository->findBy([], ["id" => "ASC"], 3);
            $postManager = new PostManager($manager);

            foreach ($listPost as $post){
                $postManager->remove($post);
            }

            $message = "Les 3 derniers post ont été supprimés.";
        }

        return $this->render('management/MenuManagement.html.twig', [
            "message" => $message
        ]);
    }
}
