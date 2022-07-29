<?php

namespace App\Controller\Ordered;

use App\Manager\OrderedManager;
use App\Repository\OrderedRepository;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DeleteOrderedController extends AbstractController
{
    /**
     * @Route("/ordered/menu/delete/{!id}", name="deleteOrdered", methods={"GET", "DELETE"})
     */
    public function delete($id, EntityManagerInterface $manager, OrderedRepository $orderedRepository): JsonResponse
    {
        if (!$this->isGranted(SymfonyRole::TRESORIER)) {
            return new JsonResponse(false);
        }

        /*
         * Delete an order will :
         * delete all the purchase linked
         * restore the quantity of the product
         * restore the balance of the client if he paid with
         * remove the fidelityPoint earned
         */
        $orderManager = new OrderedManager($manager);
        $order = $orderedRepository->find($id);

        $orderManager->removeWithRestore($order);

        return new JsonResponse(true);
    }
}