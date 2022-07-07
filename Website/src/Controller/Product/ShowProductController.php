<?php

namespace App\Controller\Product;

use App\Entity\Client;
use App\Repository\ProductRepository;
use App\Utils\Enum\ClientType;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowProductController extends AbstractController
{
    /**
     * @Route("/product/{message?}", name="product_list",methods={"GET", "POST"} )
     */
    public function show(EntityManagerInterface $manager, ProductRepository $productRepository, string $message = null): Response
    {
        $clientTypeActual = ClientType::ETUDIANT;

        if ($this->getUser() && $this->getUser()->getClientType() === ClientType::ASSOCIATION) {
             $clientTypeActual = ClientType::ASSOCIATION;
        }

        // On recupere tout les produits
        $products = $productRepository->findBy([], ["productType" => "ASC"]);

        return $this->render('product/productList.html.twig', [
            'products' => $products,
            'message' => $message,
            'clientTypeActual' => $clientTypeActual
        ]);
    }
}
