<?php

namespace App\Utils\Enum;

enum ClientTypeEnum: string {
    case ETUDIANT = "Etudiant";
    case COTISANT = "Cotisant";
    case ASSOCIATION = "Association";
    case ADMIN = "Administrateur";
}