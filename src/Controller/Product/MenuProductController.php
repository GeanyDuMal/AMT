<?php

namespace App\Controller\Product;

use App\Manager\ParameterManager;
use App\Manager\PriceManager;
use App\Repository\ProductRepository;
use App\Utils\Enum\ClientType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuProductController extends AbstractController
{

    #[Route("/product/{message?}", name: "menuProduct", methods: ["GET"])]
    public function show(EntityManagerInterface $manager, ProductRepository $productRepository, ?string $message = null): Response {
        $parameterManager = new ParameterManager($manager);
        $parameter = $parameterManager->getParameter();
        $priceManager = new PriceManager($manager);
        $clientTypeActual = $priceManager->getClientTypeUsedForPrice($this->getUser());

        // On recupere tout les produits
        $productsAvailable = $productRepository->findAllPositiveStock();
        $productsUnavailable = $productRepository->findAllEmptyStock();

        return $this->render("product/MenuProduct.html.twig", [
            "parameter" => $parameter,
            "productsAvailable" => $productsAvailable,
            "productsUnavailable" => $productsUnavailable,
            "message" => $message,
            "clientTypeActual" => $clientTypeActual
        ]);
    }
}
