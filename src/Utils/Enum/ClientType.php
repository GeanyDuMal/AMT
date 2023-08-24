<?php

namespace App\Utils\Enum;

final class ClientType extends AbstractEnumClass
{
    public const ETUDIANT = "Etudiant";
    public const COTISANT = "Cotisant";
    public const ASSOCIATION = "Association";
    public const ADMIN = "Administrateur";
}