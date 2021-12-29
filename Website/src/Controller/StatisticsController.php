<?php

namespace App\Controller;

use App\Repository\CommandRepository;
use App\Repository\ProductRepository;
use App\Repository\PurchaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class StatisticsController extends AbstractController
{
    /**
     * @Route("/statistics", name="statistics",methods={"GET", "POST"} )
     */
    public function index(ProductRepository $productRepository,PurchaseRepository $purchaseRepository,CommandRepository $commandRepository):Response{
        $products=$productRepository->findAll();
        $productSum=$productName=array();
        //we send the name of the product and number of quantity bought for each one to the template associated
        foreach($products as $product){
            $productName[]=$product->getName();
            $productSum[]=$purchaseRepository->getQuantityByProduct($product)[0]["somme"];
        }
        //
        $orders=$commandRepository->countByDate();
        $thisWeek[]=$this->thisWeek();
        $orderDate=[];
        $orderCount=[];
        for($i=0;$i<count($thisWeek[0]);$i++){
            $count=$this->existIn($thisWeek[0][$i],$orders);
            $orderDate[]=$thisWeek[0][$i];
            if($count>0)
                $orderCount[]=$count;
            else
                $orderCount[]=0;

        }

        return $this->render("statistics/statisticsModalPage.html.twig",[
            "productSum"=>json_encode($productSum),
            "productName"=>json_encode($productName),
            "orderCount"=>json_encode($orderCount),
            "orderDate"=>json_encode($orderDate),
            "thisWeek"=>json_encode($thisWeek[0])
        ]);
    }

    private function existIn($date, $orders):int
    {
        foreach ($orders as $order)
            if (strcmp($date,$order["orderDate"])==0)
                return $order["count"];
        return -1;
    }
    private function thisWeek(){
        $day_of_week = date('N', strtotime(date("Y-m-d")));

        $given_date = strtotime( date("d-m-Y"));
        $first_of_week =  date('Y-m-d', strtotime("- {$day_of_week} day", $given_date));
        $first_of_week = strtotime($first_of_week);
        for($i=0 ;$i<=7; $i++) {
            $week_array[] = date('Y-m-d', strtotime("+ {$i} day", $first_of_week));
        }
        return $week_array;
    }

}
