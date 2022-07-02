<?php

namespace App\Controller\Product;

use App\Entity\Price;
use App\Entity\Product;
use App\Manager\PriceManager;
use App\Manager\ProductManager;
use App\Repository\ClientTypeRepository;
use App\Repository\PriceRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;

class EditProductController extends AbstractController
{
    /**
     * @Route("/product/edit/{!id}", name="edit_product")
     */
    public function index($id, ProductRepository $productRepository, ValidatorInterface $validator, Request $request, EntityManagerInterface $manager, ProductTypeRepository $productTypeRepository, ClientTypeRepository $clientTypeRepository, PriceRepository $priceRepository): Response
    {
        if (!$this->isGranted('ROLE_ASSOC')){
            return $this->redirectToRoute('home');
        }
        $data = $request->request;
        $product = $productRepository->find($id);
        $productTypes = $productTypeRepository->findAll();
        $typeMember = $clientTypeRepository->findBy(["name" => "Association"]);
        $typeStudent = $clientTypeRepository->findBy(["name" => "Etudiant"]);
        $memberPrice = $priceRepository->findOneBy(["product" => $product, "clientType" => $typeMember]);
        $studentPrice = $priceRepository->findOneBy(["product" => $product, "clientType" => $typeStudent]);

        $validationErrors = "";
        $productExistsError = "";

        if($data->count()>0){
            $productManager = new ProductManager($manager);
            $priceManager = new PriceManager($manager);

            $productManager->setData($productTypeRepository, $product, $data->get("productType"), $data->get("productName"), $data->get("productStock"), $data->get("imageLink"));
            $validationErrors = $validator->validate($product);

            if($validationErrors->count() == 0){
                $ChosenProductName = $productRepository->find($id)->getName();
                $newProductName = $product->getName();

                if(strcmp($ChosenProductName, $newProductName) != 0 && $productRepository->findOneBy(['name' => $newProductName])){
                    $productExistsError="Produit existe déja";
                }else{
                    $priceManager->setData($clientTypeRepository, $memberPrice, $studentPrice, $product, $data->get("memberPrice"), $data->get("studentPrice"));
                    $validationErrors = $validator->validate($memberPrice);

                    if($validationErrors->count() == 0){
                        $validationErrors=$validator->validate($studentPrice);
                        if($validationErrors->count() == 0) {
                            $productManager->persist($product);
                            $priceManager->persist($memberPrice);
                            $priceManager->persist($studentPrice);

                            return $this->redirectToRoute('product_list', [
                                "message" => "Modification avec succès"
                            ]);
                        }
                    }
                }
            }
        }
        return $this->render('product/EditModalProduct.html.twig', [
                'productTypes' => $productTypes,
                'validationErrors' => $validationErrors,
                'productExistsError' => $productExistsError,
                'product' => $product,
                'studentPrice'=>$studentPrice->getPrice(),
                'memberPrice'=>$memberPrice->getPrice()
            ]);
    }
}