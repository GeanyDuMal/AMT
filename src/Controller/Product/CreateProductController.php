<?php

namespace App\Controller\Product;

use App\Entity\Price;
use App\Entity\Product;
use App\Manager\ParameterManager;
use App\Manager\PriceManager;
use App\Manager\ProductManager;
use App\Repository\ProductRepository;
use App\Utils\Enum\ProductType;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateProductController extends AbstractController
{
    /**
     * @Route("/product/create", name="createProduct")
     */
    public function index(ProductRepository $productRepository, Request $request, EntityManagerInterface $manager): Response
    {
        if (!$this->isGranted(SymfonyRole::ASSOC)) {
            return $this->redirectToRoute('home');
        }

        $parameterManager = new ParameterManager($manager);
        $parameter = $parameterManager->getParameter();
        $data = $request->request;
        $product = new Product();
        $productTypes = ProductType::getAll();
        $message = "";

        if ($data->count() > 0) {
            $productManager = new ProductManager($manager);
            $priceManager = new PriceManager($manager);

            $productManager->setData($product, $data->get("productType"), $data->get("productName"), $data->get("productStock"));
            $productManager->downloadPicture($data->get("imageLink"), $product);

            if ($productManager->verifyProduct($product)) {
                if ($productRepository->findBy(["name" => $product->getName()])) {
                    $message = "Le produit existe déjà";
                } else {
                    $memberPrice = new Price();
                    $studentPrice = new Price();

                    $priceManager->setData($memberPrice, $studentPrice, $product, $data->get("memberPrice"), $data->get("studentPrice"));

                    if ($priceManager->verifyPrice($memberPrice) && $priceManager->verifyPrice($studentPrice)) {
                        $productManager->persist($product);

                        $priceManager->persist($memberPrice);
                        $priceManager->persist($studentPrice);

                        return $this->redirectToRoute("menuProduct", [
                            "message" => "Ajout avec succès"
                        ]);
                    } else {
                        $message = "Merci de verifier votre saise";
                    }
                }
            } else {
                $message = "Merci de verifier votre saise";
            }
        }

        return $this->render("product/CreateProduct.html.twig", [
            "parameter" => $parameter,
            "productTypes" => $productTypes,
            "message" => $message,
            "produit" => $product
        ]);
    }
}