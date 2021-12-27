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
     * @Route("/command/create", name="commandCreate")
     */
    public function index(Request $request, EntityManagerInterface $manager): Response
    {
        if (!$this->isGranted('ROLE_ASSOC')){
            return $this->redirectToRoute('home');
        }

        $user = $this->getUser();
        $productRepository = $manager->getRepository(Product::class);
        $clientRepository = $manager->getRepository(Client::class);
        $clientTypeRepository = $manager->getRepository(ClientType::class);
        $priceRepository = $manager->getRepository(Price::class);
        $allProductPositiveStock = [];
        $inputParameterBag = $request->request;
        $productOrdered = [];
        $quantityOrdered = [];

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
                  $productOrdered[] = $product;
                  $quantityOrdered = $quantityOrdered + [$product->getId() => $quantity];
            }
        }

        //Si l'on a commandé au moins 1 produits
        if ($productOrdered){
            $clientCommande = null;
            $typeClient = $clientTypeRepository->findBy(["name" => "Etudiant"]);
            $montant = 0;

            if($inputParameterBag->get("client_commande") != null){
                $clientCommande = $clientRepository->find($inputParameterBag->get("client_commande"));
            }
            if ($clientCommande){
                $typeClient = $clientCommande->getClientType();
            }
            foreach ($productOrdered as $product){

                $montant = $montant + $priceRepository->findOneBy(["product" => $product,
                                        "clientType" => $typeClient])->getPrice()*$quantityOrdered[$product->getId()];
            }

            return $this->render('command/payement.html.twig', [
                "product" => $productOrdered,
                "quantity" => $quantityOrdered,
                "montant" => $montant
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
