<?php

namespace App\Controller\Product;

use App\Entity\ClientType;
use App\Entity\Price;
use App\Entity\Product;
use App\Repository\ClientTypeRepository;
use App\Repository\PriceRepository;
use App\Repository\ProductTypeRepository;
use Symfony\Component\HttpFoundation\Request;

class CommonProductMethods
{
    public function setDataForProduct(Product &$product,Request $request,ProductTypeRepository $productTypeRepository){
        $data = $request->request;
        $productType=$data->get('productType');
        $type=$productTypeRepository->findOneBy(["name"=>$productType]);
        $productName=$data->get("productName");
        $productStock=$data->get("productStock");
        $imageLink=$data->get("imageLink");
        //
        $product->setName($productName);
        $product->setImageLink($imageLink);
        $product->setQuantityStock($productStock);
        $product->setProductType($type);
    }
    public function setDataForPrice(Price &$memberPrice,Price &$studentPrice,Product $product,Request $request,ClientTypeRepository $clientTypeRepository){
        $data = $request->request;
        $memberType=$clientTypeRepository->findOneBy(["name"=>"Association"]);
        $studentType=$clientTypeRepository->findOneBy(["name"=>"Etudiant"]);
        //
        $memberPrice->setClientType($memberType);
        $memberPrice->setPrice($data->get("memberPrice"));
        $memberPrice->setProduct($product);
        //
        $studentPrice->setClientType($studentType);
        $studentPrice->setPrice($data->get("studentPrice"));
        $studentPrice->setProduct($product);

    }
}