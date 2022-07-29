<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220117221456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Modifcation nom de table + trigger';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B33E1689A');
        $this->addSql('CREATE TABLE `ordered` (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, payment_type_id INT NOT NULL, ordered_at DATETIME NOT NULL, INDEX IDX_F529939819EB6921 (client_id), INDEX IDX_F5299398DC058279 (payment_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `ordered` ADD CONSTRAINT FK_F529939819EB6921 FOREIGN KEY (client_id) REFERENCES client (id)  ON DELETE SET NULL');
        $this->addSql('ALTER TABLE `ordered` ADD CONSTRAINT FK_F5299398DC058279 FOREIGN KEY (payment_type_id) REFERENCES payment_type (id)');
        $this->addSql('DROP INDEX IDX_6117D13B33E1689A ON purchase');
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B4584665A');
        $this->addSql('DROP TABLE command');
        $this->addSql('ALTER TABLE purchase CHANGE command_id ordered_id INT NOT NULL');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B8D9F6D38 FOREIGN KEY (ordered_id) REFERENCES `ordered` (id)');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_6117D13B8D9F6D38 ON purchase (ordered_id)');
        $this->addSql('ALTER TABLE client CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('CREATE TRIGGER verifClientNonNull 
                            BEFORE INSERT ON `ordered` FOR EACH ROW
                            IF (ISNULL(NEW.client_id) && (SELECT name FROM payment_type WHERE id = NEW.payment_type_id) = "Solde") THEN
                                    SIGNAL SQLSTATE "45000"
                                    SET MESSAGE_TEXT = "Type de paiement incorrect, solde + client inconnu, erreur creation commande";
                            END IF;');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B8D9F6D38');
        $this->addSql('CREATE TABLE command (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, payment_type_id INT NOT NULL, ordered_at DATETIME NOT NULL, INDEX IDX_8ECAEAD4DC058279 (payment_type_id), INDEX IDX_8ECAEAD419EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE command ADD CONSTRAINT FK_8ECAEAD4DC058279 FOREIGN KEY (payment_type_id) REFERENCES payment_type (id)');
        $this->addSql('ALTER TABLE command ADD CONSTRAINT FK_8ECAEAD419EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B4584665A');
        $this->addSql('DROP INDEX IDX_6117D13B8D9F6D38 ON purchase');
        $this->addSql('DROP TABLE `ordered`');
        $this->addSql('ALTER TABLE purchase CHANGE order_id command_id INT NOT NULL');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B33E1689A FOREIGN KEY (command_id) REFERENCES command (id)');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_6117D13B33E1689A ON purchase (command_id)');
        $this->addSql('ALTER TABLE client CHANGE roles roles LONGTEXT DEFAULT \'ROLE_USER\' NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('CREATE TRIGGER verifClientNonNull 
                            BEFORE INSERT ON `command` FOR EACH ROW
                            IF (ISNULL(NEW.client_id) && (SELECT name FROM payment_type WHERE id = NEW.payment_type_id) = "Solde") THEN
                                    SIGNAL SQLSTATE "45000"
                                    SET MESSAGE_TEXT = "Type de paiement incorrect, solde + client inconnu, erreur creation commande";
                            END IF;');
    }
}
