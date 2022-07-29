<?php

namespace App\Controller\Product;

use App\Manager\ProductManager;
use App\Repository\ProductRepository;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DeleteProductController extends AbstractController
{

    /**
     * @Route("/product/delete/{!id}", name="deleteProduct", methods={"GET", "DELETE"})
     */
    public function index($id, EntityManagerInterface $manager, ProductRepository $productRepository): JsonResponse
    {
        if (!$this->isGranted(SymfonyRole::TRESORIER)) {
            return new JsonResponse(false);
        }

        $productManager = new ProductManager($manager);
        $product = $productRepository->find($id);

        $productManager->remove($product);
        
        return new JsonResponse(true);
    }
}