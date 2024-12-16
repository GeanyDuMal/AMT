<?php

namespace App\Controller\Ordered;

use App\Manager\OrderedManager;
use App\Manager\ParameterManager;
use App\Repository\OrderedRepository;
use App\Utils\Enum\SymfonyRoleEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuOrderedController extends AbstractController
{
    private EntityManagerInterface $manager;
    private ParameterManager $parameterManager;
    private OrderedRepository $orderedRepository;

    public function __construct(EntityManagerInterface $manager, OrderedRepository $orderedRepository) {
        $this->manager = $manager;
        $this->parameterManager = new ParameterManager($this->manager);
        $this->orderedRepository = $orderedRepository;
    }

    #[Route("/ordered/menu/{message?}", name: "menuOrdered", methods: ["GET", "POST"])]
    public function menu(string $message = null): Response
    {
        if (!$this->isGranted(SymfonyRoleEnum::TRESORIER->value)) {
            return $this->redirectToRoute('home');
        }

        /*
         * Recuperer toute les commandes avec leurs clients et leurs types
         * Tout faire en une seule requetes, plus opti
         */
        $allOrder = $this->orderedRepository->findAllOrderAndClientAndClientType();
        $parameter = $this->parameterManager->getParameter();

        return $this->render('ordered/MenuOrdered.html.twig', [
            "parameter" => $parameter,
            "user" => $this->getUser(),
            "message" => $message,
            "orderedList" => $allOrder,
        ]);
    }
}
