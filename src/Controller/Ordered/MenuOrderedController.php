<?php

namespace App\Controller\Ordered;

use App\Manager\OrderedManager;
use App\Manager\ParameterManager;
use App\Repository\OrderedRepository;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuOrderedController extends AbstractController
{
    /**
     * @Route("/ordered/menu/{message?}", name="menuOrdered")
     */
    public function menu(EntityManagerInterface $manager, OrderedRepository $orderedRepository, string $message = null): Response
    {
        if (!$this->isGranted(SymfonyRole::TRESORIER)) {
            return $this->redirectToRoute('home');
        }

        /*
         * Recuperer toute les commandes avec leurs clients et leurs types
         * Tout faire en une seule requetes, plus opti
         */
        $allOrder = $orderedRepository->findAllOrderAndClientAndClientType();
        $parameterManager = new ParameterManager($manager);
        $parameter = $parameterManager->getParameter();

        return $this->render('ordered/MenuOrdered.html.twig', [
            "parameter" => $parameter,
            "user" => $this->getUser(),
            "message" => $message,
            "orderedList" => $allOrder,
        ]);
    }
}
