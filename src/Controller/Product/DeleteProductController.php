<?php

namespace App\Controller\Product;

use App\Manager\ParameterManager;
use App\Manager\PriceManager;
use App\Manager\ProductManager;
use App\Repository\ProductRepository;
use App\Utils\Enum\SymfonyRoleEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class DeleteProductController extends AbstractController
{
    private EntityManagerInterface $manager;
    private ProductManager $productManager;
    private ProductRepository $productRepository;
    public function __construct(ProductRepository $productRepository, EntityManagerInterface $manager) {
        $this->manager = $manager;
        $this->productManager = new ProductManager($this->manager);
        $this->productRepository = $productRepository;
    }
    #[Route("/product/delete/{!id}", name: "deleteProduct", methods: ["GET", "DELETE"])]
    public function index($id): RedirectResponse|JsonResponse
    {
        if (!$this->isGranted(SymfonyRoleEnum::TRESORIER->value)) {
            return $this->redirectToRoute("home");
        }

        $product = $this->productRepository->find($id);

        if ($product) {
            $this->productManager->remove($product);
            return $this->redirectToRoute("menuProduct");
        } else {
            return $this->redirectToRoute("home");
        }
    }
}