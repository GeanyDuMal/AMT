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

    private EntityManagerInterface $manager;
    private ProductRepository $productRepository;

    public function __construct(EntityManagerInterface $manager, ProductRepository $productRepository) {
        $this->manager = $manager;
        $this->productRepository = $productRepository;
    }

    #[Route("/product/show/{!id}", name: "showProduct", methods: ["GET"])]
    public function index($id): Response
    {
        $parameterManager = new ParameterManager($this->manager);
        $parameter = $parameterManager->getParameter();
        $product = $this->productRepository->find($id);

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
