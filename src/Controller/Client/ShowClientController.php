<?php

namespace App\Controller\Client;

use App\Manager\ParameterManager;
use App\Repository\ClientRepository;
use App\Repository\MemberRepository;
use App\Utils\Enum\ClientType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

class ShowClientController extends AbstractController
{
    
    #[Route("/admin/client/show/{!id}", name: "showClient", methods: ["GET"])]
    public function index($id, ClientRepository $clientRepository, MemberRepository $memberRepository, EntityManagerInterface $manager): Response {
        $parameterManager = new ParameterManager($manager);
        $parameter = $parameterManager->getParameter();
        $client = $clientRepository->find($id);

        if ($client) {
            $member = $memberRepository->findOneBy(["client" => $client]);

            return $this->render('client/ShowClient.html.twig', [
                "parameter" => $parameter,
                "client" => $client,
                "member" => $member,
            ]);
        } else {
            return $this->redirectToRoute('menuClient');
        }
    }
}
