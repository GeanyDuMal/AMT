<?php

namespace App\Controller;

use App\Manager\ClientManager;
use App\Manager\OrderedManager;
use App\Manager\ParameterManager;
use App\Manager\PasswordForgotRequestManager;
use App\Manager\PostManager;
use App\Manager\ProductManager;
use App\Repository\ClientRepository;
use App\Repository\MemberRepository;
use App\Repository\OrderedRepository;
use App\Repository\PasswordForgotRequestRepository;
use App\Repository\PostRepository;
use App\Repository\ProductRepository;
use App\Utils\Enum\ClientType;
use App\Utils\Enum\MemberRole;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class MenuManagementController extends AbstractController
{

    #[Route("/management", name: "menuManagement", methods: ["GET", "POST"])]
    public function index(EntityManagerInterface $manager, Request $request,
        UserPasswordHasherInterface $passwordHasher,
        ClientRepository $clientRepository, MemberRepository $memberRepository, PostRepository $postRepository,
        OrderedRepository $orderedRepository, ProductRepository $productRepository,
        PasswordForgotRequestRepository $passwordForgotRequestRepository): Response {
        if (!$this->isGranted(SymfonyRole::PRESIDENT)) {
            return $this->redirectToRoute("home");
        }

        $parameterManager = new ParameterManager($manager);
        $parameter = $parameterManager->getParameter();
        $data = $request->request;
        $message = null;

        /**
         * Purge des cotisants
         */
        if ($data->get("clearCotisant") != "" && $parameter->isCotisantActivated()) {
            $clientManager = new ClientManager($manager);
            $listCotisant = $clientRepository->findBy(["clientType" => ClientType::COTISANT]);

            foreach ($listCotisant as $cotisant) {
                $cotisant->setClientType(ClientType::ETUDIANT);
                $clientManager->persist($cotisant);
            }

            $message = "Les cotisants ont été purgés.";
        }

        /**
         * Purge de l'association
         */
        if ($data->get("clearMembers") != "") {
            $clientManager = new ClientManager($manager);
            $listTypeAssociation = $clientRepository->findBy(["clientType" => ClientType::ASSOCIATION]);

            $president = $memberRepository->findOneBy(["role" => MemberRole::PRESIDENT])->getClient();

            foreach ($listTypeAssociation as $client) {
                if ($client !== $president) {
                    $client->setClientType(ClientType::ETUDIANT);

                    $clientManager->persist($client);
                }
            }

            $message = "L'association a été purgée.";
        }

        /**
         * Purge des anciens clients
         */
        if ($data->get("clearOldClient") != "") {
            $listClient = $clientRepository->findClientWithoutOrderedTwoYears();
            $clientManager = new ClientManager($manager);

            $actualUsername = $this->getUser()->getUsername();

            foreach ($listClient as $client) {
                if ($client->getLogin() != $actualUsername) {
                    $clientManager->remove($client);
                }
            }

            $message = "Les clients sans commandes de moins de 2 ans et qui ont créé leur compte il y a 2 ans ont été supprimés";
        }

        /**
         * Purge des anciens produits
         */
        if ($data->get("clearProduct") != "") {
            $listProduct = $productRepository->findProductEmptyWithoutCommandOneYear();
            $productManager = new ProductManager($manager);

            foreach ($listProduct as $product) {
                $productManager->remove($product);
            }

            $message = "Les produits sans commandes de moins de 1 an dont le stock est vide ont été supprimés";
        }

        /**
         * Purge des anciennes commandes sans client
         */
        if ($data->get("clearOrder") != "") {
            $listOrdered = $orderedRepository->findOrderWithoutClientTwoYearsOld();
            $orderedManager = new OrderedManager($manager);

            foreach ($listOrdered as $ordered) {
                $orderedManager->remove($ordered);
            }

            $message = "Les commandes de plus de 2 ans sans client ont été supprimées.";
        }

        /**
         * Purge des 3 posts les plus anciens
         */
        if ($data->get("clearPost") != "" && $parameter->isPostActivated()) {
            $listPost = $postRepository->findBy([], ["id" => "ASC"], 3);
            $postManager = new PostManager($manager);

            foreach ($listPost as $post) {
                $postManager->remove($post);
            }

            $message = "Les 3 derniers post ont été supprimés.";
        }

        /**
         * Reinitialisation du mot de passe d'un client
         */
        if ($data->get("resetPassword") != "") {
            $passwordForgotRequestManager = new PasswordForgotRequestManager($manager);
            $confirmationCode = $data->get("confirmationCode");
            $passwordForgotRequest = $passwordForgotRequestRepository->findOneBy(["client" => $data->get("passwordRequest")]);


            if ($passwordForgotRequest && $passwordForgotRequestManager->verifyConfirmationCode($passwordForgotRequest, $confirmationCode)) {
                $passwordForgotRequestManager->generateNewPassword($passwordForgotRequest, $passwordHasher);

                $message = "Le nouveau mot de passe est \"" . $passwordForgotRequest->getConfirmationCode() . "\" Merci de le modifier à la prochaine connexion";
            }
        }

        /**
         * Suppression d'une demande de reinitialisation de mot de passe
         */
        if ($data->get("suppressPasswordRequest") != "") {
            $passwordForgotRequestManager = new PasswordForgotRequestManager($manager);

            $passwordForgotRequest = $passwordForgotRequestRepository->findOneBy(["client" => $data->get("passwordRequest")]);

            if ($passwordForgotRequest) {
                $passwordForgotRequestManager->remove($passwordForgotRequest);
                $message = "Le demande de reinitialisation de " . $passwordForgotRequest->getClient()->getName() . " " . $passwordForgotRequest->getClient()
                                                                                                                                               ->getFirstName() . " a été supprimé";
            }
        }

        $requestList = $passwordForgotRequestRepository->findBy([], ["date" => "DESC"]);

        return $this->render("management/MenuManagement.html.twig", [
            "parameter" => $parameter,
            "message" => $message,
            "requestList" => $requestList
        ]);
    }
}
