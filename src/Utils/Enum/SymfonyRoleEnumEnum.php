<?php

namespace App\Utils\Enum;

enum SymfonyRoleEnum: string {
    case USER = "ROLE_USER";
    case ASSOC = "ROLE_ASSOC";
    case SECRETAIRE = "ROLE_SECRETAIRE";
    case TRESORIER = "ROLE_TRESORIER";
    case PRESIDENT = "ROLE_PRESIDENT";
    case ADMIN = "ROLE_ADMIN";
}