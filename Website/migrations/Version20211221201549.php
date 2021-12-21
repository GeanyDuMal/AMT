<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211221201549 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE association (member_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_FD8521CCD60322AC (role_id), PRIMARY KEY(member_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE association_role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, client_type_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, login VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, balance NUMERIC(5, 2) DEFAULT \'0\', fidelity_point INT DEFAULT 0, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', UNIQUE INDEX UNIQ_C7440455AA08CB10 (login), INDEX IDX_C74404559771C8EE (client_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE command (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, payment_type_id INT NOT NULL, ordered_at DATETIME NOT NULL, INDEX IDX_8ECAEAD419EB6921 (client_id), INDEX IDX_8ECAEAD4DC058279 (payment_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, post_type_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, image_link VARCHAR(255) NOT NULL, INDEX IDX_5A8A6C8DF8A43BA0 (post_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE price (product_id INT NOT NULL, client_type_id INT NOT NULL, price NUMERIC(5, 2) NOT NULL, INDEX IDX_CAC822D94584665A (product_id), INDEX IDX_CAC822D99771C8EE (client_type_id), PRIMARY KEY(product_id, client_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, product_type_id INT NOT NULL, name VARCHAR(255) NOT NULL, quantity_stock INT NOT NULL, image_link VARCHAR(255) NOT NULL, INDEX IDX_D34A04AD14959723 (product_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE purchase (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, command_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_6117D13B4584665A (product_id), INDEX IDX_6117D13B33E1689A (command_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE association ADD CONSTRAINT FK_FD8521CC7597D3FE FOREIGN KEY (member_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE association ADD CONSTRAINT FK_FD8521CCD60322AC FOREIGN KEY (role_id) REFERENCES association_role (id)');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C74404559771C8EE FOREIGN KEY (client_type_id) REFERENCES client_type (id)');
        $this->addSql('ALTER TABLE command ADD CONSTRAINT FK_8ECAEAD419EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE command ADD CONSTRAINT FK_8ECAEAD4DC058279 FOREIGN KEY (payment_type_id) REFERENCES payment_type (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF8A43BA0 FOREIGN KEY (post_type_id) REFERENCES post_type (id)');
        $this->addSql('ALTER TABLE price ADD CONSTRAINT FK_CAC822D94584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE price ADD CONSTRAINT FK_CAC822D99771C8EE FOREIGN KEY (client_type_id) REFERENCES client_type (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD14959723 FOREIGN KEY (product_type_id) REFERENCES product_type (id)');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B33E1689A FOREIGN KEY (command_id) REFERENCES command (id)');

        /* Creation des lignes references (Type)
         * Peuvent etre commenté ici et decommenté dans les fixture
         * Permet de créer les types obligatoires lors de l'importation de la base de données et pas uniquement avec le jeu de données de test
         */

        //Type de Payement
        $this->addSql('INSERT INTO payment_type (name) VALUES ("Carte Bancaire");');
        $this->addSql('INSERT INTO payment_type (name) VALUES ("Espece");');
        $this->addSql('INSERT INTO payment_type (name) VALUES ("Solde");');

        //Role de l'association (Ceux obligatoires)
        $this->addSql('INSERT INTO association_role (name) VALUES ("President");');
        $this->addSql('INSERT INTO association_role (name) VALUES ("Tresorier");');
        $this->addSql('INSERT INTO association_role (name) VALUES ("Vice President");');
        $this->addSql('INSERT INTO association_role (name) VALUES ("Secretaire");');
        $this->addSql('INSERT INTO association_role (name) VALUES ("Membre");');

        //Type de Client
        $this->addSql('INSERT INTO client_type (name) VALUES ("Association");');
        $this->addSql('INSERT INTO client_type (name) VALUES ("Etudiant");');

        //Type de Post
        $this->addSql('INSERT INTO post_type (name) VALUES ("Event");');
        $this->addSql('INSERT INTO post_type (name) VALUES ("Autre");');

        //Type de Produit
        $this->addSql('INSERT INTO product_type (name) VALUES ("Snack");');
        $this->addSql('INSERT INTO product_type (name) VALUES ("Boisson");');


        //Creation des triggers
        //Trigger table Association
        $this->addSql('CREATE TRIGGER modifClientTypeAdd 
                            AFTER INSERT ON association FOR EACH ROW
                            UPDATE client SET client_type_id = (SELECT id 
                                                                FROM client_type
									                            WHERE name = "Association")
				            WHERE client.id = NEW.member_id');
        $this->addSql('CREATE TRIGGER modifClientTypeRemove 
                            AFTER DELETE ON association FOR EACH ROW
                            UPDATE client SET client_type_id = (SELECT id 
                                                                FROM client_type
									                            WHERE name = "Etudiant")
				            WHERE client.id = OLD.member_id');

        //Trigger table Purchase
        $this->addSql('CREATE TRIGGER verifDispoProduit 
                            BEFORE INSERT ON `purchase` FOR EACH ROW
                            IF((SELECT quantity_stock FROM product WHERE id = NEW.product_id) < NEW.quantity) THEN
                                    SIGNAL SQLSTATE "45000"
                                    SET MESSAGE_TEXT = "Stock vide ou insuffisant, erreur creation purchase";
                            END IF;');
        $this->addSql('CREATE TRIGGER removeQteProductFromPurchase 
                            AFTER INSERT ON `purchase` FOR EACH ROW
                            UPDATE product SET quantity_stock = quantity_stock - NEW.quantity
                            WHERE product.id = NEW.product_id');

        //Trigger table Command
        $this->addSql('CREATE TRIGGER verifClientNonNull 
                            BEFORE INSERT ON command FOR EACH ROW
                            IF (ISNULL(NEW.client_id) && (SELECT name FROM payment_type WHERE id = NEW.payment_type_id) = "Solde") THEN
                                    SIGNAL SQLSTATE "45000"
                                    SET MESSAGE_TEXT = "Type de paiement incorrect, solde + client inconnu, erreur creation command";
                            END IF;');

        //Trigger table Client
        //Sur ce trigger, setup de balance et fidelity a 0 car le default ne fonctionne pas avec $manager->flush()
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
        $this->addSql('CREATE TRIGGER verifSoldePositif 
                            BEFORE UPDATE ON client FOR EACH ROW
                            IF(NEW.balance < 0) THEN
                                SIGNAL SQLSTATE "45000"
                                SET MESSAGE_TEXT = "Solde negatif, erreur Update";
                            END IF;');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE association DROP FOREIGN KEY FK_FD8521CCD60322AC');
        $this->addSql('ALTER TABLE association DROP FOREIGN KEY FK_FD8521CC7597D3FE');
        $this->addSql('ALTER TABLE command DROP FOREIGN KEY FK_8ECAEAD419EB6921');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C74404559771C8EE');
        $this->addSql('ALTER TABLE price DROP FOREIGN KEY FK_CAC822D99771C8EE');
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B33E1689A');
        $this->addSql('ALTER TABLE command DROP FOREIGN KEY FK_8ECAEAD4DC058279');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DF8A43BA0');
        $this->addSql('ALTER TABLE price DROP FOREIGN KEY FK_CAC822D94584665A');
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B4584665A');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD14959723');

        $this->addSql('DROP TABLE association');
        $this->addSql('DROP TABLE association_role');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE client_type');
        $this->addSql('DROP TABLE command');
        $this->addSql('DROP TABLE payment_type');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE post_type');
        $this->addSql('DROP TABLE price');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_type');
        $this->addSql('DROP TABLE purchase');

        $this->addSql('DROP TRIGGER db_aedi.modifClientTypeAdd');
        $this->addSql('DROP TRIGGER db_aedi.modifClientTypeRemove');
        $this->addSql('DROP TRIGGER db_aedi.verifDispoProduit');
        $this->addSql('DROP TRIGGER db_aedi.removeQteProductFromPurchase');
        $this->addSql('DROP TRIGGER db_aedi.verifClientNonNull');
        $this->addSql('DROP TRIGGER db_aedi.verifSoldePositif');
    }
}
