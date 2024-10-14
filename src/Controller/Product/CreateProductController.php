<?php

namespace App\Controller\Product;

use App\Entity\Price;
use App\Entity\Product;
use App\Manager\ParameterManager;
use App\Manager\PriceManager;
use App\Manager\ProductManager;
use App\Repository\ProductRepository;
use App\Utils\Enum\ProductTypeEnum;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateProductController extends AbstractController
{
    private EntityManagerInterface $manager;
    private ProductManager $productManager;
    private PriceManager $priceManager;
    private ParameterManager $parameterManager;
    private ProductRepository $productRepository;
    public function __construct(ProductRepository $productRepository, EntityManagerInterface $manager) {
        $this->manager = $manager;
        $this->productManager = new ProductManager($this->manager);
        $this->priceManager = new PriceManager($this->manager);
        $this->parameterManager = new ParameterManager($this->manager);
        $this->productRepository = $productRepository;
    }

    #[Route("/product/create", name: "createProduct", methods: ["GET", "POST"])]
    public function index(Request $request): Response {
        if (!$this->isGranted(SymfonyRole::ASSOC)) {
            return $this->redirectToRoute('home');
        }

        $parameter = $this->parameterManager->getParameter();
        $data = $request->request;
        $product = new Product();
        $productTypes = ProductTypeEnum::cases();
        $message = "";
        if ($data->count() > 0) {

            $this->productManager->setData($product,
                                     ProductTypeEnum::from($data->get("productType")),
                                     $data->get("productName"),
                                     $data->get("productStock"),
                                     $data->get("isActive")
            );
            $this->productManager->downloadAndApplyPicture($product, $request->files->get("productPicture"));

            if ($this->productManager->verifyProduct($product)) {
                if ($this->productRepository->findBy(["name" => $product->getName()])) {
                    $message = "Le produit existe déjà";
                } else {
                    $memberPrice = new Price();
                    $studentPrice = new Price();

                    $this->priceManager->setData($memberPrice, $studentPrice, $product, $data->get("memberPrice"), $data->get("studentPrice"));

                    if ($this->priceManager->verifyPrice($memberPrice) && $this->priceManager->verifyPrice($studentPrice)) {
                        $this->productManager->persist($product);

                        $this->priceManager->persist($memberPrice);
                        $this->priceManager->persist($studentPrice);

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