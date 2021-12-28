<?php

namespace App\Controller\Product;

use App\Entity\Product;
use App\Repository\ProductTypeRepository;
use Symfony\Component\HttpFoundation\Request;

class CommonProductMethods
{
    public function setData(Product &$product,Request $request,ProductTypeRepository $productTypeRepository){
        $data = $request->request;
        $productType=$data->get('productType');
        $type=$productTypeRepository->findOneBy(["name"=>$productType]);
        $productName=$data->get("ProductName");
        $productStock=$data->get("productStock");
        $imageLink=$data->get("imageLink");
        //
        $product->setName($productName);
        $product->setImageLink($imageLink);
        $product->setQuantityStock($productStock);
        $product->setProductType($type);
    }
}