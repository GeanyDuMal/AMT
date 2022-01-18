<?php

namespace App\Controller\Ordered;

use App\Entity\Ordered;
use App\Manager\OrderedManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DeleteOrderedController extends AbstractController
{
    /**
     * @Route("/ordered/menu/delete/{id}", name="orderedDelete", methods={"GET", "DELETE"})
     */
    public function delete($id, EntityManagerInterface $manager): JsonResponse
    {
        if (!$this->isGranted('ROLE_TRESORIER')){
            return new JsonResponse(false);
        }

        /**
         * Delete an order will :
         * delete all the purchase linked
         * restore the quantity of the product
         * restore the balance of the client if he paid with
         * remove the fidelityPoint earned
         */
        $orderRepository = $manager->getRepository(Ordered::class);
        $orderManager = new OrderedManager($manager);
        $order = $orderRepository->find($id);

        $orderManager->removeWithRestore($order);

        return new JsonResponse(true);
    }
}