<?php

namespace App\Controller\Product;

use App\Manager\ParameterManager;
use App\Manager\PriceManager;
use App\Manager\ProductManager;
use App\Repository\PriceRepository;
use App\Repository\ProductRepository;
use App\Utils\Enum\ClientType;
use App\Utils\Enum\ProductType;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EditProductController extends AbstractController
{
    /**
     * @Route("/product/edit/{!id}", name="editProduct")
     */
    public function index($id, ProductRepository $productRepository, Request $request,
                          EntityManagerInterface $manager, PriceRepository $priceRepository): Response
    {
        if (!$this->isGranted(SymfonyRole::ASSOC)) {
            return $this->redirectToRoute("home");
        }

        $parameterManager = new ParameterManager($manager);
        $parameter = $parameterManager->getParameter();
        $data = $request->request;
        $product = $productRepository->find($id);
        $memberPrice = $priceRepository->findOneBy(["product" => $product, "clientType" => ClientType::ASSOCIATION]);
        $studentPrice = $priceRepository->findOneBy(["product" => $product, "clientType" => ClientType::ETUDIANT]);
        $message = "";

        if ($data->count() > 0) {
            $productManager = new ProductManager($manager);
            $priceManager = new PriceManager($manager);

            $productManager->setData($product,
                                     $data->get("productType"),
                                     $data->get("productName"),
                                     $data->get("productStock"),
                                     $product->getImageLink());

            if ($productManager->verifyProduct($product) && !$productManager->verifyEditedProductAlreadyExist($product)) {
                if ($data->get("pictureState") === "edit"){
                    $productManager->downloadPicture($product, $request->files->get("productPicture"));
                }

                $priceManager->setData($memberPrice, $studentPrice, $product, $data->get("memberPrice"), $data->get("studentPrice"));

                if ($priceManager->verifyPrice($memberPrice) && $priceManager->verifyPrice($studentPrice)) {
                    $product->addPrice($memberPrice);
                    $product->addPrice($studentPrice);

                    $productManager->persistCascade($product);

                    return $this->redirectToRoute("menuProduct", [
                        "message" => "Modification effectué avec succès"
                    ]);
                } else {
                    $message = "Merci de verifier votre saisie";
                }
            } else {
                $message = "Merci de verifier votre saisie";
            }
        }
        return $this->render("product/EditProduct.html.twig", [
            "parameter" => $parameter,
            "productTypes" => ProductType::getAll(),
            "message" => $message,
            "product" => $product,
            "studentPrice" => $studentPrice->getPrice(),
            "memberPrice" => $memberPrice->getPrice()
        ]);
    }
}