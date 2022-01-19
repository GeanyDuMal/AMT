<?php

namespace App\Controller\Product;

use App\Entity\Client;
use App\Entity\ClientType;
use App\Entity\Price;
use App\Entity\Product;
use App\Manager\ClientManager;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowProductController extends AbstractController
{
    /**
     * @Route("/product{message}", name="product_list",methods={"GET", "POST"} )
     */
    public function show(string $message = null, EntityManagerInterface $manager): Response
    {
        $clientTypeRepository = $manager->getRepository(ClientType::class);
        $productRepository = $manager->getRepository(Product::class);
        $productIdPrices = [];

        if ($this->isGranted("ROLE_ASSOC")) {
            $clientTypeActual = $clientTypeRepository->findOneBy(["name" => "Association"]);
        } else {
            $clientTypeActual = $clientTypeRepository->findOneBy(["name" => "Etudiant"]);
        }


        $products = $productRepository->findAll();
        $prices = $manager->getRepository(Price::class)->findBy(["clientType" => $clientTypeActual]);

        foreach ($products as $product) {
            foreach ($prices as $price) {
                if ($price->getProduct() == $product) {
                    $productIdPrices = $productIdPrices + [$product->getId() => $price->getPrice()];
                }


            }
        }
        return $this->render('product/productList.html.twig', [
            'products' => $products,
            'message' => $message,
            'productIdPrices'=>$productIdPrices
        ]);
    }
}
