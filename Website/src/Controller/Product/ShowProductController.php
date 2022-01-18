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
     * @Route("/product", name="product_list")
     */
    public function index(EntityManagerInterface $manager): Response
    {
//        if (!$this->isGranted('ROLE_USER')){
//            return $this->redirectToRoute('home');
//        }
        $products=$manager->getRepository(Product::class)->findAll();
        return $this->render('product/productList.html.twig', array('products'=>$products));
    }
    /**
     * @Route("/product{message}", name="product_list_message",methods={"GET", "POST"} )
     */
    public function show($message,EntityManagerInterface $manager): Response
    {
        if (!$this->isGranted('ROLE_USER')){
            return $this->redirectToRoute('home');
        }
        $products=$manager->getRepository(Product::class)->findAll();
        return $this->render('product/productList.html.twig', array('products'=>$products,'message'=>$message));
    }
}
