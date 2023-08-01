<?php

namespace App\Manager;

use App\Entity\Parameter;
use App\Repository\ParameterRepository;
use Doctrine\ORM\EntityManagerInterface;

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

    public function setData(Parameter $parameter, string $linkLogo, int $amountFidelityPointToExchange,
        int $amountBalanceToAddAfterExchange, bool $cotisantAllowed): void {
        $parameter->setLinkLogo($linkLogo)
                  ->setAmountFidelityPointToExchange($amountFidelityPointToExchange)
                  ->setAmountBalanceToAddAfterExchange($amountBalanceToAddAfterExchange)
                  ->setCotisantAllowed($cotisantAllowed);
    }
}