<?php

namespace App\Controller\Command;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandController extends AbstractController
{
    /**
     * @Route("/command", name="commandHome")
     */
    public function index(EntityManagerInterface $manager): Response
    {
        if (!$this->isGranted('ROLE_ASSOC')){
            return $this->redirectToRoute('home');
        }

        $user = $this->getUser();
        $productRepository = $manager->getRepository(Product::class);
        $allProductPositiveStock = [];

        $allProduct = $productRepository->findAll();

        foreach ($allProduct as $product){
            if ($product->getQuantityStock() >0){
                $allProductPositiveStock[] = $product;
            }
        }

        return $this->render('command/index.html.twig', [
            "user" => $user,
            "productList" => $allProductPositiveStock
        ]);
    }
}
