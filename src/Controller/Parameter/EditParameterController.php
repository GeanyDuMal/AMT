<?php

namespace App\Controller\Parameter;

use App\Manager\ParameterManager;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditParameterController extends AbstractController
{
    /**
     * @Route("/parameter", name="editParameter")
     */
    public function index(Request $request, EntityManagerInterface $manager): Response {
        if (!$this->isGranted(SymfonyRole::PRESIDENT)) {
            return $this->redirectToRoute("home");
        }

        $parameterManager = new ParameterManager($manager);
        $parameter = $parameterManager->getParameter(true);
        $data = $request->request;
        $message = null;

        if ($data->count() > 0) {
            $cotisantActivated = (bool)$data->get("cotisantActivated");
            $postActivated = (bool)$data->get("postActivated");

            if ($parameterManager->isDataCorrect($data->get("amountFidelityPointToExchange"), $data->get("amountBalanceToAddAfterExchange"))) {
                $parameterManager->setData(
                    $parameter,
                    $parameterManager->downloadPicture($parameter, $request->files->get("associationLogo")),
                    $data->get("amountFidelityPointToExchange"),
                    $data->get("amountBalanceToAddAfterExchange"),
                    $cotisantActivated,
                    $postActivated
                );

                $parameterManager->persist($parameter);
            } else {
                $message = "Paramêtres érronés";
            }
        }

        return $this->render("parameter/EditParameter.html.twig", [
            "parameter" => $parameter,
            "message" => $message,
        ]);
    }
}
