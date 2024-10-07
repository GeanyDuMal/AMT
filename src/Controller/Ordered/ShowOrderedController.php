<?php

namespace App\Controller\Ordered;

use App\Manager\OrderedManager;
use App\Manager\ParameterManager;
use App\Utils\Enum\OrderedStatus;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowOrderedController extends AbstractController {

    private EntityManagerInterface $manager;
    private OrderedManager $orderedManager;
    private ParameterManager $parameterManager;

    public function __construct(EntityManagerInterface $manager) {
        $this->manager = $manager;
        $this->orderedManager = new OrderedManager($manager);
        $this->parameterManager = new ParameterManager($manager);
    }

    #[Route("/ordered/show&id={!idOrder}", name: "showOrdered", methods: ["GET", "POST"])]
    public function index($idOrder, Request $request): Response {
        if (!$this->isGranted(SymfonyRole::TRESORIER)) {
            return $this->redirectToRoute('home');
        }

        $parameter = $this->parameterManager->getParameter();
        $priceList = [];
        $inputParameterBag = $request->request;
        $toCancel = $inputParameterBag->get("cancel");
        $toRefund = $inputParameterBag->get("refund");

        if (is_numeric($idOrder)) {
            $ordered = $this->orderedManager->getOrderedById($idOrder);

            if ($ordered != null) {
                // Suppression ou annulation
                if ($toCancel || $toRefund) {
                    if ($toCancel) {
                        $this->orderedManager->cancel($ordered);
                    } else {
                        $this->orderedManager->refund($ordered);
                    }

                    return $this->redirectToRoute("menuOrdered", [
                        "message" => "La commande a été remboursée avec succès"
                    ]);
                }
            } else {
                return $this->redirectToRoute("home");
            }
        } else {
            return $this->redirectToRoute("home");
        }

        return $this->render("ordered/ShowOrdered.html.twig", [
            "parameter" => $parameter,
            "ordered" => $ordered,
            "priceList" => $priceList,
            "montantTotal" => $this->orderedManager->getMontantTotal($ordered),
            "statusPaid" => OrderedStatus::PAID,
            "statusWaitingPayment" => OrderedStatus::WAITING_PAYMENT,
        ]);
    }
}
