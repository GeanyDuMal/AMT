<?php

namespace App\Controller\Client;

use App\Repository\MemberRepository;
use App\Repository\ClientRepository;
use App\Utils\Enum\ClientType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowClientController extends AbstractController
{
    /**
     * @Route("/admin/client/show/{!id}", name="showClient")
     */
    public function index($id, ClientRepository $clientRepository, MemberRepository $memberRepository): Response
    {
        $client = $clientRepository->find($id);

        if ($client) {
            $member = $memberRepository->findOneBy(["client" => $client]);

            return $this->render('client/ShowClient.html.twig', [
                'client' => $client,
                'clientTypes' => ClientType::getAll(),
                'member' => $member,
            ]);
        } else {
            return $this->redirectToRoute('menuClient');
        }
    }
}
