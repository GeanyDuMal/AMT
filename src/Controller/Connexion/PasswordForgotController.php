<?php

namespace App\Controller\Connexion;

use App\Entity\PasswordForgotRequest;
use App\Manager\ClientManager;
use App\Manager\PasswordForgotRequestManager;
use App\Repository\ClientRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PasswordForgotController extends AbstractController
{
    private EntityManagerInterface $manager;
    private PasswordForgotRequestManager $passwordForgotRequestManager;
    private ClientRepository $clientRepository;


    public function __construct(EntityManagerInterface $manager, ClientRepository $clientRepository) {
        $this->manager = $manager;
        $this->passwordForgotRequestManager = new PasswordForgotRequestManager($this->manager);
        $this->clientRepository = $clientRepository;
    }

    /**
     * @Route("/profile/passwordForgot", name="passwordForgot")
     */
    #[Route("/profile/passwordForgot", name: "passwordForgot", methods: ["GET", "POST"])]
    public function index(Request $request): Response
    {
        $message = null;
        $inputParameterBag = $request->request;
        $clientName = $inputParameterBag->get('clientName');
        $clientFirstName = $inputParameterBag->get('clientFirstName');
        $clientLogin = $inputParameterBag->get('clientLogin');

        if ($clientName && $clientFirstName && $clientLogin){

            $client = $this->clientRepository->findOneBy([
                "name" => trim(strtoupper($clientName)),
                "firstName" => trim($clientFirstName),
                "login" => trim($clientLogin)
            ]);

            if ($client){
                $passwordForgotRequest = new PasswordForgotRequest();

                $this->passwordForgotRequestManager->removeOldIfExist($client);

                $this->passwordForgotRequestManager->generateCode($passwordForgotRequest);
                $passwordForgotRequest->setClient($client)
                    ->setDate(new DateTime('now'));

                $this->passwordForgotRequestManager->persist($passwordForgotRequest);

                $message = "Votre demande de mot de passe à été créée. Voici le code à conserver : "
                    . $passwordForgotRequest->getConfirmationCode() .
                    " Merci de vous rapprocher de l'administrateur ou du président de l'association afin que ce dernier
                     vous transmette votre nouveau mot de passe";
            } else {
                $message = "Les informations saisient ne correspondent à aucun client";
            }
        }

        return $this->render('connexion/PasswordForgot.html.twig', [
            "message" => $message,
        ]);
    }
}