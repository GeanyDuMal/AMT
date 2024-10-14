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
use App\Utils\Enum\ClientTypeEnum;
use App\Utils\Enum\MemberRoleEnum;
use App\Utils\Enum\SymfonyRoleEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class MenuManagementController extends AbstractController {
    private EntityManagerInterface $manager;
    private OrderedManager $orderedManager;
    private ParameterManager $parameterManager;
    private PostManager $postManager;
    private ProductManager $productManager;
    private ClientManager $clientManager;
    private PasswordForgotRequestManager $passwordForgotRequestManager;
    private ClientRepository $clientRepository;
    private ProductRepository $productRepository;
    private PasswordForgotRequestRepository $passwordForgotRequestRepository;
    private OrderedRepository $orderedRepository;
    private PostRepository $postRepository;
    private MemberRepository $memberRepository;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(EntityManagerInterface $manager, UserPasswordHasherInterface $userPasswordHasher, ClientRepository $clientRepository,
        MemberRepository $memberRepository, PostRepository $postRepository, OrderedRepository $orderedRepository, ProductRepository $productRepository,
        PasswordForgotRequestRepository $passwordForgotRequestRepository) {
        $this->manager = $manager;
        $this->parameterManager = new ParameterManager($this->manager);
        $this->postManager = new PostManager($this->manager);
        $this->orderedManager = new OrderedManager($this->manager);
        $this->productManager = new ProductManager($this->manager);
        $this->clientManager = new ClientManager($this->manager);
        $this->passwordForgotRequestManager = new PasswordForgotRequestManager($this->manager);
        $this->productRepository = $productRepository;
        $this->clientRepository = $clientRepository;
        $this->memberRepository = $memberRepository;
        $this->orderedRepository = $orderedRepository;
        $this->passwordForgotRequestRepository = $passwordForgotRequestRepository;
        $this->postRepository = $postRepository;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    #[Route("/management", name: "menuManagement", methods: ["GET", "POST"])]
    public function index(Request $request): Response {
        if (!$this->isGranted(SymfonyRoleEnum::PRESIDENT->value)) {
            return $this->redirectToRoute("home");
        }

        $parameter = $this->parameterManager->getParameter();
        $data = $request->request;
        $message = null;

        /**
         * Purge des cotisants
         */
        if ($data->get("clearCotisant") != "" && $parameter->isCotisantActivated()) {
            $listCotisant = $this->clientRepository->findBy(["clientType" => ClientTypeEnum::COTISANT]);

            foreach ($listCotisant as $cotisant) {
                $cotisant->setClientType(ClientTypeEnum::ETUDIANT);
                $this->clientManager->persist($cotisant);
            }

            $message = "Les cotisants ont été purgés.";
        }

        /**
         * Purge de l'association
         */
        if ($data->get("clearMembers") != "") {
            $listTypeAssociation = $this->clientRepository->findBy(["clientType" => ClientTypeEnum::ASSOCIATION]);

            $president = $this->memberRepository->findOneBy(["role" => MemberRoleEnum::PRESIDENT])->getClient();

            foreach ($listTypeAssociation as $client) {
                if ($client !== $president) {
                    $client->setClientType(ClientTypeEnum::ETUDIANT);

                    $this->clientManager->persist($client);
                }
            }

            $message = "L'association a été purgée.";
        }

        /**
         * Purge des anciens clients
         */
        if ($data->get("clearOldClient") != "") {
            $listClient = $this->clientRepository->findClientWithoutOrderedTwoYears();

            $actualUsername = $this->getUser()->getUsername();

            foreach ($listClient as $client) {
                if ($client->getLogin() != $actualUsername) {
                    $this->clientManager->remove($client);
                }
            }

            $message = "Les clients sans commandes de moins de 2 ans et qui ont créé leur compte il y a 2 ans ont été supprimés";
        }

        /**
         * Purge des anciens produits
         */
        if ($data->get("clearProduct") != "") {
            $listProduct = $this->productRepository->findProductEmptyWithoutCommandOneYear();

            foreach ($listProduct as $product) {
                $this->productManager->remove($product);
            }

            $message = "Les produits sans commandes de moins de 1 an dont le stock est vide ont été supprimés";
        }

        /**
         * Purge des anciennes commandes sans client
         */
        if ($data->get("clearOrderUnpaid") != "") {
            $listOrdered = $this->orderedRepository->findOrderUnpaidLastMonth();

            foreach ($listOrdered as $ordered) {
                $this->orderedManager->remove($ordered);
            }

            $message = "Les commandes de plus d'une semaine non payée ont été supprimées";
        }

        /**
         * Purge des 3 posts les plus anciens
         */
        if ($data->get("clearPost") != "" && $parameter->isPostActivated()) {
            $listPost = $this->postRepository->findBy([], ["id" => "ASC"], 3);

            foreach ($listPost as $post) {
                $this->postManager->remove($post);
            }

            $message = "Les 3 derniers post ont été supprimés.";
        }

        /**
         * Reinitialisation du mot de passe d'un client
         */
        if ($data->get("resetPassword") != "") {
            $confirmationCode = $data->get("confirmationCode");
            $passwordForgotRequest = $this->passwordForgotRequestRepository->findOneBy(["client" => $data->get("passwordRequest")]);


            if ($passwordForgotRequest && $this->passwordForgotRequestManager->verifyConfirmationCode($passwordForgotRequest, $confirmationCode)) {
                $this->passwordForgotRequestManager->generateNewPassword($passwordForgotRequest, $this->userPasswordHasher);

                $message = "Le nouveau mot de passe est \"" . $passwordForgotRequest->getConfirmationCode() . "\" Merci de le modifier à la prochaine connexion";
            }
        }

        /**
         * Suppression d'une demande de reinitialisation de mot de passe
         */
        if ($data->get("suppressPasswordRequest") != "") {

            $passwordForgotRequest = $this->passwordForgotRequestRepository->findOneBy(["client" => $data->get("passwordRequest")]);

            if ($passwordForgotRequest) {
                $this->passwordForgotRequestManager->remove($passwordForgotRequest);
                $message = "Le demande de reinitialisation de " . $passwordForgotRequest->getClient()->getName() . " " . $passwordForgotRequest->getClient()
                                                                                                                                               ->getFirstName() . " a été supprimé";
            }
        }

        $requestList = $this->passwordForgotRequestRepository->findBy([], ["date" => "DESC"]);

        return $this->render("management/MenuManagement.html.twig", [
            "parameter" => $parameter,
            "message" => $message,
            "requestList" => $requestList
        ]);
    }
}
