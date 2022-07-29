<?php

namespace App\Controller\Product;

use App\Entity\Price;
use App\Entity\Product;
use App\Manager\PriceManager;
use App\Manager\ProductManager;
use App\Repository\ProductRepository;
use App\Utils\Enum\PaymentType;
use App\Utils\Enum\ProductType;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateProductController extends AbstractController
{
    /**
     * @Route("/product/create", name="createProduct")
     */
    public function index(ProductRepository      $productRepository, ValidatorInterface $validator, Request $request,
                          EntityManagerInterface $manager): Response
    {
        if (!$this->isGranted(SymfonyRole::ASSOC)) {
            return $this->redirectToRoute('home');
        }

        $data = $request->request;
        $product = new Product();
        $productTypes = ProductType::getAll();
        $validationErrors = "";
        $productExistsError = "";

        if ($data->count() > 0) {
            $productManager = new ProductManager($manager);
            $priceManager = new PriceManager($manager);

            $productManager->setData($product, $data->get("productType"), $data->get("productName"), $data->get("productStock"), $data->get("imageLink"));
            $validationErrors = $validator->validate($product);

            if ($validationErrors->count() == 0) {
                if ($productRepository->findBy(['name' => $product->getName()])) {
                    $productExistsError = "Le produit existe déjà";
                } else {
                    $memberPrice = new Price();
                    $studentPrice = new Price();

                    $priceManager->setData($memberPrice, $studentPrice, $product, $data->get("memberPrice"), $data->get("studentPrice"));
                    $validationErrors = $validator->validate($memberPrice);

                    if ($validationErrors->count() == 0) {
                        $validationErrors = $validator->validate($studentPrice);

                        if ($validationErrors->count() == 0) {
                            $productManager->persist($product);

                            $priceManager->persist($memberPrice);
                            $priceManager->persist($studentPrice);

                            return $this->redirectToRoute('menuProduct', [
                                "message" => "Ajout avec succès"
                            ]);
                        }
                    }
                }
            }
        }
        return $this->render('product/CreateProduct.html.twig', [
            'productTypes' => $productTypes,
            'validationErrors' => $validationErrors,
            'productExistsError' => $productExistsError,
            'produit' => $product
        ]);
    }
}