<?php

namespace App\Controller\Ordered;

use App\Manager\OrderedManager;
use App\Manager\ParameterManager;
use App\Manager\PriceManager;
use App\Manager\PurchaseManager;
use App\Utils\Enum\OrderedStatus;
use App\Utils\Enum\SymfonyRole;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentOrderedController extends AbstractController {

    private EntityManagerInterface $manager;
    private PriceManager $priceManager;
    private ParameterManager $parameterManager;
    private OrderedManager $orderedManager;
    private PurchaseManager $purchaseManager;

    public function __construct(EntityManagerInterface $manager) {
        $this->manager = $manager;
        $this->priceManager = new PriceManager($this->manager);
        $this->parameterManager = new ParameterManager($this->manager);
        $this->purchaseManager = new PurchaseManager($this->manager);
        $this->orderedManager = new OrderedManager($this->manager);
    }

    #[Route("/ordered/payment/{!id}", name: "orderedPayment", methods: ["POST"])]
    public function index(int $id, Request $request): Response {
        if (!$this->isGranted(SymfonyRole::ASSOC)) {
            return $this->redirectToRoute('home');
        }

        $ordered = $this->orderedManager->getOrderedById($id);
        $parameter = $this->parameterManager->getParameter(true);
        $data = $request->request;

        $productsUnitaryPrices = [];
        foreach ($ordered->getPurchases() as $purchase) {
            $product = $purchase->getProduct();

            $productsUnitaryPrices = $productsUnitaryPrices + [$product->getId() =>
                    $this->priceManager->getPriceByProductAndClientType($product, $ordered->getClientTypeAtOrder())];
        }

        $montantTotal = $this->orderedManager->getMontantTotal($ordered);
        $paymentTypes = $this->orderedManager->getAllowedPaymentType($montantTotal, $ordered->getClient());

        //Si l'on a cliqué sur un bouton sur la page Payment
        if ($data->get("payement_type")) {
            $paymentTypeChose = $data->get("payement_type");

            if (!$this->purchaseManager->verifyDisponibilityProducts($ordered->getPurchases()->getValues())) {
                return $this->redirectToRoute("menuOrdered", [
                    "message" => "Le produit commandé n'est plus disponible !"
                ]);
            }

            $this->orderedManager->setData($ordered, $ordered->getClient(),
                                           $paymentTypeChose,
                                           new DateTime("now"),
                                           OrderedStatus::PAID,
                                           $ordered->getClientTypeAtOrder());
            $this->orderedManager->persist($ordered);

            $this->orderedManager->reduceBalanceIfNecessary($ordered);
            $this->orderedManager->addFidelityToClient($ordered);

            return $this->redirectToRoute("createOrdered", [
                "message" => "Commande réussie !"
            ]);
        }

        return $this->render('ordered/PaymentOrdered.html.twig', [
            "parameter" => $parameter,
            "purchases" => $ordered->getPurchases(),
            "productsUnitaryPrices" => $productsUnitaryPrices,
            "montantTotal" => $montantTotal,
            "client" => $ordered->getClient(),
            "paymentTypes" => $paymentTypes
        ]);
    }
}
