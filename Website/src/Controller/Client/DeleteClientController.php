<?php

namespace App\Controller\Client;

use App\Manager\ClientManager;
use App\Repository\ClientRepository;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DeleteClientController extends AbstractController
{

    /**
     * @Route("/admin/client/delete/{!id}", name="deleteClient", methods={"GET", "DELETE"})
     */
    public function index($id, EntityManagerInterface $manager, ClientRepository $clientRepository)
    {
        if (!$this->isGranted(SymfonyRole::PRESIDENT)) {
            return $this->redirectToRoute('home');
        }

        /*
         * when we delete a client
         * we delete it from association too
         * -> manipulated by a trigger called : deleteFromAssosIfMemberDeleted
         */
        $clientManager = new ClientManager($manager);

        $client = $clientRepository->find($id);
        $clientManager->remove($client);
        return new JsonResponse(true);
    }
}