<?php

namespace App\Controller\Product;

use App\Entity\Client;
use App\Manager\PriceManager;
use App\Repository\ProductRepository;
use App\Utils\Enum\ClientType;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuProductController extends AbstractController
{
    /**
     * @Route("/product/{message?}", name="menuProduct", methods={"GET", "POST"} )
     */
    public function show(EntityManagerInterface $manager, ProductRepository $productRepository, ?string $message = null): Response
    {
        $clientTypeActual = ClientType::ETUDIANT;
        $priceManager = new PriceManager($manager);

        if ($this->getUser()) {
             $clientTypeActual = $priceManager->getClientTypeUseForPrice($this->getUser()->getClientType());
        }

        // On recupere tout les produits
        $products = $productRepository->findBy([], ["productType" => "ASC", "name" => "ASC"]);

        return $this->render('product/MenuProduct.html.twig', [
            'products' => $products,
            'message' => $message,
            'clientTypeActual' => $clientTypeActual
        ]);
    }
}
