<?php

namespace App\Utils\Enum;

enum OrderedStatusEnum: string {
    case PAID = "Paye";
    case WAITING_PAYMENT = "En attente de paiement";
    case REFUNDED = "Rembourse";
    case CANCELED = "Annule";
}