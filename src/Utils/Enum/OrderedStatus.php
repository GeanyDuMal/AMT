<?php

namespace App\Utils\Enum;

final class OrderedStatus extends AbstractEnumClass
{
    public const PAID = "Payé";
    public const WAITING_PAYMENT = "En attente de paiement";
    public const REFUNDED = "Remboursé";
    public const CANCELED = "Annulé";
}