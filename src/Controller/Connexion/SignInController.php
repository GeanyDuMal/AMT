<?php

namespace App\Controller\Connexion;

use App\Entity\Client;
use App\Manager\ClientManager;
use App\Manager\ParameterManager;
use App\Utils\Enum\ClientTypeEnum;
use App\Utils\Enum\SymfonyRoleEnum;
use App\Utils\Exception\ApplicationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class SignInController extends AbstractController
{
    private EntityManagerInterface $manager;
    private ParameterManager $parameterManager;
    private ClientManager $clientManager;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(EntityManagerInterface $manager, UserPasswordHasherInterface $userPasswordHasher) {
        $this->manager = $manager;
        $this->parameterManager = new ParameterManager($this->manager);
        $this->clientManager = new ClientManager($this->manager);
        $this->userPasswordHasher = $userPasswordHasher;
    }

    #[Route("/signin", name: "signin", methods: ["GET", "POST"])]
    public function index(Request $request): Response
    {
        // Redirige vers le profil si deja connecté
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('profile');
        }

        $parameter = $this->parameterManager->getParameter();
        $data = $request->request;
        $client = new Client;
        $message = "";

        if ($data->count() > 0) {

            $this->clientManager->setData($client, $this->userPasswordHasher, strtoupper(trim($data->get("name"))),
                                          trim($data->get("firstName")), trim($data->get("login")),
                                          trim($data->get("password")), 0, ClientTypeEnum::ETUDIANT, null, 0);

            if (trim($data->get("password")) == trim($data->get("confirmPassword")) && $this->clientManager->verifyPassword(trim($data->get("password")))) {
                try {
                    $this->clientManager->persistClientIfNotExists($client);
                    return $this->redirectToRoute('login');
                } catch (ApplicationException $e) {
                    $message = $e->getCustomMessage();
                }
            } else {
                $message = "Merci de verifier les mots de passes";
            }
        }

        return $this->render('connexion/Signin.html.twig', [
            "parameter" => $parameter,
            "message" => $message
        ]);
    }
}
