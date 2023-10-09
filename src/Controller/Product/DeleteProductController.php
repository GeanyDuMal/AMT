<?php

namespace App\Controller\Product;

use App\Manager\ProductManager;
use App\Repository\ProductRepository;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class DeleteProductController extends AbstractController
{

    #[Route("/product/delete/{!id}", name: "deleteProduct", methods: ["GET", "DELETE"])]
    public function index($id, EntityManagerInterface $manager, ProductRepository $productRepository): RedirectResponse|JsonResponse
    {
        if (!$this->isGranted(SymfonyRole::TRESORIER)) {
            return $this->redirectToRoute("home");
        }

        $productManager = new ProductManager($manager);
        $product = $productRepository->find($id);

        if ($product) {
            $productManager->remove($product);
            return $this->redirectToRoute("menuProduct");
        } else {
            return $this->redirectToRoute("home");
        }
    }
}