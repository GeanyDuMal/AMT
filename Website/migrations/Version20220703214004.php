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
        // Modification du champs clientType sur Client
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C74404559771C8EE');
        $this->addSql('DROP INDEX IDX_C74404559771C8EE ON client');
        $this->addSql('ALTER TABLE client ADD client_type VARCHAR(255) NOT NULL');
        $this->addSql('UPDATE client SET client_type = (SELECT client_type.name FROM client_type, client WHERE client.client_type_id = client_type.id)');
        $this->addSql('ALTER TABLE client DROP client_type_id');

        // Modification du champs clientType sur Price
        $this->addSql('ALTER TABLE price DROP FOREIGN KEY FK_CAC822D99771C8EE');
        $this->addSql('DROP INDEX IDX_CAC822D99771C8EE ON price');
        $this->addSql('ALTER TABLE price DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE price ADD client_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE price ADD PRIMARY KEY (product_id, client_type)');
        $this->addSql('UPDATE price SET client_type = (SELECT client_type.name FROM client_type, price WHERE price.client_type_id = client_type.id)');
        $this->addSql('ALTER TABLE price DROP client_type_id');

        // Modification du champs paymentType sur Ordered
        $this->addSql('ALTER TABLE ordered DROP FOREIGN KEY FK_F5299398DC058279');
        $this->addSql('DROP INDEX IDX_C3121F99DC058279 ON ordered');
        $this->addSql('ALTER TABLE ordered ADD payment_type VARCHAR(255) NOT NULL');
        $this->addSql('UPDATE ordered SET payment_type = (SELECT payment_type.name FROM payment_type, ordered WHERE ordered.payment_type_id = payment_type.id)');
        $this->addSql('ALTER TABLE ordered DROP payment_type_id');

        // Modification du champs postType sur Post
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DF8A43BA0');
        $this->addSql('DROP INDEX IDX_5A8A6C8DF8A43BA0 ON post');
        $this->addSql('ALTER TABLE post ADD post_type VARCHAR(255) NOT NULL');
        $this->addSql('UPDATE post SET post_type = (SELECT post_type.name FROM post_type, post WHERE post.post_type_id = post_type.id)');
        $this->addSql('ALTER TABLE post DROP post_type_id');

        // Modification du champs productType sur Product
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD14959723');
        $this->addSql('DROP INDEX IDX_D34A04AD14959723 ON product');
        $this->addSql('ALTER TABLE product ADD product_type VARCHAR(255) NOT NULL');
        $this->addSql('UPDATE product SET product_type = (SELECT product_type.name FROM product_type, product WHERE product.product_type_id = product_type.id)');
        $this->addSql('ALTER TABLE product DROP product_type_id');

        // Modification du champs associationRole sur Association
        $this->addSql('ALTER TABLE association DROP FOREIGN KEY FK_FD8521CCD60322AC');
        $this->addSql('DROP INDEX IDX_FD8521CCD60322AC ON association');
        $this->addSql('ALTER TABLE association ADD role VARCHAR(255) NOT NULL');
        $this->addSql('UPDATE association SET role = (SELECT association_role.name FROM association_role, association WHERE association.role_id = association_role.id)');
        $this->addSql('ALTER TABLE association DROP role_id');

        // A effectuer une fois la migration complète terminé
        $this->addSql('DROP TABLE IF EXISTS client_type');
        $this->addSql('DROP TABLE IF EXISTS payment_type');
        $this->addSql('DROP TABLE IF EXISTS post_type');
        $this->addSql('DROP TABLE IF EXISTS product_type');
        $this->addSql('DROP TABLE IF EXISTS association_role');
    }

    public function down(Schema $schema): void
    {
        // Creation ClientType
        $this->addSql('CREATE TABLE client_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT  ENGINE = InnoDB');
        $this->addSql('INSERT INTO client_type (name) VALUES ("Association")');
        $this->addSql('INSERT INTO client_type (name) VALUES ("Etudiant")');

        // Creation PaymentType
        $this->addSql('CREATE TABLE payment_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT ENGINE = InnoDB');
        $this->addSql('INSERT INTO payment_type (name) VALUES ("Carte Bancaire");');
        $this->addSql('INSERT INTO payment_type (name) VALUES ("Espece")');
        $this->addSql('INSERT INTO payment_type (name) VALUES ("Solde")');

        // Creation PostType
        $this->addSql('CREATE TABLE post_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT ENGINE = InnoDB');
        $this->addSql('INSERT INTO post_type (name) VALUES ("Event")');
        $this->addSql('INSERT INTO post_type (name) VALUES ("Autre")');

        // Creation ProductType
        $this->addSql('CREATE TABLE product_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT ENGINE = InnoDB');
        $this->addSql('INSERT INTO product_type (name) VALUES ("Snack")');
        $this->addSql('INSERT INTO product_type (name) VALUES ("Boisson")');

        // Creation AssociationRole
        $this->addSql('CREATE TABLE association_role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT ENGINE = InnoDB');
        $this->addSql('INSERT INTO association_role (name) VALUES ("President")');
        $this->addSql('INSERT INTO association_role (name) VALUES ("Tresorier")');
        $this->addSql('INSERT INTO association_role (name) VALUES ("Vice President")');
        $this->addSql('INSERT INTO association_role (name) VALUES ("Secretaire")');
        $this->addSql('INSERT INTO association_role (name) VALUES ("Membre")');

        // Ajout du lien entre ClientType et Client
        $this->addSql('ALTER TABLE client ADD client_type_id INT NOT NULL');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C74404559771C8EE FOREIGN KEY (client_type_id) REFERENCES client_type (id)');
        $this->addSql('CREATE INDEX IDX_C74404559771C8EE ON client (client_type_id)');
        $this->addSql('UPDATE client SET client_type_id = (SELECT client_type.id FROM client_type, client WHERE client.client_type = client_type.name)');
        $this->addSql('ALTER TABLE client DROP client_type');

        // Ajout du lien entre ClientType et Price
        $this->addSql('ALTER TABLE price ADD client_type_id INT NOT NULL');
        $this->addSql('ALTER TABLE price DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE price ADD PRIMARY KEY (product_id, client_type_id)');
        $this->addSql('ALTER TABLE price ADD CONSTRAINT FK_CAC822D99771C8EE FOREIGN KEY (client_type_id) REFERENCES client_type (id)');
        $this->addSql('CREATE INDEX IDX_CAC822D99771C8EE ON price (client_type_id)');
        $this->addSql('UPDATE price SET client_type_id = (SELECT client_type.id FROM client_type, price WHERE price.client_type_id = client_type.id)');
        $this->addSql('ALTER TABLE price DROP client_type');

        // Ajout du lien entre PaymentType et Ordered
        $this->addSql('ALTER TABLE ordered ADD payment_type_id INT NOT NULL');
        $this->addSql('ALTER TABLE ordered ADD CONSTRAINT FK_F5299398DC058279 FOREIGN KEY (payment_type_id) REFERENCES payment_type (id)');
        $this->addSql('CREATE INDEX IDX_C3121F99DC058279 ON ordered (payment_type_id)');
        $this->addSql('UPDATE ordered SET payment_type_id = (SELECT payment_type.id FROM payment_type, ordered WHERE ordered.payment_type_id = payment_type.id)');
        $this->addSql('ALTER TABLE ordered DROP payment_type');

        // Ajout du lien entre PostType et Post
        $this->addSql('ALTER TABLE post ADD post_type_id INT NOT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF8A43BA0 FOREIGN KEY (post_type_id) REFERENCES post_type (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DF8A43BA0 ON post (post_type_id)');
        $this->addSql('UPDATE post SET post_type_id = (SELECT post_type.id FROM post_type, post WHERE post.post_type_id = post_type.id)');
        $this->addSql('ALTER TABLE post DROP post_type');

        // Ajout du lien entre ProductType et Product
        $this->addSql('ALTER TABLE product ADD product_type_id INT NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD14959723 FOREIGN KEY (product_type_id) REFERENCES product_type (id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD14959723 ON product (product_type_id)');
        $this->addSql('UPDATE product SET product_type_id = (SELECT product_type.id FROM product_type, product WHERE product.product_type_id = product_type.id)');
        $this->addSql('ALTER TABLE product DROP product_type');

        // Creation du lien entre AssociationRole et Association
        $this->addSql('ALTER TABLE association ADD role_id INT NOT NULL');
        $this->addSql('ALTER TABLE association ADD CONSTRAINT FK_FD8521CCD60322AC FOREIGN KEY (role_id) REFERENCES association_role (id)');
        $this->addSql('CREATE INDEX IDX_FD8521CCD60322AC ON association (role_id)');
        $this->addSql('UPDATE association SET association_role_id = (SELECT association_role.id FROM association_role, association WHERE association.association_role_id = association_role.id)');
        $this->addSql('ALTER TABLE association DROP role');
    }
}
