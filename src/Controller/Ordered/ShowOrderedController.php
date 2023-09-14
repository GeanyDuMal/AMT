<?php

namespace App\Controller\Ordered;

use App\Manager\OrderedManager;
use App\Manager\ParameterManager;
use App\Manager\PriceManager;
use App\Repository\OrderedRepository;
use App\Repository\PriceRepository;
use App\Repository\PurchaseRepository;
use App\Utils\Enum\ClientType;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowOrderedController extends AbstractController
{
    /**
     * @Route("/ordered/show&id={!idOrder}", name="showOrdered")
     */
    public function index($idOrder, EntityManagerInterface $manager, OrderedRepository $orderedRepository,
        PurchaseRepository $purchaseRepository, PriceRepository $priceRepository, Request $request): Response
    {
        if (!$this->isGranted(SymfonyRole::TRESORIER)) {
            return $this->redirectToRoute('home');
        }

        $parameterManager = new ParameterManager($manager);
        $parameter = $parameterManager->getParameter();
        $orderedManager = new OrderedManager($manager);
        $priceManager = new PriceManager($manager);
        $priceList = [];
        $clientType = ClientType::ETUDIANT;
        $inputParameterBag = $request->request;
        $toCancel = $inputParameterBag->get("cancel");
        $toRemove = $inputParameterBag->get("remove");

        if (is_numeric($idOrder)) {
            $order = $orderedRepository->find($idOrder);
            if ($order != null) {
                $clientType = $priceManager->getClientTypeUsedForPrice($order->getClient());

                $purchaseList = $purchaseRepository->findBy(["ordered" => $order]);
                //Permet de creer un tableau avec en clé les id des produits choisis et en valeur le prix
                foreach ($purchaseList as $purchase) {
                    $priceList = $priceList + [$purchase->getProduct()->getId() =>
                            $priceRepository->findOneBy(["product" => $purchase->getProduct(), "clientType" => $clientType])];
                }

                // Suppression ou annulation
                if ($toCancel || $toRemove){
                    $manager->initializeObject($order->getPurchases());
                    if($toCancel){
                        $orderedManager->removeWithRestore($order);

                    } else if($toRemove){
                        $orderedManager->remove($order);
                    }
                    return $this->redirectToRoute("menuOrdered", [
                        "message" => "La commande a été supprimé avec succès"
                    ]);
                }
            } else {
                return $this->redirectToRoute("home");
            }
        } else {
            return $this->redirectToRoute("home");
        }

        return $this->render("ordered/ShowOrdered.html.twig", [
            "parameter" => $parameter,
            "ordered" => $order,
            "clientType" => $clientType,
            "purchaseList" => $purchaseList,
            "priceList" => $priceList,
            "montantTotal" => $orderedManager->montantTotal($order)
        ]);
    }
}
