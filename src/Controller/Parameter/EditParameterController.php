<?php

namespace App\Controller\Parameter;

use App\Manager\ParameterManager;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

class EditParameterController extends AbstractController
{
    /**
     * @Route("/parameter", name="editParameter")
     */
    public function index(Request $request, EntityManagerInterface $manager): Response {
        if (!$this->isGranted(SymfonyRole::PRESIDENT)) {
            return $this->redirectToRoute("home");
        }

        $message = null;
        $parameterManager = new ParameterManager($manager);
        $parameter = $parameterManager->getParameter();
        $data = $request->request;

        if ($data->count() > 0) {

            $parameterManager->setData(
                $parameter,
                $parameterManager->downloadPicture($parameter, $request->files->get("associationLogo")),
                $data->get("amountFidelityPointToExchange"),
                $data->get("amountBalanceToAddAfterExchange"),
                $data->get("cotisantActivated"),
                $data->get("postActivated")
            );

            $parameterManager->persist($parameter);
        }

        return $this->render("parameter/EditParameter.html.twig", [
            "message" => $message,
            "parameter" => $parameter
        ]);
    }
}
