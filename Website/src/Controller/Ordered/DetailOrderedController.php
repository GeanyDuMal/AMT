<?php

namespace App\Controller\Ordered;

use App\Manager\OrderedManager;
use App\Manager\PriceManager;
use App\Repository\OrderedRepository;
use App\Repository\PriceRepository;
use App\Repository\PurchaseRepository;
use App\Utils\Enum\ClientType;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DetailOrderedController extends AbstractController
{
    /**
     * @Route("/ordered/show&id={!idOrder}", name="showOrdered")
     */
    public function index($idOrder, EntityManagerInterface $manager, OrderedRepository $orderedRepository,
                          PurchaseRepository $purchaseRepository, PriceRepository $priceRepository): Response
    {
        if (!$this->isGranted(SymfonyRole::ASSOC)) {
            return $this->redirectToRoute('home');
        }

        $orderManager = new OrderedManager($manager);
        $priceManager = new PriceManager($manager);
        $priceList = [];
        $clientType = ClientType::ETUDIANT;


        if (is_numeric($idOrder)) {
            $order = $orderedRepository->find($idOrder);
            if ($order != null) {
                if ($order->getClient()) {
                    $clientType = $priceManager->getClientTypeUseForPrice($order->getClient()->getClientType());
                }

                $purchaseList = $purchaseRepository->findBy(["ordered" => $order]);
                //Permet de creer un tableau avec en clé les id des produits choisis et en valeur le prix
                foreach ($purchaseList as $purchase) {
                    $priceList = $priceList + [$purchase->getProduct()->getId() =>
                            $priceRepository->findOneBy(["product" => $purchase->getProduct(), "clientType" => $clientType])];
                }
            } else {
                return $this->redirectToRoute('home');
            }
        } else {
            return $this->redirectToRoute('home');
        }

        return $this->render('ordered/ShowOrdered.html.twig', [
            'ordered' => $order,
            'clientType' => $clientType,
            'purchaseList' => $purchaseList,
            'priceList' => $priceList,
            'montantTotal' => $orderManager->montantTotal($order)
        ]);
    }
}
