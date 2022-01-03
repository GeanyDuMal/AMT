<?php

namespace App\Controller\Command;

use App\Entity\Command;
use App\Entity\Purchase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DeleteOrderController extends AbstractController
{
    /**
     * @Route("/command/menu/delete/{id}", name="orderDelete", methods={"GET" ,"DELETE"})
     */
    public function delete($id, EntityManagerInterface $manager): JsonResponse
    {
        if (!$this->isGranted('ROLE_TRESORIER')){
            return new JsonResponse(false);
        }
        /**
         * Delete an order will delete all the purchase linked
         */
        $orderManager = $manager->getRepository(Command::class);
        $purchaseManager = $manager->getRepository(Purchase::class);

        $order = $orderManager->find($id);
        $purchaseList = $purchaseManager->findBy(["command" => $order]);

        foreach ($purchaseList as $purchase){
            $manager->remove($purchase);
        }
        $manager->flush();

        $manager->remove($order);
        $manager->flush();
        return new JsonResponse(true);
    }
}