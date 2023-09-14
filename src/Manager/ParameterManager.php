<?php

namespace App\Manager;

use App\Entity\Parameter;
use App\Repository\ParameterRepository;
use App\Utils\CacheUtils;
use App\Utils\Exception\ApplicationException;
use App\Utils\PictureUtils;
use App\Utils\RandomUtils;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ParameterManager
{
    private EntityManagerInterface $manager;
    private ParameterRepository $parameterRepository;
    const CACHE_KEY_PARAMETER = "parameter";

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

        if ($linkLogo) {
            $parameter->setLinkLogo($linkLogo);
        }

        if ($amountFidelityPointToExchange != "" && intval($amountFidelityPointToExchange) > 0) {
            $parameter->setAmountFidelityPointToExchange(intval($amountFidelityPointToExchange));
        } else {
            $parameter->setAmountFidelityPointToExchange(150);
        }

        if ($amountBalanceToAddAfterExchange != "" && floatval($amountBalanceToAddAfterExchange)) {
            $parameter->setAmountBalanceToAddAfterExchange(floatval($amountBalanceToAddAfterExchange));
        } else {
            $parameter->setAmountBalanceToAddAfterExchange(0.8);
        }

        $parameter->setCotisantActivated($cotisantActivated)
                  ->setPostActivated($postActivated);
    }

    /**
     * @param bool $force
     * @return Parameter
     */
    public function getParameter(bool $force = false): Parameter {
        $cacheUtils = new CacheUtils();

        try {
            $parameter = $cacheUtils->getFromCache(self::CACHE_KEY_PARAMETER);

            if ($force || is_null($parameter)) {
                $parameter = $this->parameterRepository->findOneBy([]);
                $cacheUtils->saveInCache($parameter, self::CACHE_KEY_PARAMETER);
            }
        } catch (InvalidArgumentException $e) {
            $parameter = $this->getDefaultParameter();
        }

        return $parameter;
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
        }

        return $parameter->getLinkLogo();
    }

    public function isDataCorrect(string $amountFidelityPointToExchange, string $amountBalanceToAddAfterExchange): bool {
        return (
            is_numeric($amountFidelityPointToExchange) && intval($amountFidelityPointToExchange) != 0 &&
            is_numeric($amountBalanceToAddAfterExchange) && floatval($amountBalanceToAddAfterExchange) != 0
        );
    }

    /**
     * @return Parameter
     */
    private function getDefaultParameter(): Parameter {
        $parameter = new Parameter();
        $parameter->setAmountFidelityPointToExchange(150)
                  ->setAmountBalanceToAddAfterExchange(0.80)
                  ->setPostActivated(true)
                  ->setPostActivated(true);

        return $parameter;
    }
}