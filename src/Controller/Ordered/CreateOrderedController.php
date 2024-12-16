<?php

namespace App\Controller\Ordered;


use App\Manager\ClientManager;
use App\Manager\OrderedManager;
use App\Manager\ParameterManager;
use App\Manager\PriceManager;
use App\Manager\PurchaseManager;
use App\Repository\ClientRepository;
use App\Repository\ProductRepository;
use App\Utils\Enum\SymfonyRoleEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateOrderedController extends AbstractController {
    private EntityManagerInterface $manager;
    private ClientManager $clientManager;
    private ParameterManager $parameterManager;
    private PurchaseManager $purchaseManager;
    private PriceManager $priceManager;
    private OrderedManager $orderedManager;
    private ProductRepository $productRepository;
    private ClientRepository $clientRepository;

    public function __construct(EntityManagerInterface $manager, ProductRepository $productRepository, ClientRepository $clientRepository) {
        $this->manager = $manager;
        $this->clientManager = new ClientManager($this->manager);
        $this->parameterManager = new ParameterManager($this->manager);
        $this->purchaseManager = new PurchaseManager($this->manager);
        $this->orderedManager = new OrderedManager($this->manager);
        $this->priceManager = new PriceManager($this->manager);
        $this->productRepository = $productRepository;
        $this->clientRepository = $clientRepository;
    }

    #[Route("/ordered/create/{message?}", name: "createOrdered", methods: ["GET", "POST"])]
    public function index(Request $request, string $message = null): Response {
        if (!$this->isGranted(SymfonyRoleEnum::ASSOC->value)) {
            return $this->redirectToRoute('home');
        }

        $request->getSession()->clear();
        $parameter = $this->parameterManager->getParameter();
        $data = $request->request;

        $allProductPositiveStock = $this->productRepository->findAllPositiveStock();
        $allClient = $this->clientRepository->findBy([], ["name" => "ASC"]);

        if ($data->count() > 0) {
            $idClient = $data->get("orderedClient");
            $message = '';
            $purchases = new ArrayCollection();
            $client = null;

            if ($idClient) {
                $client = $this->clientManager->getClientById($idClient);
            }

            // Recupere toutes les quantités de produit selectionné
            foreach ($allProductPositiveStock as $product) {
                $quantity = $data->get("quantityOrdered_" . $product->getId());

                // Vérifie si l'on a commandé le produit $product
                if (is_numeric($quantity) && $quantity > 0 && $quantity <= $product->getQuantityStock()) {
                    $price = $this->priceManager->getPriceByProductAndClientType($product, $this->orderedManager->getClientTypeUsedForOrdered($client));
                    $purchase = $this->purchaseManager->createPurchase($product, $quantity, $price);

                    $purchases->add($purchase);
                } else {
                    if ($quantity != 0) {
                        $message = "Merci de vérifier la saisie des quantités";
                    }
                }
            }

            if (!$message && !$purchases->isEmpty()) {
                $ordered = $this->orderedManager->createOrdered($client, $purchases);
                $this->orderedManager->persist($ordered);

                return $this->redirectToRoute("orderedPayment", ["id" => $ordered->getId()], 308);
            } else {
                $message = "Merci de saisir au moins 1 produit";
            }
        }

        return $this->render("ordered/CreateOrdered.html.twig", [
            "parameter" => $parameter,
            "productList" => $allProductPositiveStock,
            "clientList" => $allClient,
            "message" => $message
        ]);
    }
}
