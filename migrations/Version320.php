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
        $this->addSql('ALTER TABLE client CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE parameter ADD association_description VARCHAR(1023) NOT NULL, ADD association_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE parameter ADD link_home_image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD active TINYINT(1) DEFAULT 1 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE product DROP active');
        $this->addSql('ALTER TABLE parameter DROP link_home_image');
        $this->addSql('ALTER TABLE parameter DROP association_name, DROP association_description');
        $this->addSql('ALTER TABLE client CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }
}
