<?php

namespace App\Controller\Command;

use App\Entity\Client;
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
        $priceRepository = $manager->getRepository(Price::class);
        $productRepository = $manager->getRepository(Product::class);

        $productOrderedIdTab = unserialize($productOrderedSerialized);

        if ($idClient != -1){
            $clientOrder = $clientRepository->find($idClient);
        }
        $montantProduct = [];

        foreach ($productOrderedIdTab as $idProduct => $quantity){
            $product = $productRepository->find($idProduct);

            dd($priceRepository->findOneBy(['product' => $product,
                'clientType' => $clientOrder->getClientType()]));

            $montantProduct = $montantProduct + [$idProduct => $priceRepository->findOneBy(['product' => $product,
                'clientType' => $clientOrder->getClientType()])->getPrice()*$quantity];
        }

        return $this->render('command/payment.html.twig', [
            'controller_name' => 'CommandPaymentController',
        ]);
    }
}
