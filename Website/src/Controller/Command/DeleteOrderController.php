<?php

namespace App\Controller\Command;

use App\Entity\Command;
use App\Entity\Purchase;
use App\Manager\OrderManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DeleteOrderController extends AbstractController
{
    /**
     * @Route("/command/menu/delete/{id}", name="orderDelete", methods={"GET", "DELETE"})
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
        $orderRepository = $manager->getRepository(Command::class);
        $orderManager = new OrderManager($manager);
        $order = $orderRepository->find($id);

        $orderManager->removeWithRestore($order);

        return new JsonResponse(true);
    }
}