<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version331 extends AbstractMigration {
    public function getDescription(): string {
        return 'Migration concernant la version 3.3.1 : 
                - Correction bug prod sur la nullité de l\'attribut paymentType de Ordered';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE ordered CHANGE payment_type payment_type VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE ordered CHANGE payment_type payment_type VARCHAR(255) NOT NULL');
    }
}
