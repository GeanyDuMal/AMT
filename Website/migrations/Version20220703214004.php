<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220703214004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration des table de type vers une chaine de caractere contenant le type';
    }

    public function up(Schema $schema): void
    {
        // modification du champs ClientType sur Client
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C74404559771C8EE');
        $this->addSql('DROP INDEX IDX_C74404559771C8EE ON client');
        $this->addSql('ALTER TABLE client ADD client_type VARCHAR(255) NOT NULL');
        $this->addSql('UPDATE client SET client_type = (SELECT client_type.name FROM client_type, client WHERE client.client_type_id = client_type.id)');
        $this->addSql('ALTER TABLE client DROP client_type_id');

        // modification du champs ClientType sur Client
        $this->addSql('ALTER TABLE price DROP FOREIGN KEY FK_CAC822D99771C8EE');
        $this->addSql('DROP INDEX IDX_CAC822D99771C8EE ON price');
        $this->addSql('ALTER TABLE price DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE price ADD client_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE price ADD PRIMARY KEY (product_id, client_type)');
        $this->addSql('UPDATE price SET client_type = (SELECT client_type.name FROM client_type, price WHERE price.client_type_id = client_type.id)');
        $this->addSql('ALTER TABLE price DROP client_type_id');

        // A effectuer une fois la migration complète terminé
        $this->addSql('DROP TABLE IF EXISTS client_type');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE client_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('INSERT INTO client_type (name) VALUES ("Association");');
        $this->addSql('INSERT INTO client_type (name) VALUES ("Etudiant");');

        $this->addSql('ALTER TABLE client ADD client_type_id INT NOT NULL');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C74404559771C8EE FOREIGN KEY (client_type_id) REFERENCES client_type (id)');
        $this->addSql('CREATE INDEX IDX_C74404559771C8EE ON client (client_type_id)');
        $this->addSql('UPDATE client SET client_type_id = (SELECT client_type.id FROM client_type, client WHERE client.client_type = client_type.name)');
        $this->addSql('ALTER TABLE client DROP client_type');

        $this->addSql('ALTER TABLE price DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE price ADD client_type_id INT NOT NULL');
        $this->addSql('ALTER TABLE price ADD CONSTRAINT FK_CAC822D99771C8EE FOREIGN KEY (client_type_id) REFERENCES client_type (id)');
        $this->addSql('CREATE INDEX IDX_CAC822D99771C8EE ON price (client_type_id)');
        $this->addSql('ALTER TABLE price ADD PRIMARY KEY (product_id, client_type_id)');
        $this->addSql('UPDATE price SET client_type_id = (SELECT client_type.id FROM client_type, price WHERE price.client_type_id = client_type.id)');
        $this->addSql('ALTER TABLE price DROP client_type');

    }
}
