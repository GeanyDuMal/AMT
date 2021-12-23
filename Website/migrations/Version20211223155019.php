<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211223155019 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TRIGGER db_aedi.verifCreationClient');
        $this->addSql('CREATE TRIGGER verifCreationClient BEFORE INSERT ON client FOR EACH ROW
                            BEGIN
                                IF(NEW.balance < 0 || ISNULL(NEW.balance)) THEN
                                    SET NEW.balance = 0;
                                END IF;
                                
                                IF(NEW.fidelity_point <> 0 || ISNULL(NEW.fidelity_point)) THEN
                                    SET NEW.fidelity_point = 0;
                                END IF;
                            END;');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TRIGGER db_aedi.verifCreationClient');
        $this->addSql('CREATE TRIGGER verifCreationClient BEFORE INSERT ON client FOR EACH ROW
                            BEGIN
                                IF(NEW.balance < 0 || ISNULL(NEW.balance)) THEN
                                    SET NEW.balance = 0;
                                END IF;
                                
                                IF(NEW.fidelity_point <> 0 || ISNULL(NEW.fidelity_point)) THEN
                                    SET NEW.fidelity_point = 0;
                                END IF;
                                
                                SET NEW.client_type_id = (SELECT id FROM client_type WHERE name = "Etudiant");
                            END;');
    }
}
