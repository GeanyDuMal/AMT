<?php

namespace App\Controller\Ordered;

use App\Entity\Ordered;
use App\Entity\Purchase;
use App\Manager\ClientManager;
use App\Manager\OrderedManager;
use App\Manager\ParameterManager;
use App\Manager\PriceManager;
use App\Manager\PurchaseManager;
use App\Repository\PriceRepository;
use App\Repository\ProductRepository;
use App\Utils\Enum\ClientType;
use App\Utils\Enum\SymfonyRole;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentOrderedController extends AbstractController {

    private ClientManager $clientManager;

    #[Route("/ordered/payment", name: "orderedPayment", methods: ["POST"])]
    public function index(EntityManagerInterface $manager, Request $request, ProductRepository $productRepository): Response {
        if (!$this->isGranted(SymfonyRole::ASSOC)) {
            return $this->redirectToRoute('home');
        }

        $parameterManager = new ParameterManager($manager);
        $parameter = $parameterManager->getParameter(true);
        $purchaseManager = new PurchaseManager($manager);
        $this->clientManager = new ClientManager($manager);
        $priceManager = new PriceManager($manager);
        $orderManager = new OrderedManager($manager);
        $productOrderedAndClient = $request->getSession()->get("productOrderedAndClient");
        $ordered = $this->mapTabToOrderedAndPurchase($productOrderedAndClient, $productRepository);
        $clientTypeUsed = $priceManager->getClientTypeUsedForPrice($ordered->getClient());
        $data = $request->request;

        $montantProduct = [];
        foreach ($ordered->getPurchases() as $purchase) {
            $product = $purchase->getProduct();

            $montantProduct = $montantProduct + [$product->getId() => $priceManager->getPriceByProductAndClientType($product,
                        $clientTypeUsed) * $purchase->getQuantity()];
        }

        $montantTotal = $orderManager->montantTotal($ordered);
        $paymentTypes = $orderManager->getAllowedPaymentType($montantTotal, $ordered->getClient());

        //Si l'on a cliqué sur un bouton sur la page Payment
        if ($data->get("payement_type")) {
            $paymentTypeChose = $data->get("payement_type");

            if (!$purchaseManager->verifyDisponibilityProducts($ordered->getPurchases()->getValues())) {
                return $this->redirectToRoute("menuOrdered", [
                    "message" => "Le produit commandé n'est plus disponible !"
                ]);
            }

            $orderManager->setData($ordered, $ordered->getClient(), $paymentTypeChose, new DateTime("now"));
            $orderManager->persist($ordered);

            foreach ($ordered->getPurchases() as $purchase) {
                $purchaseManager->setData($purchase, $purchase->getProduct(), $purchase->getQuantity(), $ordered);
                $purchaseManager->persist($purchase);
            }

            $orderManager->reduceBalanceIfNecessary($ordered);
            $orderManager->addFidelityToClient($ordered);

            return $this->redirectToRoute("createOrdered", [
                "message" => "Commande réussie !"
            ]);
        }

        return $this->render('ordered/PaymentOrdered.html.twig', [
            "parameter" => $parameter,
            "purchases" => $ordered->getPurchases(),
            "montantProduct" => $montantProduct,
            "montantTotal" => $montantTotal,
            "client" => $ordered->getClient(),
            "paymentTypes" => $paymentTypes
        ]);
    }

    /**
     * @param array $productOrderedAndClient
     * @param ProductRepository $productRepository
     * @return Ordered ["product" => Product, "quantity" => quantity]
     */
    public function mapTabToOrderedAndPurchase(array $productOrderedAndClient, ProductRepository $productRepository): Ordered {
        $ordered = new Ordered();

        foreach ($productOrderedAndClient["productOrdered"] as $idProductOrdered) {
            $product = $productRepository->find($idProductOrdered["idProduct"]);

            $purchase = new Purchase();
            $purchase->setProduct($product)
                     ->setQuantity(intval($idProductOrdered["quantity"]));

            $ordered->addPurchase($purchase);
        }

        if ($productOrderedAndClient["idClient"] != null) {
            $clientOrder = $this->clientManager->getClientById($productOrderedAndClient["idClient"]);
            $ordered->setClient($clientOrder);
        }

        return $ordered;
    }
}
