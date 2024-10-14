<?php

namespace App\Controller;

use App\Manager\OrderedManager;
use App\Manager\ParameterManager;
use App\Repository\ClientRepository;
use App\Repository\OrderedRepository;
use App\Repository\PostRepository;
use App\Repository\ProductRepository;
use App\Utils\Enum\ClientTypeEnum;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatisticsController extends AbstractController {
    private EntityManagerInterface $manager;
    private ParameterManager $parameterManager;
    private OrderedManager $orderedManager;
    private ClientRepository $clientRepository;
    private PostRepository $postRepository;
    private ProductRepository $productRepository;
    private OrderedRepository $orderedRepository;

    public function __construct(ProductRepository $productRepository, PostRepository $postRepository, OrderedRepository $orderedRepository,
        ClientRepository $clientRepository, EntityManagerInterface $manager) {
        $this->manager = $manager;
        $this->parameterManager = new ParameterManager($this->manager);
        $this->orderedManager = new OrderedManager($this->manager);
        $this->clientRepository = $clientRepository;
        $this->postRepository = $postRepository;
        $this->productRepository = $productRepository;
        $this->orderedRepository = $orderedRepository;
    }

    #[Route("/statistics", name: "statistics", methods: ["GET"])]
    public function index(): Response {
        if (!$this->isGranted(SymfonyRole::ASSOC)) {
            return $this->redirectToRoute("home");
        }

        $parameter = $this->parameterManager->getParameter();
        $countThisWeeksCommands = $this->orderedRepository->quantityThisWeeksCommands()["number"];
        $salesRevenueThisYear = 0;
        $salesRevenueThisMonth = 0;
        $salesRevenueThisWeek = 0;
        $yearOrderedList = $this->orderedRepository->thisYearOrdered();
        $monthOrderedList = $this->orderedRepository->thisMonthOrdered();
        $weekOrderedList = $this->orderedRepository->thisWeekOrdered();
        $topSoldProduct = $this->productRepository->findTopSoldProductThisMonth();
        $productsWarningStock = $this->productRepository->findAllWarningStock();
        $productsEmptyStock = $this->productRepository->findAllEmptyStock();
        $countCotisant = count($this->clientRepository->findBy(["clientType" => ClientTypeEnum::COTISANT]));
        $countClients = count($this->clientRepository->findAll());
        $postsNumber = count($this->postRepository->findAll());

        // Build the amount of purchase
        foreach ($yearOrderedList as $ordered) {
            $salesRevenueThisYear += $this->orderedManager->getMontantTotal($ordered);
        }

        foreach ($monthOrderedList as $ordered) {
            $salesRevenueThisMonth += $this->orderedManager->getMontantTotal($ordered);
        }

        foreach ($weekOrderedList as $ordered) {
            $salesRevenueThisWeek += $this->orderedManager->getMontantTotal($ordered);
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
