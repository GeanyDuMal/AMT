<?php

namespace App\Controller\Product;

use App\Entity\Client;
use App\Entity\ClientType;
use App\Entity\Price;
use App\Entity\Product;
use App\Manager\ClientManager;
use App\Repository\ClientRepository;
use App\Repository\ClientTypeRepository;
use App\Repository\PriceRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowProductController extends AbstractController
{
    /**
     * @Route("/product/{message}", name="product_list",methods={"GET", "POST"} )
     */
    public function show(string $message = null, EntityManagerInterface $manager, ClientTypeRepository $clientTypeRepository,
        ProductRepository $productRepository, PriceRepository $priceRepository): Response
    {
        if ($this->isGranted("ROLE_ASSOC")) {
            $clientTypeActual = $clientTypeRepository->findOneBy(["name" => "Association"]);
        } else {
            $clientTypeActual = $clientTypeRepository->findOneBy(["name" => "Etudiant"]);
        }

        $productIdPrices = [];
        //$products = $productRepository->findBy(["price.clientType" => $clientTypeActual], ["productType" => "DESC", "name" => "ASC"]);

        $productsWithPrice = $priceRepository->findAllProductsAndPriceByClientType($clientTypeActual->getName());

        dd($productsWithPrice);
        //Recupere pour chaque produit, le prix qui lui correspond dans la liste $prices (en fonction de son type)
        foreach ($products as $product) {
            $productIdPrices = $productIdPrices + [$product->getId() => $priceRepository->findOneBy(["product" => $product->getId(), "clientType" => $clientTypeActual])->getPrice()];
        }
        return $this->render('product/productList.html.twig', [
            'products' => $products,
            'message' => $message,
            'productIdPrices'=>$productIdPrices
        ]);
    }
}
