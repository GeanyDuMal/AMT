<?php

namespace App\Manager;

use App\Entity\Client;
use App\Entity\PasswordForgotRequest;
use App\Repository\PasswordForgotRequestRepository;
use App\Utils\Enum\ClientType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
        $this->manager->persist($passwordForgotRequest->getClient());

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
            $code = $code . $characters[rand(0, strlen($characters)-1)];
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

    /**
     * Verify if a request for this account already exist
     * @param Client $client
     * @return bool
     */
    public function verifyExist(Client $client): bool
    {
        return ($this->passwordForgotRequestRepository->findOneBy(["client" => $client]) != null);
    }

    /**
     * Remove the old request if one exist
     * @param Client $client
     * @return void
     */
    public function removeOldIfExist(Client $client)
    {
        if ($this->verifyExist($client)){
            $passwordForgotRequest = $this->passwordForgotRequestRepository->findOneBy(["client" => $client]);

            $this->remove($passwordForgotRequest);
        }
    }

    /**
     * @param PasswordForgotRequest $passwordForgotRequest
     * @param UserPasswordHasherInterface $passwordHasher
     * @return void
     */
    public function generateNewPassword(PasswordForgotRequest $passwordForgotRequest, UserPasswordHasherInterface $passwordHasher): void
    {
        $client = $passwordForgotRequest->getClient();
        $clientManager = new ClientManager($this->manager);

        // Generate a new code
        $this->generateCode($passwordForgotRequest);

        $client->setPassword($passwordHasher->hashPassword($client, trim($passwordForgotRequest->getConfirmationCode()).'.'));

        $clientManager->persist($client);
        $clientManager->deletePasswordForgotRequest($client);
    }
}