<?php

namespace App\Controller\Api\Product;

use App\Manager\ParameterManager;
use App\Manager\ProductManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiProductController extends AbstractController
{
    /**
     * @Route("/api/product/getAll", name="apiProductGetAll")
     */
    public function index(EntityManagerInterface $manager): JsonResponse
    {
        $productManager = new ProductManager($manager);

        return $this->json($productManager->getAllProductAvailable(), 200, [], ['groups' => ["product", "price"]]);
    }
}
