<?php

namespace App\Controller\Product;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowProductController extends AbstractController
{
    /**
     * @Route("/product{message}", name="product_list",methods={"GET", "POST"} )
     */
    public function show(string $message = null,EntityManagerInterface $manager): Response
    {
        $products=$manager->getRepository(Product::class)->findAll();
        return $this->render('product/productList.html.twig',[
            'products'=>$products,
            'message'=>$message
        ]);
    }
}
