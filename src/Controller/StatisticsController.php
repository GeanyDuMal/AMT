<?php

namespace App\Controller;

use App\Manager\OrderedManager;
use App\Manager\ParameterManager;
use App\Repository\ClientRepository;
use App\Repository\OrderedRepository;
use App\Repository\PostRepository;
use App\Repository\ProductRepository;
use App\Utils\Enum\ClientType;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatisticsController extends AbstractController
{

    #[Route("/statistics", name: "statistics", methods: ["GET"])]
    public function index(ProductRepository $productRepository, PostRepository $postRepository, OrderedRepository $orderedRepository,
                          ClientRepository $clientRepository, EntityManagerInterface $manager): Response
    {
        if (!$this->isGranted(SymfonyRole::ASSOC)) {
            return $this->redirectToRoute("home");
        }

        $parameterManager = new ParameterManager($manager);
        $parameter = $parameterManager->getParameter();
        $orderedManager = new OrderedManager($manager);
        $countThisWeeksCommands = $orderedRepository->quantityThisWeeksCommands()["number"];
        $salesRevenueThisYear = 0;
        $salesRevenueThisMonth = 0;
        $salesRevenueThisWeek = 0;
        $yearOrderedList = $orderedRepository->thisYearOrdered();
        $monthOrderedList = $orderedRepository->thisMonthOrdered();
        $weekOrderedList = $orderedRepository->thisWeekOrdered();
        $topSoldProduct = $productRepository->findTopSoldProductThisMonth();
        $productsWarningStock = $productRepository->findAllWarningStock();
        $productsEmptyStock = $productRepository->findAllEmptyStock();
        $countCotisant = count($clientRepository->findBy(["clientType" => ClientType::COTISANT]));
        $countClients = count($clientRepository->findAll());
        $postsNumber = count($postRepository->findAll());

        // Build the amount of purchase
        foreach ($yearOrderedList as $ordered) {
            $salesRevenueThisYear += $orderedManager->montantTotal($ordered);
        }

        foreach ($monthOrderedList as $ordered) {
            $salesRevenueThisMonth += $orderedManager->montantTotal($ordered);
        }

        foreach ($weekOrderedList as $ordered) {
            $salesRevenueThisWeek += $orderedManager->montantTotal($ordered);
        }

        if ($countThisWeeksCommands == 0) {
            $averagePerStudent = 0;
        } else {
            $averagePerStudent = $salesRevenueThisWeek / $countThisWeeksCommands;
        }

        return $this->render("statistics/Statistics.html.twig", [
            "parameter" => $parameter,
            "productsWarningStock" => $productsWarningStock,
            "topSoldProduct" => $topSoldProduct,
            "productsEmptyStock" => $productsEmptyStock,
            "countCotisant" => $countCotisant,
            "countClients" => $countClients,
            "salesRevenueThisWeek" => $salesRevenueThisWeek,
            "salesRevenueThisMonth" => $salesRevenueThisMonth,
            "salesRevenueThisYear" => $salesRevenueThisYear,
            "averagePerStudent" => $averagePerStudent,
            "postsNumber" => $postsNumber
        ]);
    }
}
