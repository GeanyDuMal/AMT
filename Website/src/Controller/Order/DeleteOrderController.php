<?php

namespace App\Controller\Order;

use App\Entity\Order;
use App\Manager\OrderManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DeleteOrderController extends AbstractController
{
    /**
     * @Route("/order/menu/delete/{id}", name="orderDelete", methods={"GET", "DELETE"})
     */
    public function delete($id, EntityManagerInterface $manager){
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
        $orderRepository = $manager->getRepository(Order::class);
        $orderManager = new OrderManager($manager);
        $order = $orderRepository->find($id);

        $orderManager->removeWithRestore($order);

        return new JsonResponse(true);
    }
}