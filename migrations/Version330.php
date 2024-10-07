<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version330 extends AbstractMigration {
    public function getDescription(): string {
        return 'Migration concernant la version 3.3.0 : 
                - Ajout de la conservation du prix dans l\'achat
                - Ajout de la conservation du type de client dans la commande';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE ordered ADD client_type_at_order VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE purchase ADD unitary_price NUMERIC(5, 2) NOT NULL');
        $this->addSql('ALTER TABLE ordered ADD status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE ordered CHANGE payment_type payment_type VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE ordered CHANGE payment_type payment_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE ordered DROP client_type_at_order');
        $this->addSql('ALTER TABLE purchase DROP unitary_price');
        $this->addSql('ALTER TABLE ordered DROP status');
    }
}
