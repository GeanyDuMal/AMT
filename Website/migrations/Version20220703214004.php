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
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C74404559771C8EE');
        $this->addSql('DROP INDEX IDX_C74404559771C8EE ON client');
        $this->addSql('ALTER TABLE client ADD client_type VARCHAR(255) NOT NULL');
        $this->addSql('UPDATE client SET client_type = (SELECT client_type.name FROM client_type, client WHERE client.client_type_id = client_type.id)');
        $this->addSql('ALTER TABLE client DROP client_type_id');

        // A effectuer une fois la migration complète terminé
        $this->addSql('DROP TABLE IF EXISTS client_type');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client ADD client_type_id INT NOT NULL, DROP client_type');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C74404559771C8EE FOREIGN KEY (client_type_id) REFERENCES client_type (id)');
        $this->addSql('CREATE INDEX IDX_C74404559771C8EE ON client (client_type_id)');
    }
}
