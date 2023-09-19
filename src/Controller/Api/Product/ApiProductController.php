<?php

namespace App\Controller\Api\Product;

use App\Manager\ClientManager;
use App\Manager\ProductManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiProductController extends AbstractController
{
    private ClientManager $clientManager;

    /**
     * @Route("/api/product/getAll", name="apiProductGetAll")
     */
    public function index(EntityManagerInterface $manager, Request $request): JsonResponse {
        $this->clientManager = new ClientManager($manager);
        $productManager = new ProductManager($manager);
        $headers = $request->headers;

        if (!$this->checkAuthenticate($headers->get("login"), $headers->get("password"))){
            return $this->json([], Response::HTTP_FORBIDDEN);
        }

        return $this->json($productManager->getAllProductAvailable(), Response::HTTP_OK, [], ["groups" => ["product", "price"]]);
    }

    private function checkAuthenticate(?string $login, ?string $password): bool {
        if (!$login || !$password) {
            return false;
        }

        $client = $this->clientManager->getClientByLogin($login);

        return ($client && password_verify($password, $client->getPassword()));
    }
}
