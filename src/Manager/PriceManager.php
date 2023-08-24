<?php

namespace App\Manager;

use App\Entity\Client;
use App\Entity\Price;
use App\Entity\Product;
use App\Repository\PriceRepository;
use App\Utils\Enum\ClientType;
use Doctrine\ORM\EntityManagerInterface;

class PriceManager {
    private EntityManagerInterface $manager;
    private PriceRepository $priceRepository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->manager = $entityManager;
        $this->priceRepository = $this->manager->getRepository(Price::class);
    }

    public function persist(Price $price): void {
        if ($this->verifyPrice($price)) {
            $this->manager->persist($price);
            $this->manager->flush();
        }
    }

    public function remove(Price $price): void {
        $this->manager->remove($price);
        $this->manager->flush();
    }

    /**
     * Set both price in one time
     * @param Price $memberPrice
     * @param Price $studentPrice
     * @param Product $product
     * @param String $memberPriceAmount
     * @param String $studentPriceAmount
     * @return void
     */
    public function setData(Price $memberPrice, Price $studentPrice, Product $product, string $memberPriceAmount, string $studentPriceAmount): void {
        $memberType = ClientType::ASSOCIATION;
        $studentType = ClientType::ETUDIANT;

        $memberPrice->setClientType($memberType)
            ->setPrice($memberPriceAmount)
            ->setProduct($product);

        $studentPrice->setClientType($studentType)
            ->setPrice($studentPriceAmount)
            ->setProduct($product);
    }

    /**
     * @param Price $price
     * @return bool
     */
    public function verifyPrice(Price $price): bool {
        return ($price->getPrice() >= 0 && $price->getProduct() != null && $price->getClientType() != null);
    }

    /**
     * Retourne le type de prix concerné par le type de client passé en paramètre
     * @param Client|null $client
     * @return string
     */
    public function getClientTypeUsedForPrice(?Client $client): string {
        if ($client) {
            switch ($client->getClientType()) {
                case ClientType::ETUDIANT :
                    $clientTypeReturn = ClientType::ETUDIANT;
                    break;

                case ClientType::ASSOCIATION :
                    $clientTypeReturn = ClientType::ASSOCIATION;
                    break;

                case ClientType::COTISANT :
                    $parameterManager = new ParameterManager(($this->manager));
                    $parameter = $parameterManager->getParameter();

                    if ($parameter->isCotisantActivated()) {
                        $clientTypeReturn = ClientType::ASSOCIATION;
                    } else {
                        $clientTypeReturn = ClientType::ETUDIANT;
                    }
                    break;

                default:
                    $clientTypeReturn = ClientType::ETUDIANT;
            }
        } else {
            $clientTypeReturn = ClientType::ETUDIANT;
        }

        return $clientTypeReturn;
    }

    public function getPriceByProductAndClientType(Product $product, string $clientType): float {
        return $this->priceRepository->findOneBy(["product" => $product, "clientType" => $clientType])->getPrice();
    }
}