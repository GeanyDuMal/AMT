<?php

namespace App\Manager;

use App\Entity\PasswordForgotRequest;
use App\Repository\PasswordForgotRequestRepository;
use Doctrine\ORM\EntityManagerInterface;

class PasswordForgotRequestManager
{
    public EntityManagerInterface $manager;
    public PasswordForgotRequestRepository $passwordForgotRequestRepository;

    public function __construct(EntityManagerInterface $managerController)
    {
        $this->manager = $managerController;
        $this->passwordForgotRequestRepository = $this->manager->getRepository(PasswordForgotRequest::class);
    }

    public function persist(PasswordForgotRequest $passwordForgotRequest): void
    {
        $this->manager->persist($passwordForgotRequest);
        $this->manager->flush();
    }

    public function remove(PasswordForgotRequest $passwordForgotRequest): void
    {
        $this->manager->remove($passwordForgotRequest);
        $this->manager->flush();
    }

    /**
     * Define a confirmation code for the current $passwordForgotRequest
     * @param PasswordForgotRequest $passwordForgotRequest
     * @return void
     */
    public function generateCode(PasswordForgotRequest $passwordForgotRequest): void
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = "";

        for ($i = 0; $i < 8; $i++) {
            $code = $code . $characters[rand(0, strlen($characters))];
        }

        $passwordForgotRequest->setConfirmationCode($code);
    }

    /**
     * @param PasswordForgotRequest $passwordForgotRequest
     * @param string $confirmationCode
     * @return bool
     */
    public function verifyConfirmationCode(PasswordForgotRequest $passwordForgotRequest, string $confirmationCode): bool
    {
        return ($passwordForgotRequest->getConfirmationCode() === trim($confirmationCode));
    }
}