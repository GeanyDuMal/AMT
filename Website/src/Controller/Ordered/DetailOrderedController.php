<?php

namespace App\Controller\Ordered;

use App\Entity\ClientType;
use App\Entity\Ordered;
use App\Entity\Price;
use App\Entity\Purchase;
use App\Manager\OrderedManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DetailOrderedController extends AbstractController
{
    /**
     * @Route("/ordered/details&id={idOrder}", name="detailOrdered")
     */
    public function index($idOrder, EntityManagerInterface $manager): Response
    {
        if (!$this->isGranted('ROLE_ASSOC')){
            return $this->redirectToRoute('home');
        }

        $clientTypeRepository = $manager->getRepository(ClientType::class);
        $orderRepository = $manager->getRepository(Ordered::class);
        $purchaseRepository = $manager->getRepository(Purchase::class);
        $priceRepository = $manager->getRepository(Price::class);
        $orderManager = new OrderedManager($manager);
        $purchaseList = [];
        $priceList = [];
        $clientType = $clientTypeRepository->findOneBy(["name" => "Etudiant"]);


        if(is_numeric($idOrder)){
            $order = $orderRepository->find($idOrder);
            if ($order != null){
                if ($order->getClient()){
                    $clientType = $order->getClient()->getClientType();
                }

                $purchaseList = $purchaseRepository->findBy(["ordered" => $order]);
                foreach ($purchaseList as $purchase){
                    $priceList = $priceList + [$purchase->getProduct()->getId() => $priceRepository->findOneBy(["product" => $purchase->getProduct(),
                            "clientType" => $clientType])];
                }
            }else{
                return $this->redirectToRoute('home');
            }
        }else{
            return $this->redirectToRoute('home');
        }

        return $this->render('ordered/detail.html.twig', [
            'ordered' => $order,
            'clientType' => $clientType,
            'purchaseList' => $purchaseList,
            'priceList' => $priceList,
            'montantTotal' => $orderManager->montantTotal($order)
        ]);
    }
}
