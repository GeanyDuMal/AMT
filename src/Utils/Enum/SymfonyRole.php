<?php

namespace App\Utils\Enum;

final class SymfonyRole extends AbstractEnumClass
{
    /**
     * Role d'admin non-présent car non assignable.
     * Il a été affecté manuellement
     */
    public const USER = "ROLE_USER";
    public const ASSOC = "ROLE_ASSOC";
    public const SECRETAIRE = "ROLE_SECRETAIRE";
    public const TRESORIER = "ROLE_TRESORIER";
    public const PRESIDENT = "ROLE_PRESIDENT";
}