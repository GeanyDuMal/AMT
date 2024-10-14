<?php

namespace App\Controller\Client;

use App\Manager\ParameterManager;
use App\Repository\ClientRepository;
use App\Repository\MemberRepository;
use App\Utils\Enum\ClientTypeEnum;
use App\Utils\Enum\SymfonyRoleEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowClientController extends AbstractController
{
    private EntityManagerInterface $manager;
    private ParameterManager $parameterManager;
    private ClientRepository $clientRepository;
    private MemberRepository $memberRepository;

    public function __construct(ClientRepository $clientRepository, MemberRepository $memberRepository, EntityManagerInterface $manager) {
        $this->manager = $manager;
        $this->parameterManager = new ParameterManager($this->manager);
        $this->clientRepository = $clientRepository;
        $this->memberRepository = $memberRepository;
    }

    #[Route("/admin/client/show/{!id}", name: "showClient", methods: ["GET"])]
    public function index($id): Response {
        if (!$this->isGranted(SymfonyRoleEnum::ASSOC->value)) {
            return $this->redirectToRoute('home');
        }

        $parameter = $this->parameterManager->getParameter();
        $client = $this->clientRepository->find($id);

        if ($client) {
            $member = $this->memberRepository->findOneBy(["client" => $client]);

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
