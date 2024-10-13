<?php

namespace App\Utils\Enum;

enum MemberRoleEnum: string {
    case MEMBRE = "Membre";
    case SECRETAIRE = "Secretaire";
    case TRESORIER = "Tresorier";
    case VICE_PRESIDENT = "Vice President";
    case PRESIDENT = "President";
}