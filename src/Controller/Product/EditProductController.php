<?php

namespace App\Controller\Product;

use App\Manager\ParameterManager;
use App\Manager\PriceManager;
use App\Manager\ProductManager;
use App\Repository\PriceRepository;
use App\Repository\ProductRepository;
use App\Utils\Enum\ClientTypeEnum;
use App\Utils\Enum\ProductTypeEnum;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditProductController extends AbstractController {
    private EntityManagerInterface $manager;
    private ProductManager $productManager;
    private PriceManager $priceManager;
    private ParameterManager $parameterManager;
    private ProductRepository $productRepository;
    private PriceRepository $priceRepository;

    public function __construct(ProductRepository $productRepository, EntityManagerInterface $manager, PriceRepository $priceRepository) {
        $this->manager = $manager;
        $this->productManager = new ProductManager($this->manager);
        $this->priceManager = new PriceManager($this->manager);
        $this->parameterManager = new ParameterManager($this->manager);
        $this->productRepository = $productRepository;
        $this->priceRepository = $priceRepository;
    }

    #[Route("/product/edit/{!id}", name: "editProduct", methods: ["GET", "POST"])]
    public function index($id, Request $request): Response {
        if (!$this->isGranted(SymfonyRole::ASSOC)) {
            return $this->redirectToRoute("home");
        }

        $parameter = $this->parameterManager->getParameter();
        $data = $request->request;
        $product = $this->productRepository->find($id);
        $memberPrice = $this->priceRepository->findOneBy(["product" => $product, "clientType" => ClientTypeEnum::ASSOCIATION]);
        $studentPrice = $this->priceRepository->findOneBy(["product" => $product, "clientType" => ClientTypeEnum::ETUDIANT]);
        $message = "";

        if ($data->count() > 0) {

            $this->productManager->setData($product,
                                           ProductTypeEnum::from($data->get("productType")),
                                           $data->get("productName"),
                                           $data->get("productStock"),
                                           $data->get("isActive"),
                                           $product->getImageLink()
            );

            if ($this->productManager->verifyProduct($product) && !$this->productManager->verifyEditedProductAlreadyExist($product)) {
                if ($data->get("pictureState") === "edit") {
                    $this->productManager->downloadAndApplyPicture($product, $request->files->get("productPicture"));
                }

                $this->priceManager->setData($memberPrice, $studentPrice, $product, $data->get("memberPrice"), $data->get("studentPrice"));

                if ($this->priceManager->verifyPrice($memberPrice) && $this->priceManager->verifyPrice($studentPrice)) {
                    $product->addPrice($memberPrice);
                    $product->addPrice($studentPrice);

                    $this->productManager->persistCascade($product);

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
            "productTypes" => ProductTypeEnum::cases(),
            "message" => $message,
            "product" => $product,
            "studentPrice" => $studentPrice->getPrice(),
            "memberPrice" => $memberPrice->getPrice()
        ]);
    }
}