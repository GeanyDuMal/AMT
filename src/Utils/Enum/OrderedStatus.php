<?php

namespace App\Utils\Enum;

final class OrderedStatus extends AbstractEnumClass
{
    public const PAID = "Paye";
    public const WAITING_PAYMENT = "En attente de paiement";
    public const REFUNDED = "Rembourse";
    public const CANCELED = "Annule";
}