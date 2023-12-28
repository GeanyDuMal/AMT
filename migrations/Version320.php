<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration concernant le version 3.2.0 : 
                - Gestion du parametrage de l\'écran d\'accueil';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE parameter ADD association_name VARCHAR(255) NOT NULL, CHANGE link_logo link_logo VARCHAR(255) NOT NULL, CHANGE amount_balance_to_add_after_exchange amount_balance_to_add_after_exchange NUMERIC(10, 2) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE parameter DROP association_name, CHANGE link_logo link_logo VARCHAR(255) DEFAULT NULL, CHANGE amount_balance_to_add_after_exchange amount_balance_to_add_after_exchange NUMERIC(5, 2) NOT NULL');
    }
}
