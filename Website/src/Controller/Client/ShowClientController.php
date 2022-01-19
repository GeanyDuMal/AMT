<?php

namespace App\Controller\Client;

use App\Repository\ClientRepository;
use App\Repository\ClientTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Client;
use App\Entity\ClientType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Manager\ClientManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ShowClientController extends AbstractController
{
    /**
     * @Route("/admin/client/{message}", name="client_list",methods={"GET", "POST"} )
     */
    public function show(string $message = null,EntityManagerInterface $manager): Response
    {
        if (!$this->isGranted('ROLE_PRESIDENT')){
            return $this->redirectToRoute('home');
        }
        $clients = $manager->getRepository(Client::class)->findAll();
        return $this->render('client/index.html.twig', [
            'clients' => $clients,
            'message'=>$message
        ]);
    }

}
