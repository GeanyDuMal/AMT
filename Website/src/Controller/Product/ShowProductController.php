<?php

namespace App\Controller\Product;

use App\Repository\ClientTypeRepository;
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
        ProductRepository $productRepository): Response
    {
        if ($this->isGranted("ROLE_ASSOC")) {
            $clientTypeActual = $clientTypeRepository->findOneBy(["name" => "Association"]);
        } else {
            $clientTypeActual = $clientTypeRepository->findOneBy(["name" => "Etudiant"]);
        }

        // On recupere tout les produits
        $products = $productRepository->findAll();

        return $this->render('product/productList.html.twig', [
            'products' => $products,
            'message' => $message,
            'clientTypeActual' => $clientTypeActual->getName()
        ]);
    }
}
