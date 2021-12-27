<?php

namespace App\Controller\Command;

use App\Entity\Client;
use App\Entity\ClientType;
use App\Entity\Price;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandPaymentController extends AbstractController
{
    /**
     * @Route("/command/payment/{productOrderedSerialized}{idClient}", name="command_payment")
     */
    public function index($productOrderedSerialized, $idClient, EntityManagerInterface $manager): Response
    {
        $clientRepository = $manager->getRepository(Client::class);
        $clientTypeRepository = $manager->getRepository(ClientType::class);
        $priceRepository = $manager->getRepository(Price::class);
        $productRepository = $manager->getRepository(Product::class);
        $clientOrder = new Client();
        $clientType = $clientTypeRepository->findOneBy(["name" => "Etudiant"]);
        $listProduct = [];

dd($idClient);
        $productOrderedIdTab = unserialize($productOrderedSerialized);

        if ($idClient != null){
            $clientOrder = $clientRepository->find($idClient);
            $clientType = $clientOrder->getClientType();
        }
        $montantProduct = [];
        $montantTotal = 0;

        foreach ($productOrderedIdTab as $idProduct => $quantity){
            $product = $productRepository->find($idProduct);
            $listProduct[] = $product;


            $montantProduct = $montantProduct + [$idProduct => $priceRepository->findOneBy(['product' => $product,
                'clientType' => $clientType])->getPrice() *$quantity];

            $montantTotal = $montantTotal + $montantProduct[$idProduct];
        }



        return $this->render('command/payment.html.twig', [
            'listProduct' => $listProduct,
            'productQuantity' => $productOrderedIdTab,
            'montantProduct' => $montantProduct,
            'montantTotal' => $montantTotal
        ]);
    }
}
