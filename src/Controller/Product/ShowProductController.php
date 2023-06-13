<?php

namespace App\Controller\Product;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowProductController extends AbstractController
{
    /**
     * @Route("/product/show/{!id}", name="showProduct")
     */
    public function index($id, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);

        if ($product) {

            return $this->render("product/ShowProduct.html.twig", [
                "product" => $product,
            ]);
        } else {
            return $this->redirectToRoute("menuProduct");
        }
    }
}
