<?php

namespace App\Controller\Api\Product;

use App\Manager\ClientManager;
use App\Manager\ProductManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiProductController extends AbstractController
{
    private EntityManagerInterface $manager;
    private ProductManager $productManager;

    public function __construct(EntityManagerInterface $manager) {
        $this->manager = $manager;
        $this->productManager = new ProductManager($manager);
    }

    #[Route("/api/product/getAll", name: "apiProductGetAll", methods: ["GET"])]
    public function index(): JsonResponse {

        return $this->json($this->productManager->getAllProductAvailable(), Response::HTTP_OK, [], ["groups" => ["product", "price"]]);
    }
}
