<?php

namespace App\Controller\Parameter;

use App\Manager\ParameterManager;
use App\Utils\Enum\SymfonyRoleEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditParameterController extends AbstractController
{
    private EntityManagerInterface $manager;
    private ParameterManager $parameterManager;

    public function __construct(EntityManagerInterface $manager) {
        $this->manager = $manager;
        $this->parameterManager = new ParameterManager($this->manager);
    }

    #[Route("/parameter", name: "editParameter", methods: ["GET", "POST"])]
    public function index(Request $request): Response {
        if (!$this->isGranted(SymfonyRoleEnum::PRESIDENT->value)) {
            return $this->redirectToRoute("home");
        }

        $parameter = $this->parameterManager->getParameter(true);
        $data = $request->request;
        $message = null;

        if ($data->count() > 0) {
            $cotisantActivated = (bool)$data->get("cotisantActivated");
            $postActivated = (bool)$data->get("postActivated");
            $associationName = $data->get("associationName");
            $associationDescription = $data->get("associationDescription");

            if ($this->parameterManager->isDataCorrect($data->get("amountFidelityPointToExchange"), $data->get("amountBalanceToAddAfterExchange"))) {
                $this->parameterManager->setData(
                    $parameter,
                    $associationName,
                    $associationDescription,
                    $this->parameterManager->downloadPictureHome($parameter, $request->files->get("homeImage")),
                    $this->parameterManager->downloadPictureLogo($parameter, $request->files->get("associationLogo")),
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
