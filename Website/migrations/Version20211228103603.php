<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211228103603 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client CHANGE client_type_id client_type_id INT NOT NULL');

        $this->addSql('DROP TRIGGER IF EXISTS `db_aedi`.`verifSoldePositif`;
                            CREATE TRIGGER verifSoldeAndFidelity 
                                BEFORE UPDATE ON client FOR EACH ROW
                                BEGIN
                                    IF(NEW.balance < 0) THEN
                                        SIGNAL SQLSTATE "45000"
                                        SET MESSAGE_TEXT = "Solde negatif, erreur Update";
                                    END IF;
                                    IF(NEW.fidelity_point >= 200)THEN
                                        BEGIN
                                            SET NEW.fidelity_point = NEW.fidelity_point - 150;
                                            SET NEW.balance = NEW.balance + 0.8; 
                                        END;
                                    END IF;
                                END;');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client CHANGE client_type_id client_type_id INT DEFAULT NULL');
        $this->addSql('DROP TRIGGER IF EXISTS `db_aedi`.`verifSoldeAndFidelity`;
                            CREATE TRIGGER verifSoldePositif 
                                BEFORE UPDATE ON client FOR EACH ROW
                                BEGIN
                                    IF(NEW.balance < 0) THEN
                                        SIGNAL SQLSTATE "45000"
                                        SET MESSAGE_TEXT = "Solde negatif, erreur Update";
                                    END IF;
                                END;');
    }
}
