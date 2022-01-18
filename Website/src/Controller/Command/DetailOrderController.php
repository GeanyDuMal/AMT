<?php

namespace App\Controller\Command;

use App\Entity\Command;
use App\Entity\Price;
use App\Entity\Purchase;
use App\Manager\OrderManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DetailOrderController extends AbstractController
{
    /**
     * @Route("/detail/order&id={idOrder}", name="detailOrder")
     */
    public function index($idOrder, EntityManagerInterface $manager): Response
    {
        if (!$this->isGranted('ROLE_ASSOC')){
            return $this->redirectToRoute('home');
        }

        $orderRepository = $manager->getRepository(Command::class);
        $purchaseRepository = $manager->getRepository(Purchase::class);
        $priceRepository = $manager->getRepository(Price::class);
        $orderManager = new OrderManager($manager);
        $purchaseList = [];
        $priceList = [];

        if(is_numeric($idOrder)){
            $order = $orderRepository->find($idOrder);
            if ($order != null){
                $purchaseList = $purchaseRepository->findBy(["command" => $order]);
                foreach ($purchaseList as $purchase){
                    $priceList = $priceList + [$purchase->getProduct()->getId() => $priceRepository->findOneBy(["product" => $purchase->getProduct(),
                            "clientType" => $order->getClient()->getClientType()])];
                }
            }else{
                return $this->redirectToRoute('home');
            }
        }else{
            return $this->redirectToRoute('home');
        }

        return $this->render('order/detail.html.twig', [
            'order' => $order,
            'purchaseList' => $purchaseList,
            'priceList' => $priceList,
            'montantTotal' => $orderManager->montantTotal($order)
        ]);
    }
}
