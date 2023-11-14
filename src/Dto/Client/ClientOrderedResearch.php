<?php

namespace App\Dto\Client;

use App\Entity\Client;

class ClientOrderedResearch
{
    private int $id;
    private string $name;

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): ClientOrderedResearch {
        $this->id = $id;
        return $this;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): ClientOrderedResearch {
        $this->name = $name;
        return $this;
    }

    public static function clientAsClientOrderedResearch(Client $client): ClientOrderedResearch {
        $clientDto = new ClientOrderedResearch();

        $clientDto->setName($client->getFirstName() . " " . $client->getName())
                  ->setId($client->getId());

        return $clientDto;
    }
}