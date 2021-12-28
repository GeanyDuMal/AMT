<?php

namespace App\Controller\Product;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\ProductTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddProductController extends AbstractController
{
    /**
     * @Route("/product/add", name="add_product")
     */
    public function index(ProductRepository $productRepository,ValidatorInterface $validator,Request $request,EntityManagerInterface $manager,ProductTypeRepository $productTypeRepository): Response
    {
        if (!$this->isGranted('ROLE_ASSOC')){
            return $this->redirectToRoute('home');
        }
        $data = $request->request;
        $produit=new Product();
        $productTypes=$productTypeRepository->findAll();
        $validationErrors="";
        $productExistsError="";
        if($data->count()>0){
            $commonMethods=new CommonProductMethods();
            $commonMethods->setData($produit,$request,$productTypeRepository);
            $validationErrors = $validator->validate($produit);
            if($validationErrors->count()==0){
                if($productRepository->findBy(['name'=>$produit->getName()]))
                    $productExistsError="Produit existe déja";
                else{
                    $manager->persist($produit);
                    $manager->flush();
                    return $this->redirectToRoute('product_list',["message"=>"Ajout avec succès"]);
                }
            }
        }
        return $this->render('product/AddModalProduct.html.twig',
            [
                'productTypes' => $productTypes,
                'validationErrors' => $validationErrors,
                'productExistsError' => $productExistsError,
                'produit' => $produit
            ]
        );
    }
}