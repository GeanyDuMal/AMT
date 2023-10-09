<?php

namespace App\Controller\Product;

use App\Manager\ParameterManager;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowProductController extends AbstractController
{

    #[Route("/product/show/{!id}", name: "showProduct", methods: ["GET"])]
    public function index(EntityManagerInterface $manager, $id, ProductRepository $productRepository): Response
    {
        $parameterManager = new ParameterManager($manager);
        $parameter = $parameterManager->getParameter();
        $product = $productRepository->find($id);

        if ($product) {

            return $this->render("product/ShowProduct.html.twig", [
                "parameter" => $parameter,
                "product" => $product,
            ]);
        } else {
            return $this->redirectToRoute("menuProduct");
        }
    }
}
