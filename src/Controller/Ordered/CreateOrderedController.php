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
     * @Route("/ordered/create/{message?}", name="createOrdered")
     */
    public function index(Request $request, EntityManagerInterface $manager, ProductRepository $productRepository,
                          ClientRepository $clientRepository, string $message = null): Response
    {
        if (!$this->isGranted(SymfonyRole::ASSOC)) {
            return $this->redirectToRoute('home');
        }

        $inputParameterBag = $request->request;
        $productOrdered = [];

        // Recupere tout les produits avec un stock positif afin d'afficher uniquement ceux disponibles
        $allProductPositiveStock = $productRepository->findAllPositiveStock();
        // Recupere tout les clients par ordre alphabetique
        $allClient = $clientRepository->findBy([], ["name" => "ASC"]);

        if ($request->request->count() > 0) {
            $message = '';

            // Recupere toutes les quantités de produit selectionné
            foreach ($allProductPositiveStock as $product) {
                $quantity = $inputParameterBag->get("quantityOrdered_" . $product->getId());

                // Vérifie si l'on a commandé le produit $product
                if (is_numeric($quantity) && $quantity > 0 && $quantity <= $product->getQuantityStock()) {
                    $productOrdered[] = ["idProduct" => $product->getId(), "quantity" => $quantity];
                } else if ($quantity != 0) {
                    $message = "Merci de vérifier la saisie des quantités";
                }
            }

            if (!$message) {
                // Si l'on a commandé au moins 1 produit
                if ($productOrdered) {
                    $idClient = $inputParameterBag->get("orderedClient");

                    $productOrderedAndClient = ["idClient" => $idClient, "productOrdered" => $productOrdered];
                    $request->getSession()->set("productOrderedAndClient", $productOrderedAndClient);

                    return $this->redirectToRoute("orderedPayment", [], 308);
                } else {
                    $message = 'Merci de saisir au moins 1 produit';
                }
            }
        }

        return $this->render('ordered/CreateOrdered.html.twig', [
            "productList" => $allProductPositiveStock,
            "clientList" => $allClient,
            "message" => $message
        ]);
    }
}
