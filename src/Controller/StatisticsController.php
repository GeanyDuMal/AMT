<?php

namespace App\Controller;

use App\Manager\OrderedManager;
use App\Repository\ClientRepository;
use App\Repository\OrderedRepository;
use App\Repository\PostRepository;
use App\Repository\ProductRepository;
use App\Repository\PurchaseRepository;
use App\Utils\Enum\ClientType;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class StatisticsController extends AbstractController
{
    /**
     * @Route("/statistics", name="statistics",methods={"GET", "POST"} )
     */
    public function index(ProductRepository $productRepository, PostRepository $postRepository, PurchaseRepository $purchaseRepository,
                          OrderedRepository $orderedRepository, ClientRepository $clientRepository, EntityManagerInterface $manager): Response
    {
        if (!$this->isGranted(SymfonyRole::ASSOC)){
            return $this->redirectToRoute('home');
        }

        $products = $productRepository->findAll();
        $productSum = $productName = [];
        $countClients = count($clientRepository->findAll());
        $orderedManager = new OrderedManager($manager);
        $countThisWeeksCommands = $orderedRepository->quantityThisWeeksCommands()["number"];
        $salesRevenueThisMonth = 0;
        $salesRevenueThisWeek = 0;
        $monthOrderedList = $orderedRepository->thisMonthOrdered();
        $weekOrderedList = $orderedRepository->thisWeekOrdered();

        // Build the amount of purchase for the current month and the current week
        foreach ($monthOrderedList as $ordered){
                $salesRevenueThisMonth += $orderedManager->montantTotal($ordered);
        }
        foreach ($weekOrderedList as $ordered){
                $salesRevenueThisWeek += $orderedManager->montantTotal($ordered);
        }

        if($countThisWeeksCommands == 0){
            $averagePerStudent = 0;
        }else {
            $averagePerStudent = number_format($salesRevenueThisWeek / $countThisWeeksCommands, 2);
        }
        $postsNumber = count($postRepository->findAll());
        $noStock = [];

        //we send the name of the product and number of quantity bought for each one to the template associated
        foreach($products as $product){
            if($product->getQuantityStock() <= 10)
                $noStock[] = $product;
            $productName[] = $product->getName();
            $productSum[] = $purchaseRepository->getQuantityByProduct($product)[0]["somme"];
        }

        $orders = $orderedRepository->countByDate();
        $thisWeek[] = $this->thisWeek();
        $orderCount = [];
        for($i = 0; $i < count($thisWeek[0]); $i++){
            $count = $this->existIn($thisWeek[0][$i], $orders);
            if($count > 0){
                $orderCount[] = $count;
            }else{
                $orderCount[] = 0;
            }
        }

        $productsWarningStock = $productRepository->findAllWarningStock();
        $topSoldProduct = $productRepository->findTopSoldProductThisMonth();
        $productsEmptyStock = $productRepository->findAllEmptyStock();
        $countCotisant = count($clientRepository->findBy(['clientType' => ClientType::COTISANT]));

        return $this->render("statistics/Statistics.html.twig",[
            "productsWarningStock" => $productsWarningStock,
            "topSoldProduct" => $topSoldProduct,
            "productsEmptyStock" => $productsEmptyStock,
            "countCotisant" => $countCotisant,

            "productSum" => json_encode($productSum),
            "productName" => json_encode($productName),
            "orderCount" => json_encode($orderCount),
            "thisWeek" => json_encode($thisWeek[0]),
            "countClients" => json_encode($countClients),
            "noStock" => $noStock,
            "salesRevenueThisWeek" => $salesRevenueThisWeek,
            "salesRevenueThisMonth" => $salesRevenueThisMonth,
            "averagePerStudent" => $averagePerStudent,
            "postsNumber" => $postsNumber
        ]);
    }

    private function existIn($date, $orders): int
    {
        foreach ($orders as $order)
            if (strcmp($date,$order["orderDate"]) == 0)
                return $order["count"];
        return -1;
    }

    private function thisWeek(): array
    {
        $day_of_week = date('N', strtotime(date("Y-m-d")));

        $given_date = strtotime( date("d-m-Y"));
        $first_of_week = date('Y-m-d', strtotime("- {$day_of_week} day", $given_date));
        $first_of_week = strtotime($first_of_week);
        for($i=0; $i <= 7; $i++) {
            $week_array[] = date('Y-m-d', strtotime("+ {$i} day", $first_of_week));
        }
        return $week_array;
    }
}
