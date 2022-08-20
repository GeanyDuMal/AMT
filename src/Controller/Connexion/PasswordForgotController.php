<?php

namespace App\Controller\Connexion;

use App\Entity\PasswordForgotRequest;
use App\Manager\ClientManager;
use App\Manager\PasswordForgotRequestManager;
use App\Repository\ClientRepository;
use App\Utils\Enum\SymfonyRole;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PasswordForgotController extends AbstractController
{

    /**
     * @Route("/profile/passwordForgot", name="passwordForgot")
     */
    public function index(EntityManagerInterface $manager, ClientRepository $clientRepository, Request $request): Response
    {
        $message = null;
        $inputParameterBag = $request->request;
        $clientName = $inputParameterBag->get('clientName');
        $clientFirstName = $inputParameterBag->get('clientFirstName');
        $clientLogin = $inputParameterBag->get('clientLogin');

        if ($clientName && $clientFirstName && $clientLogin){
            $client = $clientRepository->findOneBy([
                "name" => $clientName,
                "firstName" => $clientFirstName,
                "login" => $clientLogin
            ]);

            if ($client){
                $passwordForgotRequestManager = new PasswordForgotRequestManager($manager);
                $passwordForgotRequest = new PasswordForgotRequest();

                $passwordForgotRequestManager->removeOldIfExist($client);

                $passwordForgotRequestManager->generateCode($passwordForgotRequest);
                $passwordForgotRequest->setClient($client)
                    ->setDate(new DateTime('now'));

                $passwordForgotRequestManager->persist($passwordForgotRequest);

                $message = "Votre demande de mot de passe à été créée. Voici le code à conserver : "
                    . $passwordForgotRequest->getConfirmationCode() .
                    ". \n Merci de vous rapprocher de l'administrateur ou du président de l'association afin que ce dernier
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