<?php

namespace App\Manager;

use App\Entity\Client;
use App\Entity\Price;
use App\Entity\Product;
use App\Utils\Enum\ClientType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use JetBrains\PhpStorm\Pure;
use function PHPUnit\Framework\assertContains;

class PriceManager
{
    public EntityManagerInterface $manager;
    public ObjectRepository $priceRepository;

    public function __construct(EntityManagerInterface $managerController)
    {
        $this->manager = $managerController;
        $this->priceRepository = $this->manager->getRepository(Price::class);
    }

    public function persist(Price $price): void
    {
        if ($this->verifyPrice($price)) {
            $this->manager->persist($price);
            $this->manager->flush();
        }
    }

    public function remove(Price $price): void
    {
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
    public function setData(Price  $memberPrice, Price $studentPrice, Product $product, string $memberPriceAmount,
                            string $studentPriceAmount) : void
    {
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
    public function verifyPrice(Price $price): bool
    {
        return ($price->getPrice() >= 0 && $price->getProduct() != null && $price->getClientType() != null);
    }

    /**
     * Retourne le type de prix concerné par le type de client passé en paramètre
     * @param string $clientType
     * @return string
     */
    public function getClientTypeUseForPrice(string $clientType): string
    {
        $clientTypeReturn = ClientType::ETUDIANT;

        if (in_array($clientType, ClientType::getAll(), true)){
            switch ($clientType){
                case ClientType::ETUDIANT :
                    $clientTypeReturn = ClientType::ETUDIANT;
                    break;
                case ClientType::ASSOCIATION || ClientType::COTISANT :
                    $clientTypeReturn = ClientType::ASSOCIATION;
                    break;
            }
        }

        return $clientTypeReturn;
    }
}