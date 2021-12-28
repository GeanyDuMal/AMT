<?php

namespace App\Controller\Command;

use App\Entity\Client;
use App\Entity\ClientType;
use App\Entity\Price;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateOrderController extends AbstractController
{
    /**
     * @Route("/command/create", name="orderCreate")
     */
    public function index(Request $request, EntityManagerInterface $manager): Response
    {
        if (!$this->isGranted('ROLE_ASSOC')){
            return $this->redirectToRoute('home');
        }

        $user = $this->getUser();
        $productRepository = $manager->getRepository(Product::class);
        $clientRepository = $manager->getRepository(Client::class);
        $allProductPositiveStock = [];
        $inputParameterBag = $request->request;
        $productOrdered = [];

        $allProduct = $productRepository->findAll();
        foreach ($allProduct as $product){
            if ($product->getQuantityStock() >0){
                $allProductPositiveStock[] = $product;
            }
        }

        //Recupere toutes les quantités de produit selectionné
        foreach ($allProductPositiveStock as $product){
            $quantity = $inputParameterBag->get("quantity_product_" . $product->getId());
            if ($quantity != 0 ){
                  $productOrdered = $productOrdered + [$product->getId() => $quantity];
            }
        }

        //Si l'on a commandé au moins 1 produits
        if ($productOrdered){
            return $this->redirectToRoute("orderPayment", [
                "productOrderedSerialized" => serialize($productOrdered),
                "idClient" => $inputParameterBag->get("client_commande")
            ]);
        }




        $allClient = $clientRepository->findAll();

        return $this->render('command/create.html.twig', [
            "user" => $user,
            "productList" => $allProductPositiveStock,
            "clientList" => $allClient
        ]);
    }
}
