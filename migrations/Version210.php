<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration concernant le version 2.1.0 : 
                - Ajout des dates de creations sur les clients et les posts
                - Création de la table password_forgot_request';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE post ADD creation_date DATE NOT NULL');
        $this->addSql('UPDATE post SET creation_date = "2022-02-01"'); // Defini pour les posts déja existant la date de création du site, a modifier ensuite

        $this->addSql('ALTER TABLE client ADD creation_date DATE NOT NULL');
        $this->addSql('UPDATE client SET creation_date = "2022-02-01"'); // Defini pour les clients déja existant la date de création du site, a modifier ensuite

        $this->addSql('CREATE TABLE password_forgot_request (client_id INT NOT NULL, date DATE NOT NULL, confirmation_code VARCHAR(255) NOT NULL, PRIMARY KEY(client_id))');
        $this->addSql('ALTER TABLE password_forgot_request ADD CONSTRAINT FK_FD4D596519EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS password_forgot_request');

        $this->addSql('ALTER TABLE post DROP creation_date');

        $this->addSql('ALTER TABLE client DROP creation_date');
    }
}
