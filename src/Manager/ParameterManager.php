<?php

namespace App\Manager;

use App\Entity\Parameter;
use App\Repository\ParameterRepository;
use App\Utils\PictureUtils;
use App\Utils\RandomUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ParameterManager
{
    public EntityManagerInterface $manager;
    private ParameterRepository $parameterRepository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->manager = $entityManager;
        $this->parameterRepository = $this->manager->getRepository(Parameter::class);
    }

    public function persist(Parameter $parameter): void {
        $this->manager->persist($parameter);
        $this->manager->flush();
    }

    public function remove(Parameter $parameter): void {
        $this->manager->remove($parameter);
        $this->manager->flush();
    }

    public function setData(Parameter $parameter, ?string $linkLogo, ?int $amountFidelityPointToExchange,
        ?string $amountBalanceToAddAfterExchange, ?bool $cotisantActivated, ?bool $postActivated): void {

        if ($linkLogo){
            $parameter->setLinkLogo($linkLogo);
        }

        if ($amountFidelityPointToExchange != "" && intval($amountFidelityPointToExchange) > 0){
            $parameter->setAmountFidelityPointToExchange(intval($amountFidelityPointToExchange));
        } else {
            $parameter->setAmountFidelityPointToExchange(150);
        }

        if ($amountBalanceToAddAfterExchange != "" && floatval($amountBalanceToAddAfterExchange)){
            $parameter->setAmountBalanceToAddAfterExchange(floatval($amountBalanceToAddAfterExchange));
        } else {
            $parameter->setAmountBalanceToAddAfterExchange(0.8);

        }

        $parameter->setCotisantActivated($cotisantActivated)
                  ->setPostActivated($postActivated);
    }

    public function getParameter(): Parameter {
        return $this->parameterRepository->findOneBy([]);
    }

    public function downloadPicture(Parameter $parameter, ?UploadedFile $file): ?string {
        if ($file != null) {
            $pictureUtils = new PictureUtils();
            $randomUtils = new RandomUtils();
            $characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

            if (($parameter->getLinkLogo() != (null || "")) && !str_contains($parameter->getLinkLogo(), "placeholder")) {
                $pictureUtils->deletePicture($parameter->getLinkLogo());
            }

            $newLocation = "/img/entity/parameter/img_" . $randomUtils->randomString(4, $characters) . ".png";
            $parameter->setLinkLogo($pictureUtils->downloadPictureFromFile($file, $newLocation));
        } else {
            //$parameter->setLinkLogo("/img/entity/placeholder.png");
        }

        return $parameter->getLinkLogo();
    }
}