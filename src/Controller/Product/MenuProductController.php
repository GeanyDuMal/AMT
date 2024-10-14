<?php

namespace App\Controller\Product;

use App\Manager\OrderedManager;
use App\Manager\ParameterManager;
use App\Manager\PriceManager;
use App\Repository\ProductRepository;
use App\Utils\Enum\ClientTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuProductController extends AbstractController
{
    private EntityManagerInterface $manager;
    private OrderedManager $orderedManager;
    private ParameterManager $parameterManager;
    private ProductRepository $productRepository;

    public function __construct(EntityManagerInterface $manager, ProductRepository $productRepository) {
        $this->manager = $manager;
        $this->orderedManager = new OrderedManager($this->manager);
        $this->parameterManager = new ParameterManager($this->manager);
        $this->productRepository = $productRepository;
    }

    #[Route("/product/{message?}", name: "menuProduct", methods: ["GET"])]
    public function show(string $message = null): Response {
        $parameter = $this->parameterManager->getParameter();
        $clientTypeActual = $this->orderedManager->getClientTypeUsedForOrdered($this->getUser());

        // On recupere tout les produits
        $productsAvailable = $this->productRepository->findAllPositiveStock();
        $productsEmptyStock = $this->productRepository->findAllEmptyStock();
        $productsNotActive = $this->productRepository->findAllNotActive();

        return $this->render("product/MenuProduct.html.twig", [
            "parameter" => $parameter,
            "productsAvailable" => $productsAvailable,
            "productsEmptyStock" => $productsEmptyStock,
            "productsNotActive" => $productsNotActive,
            "message" => $message,
            "clientTypeActual" => $clientTypeActual
        ]);
    }
}
