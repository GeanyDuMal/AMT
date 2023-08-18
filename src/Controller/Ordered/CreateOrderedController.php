<?php

namespace App\Controller\Ordered;


use App\Manager\ParameterManager;
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
        ClientRepository $clientRepository, string $message = null): Response {
        if (!$this->isGranted(SymfonyRole::ASSOC)) {
            return $this->redirectToRoute('home');
        }

        $parameterManager = new ParameterManager($manager);
        $parameter = $parameterManager->getParameter();
        $data = $request->request;
        $productOrdered = [];

        $allProductPositiveStock = $productRepository->findAllPositiveStock();
        $allClient = $clientRepository->findBy([], ["name" => "ASC"]);

        if ($data->count() > 0) {
            $message = '';

            // Recupere toutes les quantités de produit selectionné
            foreach ($allProductPositiveStock as $product) {
                $quantity = $data->get("quantityOrdered_" . $product->getId());

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
                    $idClient = $data->get("orderedClient");

                    $productOrderedAndClient = ["idClient" => $idClient, "productOrdered" => $productOrdered];
                    $request->getSession()->set("productOrderedAndClient", $productOrderedAndClient);

                    return $this->redirectToRoute("orderedPayment", [], 308);
                } else {
                    $message = "Merci de saisir au moins 1 produit";
                }
            }
        }

        return $this->render("ordered/CreateOrdered.html.twig", [
            "parameter" => $parameter,
            "productList" => $allProductPositiveStock,
            "clientList" => $allClient,
            "message" => $message
        ]);
    }
}
