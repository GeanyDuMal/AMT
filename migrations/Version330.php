<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version330 extends AbstractMigration {
    public function getDescription(): string {
        return 'Migration concernant la version 3.3.0 : 
                - Ajout de la conservation du prix dans l\'achat
                - Ajout de la conservation du type de client dans la commande
                - Ajout du statut de commande
                - MAJ des lignes existantes';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE ordered ADD client_type_at_order VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE purchase ADD unitary_price NUMERIC(5, 2) NOT NULL');
        $this->addSql('ALTER TABLE ordered ADD status VARCHAR(255) NOT NULL');
        $this->addSql('UPDATE ordered o
                           SET o.client_type_at_order = "Etudiant";');
        $this->addSql('UPDATE ordered o
                           LEFT JOIN client c ON o.client_id = c.id
                           SET o.status = "Paye",
                               o.client_type_at_order = c.client_type
                           WHERE NOT(o.client_id IS NULL);');
        $this->addSql('UPDATE purchase pu
                           LEFT JOIN price pr ON pu.product_id = pr.product_id
                           LEFT JOIN ordered o ON pu.ordered_id = o.id
                           LEFT JOIN client c ON o.client_id = c.id
                           SET pu.unitary_price = pr.price
                           WHERE pr.client_type = "Etudiant" 
                           AND (o.client_id IS NULL 
                           OR c.client_type = "Etudiant");');
        $this->addSql('UPDATE purchase pu
                           LEFT JOIN price pr ON pu.product_id = pr.product_id 
                           LEFT JOIN ordered o ON pu.ordered_id = o.id
                           LEFT JOIN client c ON o.client_id = c.id
                           SET pu.unitary_price = pr.price
                           WHERE pr.client_type = "Association" 
                           AND (c.client_type = "Association" 
                           OR c.client_type = "Administrateur" 
                           OR c.client_type = "Cotisant");');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE ordered DROP client_type_at_order');
        $this->addSql('ALTER TABLE purchase DROP unitary_price');
        $this->addSql('ALTER TABLE ordered DROP status');
    }
}
