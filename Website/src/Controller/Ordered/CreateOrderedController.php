<?php

namespace App\Controller\Ordered;


use App\Repository\ClientRepository;
use App\Repository\ProductRepository;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateOrderedController extends AbstractController
{
    /**
     * @Route("/ordered/create/{message?}", name="orderedCreate")
     */
    public function index(Request          $request, EntityManagerInterface $manager, ProductRepository $productRepository,
                          ClientRepository $clientRepository, string $message = null): Response
    {
        if (!$this->isGranted(SymfonyRole::ASSOC)) {
            return $this->redirectToRoute('home');
        }

        $user = $this->getUser();
        $allProductPositiveStock = [];
        $inputParameterBag = $request->request;
        $productOrdered = [];

        // Recupere tout les produits avec un stock positif afin d'afficher uniquement ceux disponibles
        $allProduct = $productRepository->findBy([], ["productType" => "ASC"]);
        foreach ($allProduct as $product) {
            if ($product->getQuantityStock() > 0) {
                $allProductPositiveStock[] = $product;
            }
        }

        // Recupere toutes les quantités de produit selectionné
        foreach ($allProductPositiveStock as $product) {
            $quantity = $inputParameterBag->get("quantity_product_" . $product->getId());
            if ($quantity != 0) {
                $productOrdered = $productOrdered + [$product->getId() => $quantity];
            }
        }

        // Si l'on a commandé au moins 1 produit
        if ($productOrdered) {
            return $this->redirectToRoute("orderedPayment", [
                "productOrderedSerialized" => serialize($productOrdered),
                "idClient" => $inputParameterBag->get("client_commande")
            ]);
        }

        // Recupere tout les clients par ordre alphabetique
        $allClient = $clientRepository->findBy([], ["name" => "ASC"]);

        return $this->render('ordered/create.html.twig', [
            "user" => $user,
            "productList" => $allProductPositiveStock,
            "clientList" => $allClient,
            "message" => $message
        ]);
    }
}
