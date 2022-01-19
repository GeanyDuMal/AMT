<?php

namespace App\Controller\Product;

use App\Entity\Client;
use App\Entity\ClientType;
use App\Entity\Price;
use App\Entity\Product;
use App\Manager\ClientManager;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowProductController extends AbstractController
{
    /**
     * @Route("/product{message}", name="product_list",methods={"GET", "POST"} )
     */
    public function show(string $message = null,EntityManagerInterface $manager): Response
    {
        $clientTypeRepository = $manager->getRepository(ClientType::class);

        if ($this->isGranted("ROLE_ASSOC")){
            $clientTypeActual = (Client::class)($this->getUser())->getClientType();
        }else{
            $clientTypeActual = $clientTypeRepository->findOneBy(["name" => "Etudiant"]);
        }



        $products=$manager->getRepository(Product::class)->findAll();
        $prices=$manager->getRepository(Price::class)->findBy(["clientType"=> $clientTypeActual]);
        foreach ($prices as $price){

        }
        return $this->render('product/productList.html.twig',[
            'products'=>$products,
            'message'=>$message
        ]);
    }
}
