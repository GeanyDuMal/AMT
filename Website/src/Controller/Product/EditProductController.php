<?php

namespace App\Controller\Product;

use App\Entity\Price;
use App\Entity\Product;
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
     * @Route("/product/edit/{id}", name="edit_product")
     */
    public function index($id,ProductRepository $productRepository,ValidatorInterface $validator,Request $request,EntityManagerInterface $manager,ProductTypeRepository $productTypeRepository,ClientTypeRepository $clientTypeRepository,PriceRepository $priceRepository): Response
    {
        if (!$this->isGranted('ROLE_ASSOC')){
            return $this->redirectToRoute('home');
        }
        $data = $request->request;
        $product=$productRepository->find($id);
        $productTypes=$productTypeRepository->findAll();
        $typeMember=$clientTypeRepository->findBy(["name"=>"Association"]);
        $typeStudent=$clientTypeRepository->findBy(["name"=>"Etudiant"]);
        $memberPrice=$priceRepository->findBy(["product"=>$product,"clientType"=>$typeMember])[0];
        $studentPrice=$priceRepository->findBy(["product"=>$product,"clientType"=>$typeStudent])[0];
        //
        $validationErrors="";
        $productExistsError="";
        if($data->count()>0){
            $commonMethods=new CommonProductMethods();
            $commonMethods->setDataForProduct($product,$request,$productTypeRepository);
            $validationErrors = $validator->validate($product);
            if($validationErrors->count()==0){
                $ChosenProductName=$productRepository->find($id)->getName();
                $newProductName=$product->getName();
                if(
                    strcmp($ChosenProductName,$newProductName)!=0 &&
                    $productRepository->findOneBy(['name'=>$newProductName])
                )
                    $productExistsError="Produit existe déja";
                else{
                    $commonMethods->setDataForPrice($memberPrice,$studentPrice,$product,$request,$clientTypeRepository);
                    $validationErrors=$validator->validate($memberPrice);
                    if($validationErrors->count()==0){
                        $validationErrors=$validator->validate($studentPrice);
                        if($validationErrors->count()==0) {
                            $manager->persist($product);
                            $manager->flush();
                            $manager->persist($memberPrice);
                            $manager->flush();
                            $manager->persist($studentPrice);
                            $manager->flush();
                            return $this->redirectToRoute('product_list', ["message" => "Modification avec succès"]);
                        }
                    }
                }
            }
        }
        return $this->render('product/EditModalProduct.html.twig',
            [
                'productTypes' => $productTypes,
                'validationErrors' => $validationErrors,
                'productExistsError' => $productExistsError,
                'product' => $product,
                'studentPrice'=>$studentPrice->getPrice(),
                'memberPrice'=>$memberPrice->getPrice()
            ]
        );
    }
}