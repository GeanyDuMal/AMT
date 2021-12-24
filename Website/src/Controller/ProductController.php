<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product")
     */
    public function index(EntityManagerInterface $em): Response
    {
        $products=$em->getRepository(Product::class)->findAll();
        return $this->render('product/productList.html.twig', array('products'=>$products));
    }
}
