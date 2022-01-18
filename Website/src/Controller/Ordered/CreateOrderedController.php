<?php

namespace App\Controller\Ordered;

use App\Entity\Client;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateOrderedController extends AbstractController
{
    /**
     * @Route("/ordered/create", name="orderedCreate")
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

        //Recupere tout les produits avec un stock positif afin d'afficher uniquement ceux disponibles
        $allProduct = $productRepository->findAll();
        foreach ($allProduct as $product){
            if ($product->getQuantityStock() > 0){
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
            return $this->redirectToRoute("orderedPayment", [
                "productOrderedSerialized" => serialize($productOrdered),
                "idClient" => $inputParameterBag->get("client_commande")
            ]);
        }

        $allClient = $clientRepository->findBy([], ["name" => "ASC"]);

        return $this->render('ordered/create.html.twig', [
            "user" => $user,
            "productList" => $allProductPositiveStock,
            "clientList" => $allClient
        ]);
    }
}
