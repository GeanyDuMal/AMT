<?php

namespace App\Utils\Enum;

enum PaymentTypeEnum: string {
    case CARTE_BANCAIRE = "Carte Bancaire";
    case ESPECE = "Espece";
    case SOLDE = "Solde";
}