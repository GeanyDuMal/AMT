<?php

namespace App\Controller\Product;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DeleteProductController extends AbstractController
{

    /**
     * @Route("/product/delete/{id}", name="delete_product", methods={"GET", "DELETE"})
     */
    public function index($id, EntityManagerInterface $manager, ProductRepository $productRepository)
    {
        if (!$this->isGranted('ROLE_USER')){
            return $this->redirectToRoute('home');
        }

        $product = $productRepository->find($id);
        $manager->remove($product);
        $manager->flush();
    }
}